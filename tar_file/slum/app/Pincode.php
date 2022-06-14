<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pincode extends Model{
    
    protected $table = 'pincodes';

    protected $guarded = ['id'];
    

    protected $fillable = [
        'state_id',
        'city_id',
        'pin',
        'status'
    ];

    public function pincodeState(){
    	return $this->belongsTo('App\State', 'state_id');
    }

    public function pincodeCity(){
    	return $this->belongsTo('App\City', 'city_id');
    }
}