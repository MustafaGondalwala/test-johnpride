<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ShippingZone extends Model{
    
    protected $table = 'shipping_zones';

    protected $guarded = ['id'];

    protected $fillable = [];

    public $timestamps = false;

    public function shippingZoneCities(){
        return $this->belongsToMany('App\City', 'shipping_zones_city', 'shipping_zones_id');
    }


}