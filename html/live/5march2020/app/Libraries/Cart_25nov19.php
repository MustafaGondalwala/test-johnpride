<?php
namespace App\Libraries;

use App\UserCartItem;
use App\Helpers\CustomHelper;
use DB;

/**
 * Custom Cart Library
 */

class Cart{



    public static function test(){
        echo "Welcome to Custom Cart library";
    }

    public static function getContent(){

        $sessionToken = csrf_token();

        //prd($sessionToken);

        $userCart = '';

        if(auth()->check()){
            $user = auth()->user();
            $userId = $user->id;
            //DB::enableQueryLog();
            $userCart = UserCartItem::where('user_id', $userId)->orderBy('created_at', 'desc')->get();
            //prd(DB::getQueryLog());
        }
        else{
            $userCart = UserCartItem::where('session_token', $sessionToken)->orderBy('created_at', 'desc')->get();
        }

        //prd($userCart->toArray());

        $orderId = 0;

        if(!empty($userCart) && count($userCart) > 0){

            foreach($userCart as $item){
                if(is_numeric($item->order_id) && $item->order_id > 0){
                    $orderId = $item->order_id;
                    break;
                }
            }

            if(is_numeric($orderId) && $orderId > 0){
                $order = DB::table('orders')->select('id','payment_status')->where('id', $orderId)->first();

                    //prd($order);

                    if(isset($order->id) && $order->id == $orderId){
                        if(strtolower(trim($order->payment_status)) === 'success'){
                            Self::clear();

                            return;
                        }
                    }
            }
        }

        return $userCart;
    }

    public static function get($cartId=0){

        $userCart = '';

        if(is_numeric($cartId) && $cartId > 0){

            $sessionToken = csrf_token();

            $where = [];

            if(auth()->check()){
                $user = auth()->user();
                $userId = $user->id;

                $where['user_id'] = $userId;

            }
            else{
                $where['session_token'] = $sessionToken;
            }
            $where['id'] = $cartId;

            $userCart = UserCartItem::where($where)->first();

            //prd($userCart->toArray());
        }

        return $userCart;
    }

    public static function getTotal($cartContent){

        $cartTotal = 0;

        $sessionToken = csrf_token();

        $where = [];

        /*if(auth()->check()){
            $user = auth()->user();
            $userId = $user->id;

            $where['user_id'] = $userId;

        }
        else{
            $where['session_token'] = $sessionToken;
        }*/
        

        //$cartTotal = DB::table('user_cart')->where($where)->sum('cart_price');

        //$cartTotal = DB::table('user_cart')->where($where)->sum(DB::raw('cart_price * qty'));

        if(!empty($cartContent) && count($cartContent) > 0){
            $cartTotal = $cartContent->sum(function($item){
                return $item['qty']*$item['cart_price'];
            });
        }
        //pr($cartTotal);

        return $cartTotal;

    }

    public static function getTotalPrice($cartContent){

        $sessionToken = csrf_token();

        $where = [];

        if(auth()->check()){
            $user = auth()->user();
            $userId = $user->id;

            $where['user_id'] = $userId;

        }
        else{
            $where['session_token'] = $sessionToken;
        }

        //$cartTotal = DB::table('user_cart')->where($where)->sum('cart_price');
        $subTotal = 0;

        if(!empty($cartContent) && count($cartContent) > 0){
            $cartTotal = $cartContent->sum(function($item){
                return $item['qty']*$item['price'];
            });
        }

        return $cartTotal;

    }

    public static function add($cartData=array()){

        $cartId = 0;

        if(!empty($cartData) && count($cartData) > 0){

            $productId = (isset($cartData['product_id']))?$cartData['product_id']:0;
            $sizeId = (isset($cartData['size_id']))?$cartData['size_id']:0;

            if(!is_numeric($productId) || !$productId > 0){
                return 'Invalid product';
            }
            elseif(!is_numeric($sizeId) || !$sizeId > 0){
                return 'Invalid product size';
            }

            $sessionToken = csrf_token();

            $exist = '';

            $where = [];

            if(auth()->check()){
                $user = auth()->user();
                $userId = $user->id;

                $where['user_id'] = $userId;

            }
            else{
                $where['session_token'] = $sessionToken;
            }

            $where['product_id'] = $productId;
            $where['size_id'] = $sizeId;

            $exist = UserCartItem::where($where)->first();

            //prd($cartData);

            if(!empty($exist) && count($exist) > 0){
                $new_qty = $exist->qty + $cartData['qty'];

                $cartData['qty'] = $new_qty;

                $exist->update($cartData);

                $cartId = $exist->id;
            }
            else{
                $cartId = UserCartItem::insertGetId($cartData);
            }

        }


        return $cartId;

    }

    public static function update($id, $data){
        $isUpdated = false;
        if(is_numeric($id) && $id > 0){
            $userCart = UserCartItem::find($id);

            if(isset($userCart->id) && $userCart->id == $id){
                $isUpdated = $userCart->update($data);
            }
        }

        return $isUpdated;
    }

    public static function remove($id){
        $isDeleted = false;
        if(is_numeric($id) && $id > 0){
            $userCart = UserCartItem::find($id);

            if(isset($userCart->id) && $userCart->id == $id){
                $isDeleted = $userCart->delete();
            }
        }

        return $isDeleted;
    }

    /*public static function getTax($cartTotal=0){
        $tax = 0;
        if(is_numeric($cartTotal) && $cartTotal > 0){
            $AMOUNT_FOR_GST_5 = CustomHelper::WebsiteSettings('AMOUNT_FOR_GST_5');

            $gstPercent = 5;

            if(!is_numeric($AMOUNT_FOR_GST_5) || !$AMOUNT_FOR_GST_5 > 0){
                $AMOUNT_FOR_GST_5 = 1000;
            }

            if($cartTotal >= $AMOUNT_FOR_GST_5){
                $gstPercent = 12;
            }

            $tax = ( $cartTotal*($gstPercent/100) );
        }

        return $tax;
    }*/



    public static function getTax($cartContent){
        $tax = 0;

//prd($cartContent->toArray());

        if(!empty($cartContent) && count($cartContent) > 0){

            $AMOUNT_FOR_GST_5 = CustomHelper::WebsiteSettings('AMOUNT_FOR_GST_5');

            if(!is_numeric($AMOUNT_FOR_GST_5) || !$AMOUNT_FOR_GST_5 > 0){
                $AMOUNT_FOR_GST_5 = 1000;
            }

            foreach($cartContent as $cart){
                $product = $cart->product;

                $qty = ($cart->qty > 0)?$cart->qty:1;

                if(!empty($product) && count($product) > 0){
                    $price = $product->price;
                    $salePrice = $product->sale_price;

                    $gst = 5;

                    if(is_numeric($gst) && $gst > 0){
                        $amountForGst = $price;
                        if(is_numeric($salePrice) && $salePrice > 0 && $salePrice < $price){
                            $amountForGst = $salePrice;
                        }

                        if($amountForGst > $AMOUNT_FOR_GST_5){
                            $gst = 12;
                        }

                        if($amountForGst > 0){
                            $productTax = ( ($amountForGst*$qty) * ($gst/100) );
                            //pr($productTax);

                            $tax = $tax + $productTax;
                            //pr($tax);
                        }
                    }
                }
            }
        }

        //pr('==================================');
        //prd($tax);

        return $tax;
    }


    public static function clear(){
        $isClear = false;

        if(auth()->check()){
            $user = auth()->user();
            $userId = $user->id;

            $where['user_id'] = $userId;

            $isClear = UserCartItem::where($where)->delete();

        }
         return $isClear;
    }


    /* end of class */
}