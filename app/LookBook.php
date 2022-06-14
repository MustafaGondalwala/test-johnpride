<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LookBook extends Model{
    
    protected $table = 'look_books';

    protected $guarded = ['id'];
    

    protected $fillable = [
        'title',
        'product_sku',
        'url',
        'image',
        'sort_order',
        'featured',
        'status'
    ];

    public function product() {
        return $this->hasOne('App\Product', 'sku','product_sku');
    }

    

}