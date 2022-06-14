<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Review extends Model{

    protected $table = 'reviews';

    protected $guarded = ['id'];

    protected $fillable = [
        'user_id',
        'product_id',
        'heading',
        'comment',
        'rating',
        'status',
    ];


    public function reviewUser(){
        return $this->belongsTo('App\User', 'user_id');
    }

    public function reviewProduct(){
        return $this->belongsTo('App\Product', 'product_id');
    }

}