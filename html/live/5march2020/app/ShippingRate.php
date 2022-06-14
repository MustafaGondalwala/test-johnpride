<?php

namespace App;

use DB;

use Illuminate\Database\Eloquent\Model;

class ShippingRate extends Model{
    
    protected $table = 'shipping_rates';

    protected $guarded = ['id'];

    protected $fillable = [
        'shipping_zone_id',
        'min_weight',
        'max_weight',
        'rate'
    ];

    public $timestamps = false;


    public function shippingZone(){
        return $this->belongsTo('App\ShippingZone', 'shipping_zone_id');
    }


   

}