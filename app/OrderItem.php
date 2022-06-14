<?php
namespace App;

use Illuminate\Database\Eloquent\Model;


class OrderItem extends Model{

    protected $table = 'order_items';

    protected $guarded = ['id'];

    protected $fillable = [];

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