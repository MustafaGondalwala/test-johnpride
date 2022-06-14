<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Property extends Model{

    protected $table = 'properties';

    protected $guarded = ['id'];

    protected $fillable = [
        
        'parent_id',
        'name',
        'slug',
        'description',
        'image',
        'sort_order',
        'meta_title',
        'meta_keyword',
        'meta_description',
        'status'
    ];

    public function getproducts()
    {

        return $this->belongsToMany('App\Product', 'product_properties', 'property_id', 'product_id');


    }



}