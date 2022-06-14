<?php

namespace App\Helpers;

use App\Coupon;

use DB;

use Mail;

use Storage;
use App\UserWallet;
use App\User;
use App\Category;
use App\ColorMaster;
use App\ShippingRate;
use App\ShippingZone;

use App\Libraries\CurrencyConverter;
use App\Libraries\MobileDetect;

use Validator;
use Image;

class CustomHelper{

    /**
     * Render S3 image URL
     *
     * @param $name
     * @param bool $thumbnail
     * @return string
     */
    public static function getServerIp() {
        $IP = request()->server('SERVER_ADDR');

        return $IP;
    }

    public static function isSmsGateway() {
        $IP = Self::getServerIp();

        if($IP == '192.168.1.30'){
            return false;
        }

        return true;
    }

    public static function image($name, $thumbnail = false) {
        return env('AWS_URL') . '/' . env('AWS_BUCKET') . '/photos' . ($thumbnail ? '/thumbnails/' : '/') . $name;
    }

    /**
     * Format a 10-digit phone number from xxxxxxxxxx to (xxx) xxx-xxxx
     *
     * @param $phone
     * @return string
     */
    public static function formatPhoneNumber($phone) {
        return strlen($phone) == 10 ? '(' . substr($phone, 0, 3) . ') ' . substr($phone, 3, 3) . '-' .substr($phone,6) : $phone;
    }

    public static function computeFees() {
        $cart = session('cart');

        $fees = [
            'items' => collect($cart['items'])->sum('quantity'),
            'subtotal' => collect($cart['items'])->reduce(function ($carry, $item) { return $carry + ($item['price'] * $item['quantity']); })
        ];

        // Compute tax
        $fees['tax'] = round($fees['subtotal'] * config('custom.tax') / 100, 2);

        // Compute shipping fees
        $shipping = [
            'config' => config('custom.checkout.shipping')
        ];

        $shipping['default'] = $shipping['config']['default'];

        if (isset($cart['shipping'])) {
            if ($cart['shipping'] == 'cash_on_delivery') {
                $fees['shipping'] = 0;
            } else {
                $fees['shipping'] = $shipping['config']['carriers'][$cart['shipping']['carrier']]['plans'][$cart['shipping']['plan']]['fee'];
            }
        } else {
            $fees['shipping'] = $shipping['config']['carriers'][$shipping['default'][0]]['plans'][$shipping['default'][1]]['fee'];
        }

        $fees['discount'] = 0;

        // Apply coupon, if given
        if (!empty($cart['coupon'])) {
            // Retrieve the Coupon from Database
            $coupon = Coupon::where('id', $cart['coupon']['id'])->where('active', true)->with('products')->first();

            // If Coupon is invalid, simply remove Coupon
            if (!$coupon ||
                !$coupon['active'] ||
                ($coupon['limited'] && $coupon['usage'] >= $coupon['limit']) ||
                (!empty($coupon['start']) && $coupon['start']->isFuture()) ||
                (!empty($coupon['end']) && $coupon['end']->isPast())) {
                session()->forget('cart.coupon');
            }
            // Else, Coupon is valid
            else {
                // Update the updated Coupon to the current session
                session(['cart.coupon' => $coupon->toArray()]);

                $cart['coupon'] = session('cart.coupon');

                if ($cart['coupon']['shipping']) {
                    $fees['shipping'] = 0;
                }

                $couponProducts = collect(session('cart.coupon.products'))->pluck('id')->toArray();
                /*$cartProducts = collect(session('cart.items'))->pluck('product_id')->unique()->toArray();
                $eligibleProducts = array_intersect($couponProducts, $cartProducts);*/

                // If Coupon is applicable for all Products in the Cart
                if (count($couponProducts) == 0) {
                    switch ($cart['coupon']['type']) {
                        case 'percentage':
                        if (!empty($cart['coupon']['discount_percentage'])) {
                            $fees['discount'] = ($cart['coupon']['discount_percentage']  / 100) * $fees['subtotal'];
                        }
                        break;

                        case 'amount':
                        if (!empty($cart['coupon']['discount_amount'])) {
                            $fees['discount'] = $cart['coupon']['discount_amount'];
                        }
                        break;
                    }
                }
                // Else, Coupon is only applicable for some (or all) Products in the Cart
                else {
                    // Walk through each Cart Item
                    foreach ($cart['items'] as $cartItem) {
                        // If this Cart Item's Product is eligible for the Coupon, compute the discount on this Cart Item
                        if (in_array($cartItem['product_id'], $couponProducts)) {
                            switch ($cart['coupon']['type']) {
                                case 'percentage':
                                if (!empty($cart['coupon']['discount_percentage'])) {
                                    $fees['discount'] += ($cart['coupon']['discount_percentage'] / 100) * $cartItem['price'] * $cartItem['quantity'];
                                }
                                break;

                                case 'amount':
                                if (!empty($cart['coupon']['discount_amount']) && $cart['coupon']['discount_amount'] <= $cartItem['price']) {
                                    $fees['discount']+= $cart['coupon']['discount_amount'] * $cartItem['quantity'];
                                }
                                break;
                            }
                        }
                    }
                }

                $fees['discount'] = round($fees['discount'], 2);

                //$fees['subtotal'] = round(($fees['subtotal'] - $fees['discount']), 2);

                // Compute tax
                $fees['tax'] = round(($fees['subtotal'] - $fees['discount']) * config('custom.tax') / 100, 2);
            }
        }

        $fees['total'] = $fees['subtotal'] - $fees['discount'] + $fees['tax'] + $fees['shipping'];

        return $fees;
    }




    public static function GetSlugBySelf($slug_array, $text) {

        $slug = '';

    // echo $text; die;
    // replace non letter or digits by -
        $text = preg_replace ( '~[^\pL\d]+~u', '-', $text );

    // transliterate
        $text = iconv ( 'utf-8', 'us-ascii//TRANSLIT', $text );

    // remove unwanted characters
        $text = preg_replace ( '~[^-\w]+~', '', $text );

    // trim
        $text = trim ( $text, '-' );

    // remove duplicate -
        $text = preg_replace ( '~-+~', '-', $text );

    // lowercase
        $text = strtolower ( $text );
    // echo $text; die;
        if (empty ( $text )) {
    // return 'n-a';
        }

        $slug = self::GetUniqueSlugBySelf ( $slug_array, $text );
    // echo $slug; die;
       
        return $slug;
    }

    public static function GetUniqueSlugBySelf($slug_array, $slug = '', &$num = '') {

        $new_slug = $slug . $num;

        //pr($new_slug);

        $slug = $new_slug;

        if(is_array($slug_array) && in_array($slug, $slug_array)){
            $num = (int)$num + 1;
            $slug = self::GetUniqueSlugBySelf ( $slug_array, $new_slug, $num );
        }

        return $slug;
    }




    public static function GetSlug($tbl_name, $id_field, $row_id = '', $text = '') {
    // echo $text; die;
    // replace non letter or digits by -
        $text = preg_replace ( '~[^\pL\d]+~u', '-', $text );

    // transliterate
        $text = iconv ( 'utf-8', 'us-ascii//TRANSLIT', $text );

    // remove unwanted characters
        $text = preg_replace ( '~[^-\w]+~', '', $text );

    // trim
        $text = trim ( $text, '-' );

    // remove duplicate -
        $text = preg_replace ( '~-+~', '-', $text );

    // lowercase
        $text = strtolower ( $text );
    // echo $text; die;
        if (empty ( $text )) {
    // return 'n-a';
        }
    // echo $text; die;
        $slug = self::GetUniqueSlug ( $tbl_name, $id_field, $row_id, $text );
    // echo $slug; die;
        return $slug;
    }

    public static function GetUniqueSlug($tbl_name, $id_field, $row_id = '', $slug = '', &$num = '') {

//prd($num);

        $new_slug = $slug . $num;

        $query = DB::table($tbl_name);
        $query->where('slug', $new_slug);
        $row = $query->first();

        if (empty ( $row )) {
            $slug = $new_slug;
        } else {
// echo 'here'; die;
            if (! empty ( $row_id ) && $row->$id_field == $row_id) {
                $slug = $new_slug;
            } else {
                $num = (int)$num + 1;
                $slug = self::GetUniqueSlug ( $tbl_name, $id_field, $row_id, $new_slug, $num );
            }
        }
        return $slug;
    }

    public static function getStatusStr($status){

        if(is_numeric($status) && strlen($status) > 0){
            if($status == 1){
                $status = 'Active';
            }
            else{
                $status = 'Inactive';
            }

        }
        return $status;
    }

    public static function getStatusHTML($status, $tbl_id=0, $class='', $id='', $type='status', $activeTxt='Active', $inActiveTxt='In-active'){

        $status_str = '';

        if(is_numeric($status) && strlen($status) > 0){
            $status_name = '';
            $a_label = '';

            if($status == 1){
                $status_name = $activeTxt;
                $a_label = 'label-success';
            }
            else{
                $status_name = $inActiveTxt;
                $a_label = 'label-warning';
            }
            $status_str = '<a href="javascript:void(0)" class="label '.$a_label.' '.$class.'" id="'.$id.'" data-id="'.$tbl_id.'" data-status="'.$status.'" data-type="'.$type.'" >'.$status_name.'</a>';
        }

        if(empty($status_str)){
            $status_str = $status;
        }

        return $status_str;
    }


    public static function CheckAndFormatDate($date, $toFormat='Y-m-d H:i:s', $fromFormat=''){
        $new_date = $date;

        $date = preg_replace(array('/\//', '/\./'), '-', $date);

    //echo $date; die;

        $new_date = self::DateFormat($date, $toFormat, $fromFormat='y-m-d');

        return $new_date;
    }

    public static function DateFormat($date, $toFormat='Y-m-d H:i:s', $fromFormat=''){

        $new_date = $date;

        $formatArr = array('d-m-y', 'd-m-Y', 'd/m/Y', 'd/m/y', 'd/m/Y H:i:s', 'd/m/y H:i:s', 'd/m/Y H:i A', 'd/m/y H:i A',);

        if(empty($toFormat)){
            $toFormat='Y-m-d H:i:s';
        }

        if($date != '0000-00-00 00:00:00' && $date != '0000-00-00' && $date != ''){
            if(empty($fromFormat) || $fromFormat == '' || !in_array($fromFormat, $formatArr)){
                $new_date = date($toFormat, strtotime($date));         
            }
            elseif($fromFormat == 'd-m-y' || $fromFormat == 'd-m-Y'){
                $date_arr = explode('-', $date);
                $date_str = $date_arr[2].'-'.$date_arr[1].'-'.$date_arr[0];
                $new_date = date($toFormat, strtotime($date_str));
            }
            elseif($fromFormat == 'd/m/Y' || $fromFormat == 'd/m/y'){
                $datetime_arr = explode(' ', $date);

                $date_arr = explode('/', $datetime_arr[0]);
                $date_str = $date_arr[2].'-'.$date_arr[1].'-'.$date_arr[0];

                $new_date = date($toFormat, strtotime($date_str));
            }
            elseif($fromFormat == 'd/m/Y H:i:s' || $fromFormat == 'd/m/y H:i:s'){
                $datetime_arr = explode(' ', $date);

                $time = $datetime_arr[1];

                $date_arr = explode('/', $datetime_arr[0]);
                $date_str = $date_arr[2].'-'.$date_arr[1].'-'.$date_arr[0];

                $new_date = date($toFormat, strtotime($date_str.' '.$time));
            }
            elseif($fromFormat == 'd/m/Y H:i A' || $fromFormat == 'd/m/y H:i A'){
                $datetime_arr = explode(' ', $date);

                $time = $datetime_arr[1].' '.$datetime_arr[2];

                $date_arr = explode('/', $datetime_arr[0]);
                $date_str = $date_arr[2].'-'.$date_arr[1].'-'.$date_arr[0];

                $new_date = date($toFormat, strtotime($date_str.' '.$time));
            }

        }
        else{
            $new_date = '';
        }

        return $new_date;
    }

    public static function DateDiff($date1, $date2){

        $date_diff = '';

        $date1 = Self::DateFormat($date1, 'Y-m-d');
        $date2 = Self::DateFormat($date2, 'Y-m-d');

        if(!empty($date1) && !empty($date2)){
            $date1 = date_create($date1);
            $date2 = date_create($date2);
            $diff = date_diff($date1,$date2);

            $date_diff = $diff->format("%a");
        }
        return $date_diff;
    }

    public static function getStartAndEndDateOfWeek($week, $year, $format='Y-m-d H:i:s') {
        $dateTime = new \DateTime();
        $dateTime->setISODate($year, $week);
        $result['start_date'] = $dateTime->format($format);
        $dateTime->modify('+6 days');
        $result['end_date'] = $dateTime->format($format);
        return $result;
    }

    /* Note: this function requires laravel intervention/image package */
    public static function UploadImage($file, $path, $ext='', $width=768, $height=768, $is_thumb=false, $thumb_path, $thumb_width=300, $thumb_height=300){

        if(empty($ext)){
            $ext='jpg,jpeg,png,gif';
        }

        //echo url('public/uploads'); die;

        $result['success'] = false;

        $result['org_name'] = '';
        $result['file_name'] = '';

        if ($file) {

            //$path = 'designs/';
            //$thumb_path = 'designs/thumb/';

            $validator = Validator::make(['file' => $file], ['file' => 'mimes:'.$ext]);

            if ($validator->passes()) {
                $handle = fopen($file, "r");
                $opening_bytes = fread($handle, filesize($file));

                fclose($handle);

                if( strlen(strpos($opening_bytes,'<?php')) > 0 && (strpos($opening_bytes,'<?php') >= 0 || strpos($opening_bytes,'<?PHP') >= 0) )
                {
                    $result['errors']['file'] = "Invalid image!";
                }
                else{

                    if(!Storage::exists('public/'.$path)){
                        Storage::makeDirectory('public/'.$path,0777,true,true);
                    }

                    if(!Storage::exists('public/'.$thumb_path)){
                        Storage::makeDirectory('public/'.$thumb_path,0777,true,true);
                    }

                    $extension = $file->getClientOriginalExtension();
                    $fileOriginalName = $file->getClientOriginalName();
                    $fileName = date('dmyhis').'-'.$fileOriginalName;

                    $is_uploaded = Image::make($file)->resize($width, $height, function ($constraint) {
                            $constraint->aspectRatio();
                        })->save(public_path('storage/'.$path . $fileName));

                    if($is_uploaded){

                        $result['success'] = true;

                        if($is_thumb){
                            $thumb = Image::make($file)->resize($thumb_width, $thumb_height, function ($constraint) {
                                $constraint->aspectRatio();
                            })->save(public_path('storage/'.$thumb_path . $fileName));
                        }

                        $result['org_name'] = $fileOriginalName;
                        $result['file_name'] = $fileName;
                    }
                }
            }
            else{
                $result['errors'] = $validator->errors();
            }

        }

        return $result;
    }

    public static function UploadFile($file, $path, $ext=''){

        if(empty($ext)){
            $ext='jpg,jpeg,png,gif,doc,docx,txt,pdf';
        }

        //$path = 'public/folder_name';

        $result['success'] = false;

        $result['org_name'] = '';
        $result['file_name'] = '';
        $result['file_path'] = '';

        if ($file) {

            $validator = Validator::make(['file' => $file], ['file' => 'mimes:'.$ext]);

            if ($validator->passes()) {
                $handle = fopen($file, "r");
                $opening_bytes = fread($handle, filesize($file));

                fclose($handle);

                if( strlen(strpos($opening_bytes,'<?php')) > 0 && (strpos($opening_bytes,'<?php') >= 0 || strpos($opening_bytes,'<?PHP') >= 0) ){
                    $result['errors']['file'] = "Invalid file!";
                }
                else{

                    if(!Storage::exists($path)){
                        Storage::makeDirectory($path,0777,true,true);
                    }
                    
                    $extension = $file->getClientOriginalExtension();
                    $fileOriginalName = $file->getClientOriginalName();
                    $fileName = date('dmyhis').'-'.$fileOriginalName;

                    $path = $file->storeAs($path, $fileName);

                    if($path){
                        $result['success'] = true;

                        $result['org_name'] = $fileOriginalName;
                        $result['file_name'] = $fileName;
                        $result['file_path'] = $path;
                    }
                }
            }
            else{
                $result['errors'] = $validator->errors();
            }

        }
        return $result;
    }


    public static function WebsiteSettings($name){

        $value = '';
        $settings = DB::table('website_settings')->where('name', $name)->first();
        
        if(!empty($settings) && isset($settings->value)){
            $value = $settings->value;
        }
        return $value;
    }


    public static function websiteSettingsArray($nameArr){

        $settings = '';

        if(is_array($nameArr) && !empty($nameArr) && count($nameArr) > 0){
            $settings = DB::table('website_settings')->whereIn('name', $nameArr)->get()->keyBy('name');
            //prd($settings);
        }
        return $settings;
    }


    public static function formatUserAddress($userAddr){

        $addressArr = [];

        if(!empty($userAddr) && count($userAddr) > 0){

            $address = $userAddr->address;
            $locality = $userAddr->locality;
            $pincode = $userAddr->pincode;

            if(!empty($address)){
                $addressArr[] = $address;
            }
            if(!empty($locality)){
                $addressArr[] = $locality;
            }

            $addressState = '';
            $addressCity = '';

            if(isset($userAddr->userState)){
                $addressState = $userAddr->userState;
            }
            elseif($userAddr->addressState){
                $addressState = $userAddr->addressState;
            }

            if(isset($userAddr->userCity)){
                $addressCity = $userAddr->userCity;
            }
            elseif($userAddr->addressCity){
                $addressCity = $userAddr->addressCity;
            }

            /*$addressState = ($userAddr->addressState)?$userAddr->addressState:'';
            $addressCity = ($userAddr->addressCity)?$userAddr->addressCity:'';*/

            if(!empty($addressState) && count($addressState) > 0){
                if(!empty($addressState->name)){
                    $addressArr[] = $addressState->name;
                }
            }

            if(!empty($addressCity) && count($addressCity) > 0){
                if(!empty($addressCity->name)){
                    $addressArr[] = $addressCity->name;
                }
            }

            if(!empty($pincode)){
                $addressArr[] = 'Pincode:'.$pincode;
            }

        }
        return $addressArr;
    }


    public static function formatOrderAddress($order, $isBilling=true, $isPhone=true, $isEmail=true){

        $orderAddrArr = [];

        if(!empty($order) && count($order) > 0){

            $name = '';
            $address = '';
            $locality = '';
            $pincode = '';
            $cityName = '';
            $stateName = '';
            $countryName = '';
            $phone = '';
            $email = '';

            if($isBilling){

                $name = $order->billing_name;
                $address = $order->billing_address;
                $locality = $order->billing_locality;
                $pincode = $order->billing_pincode;

                $billingCity = $order->billingCity;
                $billingState = $order->billingState;
                $billingCountry = $order->billingCountry;

                if(isset($billingCity->name) && !empty($billingCity->name)){
                    $cityName = $billingCity->name;
                }
                if(isset($billingState->name) && !empty($billingState->name)){
                    $stateName = $billingState->name;
                }
                if(isset($billingCountry->name) && !empty($billingCountry->name)){
                    $countryName = $billingCountry->name;
                }

                $phone = $order->billing_phone;
                $email = $order->billing_email;

            }
            else{
                $name = $order->shipping_name;
                $address = $order->shipping_address;
                $locality = $order->shipping_locality;
                $pincode = $order->shipping_pincode;

                $shippingCity = $order->shippingCity;
                $shippingState = $order->shippingState;
                $shippingCountry = $order->shippingCountry;


                if(isset($shippingCity->name) && !empty($shippingCity->name)){
                    $cityName = $shippingCity->name;
                }
                if(isset($shippingState->name) && !empty($shippingState->name)){
                    $stateName = $shippingState->name;
                }
                if(isset($shippingCountry->name) && !empty($shippingCountry->name)){
                    $countryName = $shippingCountry->name;
                }

                $phone = $order->shipping_phone;
                $email = $order->shipping_email;
            }

            if(!empty($name)){
                $orderAddrArr[] = $name;
            }

            if(!empty($address)){
                $orderAddrArr[] = $address;
            }

            if(!empty($cityName) && !empty($pincode)){
                $cityName = $cityName.'-'.$pincode;
            }

            $cityArr = [];

            if(!empty($locality)){
                $cityArr[] = $locality;
            }
            if(!empty($cityName)){
                $cityArr[] = $cityName;
            }

            $orderAddrArr[] = implode(', ', $cityArr);

            $countryArr = [];

            if(!empty($stateName)){
                $countryArr[] = $stateName;
            }
            if(!empty($countryName)){
                $countryArr[] = $countryName;
            }

            $orderAddrArr[] = implode(', ', $countryArr);

            if($isPhone && !empty($phone)){
                $orderAddrArr[] = '<span class="addr_label">Phone: </span>'.$phone;
            }

            if($isEmail && !empty($email)){
                $orderAddrArr[] = '<span class="addr_label">Email: </span>'.$email;
            }

        }
        return $orderAddrArr;
    }



    public static function GetCountry($id=0, $col_name=''){

        $value = '';

        if(is_numeric($id) && $id > 0){
            $country = DB::table('countries')->where('id', $id)->first();

            if(!empty($col_name) && isset($country->{$col_name})){
                $value = $country->{$col_name};
            }
            else{
                $value = $country;
            }
        }

        return $value;
    }

    
    public static function GetParentCategory($category){
        //echo "categoryParentForBreadcrumb=";

        //prd($category->toArray());

        $parent = '';

        if( isset($category->parent) && count($category->parent) > 0 ){
            $parent = $category->parent;            
        }

        //prd($parents_arr);
        return $parent;
    }

    public static function GetParentCategories($id='', $type='', $params=array()){
        $categories = '';

        $orderBy = (isset($params['orderBy']) && ( $params['orderBy'] == 'desc' || $params['orderBy'] == 'asc' ))?$params['orderBy']:'asc';

        $order_type = (isset($params['order_type']) && ( $params['order_type'] == 'desc' || $params['order_type'] == 'asc' ))?$params['order_type']:'asc';

        $category_query = Category::where('status', 1);

        if($type == 'design'){
            $category_query->where('parent_id', 0);
        }

        if(!empty($type)){
            $category_query->where('type', $type);
        }

        if(isset($params['orderBy']) && !empty($params['orderBy'])){
            $category_query->orderBy($params['orderBy'], $order_type);
        }

        if(isset($params['limit']) && is_numeric($params['limit']) && $params['limit'] > 0){
            $category_query->limit($params['limit']);
        }

        if(is_numeric($id) && $id > 0){
            $category_query->where('id', $id);
            $categories = $category_query->first();
        }
        else{
            $categories = $category_query->get();
        }

        return $categories;
    }

    public static function getCategories($id='', $parent_id=0, $params=array()){

        $categories = '';

        $category_query = Category::where('status', 1);
        $category_query->where('parent_id', $parent_id);

        if(!empty($id)){
            //$category_query->where('id', $id);
            $category_query->where(function ($query) use ($id) {
                $query->where('id', $id)
                ->orWhere('slug', $id);
            });
            $categories = $category_query->first();
        }
        else{
            $categories = $category_query->orderBy('sort_order', 'asc')->get();
        }

        return $categories;
    }


    public static function CategoriesMenu($type='', $className='', $idName=''){
        $CatParams = array();
        $CatParams['orderBy'] = 'sort_order';
        $CatParams['order_type'] = 'asc';

        $ParentCategories = Self::GetParentCategories('', $type, $CatParams);

        //pr($ParentCategories); die;
        $all_menu = url('designs');
        $menu_list = '';
        $menu_list .= '<ul class="'.$className.'" id="'.$idName.'">';

        $menu_list .= '<li><a href="'.$all_menu.'">All Designs</a></li>';
        $menu_list .= '<li><a href="#">All Best Sellers</a></li>';

        if(!empty($ParentCategories) && count($ParentCategories)){

            foreach($ParentCategories as $parentCat){

                $childrenCat = $parentCat->children;

                $cat_url = url('designs?cat='.$parentCat->slug);

                if(isset($childrenCat) && count($childrenCat) > 0){
                    $cat_url = 'javascript:void(0)';
                }

                $menu_list .= '<li><a href="'.$cat_url.'">'.$parentCat->name.'</a>';

                if(isset($childrenCat) && count($childrenCat) > 0){

                    $childrenCat = $childrenCat->sortBy('sort_order');

                    $menu_list .= Self::CategoriesMenuChild($childrenCat, $className, $idName);
                }
                $menu_list .= '</li>';

            }

        }
        $menu_list .= '</ul>';

        return $menu_list;
    }

    public static function CategoriesMenuChild($childCategories, $className='', $idName=''){
        $menu_list_child = '';

        if(!empty($childCategories) && count($childCategories) > 0){
            $menu_list_child .= '<ul class="'.$className.'" id="'.$idName.'">';

            foreach($childCategories as $childCat){

                $childrenCat = $childCat->children;

                $cat_url = url('designs?cat='.$childCat->slug);

                if(isset($childrenCat) && count($childrenCat) > 0){
                    $cat_url = 'javascript:void(0)';
                }

                $menu_list_child .= '<li><a href="'.$cat_url.'">'.$childCat->name.'</a>';

                if(isset($childrenCat) && count($childrenCat) > 0){

                    $childrenCat = $childrenCat->sortBy('sort_order');

                    $menu_list_child .= Self::CategoriesMenuChild($childrenCat, $className, $idName);
                }
                $menu_list_child .= '</li>';

            }

            $menu_list_child .= '</ul>';
        }

        return $menu_list_child;
    }



    private static $parentCatArr = [];

    public static function categoryParentForBreadcrumb($category){

        if( isset($category->parent) && count($category->parent) > 0 ){
            $parent_category = $category->parent;

            Self::$parentCatArr[] = $parent_category->toArray();

            if( isset($parent_category->parent) && count($parent_category->parent) > 0 ){
                Self::categoryParentForBreadcrumb($parent_category);
            }
            
        }
    }


    public static function CategoryBreadcrumb($category, $first_uri, $first_uri_name, $is_last_link = false){

        Self::$parentCatArr = [];

        $BackUrl = Self::BackUrl();

        //prd($category->toArray());
        $breadcrumb = '';

        if(!empty($first_uri_name)){
            $breadcrumb .= '<a href="'.url($first_uri).'" class="btn-link" >'.$first_uri_name.'</a>';
        }

        $hierarchy_arr = [];

        if(!empty($category) && count($category) > 0){

            Self::categoryParentForBreadcrumb($category);

            $hierarchy_arr = Self::$parentCatArr;

            $hierarchy_arr_rev = array_reverse($hierarchy_arr);

            //prd($hierarchy_arr_rev);

            if(!empty($hierarchy_arr_rev) && count($hierarchy_arr_rev) > 0){
                foreach($hierarchy_arr_rev as $cat){

                    $cat = (object)$cat;

                    if(isset($cat->name)){
                        if(!empty($first_uri_name)){
                            $breadcrumb .= '&nbsp;<i aria-hidden="true" class="fa fa-angle-double-right"></i>&nbsp;';
                        }

                        $breadcrumb .= '<a href="'.url($first_uri.'&parent_id='.$cat->id).'" class="btn-link" >'.$cat->name.'</a>';
                        $breadcrumb .= '&nbsp;<i aria-hidden="true" class="fa fa-angle-double-right"></i>&nbsp;';
                    }
                }
                //$breadcrumb .= '&nbsp;<i aria-hidden="true" class="fa fa-angle-double-right"></i>&nbsp;';
            }
            elseif(!empty($first_uri_name)){
                $breadcrumb .= '&nbsp;<i aria-hidden="true" class="fa fa-angle-double-right"></i>&nbsp;';
            }
            if($is_last_link){
                $breadcrumb .= '<a href="'.url('admin/categories?parent_id='.$category->id.'&back_url='.$BackUrl).'">'.$category->name.'</a>';
            }
            else{
                $breadcrumb .= '<a href="javascript:void(0)">'.$category->name.'</a>';
            }            
            
        }

        return $breadcrumb;
    }


    public static function CategoryBreadcrumbFrontend($category, $first_uri, $first_uri_name, $is_last_link = false){

        Self::$parentCatArr = [];

        //prd($category->toArray());
        $breadcrumb = '';

        if(!empty($first_uri_name)){
            $breadcrumb .= '<a href="'.url($first_uri).'" >'.$first_uri_name.'</a>';
        }

        $hierarchy_arr = [];

        if(!empty($category) && count($category) > 0){

            $category_id = (isset($category->pivot->id))?$category->pivot->id:0;
            $p1_cat = (isset($category->pivot->p1_cat))?$category->pivot->p1_cat:0;
            $p2_cat = (isset($category->pivot->p2_cat))?$category->pivot->p2_cat:0;

            Self::categoryParentForBreadcrumb($category);

            $hierarchy_arr = Self::$parentCatArr;

            $hierarchy_arr_rev = array_reverse($hierarchy_arr);

            //prd($hierarchy_arr_rev);

            $pcat = '';

            if(!empty($hierarchy_arr_rev) && count($hierarchy_arr_rev) > 0){

                foreach($hierarchy_arr_rev as $cat){

                    $cat = (object)$cat;

                    if(isset($cat->name)){
                        if(!empty($first_uri_name)){
                            $breadcrumb .= '&nbsp;<i aria-hidden="true" class="fa fa-angle-double-right"></i>&nbsp;';
                        }

                        $pCatUrl = route('products.list', ['pcat'=>$cat->slug]);

                        if($cat->id == $p1_cat){
                            $pcat = $cat->slug;
                            $pCatUrl = route('products.list', ['pcat'=>$cat->slug]);
                        }
                        elseif($cat->id == $p2_cat){
                            //$pCatUrl = 'javascript:void(0)';
                            $pCatUrl = route('products.list', ['pcat'=>$pcat,'p2cat'=>$cat->slug]);
                        }

                        $breadcrumb .= '<a href="'.$pCatUrl.'" >'.$cat->name.'</a>';
                        $breadcrumb .= '&nbsp;<i aria-hidden="true" class="fa fa-angle-double-right"></i>&nbsp;';
                    }
                }
                //$breadcrumb .= '&nbsp;<i aria-hidden="true" class="fa fa-angle-double-right"></i>&nbsp;';
            }
            elseif(!empty($first_uri_name)){
                $breadcrumb .= '&nbsp;<i aria-hidden="true" class="fa fa-angle-double-right"></i>&nbsp;';
            }

            if($is_last_link){

                $catUrl = route('products.list', ['pcat'=>$pcat,'cat[]'=>$category->slug]);

                $breadcrumb .= '<a href="'.$catUrl.'">'.$category->name.'</a>';
            }
            else{
                //$breadcrumb .= '<a href="javascript:void(0)">'.$category->name.'</a>';
                $breadcrumb .= $category->name;
            }
            
        }

        return $breadcrumb;
    }



    public static function categoryDropDown($dropdown_name, $classAttr='', $idAtrr='', $selected_value='', $allow_multiple=false){

        $dropdown = '<select name="'.$dropdown_name.'" class="'.$classAttr.'" id="'.$idAtrr.'" >';

        if($allow_multiple){

            $dropdown = '<select name="'.$dropdown_name.'[]" class="'.$classAttr.'" id="'.$idAtrr.'" multiple>';
        }

        $dropdown .= '<option value="">--Select--</option>';

        $categories = Category::where(['parent_id'=>0])->orderBy('name')->get();

        if(!empty($categories) && count($categories) > 0){
            foreach($categories as $category){
                $dropdown .= Self::makeCategoryDropDown($category, $selected_value);
            }
        }

        $dropdown .= '</select>';

        return $dropdown;
    }




    public static function makeCategoryDropDown($category, $selected_value=''){

        $selected = '';


        if(is_array($selected_value))
        {
            if(in_array($category->id,$selected_value))
            {
                $selected = 'selected';
            }

        }
        else
        {
            if($category->id == $selected_value)
            {
                $selected = 'selected';
            }

        }



        $category_name = $category->name;

        if(isset($category->parent) && count($category->parent) > 0){
            $mark = Self::markCategoryParent($category);
            $category_name = $mark.$category_name;
        }

        $options = '<option value="'.$category->id.'" '.$selected.' >'.$category_name.'</option>';

        if(isset($category->children) && count($category->children) > 0){

            foreach($category->children as $child_cat){
                $options .= Self::makeCategoryDropDown($child_cat, $selected_value);
            }

        }
        return $options;
    }

    public static function markCategoryParent($category){
        $mark = '';

        if(isset($category->parent) && count($category->parent) > 0){
            $mark .= ' - ';
            $category_parent = $category->parent;
            $mark .= Self::markCategoryParent($category_parent);
        }

        return $mark;
    }


    public static function getNameFromNumber($num){

        $index = 0;
        $index = abs($index * 1);
        $numeric = ($num - $index) % 26;
        $letter = chr(65 + $numeric);
        $num2 = intval(($num - $index) / 26);
        if ($num2 > 0) {
            return Self::getNameFromNumber(
                $num2 - 1 + $index
            ) . $letter;
        } else {
            return $letter;
        }
    }

    public static function BackUrl(){
        $uri = request()->path();
        if (count(request()->input()) > 0){
            $request_input = request()->input();
            if(isset($request_input['back_url'])){
                unset($request_input['back_url']);
            }
            $uri .= '?' . http_build_query($request_input, '', "&");
        }
        //rawurlencode(str)
        //return rawurlencode($uri);
        return $uri;
    }

    public static function sendEmail($viewPath, $viewData, $to, $from, $replyTo, $subject, $params=array()){

        try{

            Mail::send(
                $viewPath,
                $viewData,
                function($message) use ($to, $from, $replyTo, $subject, $params) {
                    $attachment = (isset($params['attachment']))?$params['attachment']:'';

                    if(!empty($replyTo)){
                        $message->replyTo($replyTo);
                    }
                    
                    if(!empty($from)){
                        $message->from($from);
                    }

                    if(!empty($attachment)){
                        $message->attach($attachment);
                    }

                    $message->to($to);
                    $message->subject($subject);

                }
            );
        }
        catch(\Exception $e){
            // Never reached
        }

        if( count(Mail::failures()) > 0 ) {
            return false;
        }       
        else {
            return true;
        }

    }

    public static function getCMSPage($name, $cols=array('*')){

        //prd($name);

        $data = [];

        $data['title'] = '';
        $data['heading'] = '';
        $data['content'] = '';
        $data['meta_title'] = '';
        $data['meta_keyword'] = '';
        $data['meta_description'] = '';

        if(!empty($name)){
            $cms_data = DB::table('cms_pages')->where('name', $name)->select($cols)->first();

            if(!empty($cms_data) && count($cms_data) > 0){

                $title = (isset($cms_data->title))?$cms_data->title:'';
                $heading = (isset($cms_data->heading))?$cms_data->heading:'';
                $content = (isset($cms_data->content))?$cms_data->content:'';

                $meta_title = (isset($cms_data->meta_title))?$cms_data->meta_title:'';
                $meta_keyword = (isset($cms_data->meta_keyword))?$cms_data->meta_keyword:'';
                $meta_description = (isset($cms_data->meta_description))?$cms_data->meta_description:'';

                $data['title'] = $title;
                $data['heading'] = $heading;
                $data['content'] = $content;
                $data['meta_title'] = $meta_title;
                $data['meta_keyword'] = $meta_keyword;
                $data['meta_description'] = $meta_description;
            }
        }

        return $data;
    }

    public static function updateData($tbl, $id_col, $id, $data){

        $is_updated = 0;

        if(!empty($tbl) && !empty($id_col) && is_numeric($id) && $id > 0 && is_array($data) && count($data) > 0){
            $is_updated = DB::table($tbl)->where($id_col, $id)->update($data);
        }

        return $is_updated;
    }


    public static function isSerialized($value, &$result = null){

        if(empty($value)){
            return false;
        }

    // Bit of a give away this one
        if (!is_string($value))
        {
            return false;
        }
    // Serialized false, return true. unserialize() returns false on an
    // invalid string or it could return false if the string is serialized
    // false, eliminate that possibility.
        if ($value === 'b:0;')
        {
            $result = false;
            return true;
        }
        $length = strlen($value);
        $end    = '';
        switch ($value[0])
        {
            case 's':
            if ($value[$length - 2] !== '"')
            {
                return false;
            }
            case 'b':
            case 'i':
            case 'd':
            // This looks odd but it is quicker than isset()ing
            $end .= ';';
            case 'a':
            case 'O':
            $end .= '}';
            if ($value[1] !== ':')
            {
                return false;
            }
            switch ($value[2])
            {
                case 0:
                case 1:
                case 2:
                case 3:
                case 4:
                case 5:
                case 6:
                case 7:
                case 8:
                case 9:
                break;
                default:
                return false;
            }
            case 'N':
            $end .= ';';
            if ($value[$length - 1] !== $end[0])
            {
                return false;
            }
            break;
            default:
            return false;
        }
        if (($result = @unserialize($value)) === false)
        {
            $result = null;
            return false;
        }
        return true;
    }


    public static function makeStarRatingArr($rating=5){

        $ratingArr = explode('.', $rating);

        $count = $ratingArr[0];

        $revCount = 5 - $count;

        $starColorArr = [];

        for($i=1; $i<=$count; $i++){
            $starColorArr[] = '<span class="fa fa-star color"></span>';
        }

        $starArr = [];

        if($revCount > 0){
            for($r=1; $r<=$revCount; $r++){
                $starArr[] = '<span class="fa fa-star"></span>';
            }
        }

        $starArray = array_merge($starColorArr, $starArr);

        return $starArray;

    }


    public static function addUserWallet($user_id, $by_user_id, $credit_amount, $debit_amount, $params=''){

        $user = User::where('id', $user_id)->select(['id', 'wallet_bal'])->first();

        $wallet_bal = $user->wallet_bal;

        if(empty($wallet_bal)){
            $wallet_bal = self::updateUserWalletBal($user_id, 0);
        }

        $new_balance = $wallet_bal - $debit_amount;

        $description = (isset($params['description']))?$params['description']:'';
        $orderNumber = (isset($params['orderNumber']))?$params['orderNumber']:0;

        $wallet_data['user_id'] = $user_id;
        $wallet_data['by_user_id'] = $by_user_id;
        $wallet_data['credit_amount'] = $credit_amount;
        $wallet_data['debit_amount'] = $debit_amount;
        $wallet_data['balance'] = $new_balance;
        $wallet_data['description'] = $description;
        $wallet_data['created'] = date('Y-m-d H:i:s');
        $wallet_data['updated'] = $wallet_data['created'];

        $is_saved = UserWallet::create($wallet_data);

        if($is_saved){
            self::updateUserWalletBal($user_id, $new_balance);

            if(!empty($orderNumber)){
                Order::where('order_number', $orderNumber)->update(['wallet_used'=>1,'wallet_amount'=>$debit_amount]);
            }
        }

    }


    public static function updateUserWalletBal($user_id, $balance){
        if(is_numeric($user_id) && $user_id){
            if(is_numeric($balance) && $balance > 0){

                $update_data['wallet_bal'] = $balance;
                $update_data['updated_at'] = date('Y-m-d H:i:s');

                $is_updated = User::where('id', $user_id)->update($update_data);
            }
            else{
                $UsersWalletQuery = UserWallet::where('user_id', $user_id);

                $count_user_wallet = $UsersWalletQuery->count();

                if($count_user_wallet > 0){

                    $user_wallet = $UsersWalletQuery->get();

                    $credit_total = 0;
                    $debit_total = 0;
                    foreach($user_wallet as $uw){
                        $credit_total = $credit_total + $uw->credit_amount;
                        $debit_total = $debit_total + $uw->debit_amount;
                    }
                    $balance = $credit_total - $debit_total;

                    $update_data['wallet_bal'] = $balance;
                    $update_data['updated_at'] = date('Y-m-d H:i:s');

                    $is_updated = User::where('id', $user_id)->update($update_data);
                }
            }
        }
        return $balance;
    }

    public static function checkUserWalletBal($user_id, $amount){

        $result = false;

        if(is_numeric($user_id) && $user_id && is_numeric($amount) && $amount > 0){
            $user = User::where('id', $user_id)->select(['id', 'wallet_bal'])->first();

            //prd($user);

            $wallet_bal = $user->wallet_bal;

            if(empty($wallet_bal) || $wallet_bal == 0.00){
                $UsersWalletQuery = UserWallet::where('user_id', $user_id);

                $count_user_wallet = $UsersWalletQuery->count();

                if($count_user_wallet > 0){

                    $user_wallet = $UsersWalletQuery->get();

                    $credit_total = 0;
                    $debit_total = 0;
                    foreach($user_wallet as $uw){
                        $credit_total = $credit_total + $uw->credit_amount;
                        $debit_total = $debit_total + $uw->debit_amount;
                    }
                    $wallet_bal = $credit_total - $debit_total;

                    $update_data['wallet_bal'] = $wallet_bal;
                    $update_data['updated_at'] = date('Y-m-d H:i:s');

                    $is_updated = User::where('id', $user_id)->update($update_data);

                    if($wallet_bal >= $amount){
                        $result = true;
                    }
                }
            }
            elseif($wallet_bal >= $amount){
                $result = true;
            }
        }
        return $result;
    }

    public static function calculateUserWalletBal($user_id){
        $wallet_bal = 0;

        if(is_numeric($user_id) && $user_id > 0){

            $UsersWalletQuery = UserWallet::where('user_id', $user_id);

            $count_user_wallet = $UsersWalletQuery->count();

            if($count_user_wallet > 0){

                $user_wallet = $UsersWalletQuery->get();

                $credit_total = 0;
                $debit_total = 0;
                foreach($user_wallet as $uw){
                    $credit_total = $credit_total + $uw->credit_amount;
                    $debit_total = $debit_total + $uw->debit_amount;
                }
                $wallet_bal = $credit_total - $debit_total;

                $update_data['wallet_bal'] = $wallet_bal;
                $update_data['updated_at'] = date('Y-m-d H:i:s');

                $is_updated = User::where('id', $user_id)->update($update_data);

            }
        }

        return $wallet_bal;
    }


    public static function wordsLimit($str, $limit = 150, $isStripTags=false, $allowTags=''){
        $newStr = '';
        if(strlen($str) <= $limit){
            $newStr = $str;
        }
        else{
            $newStr = substr($str, 0, $limit).' ...';
        }

        if($isStripTags){
            if(!empty($allowTags)){
                $newStr = strip_tags($newStr, $allowTags);
            }
            else{
                $newStr = strip_tags($newStr);
            }
        }

        return $newStr;
    }

    public static function convertCurrency($amount, $from='INR', $to='USD', $decimals=0){
        $CurrencyConverter = new CurrencyConverter();

        $converted = $CurrencyConverter->convert($amount, $from, $to, $decimals);

        return $converted;
    }

    public static function isMobile($userAgent = null, $httpHeaders = null){
        $detect = new MobileDetect;

        $detected = $detect->isMobile($userAgent = null, $httpHeaders = null);

        return $detected;
    }
    

    private static $categoryAttributes = [];

    public static function getParentCategoryAttributes($category){

        if(!empty($category) && count($category) > 0){

            if(isset($category->parent) && count($category->parent) > 0){
                Self::getParentCategoryAttributes($category->parent);
            }

            $attributes = (isset($category->categoryAttributes))?$category->categoryAttributes:'';
            if(!empty($attributes) && count($attributes) > 0){
                Self::$categoryAttributes[] = $attributes;
            }
        }

        return Self::$categoryAttributes;
    }



    public static function getData($tbl, $id=0, $where='', $selectArr=['*']){
        
        $result = '';

        $query = DB::table($tbl);

        $query->select($selectArr);

        if(!empty($where) && count($where) > 0){
            $query->where($where);
        }

        if(is_numeric($id) && $id > 0){
            $query->where('id', $id);
            $result = $query->first();
        }
        else{
            $result = $query->get();
        }
        
        return $result;
    }

    public static function calculateProductDiscount($mainPrice, $salePrice){

        $discount = 0;

        if(is_numeric($mainPrice) && $mainPrice > 0 && is_numeric($salePrice) && $salePrice > 0 ){ 
            $discount = (($mainPrice - $salePrice)/$mainPrice)*100;
        }
        
        return $discount;
    }

    public static function getShippingZoneDeliveryDays($city_id){

        $delivery_days = '';

        //prd($city_id);

        if(is_numeric($city_id) && $city_id > 0 ){
            $shippingZoneQuery = ShippingZone::whereHas('shippingZoneCities', function($query) use($city_id){
                $query->where('shipping_zones_city.city_id', $city_id);
            });

            //DB::enableQueryLog();
            $shippingZone = $shippingZoneQuery->first();
            //prd(DB::getQueryLog());

            //prd($shippingZone->toArray());

            $delivery_days = (isset($shippingZone->delivery_days))?$shippingZone->delivery_days:'';
        }
        
        return $delivery_days;
    }

    public static function calculateProductShipping($weight, $qty){

        $shippingCharge = 0;

        if(is_numeric($weight) && $weight > 0 && is_numeric($qty) && $qty > 0){

        }
        
        return $shippingCharge;
    }







/* End of helper class */
}