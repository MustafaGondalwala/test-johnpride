<?php
namespace App;
use DB;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';

    protected $guarded = ['order_id'];

    public $timestamps = true;

    protected $fillable = [];

    public $primaryKey= 'order_id';




   //getting order status, like ...
   public function orderStatus($order_status_id=0)
   {
        $res=DB::table('order_status')->where(['status_id'=>$order_status_id])->first();
   	    return  $res->status;
   }

   public function order_products() 
   {
        
       return $this->hasMany('App\Orderproducts', 'order_id');
   }

   static function save_order_history($data= array())
   {  

      if(!empty($data)  && !empty($data['order_id']))
      {

          $res=DB::table('order_history')->insert($data);
      }

   }



}