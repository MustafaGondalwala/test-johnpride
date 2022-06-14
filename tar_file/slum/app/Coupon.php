<?php

namespace App;

use DB;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $table = 'coupons';

    protected $guarded = ['id'];

    protected $fillable = [
        'name',
        'code',
        'type',
        'discount',
        'use_limit',
        'max_discount',
        'min_amount',
        'start_date',
        'expiry_date',
        'status',
        'created_at',
        'updated_at'
    ];

    public $timestamps = false;
}