<?php

namespace App\Http\Controllers;

use App\CmsPages;

use App\Category;
use App\Product;
use App\Banner;
use App\Blog;
use App\HomeImage;
use App\Brand;
use App\CustomerPicture;
use App\LookBook;
use App\UserCartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\CustomHelper;

use App\Libraries\InstagramApi;
use Mail;
use DB;
use Validator;


class HomeController extends Controller {

	private $limit;
    /**
     * Homepage
     * URL: /
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */

    public function __construct(){
    	$this->limit = 20;
        
    }

    public function index(){   
        
        
        $data = [];
        $limit = $this->limit;

        $isMobile = CustomHelper::isMobile();

        $bannerType = 'desktop';

        if($isMobile){
            $bannerType = 'mobile';
        }

        $bannerWhere = [];
        $bannerWhere['page'] = 'home';
        $bannerWhere['status'] = 1;
        $bannerWhere['device_type'] = $bannerType;

        $banners = Banner::where($bannerWhere)->orderBy('sort_order')->limit($limit)->get();

        //$productsPopular = Product::where(['popularity'=>1, 'status'=>1])->orderBy('sort_order')->limit(3)->get();
        $featuredBlog = Blog::where(['featured'=>1, 'status'=>1])->orderBy('sort_order')->limit(12)->get();
        //$productsTrending = Product::where(['trending'=>1, 'status'=>1])->orderBy('sort_order')->limit(3)->get();

        $product_query = Product::where(['featured'=>1, 'status'=>1]);

        $product_query->whereHas('productInventorySize', function($stocks) {
            $stocks->havingRaw('SUM(stock) > 0');
        });

        $productsBestSeller = $product_query->orderBy('sort_order')->limit(3)->get();

        //$productsBestSeller = Product::where(['featured'=>1, 'status'=>1])->orderBy('sort_order')->limit(3)->get();
        $categories = Category::where(['featured'=>1, 'status'=>1])->where('parent_id', '!=', 0)->orderBy('sort_order')->limit(9)->get();
        //prd($categories);
        $collections = Brand::where(['featured'=>1, 'status'=>1])->orderBy('sort_order')->limit(6)->get();
        $customerPictures = CustomerPicture::where(['featured'=>1, 'status'=>1])->orderBy('sort_order')->limit(4)->get();
        $lookBooks = LookBook::where(['featured'=>1, 'status'=>1])->orderBy('sort_order')->limit(4)->get();

        $HomeImages = HomeImage::where(['status'=>1])->where('image', '!=', "")->whereNotNull('image')->orderBy('sort_order')->limit(6)->get();
        //pr($collection->toArray());
        $instaMedia = '';

        $insta = new InstagramApi();

        $instaMedia = $insta->userMedia();

        //pr($instaMedia);

        $data['meta_title'] = 'Johnpride';
        $data['banners'] = $banners;
        //$data['productsPopular'] = $productsPopular;
        //$data['productsTrending'] = $productsTrending;
        $data['productsBestSeller'] = $productsBestSeller;
        $data['featuredBlogs'] = $featuredBlog;//brands
        $data['categories'] = $categories;//brands
        $data['collections'] = $collections;//brands
        $data['customerPictures'] = $customerPictures;//brands
        $data['lookBooks'] = $lookBooks;//brands
        $data['HomeImages'] = $HomeImages;
        $data['instaMedia'] = $instaMedia;
        $data['isMobile'] = $isMobile;

        return view('home.index', $data);
    }


    public function index_test(){   
        
        
        $data = [];
        $limit = $this->limit;

        $isMobile = CustomHelper::isMobile();

        $bannerType = 'desktop';

        if($isMobile){
            $bannerType = 'mobile';
        }

        $bannerWhere = [];
        $bannerWhere['page'] = 'home';
        $bannerWhere['status'] = 1;
        $bannerWhere['device_type'] = $bannerType;

        $banners = Banner::where($bannerWhere)->orderBy('sort_order')->limit($limit)->get();

        //$productsPopular = Product::where(['popularity'=>1, 'status'=>1])->orderBy('sort_order')->limit(3)->get();
        $featuredBlog = Blog::where(['featured'=>1, 'status'=>1])->orderBy('sort_order')->limit(12)->get();
        //$productsTrending = Product::where(['trending'=>1, 'status'=>1])->orderBy('sort_order')->limit(3)->get();

        $product_query = Product::where(['featured'=>1, 'status'=>1]);

        $product_query->whereHas('productInventorySize', function($stocks) {
            $stocks->havingRaw('SUM(stock) > 0');
        });

        $productsBestSeller = $product_query->orderBy('sort_order')->limit(3)->get();

        //$productsBestSeller = Product::where(['featured'=>1, 'status'=>1])->orderBy('sort_order')->limit(3)->get();
        $categories = Category::where(['featured'=>1, 'status'=>1])->where('parent_id', '!=', 0)->orderBy('sort_order')->limit(9)->get();
        //prd($categories);
        $collections = Brand::where(['featured'=>1, 'status'=>1])->orderBy('sort_order')->limit(6)->get();
        $customerPictures = CustomerPicture::where(['featured'=>1, 'status'=>1])->orderBy('sort_order')->limit(4)->get();
        $lookBooks = LookBook::where(['featured'=>1, 'status'=>1])->orderBy('sort_order')->limit(4)->get();

        $HomeImages = HomeImage::where(['status'=>1])->where('image', '!=', "")->whereNotNull('image')->orderBy('sort_order')->limit(6)->get();
        //pr($collection->toArray());
        $instaMedia = '';

        $insta = new InstagramApi();

        $instaMedia = $insta->userMedia();

        //pr($instaMedia);

        $data['meta_title'] = 'Johnpride';
        $data['banners'] = $banners;
        //$data['productsPopular'] = $productsPopular;
        //$data['productsTrending'] = $productsTrending;
        $data['productsBestSeller'] = $productsBestSeller;
        $data['featuredBlogs'] = $featuredBlog;//brands
        $data['categories'] = $categories;//brands
        $data['collections'] = $collections;//brands
        $data['customerPictures'] = $customerPictures;//brands
        $data['lookBooks'] = $lookBooks;//brands
        $data['HomeImages'] = $HomeImages;
        $data['instaMedia'] = $instaMedia;
        $data['isMobile'] = $isMobile;

        return view('home.index_test', $data);
    }


    public function tokentest()
    {
        // $start_time = date('Y-m-d H:i:s');
        // $end_time = date('2020-11-01 00:00:03');
        // $time = CustomHelper::calculate_time_span($start_time,$end_time);

        // echo $time;

    CustomHelper::getUnicommerceAccessTokenSave();



    }

    public function logout(Request $request){
        
        $method = $request->method();

        /*$userId = 0;
        if(auth()->check()){
            $userId = auth()->user()->id;
        }*/

        //if($method == 'POST'){
            Auth::logout();

            if(!auth()->check()){
                session()->flush();
                if (session()->has('couponData')) {
                    session()->forget('couponData');
                }

                session()->flush();
        
               /* $sessionToken = csrf_token();

                if(is_numeric($userId) && $userId > 0){
                    UserCartItem::where(['session_token'=>$sessionToken, 'user_id'=>$userId])->update(['session_token'=>'']);
                }*/
            }

            return redirect(url(''))->with('alert-success', 'You have successfully logged out!');

            //return redirect(url('account/login'))->with('alert-success', 'You have successfully logged out!');
        //}
    }

    public function contact(Request $request){
        //phpinfo(); die;
        $countries = DB::table('countries')->orderBy('name')->get();
        $data = [];

        //echo date('d M Y H:i A'); die;

        $select_cols = '*';

        $page_name = 'contact_us';
        $cms_data = CustomHelper::GetCMSPage($page_name, $select_cols);

        $data = array_merge($data, $cms_data);

        if($request->method() == 'POST' || $request->method() == 'post'){
            $attributes['scode'] = 'Security Code';

            $rules['name'] = 'required';
            $rules['email'] = 'required|email';
            $rules['message'] = 'required';
            $rules['scode'] = 'required|captcha';

            $message['scode.captcha'] = "Invalid Captcha";

            $validator = Validator::make($request->all(), $rules, $message);

            $validator->setAttributeNames($attributes);

            if ($validator->fails()){
                return back()->withInput()->withErrors($validator);
            }
            else{
                $email_subject = "Contact us From :: Johnpride";
                $ADMIN_EMAIL = CustomHelper::WebsiteSettings('ADMIN_EMAIL');
                //$ADMIN_EMAIL = "anand@ehostinguk.com"; 

                $emailData['name']= $name = $request->name;
                $emailData['email'] = $request->email;
                $emailData['phone'] = $request->phone;
                $emailData['subject'] = $request->subject;
                $emailData['msg'] = $request->message;

                /*$viewHtml = view('emails.contact', $emailData)->render();

                prd($viewHtml);*/

                $query_email = CustomHelper::sendEmail('emails.contact', $emailData, $ADMIN_EMAIL, $ADMIN_EMAIL, $ADMIN_EMAIL, $email_subject);

               // echo 'Mail-'.$query_email;die;

                if($query_email){
                    return redirect(url('contact'))->with('alert-success', 'Thanks for visiting and giving us an opportunity to serve you. We will be back with the answer of your query with in next 24 business hours.');
                }
                else{
                    return redirect(url('contact'))->with('alert-danger', '<b>Opps! something went wrong. Your enquiry could not be submitted successfully.</b>');
                }
            }
            
        }

        //prd(captcha());
        $data['countries'] = $countries;
        $data['captcha_img'] = captcha_img('custom');

        return view('home.contact', $data);
    }


    /*enquiry-form*/
    public function enquiryForm(Request $request){
        
        $data = [];

        if($request->method() == 'POST' || $request->method() == 'post'){
            //$attributes['scode'] = 'Security Code';
            $message = [];
            $rules['name'] = 'required';
            $rules['email'] = 'required|email';
            $rules['phone'] = 'required|numeric';
            $rules['location'] = 'required';
            //$rules['scode'] = 'required|captcha';

            //$message['scode.captcha'] = "Invalid Captcha";

            $validator = Validator::make($request->all(), $rules, $message);

            //$validator->setAttributeNames($attributes);

            if ($validator->fails()){
                return back()->withInput()->withErrors($validator);
            }
            else{
                $email_subject = "Enquiry From :: Johnpride";

                $ADMIN_EMAIL = CustomHelper::WebsiteSettings('ADMIN_EMAIL');

                $FROM_EMAIL = CustomHelper::WebsiteSettings('FROM_EMAIL');
                //$ADMIN_EMAIL = "anand@ehostinguk.com";
                if(empty($FROM_EMAIL)){
                    $FROM_EMAIL = config('custom.admin_email');
                }

                $emailData['name']= isset($request->name) ? $request->name:'';
                $emailData['phone'] = isset($request->phone) ? $request->phone:'';
                $emailData['alternate_phone'] = isset($request->alternate_phone) ? $request->alternate_phone:'';
                $emailData['email'] = isset($request->email) ? $request->email:'';
                $emailData['profession'] = isset($request->profession) ? $request->profession:'';
                $emailData['business'] = isset($request->business) ? $request->business:'';
                $emailData['city'] = isset($request->city) ? $request->city:'';
                $emailData['location'] = isset($request->location) ? $request->location:'';
                $emailData['market'] = isset($request->market) ? $request->market:'';
                $emailData['location_address'] = isset($request->location_address) ? $request->location_address:'';
                $emailData['store_size'] = isset($request->store_size) ? $request->store_size:'';

                $viewHtml = view('emails.enquery_form', $emailData)->render();

                //prd($viewHtml);

                $query_email = CustomHelper::sendEmail('emails.enquery_form', $emailData, $ADMIN_EMAIL, $FROM_EMAIL, $FROM_EMAIL, $email_subject);

               // echo 'Mail-'.$query_email;die;

                if($query_email){
                    return redirect(url('enquiry-form'))->with('alert-success', 'Thanks for visiting and giving us an opportunity to serve you. We will be back with the answer of your query with in next 24 business hours.');
                }
                else{
                    return redirect(url('enquiry-form'))->with('alert-danger', '<b>Opps! something went wrong. Your enquiry could not be submitted successfully.</b>');
                }
            }
            
        }

        return view('home.enquiry_form', $data);
    }


    public function cmsPage(){

        $segments1 = request()->segment(1);

        //prd($segments1);

        $data = [];

        $select_cols = '*';

        if(!empty($segments1)){

            $page_name = $segments1;

            $cms_data = CustomHelper::getCMSPage($page_name, $select_cols);

            $data['cms'] = $cms_data;

            return view('home.cms_page', $data);
        }

        abort(404);
    }


  public function track_order(Request $request){
   // echo "ok";die;
        $data = [];
        $orderHistory = '';
        $orderStatus = '';
        $order_no = '';

        $unicommerce_api_mode = config('custom.unicommerce_api_mode');
        $unicommerce_facility = config('custom.unicommerce_facility');
       if($unicommerce_api_mode == 'DEMO')
        {

            $unicommerce_token_data = DB::table('unicommerce_demo_api')->select('access_token')->orderBy('updated_at', 'desc')->first();
        }
        else
        {       
            $unicommerce_token_data = DB::table('unicommerce_api')->select('access_token')->orderBy('updated_at', 'desc')->first();
        }

       // $unicommerce_token_data = DB::table('unicommerce_api')->first();

        $unicommerce_access_token = $unicommerce_token_data->access_token;
        //$unicommerce_access_token = '5a6dafc4-958b-468c-ae0a-a3543674d046';

        $order_items_data = '';
        $error_description = '';
        $order_status = '';

        if($request->method() == 'POST' || $request->method() == 'post'){


            $message = [];
            $rules['order_no'] = 'required';

            $validator = Validator::make($request->all(), $rules, $message);

            //$validator->setAttributeNames($attributes);

            if ($validator->fails()){
                return back()->withInput()->withErrors($validator);
            }
            else
            {
               
                $order_no = isset($request->order_no) ? $request->order_no:'';
                
                //** Sale order api call

              $unicommerce_api_url = config('custom.unicommerce_api_url');
              
                $ch = curl_init();

                curl_setopt($ch, CURLOPT_URL, $unicommerce_api_url."services/rest/v1/oms/saleorder/get" );
              //  curl_setopt($ch, CURLOPT_URL, "https://stgbloomexim.unicommerce.com/services/rest/v1/oms/saleorder/get" );

                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
                curl_setopt($ch, CURLOPT_POST,           1 );
                curl_setopt($ch, CURLOPT_POSTFIELDS,     '{"code": "'.$order_no.'","facilityCodes": ["bloomexim"]}'); 
                curl_setopt($ch, CURLOPT_HTTPHEADER,array('Content-Type: application/json', 'Authorization:bearer '.$unicommerce_access_token)); 

                $result=curl_exec($ch);
                $order_data = json_decode($result);
                // prd($order_data);
                // prd($order_data->errors[0]->description);

                if(isset($order_data) && !empty($order_data))
                {
                    //echo "ok";
                    $order_status = isset($order_data->successful) ? $order_data->successful : '';
                    $new_order_data = isset($order_data->saleOrderDTO) ? $order_data->saleOrderDTO : '';
                    $order_items_data = isset($new_order_data->saleOrderItems) ? $new_order_data->saleOrderItems : '' ;
                    //$error_description = '';
                    if(isset($order_data->errors[0]->description) && $order_data->errors[0]->description != '')
                    {
                        $error_description =$order_data->errors[0]->description;
                    }
                    else if(isset($order_data->error_description) && $order_data->error_description != '')
                    {
                         $error_description = $order_data->error_description;
                    }
                    else
                    {
                         $error_description = '';
                    }

                    

                }

                // else if(isset($order_data) && !empty($order_data) && $order_data->successful == '')
                // {
                //     $order_status = $order_data->successful;
                //     $new_order_data = '';
                //     $order_items_data = '';
                //     $error_description = $order_data->errors[0]->description;
                // }



            }
        }

//        $data['order_status'] = $order_data->successful;
        $data['order_no'] = $order_no;
        $data['order_status'] = $order_status;
        $data['order_items_data'] = $order_items_data;
        $data['error_description'] = $error_description;

        //prd($data);

        return view('home.track_order', $data);
      
}


    public function mail_test(Request $request)
    {
        //$isMailCustomer = CustomHelper::sendEmail('emails.orders.customer', $emailData, $to=$toEmail, $fromEmail, $replyTo = $fromEmail, $subject);

       // echo date('Y-m-d H:i:s');

         Mail::raw('hello world', function($message) {
          $message->subject('Testing email')->to('anmol@ehostinguk.com')->from('anmol@ehostinguk.com');
         });

    }



/* end of controller */
}
