<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model{

    protected $table = 'orders';

    protected $guarded = ['id'];

    protected $fillable = [];

   public function customerDetails(){
       return $this->belongsTo('App\User', 'user_id');
   }

   public function orderItems(){
       return $this->hasMany('App\OrderItem', 'order_id');
   }

   public function billingCity(){
       return $this->belongsTo('App\City', 'billing_city');
   }

   public function billingState(){
       return $this->belongsTo('App\State', 'billing_state');
   }

   public function billingCountry(){
       return $this->belongsTo('App\Country', 'billing_country');
   }

   public function shippingCity(){
       return $this->belongsTo('App\City', 'shipping_city');
   }

   public function shippingState(){
       return $this->belongsTo('App\State', 'shipping_state');
   }

   public function shippingCountry(){
       return $this->belongsTo('App\Country', 'shipping_country');
   }

   public function orderStatusDetails(){
       return $this->belongsTo('App\OrderStatusMaster', 'order_status', 'code');
   }

   public function couponDetails(){
       return $this->belongsTo('App\Coupon', 'coupon_id');
   }



}