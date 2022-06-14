<?php
namespace App;

use Illuminate\Database\Eloquent\Model;


class TempOrderItem extends Model{

    protected $table = 'temp_order_items';

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
        'sub_order_id',
        'sub_order_no',
        'coupon_id',
        'coupon_discount',
        'loyalty_discount',
        'coupon_data',
        'discount',
        'sub_total',
        'total',
        'shipping_charge',
        'loyalty_points',
        'used_wallet_amount',
        'tax',
        'order_status',
        'refund_mode',
        'reason',
        'reason_comment',
        'bank_details',

    ];

    public function order(){
        return $this->belongsTo('App\Order');
    }

    public function subOrderStatusDetails(){
       return $this->belongsTo('App\OrderStatusMaster', 'order_status', 'code');
   }

    public function productDetail(){
       return $this->belongsTo('App\Product', 'product_id');
   }




}