<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model{

    protected $table = 'coupons';

    protected $guarded = ['id'];

    protected $fillable = [
        'name',
        'code',
        'type',
        'discount',
        'max_discount_amt',
        'min_amount',
        'max_amount',
        'description',
        'use_limit',
        'start_date',
        'expiry_date',
        'status'
    ];

    public $timestamps = false;
}