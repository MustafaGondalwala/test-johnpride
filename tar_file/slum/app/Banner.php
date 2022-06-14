<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model{
    
    protected $table = 'banners';

    protected $guarded = ['id'];
    

    protected $fillable = [
        'title',
        'subtitle',
        'brief',
        'page',
        'image',
        'link',
        'sort_order',
        'status'
    ];

    public function Images() {
        return $this->hasMany('App\BannerImage');
    }
}