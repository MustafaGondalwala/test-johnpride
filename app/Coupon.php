<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model{

    protected $table = 'coupons';

    protected $guarded = ['id'];

    protected $fillable = [];

    public $timestamps = false;

    public function couponUsers(){
        return $this->belongsToMany('App\User','coupon_users','coupon_id','user_id');
    }
}