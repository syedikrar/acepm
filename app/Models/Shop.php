<?php

namespace App\Models;

use App\Models\User;
use App\Contracts\FreePassRepository;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Class Shop.
 *
 * @package namespace App\Models;
 */
class Shop extends BaseModel
{

    protected $hidden = [
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'domain',
        'token',
        'name',
        'theme_id',
        'shopify_plan'
    ];

    public function owner()
    {
        return $this->users()->wherePivot('role', 'owner');

    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    /**
     * @return HasOne
     */
    public function freePass()
    {
        return $this->hasOne('App\Models\FreePass', 'shop', 'domain');
    }

    /**
     * @return HasOne
     */
    public function billing()
    {
        return $this->hasOne('App\Models\Billing');
    }

    public function oldBills()
    {
        return $this->hasMany(Billing::class, 'shop_domain', 'domain');
    }

    public function lastBill()
    {
        return $this->oldBills()->withTrashed()->where('deleted_at', '!=', null)->get()->last();
    }

    /**
     * helper method to check if shop should get free access to our app
     * @return bool
     */
    public function shouldGetFreePass()
    {
        /** @var \App\Repositories\FreePassRepositoryEloquent $freePassRepo */
        $freePassRepo = \App::make(FreePassRepository::class);
        $shouldGet = $freePassRepo
            ->findWhere([
                'shop'      => $this->domain,
                'active'    => true
            ])
            ->first();

        return $shouldGet != null;
    }

}
