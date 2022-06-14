<?php
namespace App;

use Illuminate\Database\Eloquent\Model;


class OrderItem extends Model{

    protected $table = 'order_items';

    protected $guarded = ['id'];

    protected $fillable = [
    	'order_id',
    	'product_id',
    	'size_id',
    	'product_name',
    	'size_name',
    	'product_slug',
    	'product_sku',
    	'product_gender',
    	'qty',
    	'price',
    	'sale_price',
    	'item_price',
    	'gst',
    	'weight',
    	'color_id',
    	'color_name',

    ];


    public function productDetail(){
       return $this->belongsTo('App\Product', 'product_id');
   }


}