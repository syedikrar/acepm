<?php

namespace App\Repositories;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Contracts\DiscountCouponRepository;
use App\Models\DiscountCoupon;

/**
 * Class DiscountCouponRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class DiscountCouponRepositoryEloquent extends BaseRepository implements DiscountCouponRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return DiscountCoupon::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }


    /**
     * @param Request $request
     * @return array
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function persist(Request $request)
    {
        $shop = $request->cookie('urgency-shop');
        //----------For CSUI
        $shop = $request->get('csui-shop', $shop);

        $decision = $request->get('decision', null);
        $args = [
            'shop_name'         => $shop,
            'coupon'            => ($decision == 'granted') ? $this->generateCouponCode() : null,
            'status'            => $decision,
            'percentage'        => ($decision == 'granted') ? $request->get('percentage', null) : null
        ];

        if($decision == 'requested') $args['requested_on'] = Carbon::now();
        if($decision == 'availed') $args['availed_on'] = Carbon::now();

        $this->updateOrCreate([
            'shop_name' => $shop
        ], $args);

        return ['status' => true];
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function check(Request $request){
        $shop = $request->cookie('urgency-shop');
        //----------For CSUI
        $shop = $request->get('csui-shop', $shop);
        $code = $request->route('code', null);

        if($code == null) $code = $request->get('code', null);

        $result = $this->findByField('shop_name', $shop)
            ->where('status', '=', 'granted')
            ->where('coupon', '=', $code)
            ->first();
        return $result;
    }

    /**
     * @param Request $request
     */
    public function avail(Request $request){
        $shop = $request->cookie('urgency-shop');
        //----------For CSUI
        $shop = $request->get('csui-shop', $shop);

        $code = $request->route('code', null);
        if($code == null) $code = $request->get('code', null);

        $coupon = $this->findByField('shop_name', $shop)
            ->where('status', '=', 'granted')
            ->where('coupon', '=', $code)
            ->first();

        if($coupon != null) {$coupon->status = 'availed'; $coupon->availed_on = Carbon::now();}
    }

    /**
     * @return string
     */
    private function generateCouponCode(){
        $discountCode = hash('crc32',Str::random(8));
        return $discountCode;
    }

    /**
     * @param $shop
     * @param $price
     * @param $code
     * @return float
     */
    public function getDiscountedPrice($shop, $price, $code){
        $result = $this->findByField('shop_name', $shop->domain)
            ->where('status', '=', 'granted')
            ->where('coupon', '=', $code)
            ->first();

        if(!empty($result)) $price = round($price - (($result->percentage/100) * $price), 2);

        return $price;
    }
}
