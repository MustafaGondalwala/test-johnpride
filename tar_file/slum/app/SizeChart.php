<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SizeChart extends Model{
    
    protected $table = 'size_chart';

    protected $guarded = ['id'];
    

    protected $fillable = [
        'title',
        'image',
        'status'
    ];

    public function countProducts(){
        return $this->hasMany('App\Product', 'size_chart_id')->count();
    }
    
}