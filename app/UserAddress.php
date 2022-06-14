<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model{

    protected $table = 'user_addresses';

    protected $guarded = ['id'];

    protected $fillable = [
        'user_id',
        'type',
        'name',
        'company_name',
        'phone',
        'address',
        'city',
        'state',
        'country',
        'pincode',
        'default_delivery',
        'default_billing',
        'is_default',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'default_delivery' => 'boolean',
        'default_billing' => 'boolean',
    ];


    public function addressState(){
        return $this->belongsTo('App\State', 'state');
    }

    public function addressCity(){
        return $this->belongsTo('App\City', 'city');
    }

    public function addressCountry(){
        return $this->belongsTo('App\Country', 'country');
    }

}
