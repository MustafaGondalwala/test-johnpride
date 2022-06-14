<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductInventory extends Model{
    
    protected $table = 'product_inventory';

    protected $guarded = ['id'];    

    protected $fillable = [
        'sku',
        'product_id',
        'size_id',
        'size_name',
        'price',
        'stock'
    ];

    public function inventorySize(){
    	return $this->belongsTo('App\Size', 'size_id');
    }

    public function inventoryProduct(){
        return $this->belongsTo('App\Product', 'product_id');
    }

}