<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model{
    
    protected $table = 'product_images';

    protected $guarded = ['id'];

    protected $fillable = [
        
        'product_id',
        'image',
        'is_default'

    ];


    
}