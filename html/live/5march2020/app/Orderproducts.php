<?php
namespace App;
use DB;
use Illuminate\Database\Eloquent\Model;


class Orderproducts extends Model{
    protected $table = 'order_products';

    protected $guarded = ['order_product_id'];
    public $timestamps = false;

    public $primaryKey = 'order_product_id';

    protected $fillable = [];


    public function Design() {
    	return $this->belongsTo('App\Product', 'design_id');
    }


}