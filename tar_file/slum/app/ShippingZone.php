<?php

namespace App;

use DB;

use Illuminate\Database\Eloquent\Model;

class ShippingZone extends Model{
    
    protected $table = 'shipping_zones';

    protected $guarded = ['id'];

    protected $fillable = [
        'name',
        'updated_at',
    ];

    public $timestamps = false;

    public function thisZoneCities(){

        $shipping_zones_id = $this->id;

        $select = DB::table('shipping_zones_city')->where('shipping_zones_id', $shipping_zones_id);
        $result = $select->get();

        return $result;
    }

    public function ShippingZonesCities($shipping_zones_id=0, $params=''){

        //$shipping_zones_id = $this->id;

        if($shipping_zones_id > 0){
    	   $select = DB::table('shipping_zones_city')->where('shipping_zones_id', $shipping_zones_id);
        }
        else{
            $select = DB::table('shipping_zones_city')->select('id', 'shipping_zones_id', 'city_id', 'created_at', 'updated_at');
        }

        if(isset($params['not_shipping_zones_id']) && !empty($params['not_shipping_zones_id'])){
            $result = $select->where('shipping_zones_id', '!=', $params['not_shipping_zones_id']);
        }

    	if(isset($params['count']) && !empty($params['count'])){
    		$result = $select->count();
    	}
    	else{
    		$result = $select->get();
    	}

    	return $result;
    }

    public function DeleteShippingZonesCities($shipping_zones_id){

    	$result = DB::table('shipping_zones_city')->where('shipping_zones_id', $shipping_zones_id)->delete();

    	return $result;

    }

     public function Cities(){
    	return DB::table('cities')->get();
    }


}