<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

use Zizaco\Entrust\Traits\EntrustUserTrait;

class User extends Authenticatable{

    protected $table = 'users';

    protected $guarded = ['id'];
    

    protected $fillable = [
        'role_id',
        'first_name',
        'last_name',
        'business_name',
        'email',
        'phone',
        'password',
        'verify_token',
        'is_verified',
        'forgot_token',
        'address',
        'city',
        'state',
        'country',
        'pincode',
        'status',
        'is_wallet',
        'wallet_bal',
        'referer',
        'deleted'
    ];


    public function userAddresses(){
        return $this->hasMany('App\UserAddress', 'user_id');
    }


    public function userWishlist(){
        return $this->hasMany('App\UserWishlist', 'user_id');
    }

    public function wishlistProducts(){
        return $this->belongsToMany('App\Product', 'user_wishlist', 'user_id')->withPivot('user_id', 'product_id');
    }


    public function userState(){
        return $this->belongsTo('App\State', 'state');
    }

    public function userCity(){
        return $this->belongsTo('App\City', 'city');
    }

    public function userCountry(){
        return $this->belongsTo('App\Country', 'country');
    }


}