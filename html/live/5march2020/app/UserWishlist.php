<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserWishlist extends Model{

    protected $table = 'user_wishlist';

    protected $guarded = ['id'];

    protected $fillable = [
        'user_id',
        'product_id',
        'size_id'
    ];


    public function user(){
        return $this->belongsTo('App\User', 'user_id');
    }

    public function product(){
        return $this->belongsTo('App\Product', 'product_id');
    }

    public function size(){
        return $this->belongsTo('App\Size', 'size_id');
    }

}
