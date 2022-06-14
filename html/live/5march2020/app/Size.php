<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Size extends Model{
    
    protected $table = 'sizes';

    protected $guarded = ['id'];
    

    protected $fillable = [
        'name',
        'sort_order',
        'status',
    ];

    /*public function countProducts(){
        return $this->hasMany('product_sizes', 'size_id')->count();
    }*/

    public function countProducts(){
        return $this->belongsToMany('App\Product', 'product_inventory', 'size_id')->count();
    }


}