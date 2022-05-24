<?php
/**
 * Created by PhpStorm.
 * User: Bilal
 * Date: 3/8/2018
 * Time: 02:23 PM
 */

namespace App\Services;


use App\Contracts\BillingRepository;
use App\Contracts\DiscountCouponRepository;
use App\Models\Shop;
use App\Traits\ShopifyTrait;
use Carbon\Carbon;
use Illuminate\Database\Schema\Grammars\ChangeColumn;
use Illuminate\Http\Request;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;

class BillingService
{
    use ShopifyTrait;

    /**
     * @var BillingRepository
     */
    protected $repository;

    /**
     * @var Shop
     */
    protected $shop;
    /**
     * @var DiscountCouponRepository
     */
    private $discountCouponRepository;

    /**
     * BillingService constructor.
     * @param BillingRepository $billingRepository
     * @param DiscountCouponRepository $discountCouponRepository
     */
    public function __construct(BillingRepository $billingRepository, DiscountCouponRepository $discountCouponRepository)
    {
        $this->repository = $billingRepository;
        $this->discountCouponRepository = $discountCouponRepository;
    }

    /**
     * @param Shop $shop
     * @return BillingService
     */
    public function setShop(Shop $shop)
    {
        $this->shop = $shop;
        return $this;
    }

    /**
     * Check if we should charge the customer or whitelist them
     * @param null $plan
     * @return string
     */
    public function inspect($plan = null)
    {
        $affiliatePlans = ['partner_test', 'affiliate'];

        if (!env('SHOPIFY_CHARGE')) {
            if (env('SHOPIFY_BILLING_PLANS')) $this->enroll('enterprise', null);
            return ['target' => 'loggedin', 'redirect' => null];
        } elseif (env('SHOPIFY_SUPPORT_FREEPASS') && $this->shop->freePass->active == true) {
            return ['target' => 'loggedin', 'redirect' => null];
        } elseif (env('SHOPIFY_SUPPORT_AFFILIATES') && in_array($this->shop->shopify_plan, $affiliatePlans)) {
            return ['target' => 'loggedin', 'redirect' => null];
        } elseif (env('SHOPIFY_BILLING_PLANS')) {
            return ['target' => 'loggedin', 'redirect' => null];
        } else {
            $this->setShop($this->shop);
            $this->cleanup();
            return $this->enroll($plan);
        }
    }

    /**
     * enroll a shop into billing module
     * @param null $plan
     * @param null $couponCode
     * @return mixed
     */
    public function enroll($plan = null, $couponCode = null)
    {
        try {
            if ($plan != null) {
                $planDetails = config('plans')[$plan];
                if ($planDetails['price'] == 0 || !env('SHOPIFY_CHARGE')) {
                    $this->repository->create([
                        'shop_id'       => $this->shop->id,
                        'shop_domain'   => $this->shop->domain,
                        'id'            => 111111,
                        'plan'          => $plan,
                        'price'         => 0,
                        'status'        => 'active',
                        'trial_days'    => 0
                    ]);

                    return [
                        'status'    => 'charged',
                        'redirect'  => null
                    ];
                }
            }
            $trial = $this->calculateTrial();
            $shopify = $this->getShopifyObj($this->shop);
            $price = $this->discountCouponRepository->getDiscountedPrice($this->shop, env('SHOPIFY_PRICE'), $couponCode);

            $resp = $shopify->call([
                'METHOD'    => 'POST',
                'URL'       => '/admin/api/'.env('SHOPIFY_API_VERSION').'/recurring_application_charges.json',
                'DATA'      => [
                    'recurring_application_charge' => [
                        'name'          => $trial['message'],
                        'price'         => $price,
                        'return_url'    => route('shopify.charge-callback').'?discountCode='.$couponCode,
                        'trial_days'    => $trial['days'],
                        'test'          => env('SHOPIFY_SANDBOX')
                    ]
                ]
            ]);

            $params = $resp->recurring_application_charge;
            $params->shop_id = $this->shop->id;
            $params->shop_domain = $this->shop->domain;
            $params->plan = $plan;
            $this->repository->create(get_object_vars($params));

            return [
                'status'    => 'billing',
                'redirect'  => $params->confirmation_url
            ];
        } catch (\Exception $e) {
            echo $e->getMessage();
            exit;
        }
    }

    public function approveCharge(Request $request)
    {
        try {
            $chargeId = $request->get('charge_id');
            $billing = $this->repository->findByField('shopify_billing_id', $request->get('charge_id'))->first();
            $this->setShop($billing->shop);
            $shopify = $this->getShopifyObj($billing->shop);
            $charge = $shopify->call(['URL' => 'admin/api/'.env('SHOPIFY_API_VERSION').
                '/recurring_application_charges/'.$chargeId.'.json', 'METHOD' => 'GET']);
            $chargeDetails = $charge->recurring_application_charge;
            $billing->status = $chargeDetails->status;
            $billing->save();

            if ($chargeDetails->status == 'declined') {
                return false;
            } elseif ($chargeDetails->status == 'active') {
                $couponCode = \request()->discountCode;
                if(!empty($couponCode)){
                    $request = new Request();
                    $request->request->set('csui-shop', $this->shop->domain);
                    $request->request->set('decision', 'availed');
                    $this->discountCouponRepository->persist($request);
                }
                return $this->activate($chargeDetails);
            }

        } catch (\Exception $e) {
            echo $e->getMessage();
            return '';
        }
    }

    public function activate($chargeDetails)
    {
        $billing = $this->shop->billing;
        $billing->activated_on = $chargeDetails->activated_on;
        $billing->billing_on = $chargeDetails->billing_on;
        $billing->status = $chargeDetails->status;
        $billing->save();

        return true;
    }

    public function cleanup()
    {
        try {
            $shopify = $this->getShopifyObj($this->shop);
            $oldCharges = $shopify->call(['URL' => 'admin/recurring_application_charges.json', 'METHOD' => 'GET']);

            foreach ($oldCharges->recurring_application_charges as $crntCharge) {
                if ($crntCharge->status == 'active') {
                    $del = $shopify->call(['URL' => 'admin/recurring_application_charges/'.$crntCharge->id.'.json', 'METHOD' => 'DELETE']);
                }
            }
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function optout()
    {
        try {
            $billing = $this->shop->billing;
            if ($billing->status == 'declined') {
                $billing->forceDelete();
            } else {
                $billing->status = 'cancelled';
                $billing->save();
                $billing->delete();
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function hasComplied()
    {
        return $this->shop->billing->status == 'accepted';
    }

    public function hasDeclined()
    {
        return $this->shop->billing->status == 'declined';
    }

    /**
     * helper method to check if use is coming back,
     * 1. if they opted out and coming back within trial period, then continue trial
     * 2. if they opted out and comeback in same month, give remainder as trial
     */
    public function calculateTrial()
    {
        $trial = intval(env('SHOPIFY_TRIAL_DAYS'));
        $message = 'Recurring Application Charge';
        $lastBill = $this->shop->lastBill();

        if ($trial > 0 && $lastBill != null && $lastBill->status != 'declined' && $lastBill->billing_on != null && $lastBill->price != 0) {
            $now = Carbon::now()->addDay();
            $installedOn = $lastBill->activated_on;
            $billedOn = $lastBill->billing_on;
            $uninstalledOn = $lastBill->deleted_at;

            $daysInstalled = $uninstalledOn->diffInDays($installedOn);
            $wasCharged = $uninstalledOn->greaterThan($billedOn);
            $goodThru = $billedOn->copy()->addDays(30);

            $trialDays = $lastBill->trial_days;

            // ----- check if user is still in trial days
            if ($now->diffInDays($uninstalledOn) <= env('SHOPIFY_REINSTALL_BUFFER_DAYS')) {
                $trial = $daysInstalled > $trialDays ? 0 : $trialDays - $daysInstalled;
            }

            // ----- check if charge accepted, and they are billed for the month ...
            if ($wasCharged && $lastBill->status == 'cancelled' && $billedOn->lessThanOrEqualTo($now) && $goodThru->greaterThan($now)) {
                $trial = $now->diffInDays($goodThru);

                $message = 'You were last billed on '.$billedOn->toDateString(). ', so you are good through '. $goodThru->toDateString()
                    .'. Therefore we are giving you rest of the ' . $trial . ' days as free trial and will start charging you after that.';
            }
        }

        return [
            'days'      => $trial,
            'message'   => $message
        ];
    }

}
