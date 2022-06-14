<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CategoryImage extends Model{
    
    protected $table = 'category_images';

    protected $guarded = ['id'];    

    protected $fillable = [
        'category_id',
        'image',
        'is_default'
    ];
}