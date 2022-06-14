<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RelatedProductCategory extends Model{
    
    protected $table = 'related_product_categories';

    protected $guarded = ['id'];    

    protected $fillable = [
        'product_id',
        'category_id'
    ];

    

    public function relatedProduct(){
        return $this->belongsTo('App\Product', 'product_id');
    }

}