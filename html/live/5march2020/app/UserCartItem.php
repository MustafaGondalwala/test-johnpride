<?php
namespace App;
use Illuminate\Database\Eloquent\Model;

class UserCartItem extends Model{

    protected $table = 'user_cart_items';

    protected $guarded = ['id'];

    public $timestamps = false;

    protected $fillable = [
        'session_token',
        'user_id',
        'order_id',
        'product_id',
        'product_name',
        'product_slug',
        'product_sku',
        'product_gender',
        'qty',
        'price',
        'sale_price',
        'cart_price',
        'gst',
        'weight',
        'color_id',
        'color_name',
        'size_id',
        'size_name',
    ];

    public function user(){
        return $this->belongsTo('App\User', 'user_id');
    }

    public function product(){
        return $this->belongsTo('App\Product', 'product_id');
    }
}