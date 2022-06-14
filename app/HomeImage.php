<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HomeImage extends Model{
    
    protected $table = 'home_images';

    protected $guarded = ['id'];
    

    protected $fillable = [
        'title',
        'subtitle',
        'image',
        'link',
        'sort_order',
        'status'
    ];
    
}