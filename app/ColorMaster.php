<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ColorMaster extends Model{
    
    protected $table = 'colors_master';

    protected $guarded = ['id'];

    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'code',
        'status'
    ];

    public function countProducts(){
        return $this->hasMany('App\Product', 'color_id')->count();
    }

    
}