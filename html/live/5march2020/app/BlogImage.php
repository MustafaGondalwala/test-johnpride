<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BlogImage extends Model{
	
    protected $table = 'blog_images';

    protected $guarded = ['id'];

    protected $fillable = [
        'blog_id',
        'image',
        'created_at',
        'updated_at'
    ];

    //public $timestamps = false;
}