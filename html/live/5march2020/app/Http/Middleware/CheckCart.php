<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

use DB;

class CheckCart
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (session()->has('cart') && auth()->check()) {

            $customer_id = auth()->user()->id;

            $cartItems = session()->pull('cart.items', []);
                    //prd($cartItems);

            if(count($cartItems) > 0){
                foreach($cartItems as $item_k=>$item_v){
                    //prd($item_v);

                    $if_exists = DB::table('cart')->where(['customer_id'=>$customer_id, 'product_id'=>$item_v['product_id']])->select('id')->count();

                    if($if_exists > 0){
                        $updateData['quantity'] = $item_v['quantity'];
                        $updateData['updated_at'] = date('Y-m-d H:i:s');

                        DB::table('cart')->where(['customer_id'=>$customer_id, 'product_id'=>$item_v['product_id']])->update($updateData);
                    }
                    else{
                        $insertData['customer_id'] = $customer_id;
                        $insertData['product_id'] = $item_v['product_id'];
                        $insertData['product_name'] = $item_v['product_name'];
                        $insertData['price'] = $item_v['price'];
                        $insertData['quantity'] = $item_v['quantity'];

                        DB::table('cart')->insert($insertData);
                    }
                }
            }
        }

        return $next($request);
    }

}
