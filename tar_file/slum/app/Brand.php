<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model{
    
    protected $table = 'brands';

    protected $guarded = ['id'];
    

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'image',
        'featured',
        'sort_order',
        'status'
    ];

    public function countProducts(){
        return $this->hasMany('App\Product', 'brand_id')->count();
    }

}