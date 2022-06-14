<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductSizeNotification extends Model{
    
    protected $table = 'product_size_notifications';

    protected $guarded = ['id'];    

    protected $fillable = [
        'user_id',
        'product_id',
        'size_id',
        'is_notified',
    ];

    public function notificationUser(){
    	return $this->belongsTo('App\User', 'user_id');
    }

    public function notificationProduct(){
    	return $this->belongsTo('App\Product', 'product_id');
    }

    public function notificationSize(){
    	return $this->belongsTo('App\Size', 'size_id');
    }

}