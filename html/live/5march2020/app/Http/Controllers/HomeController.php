<?php

namespace App\Http\Controllers;

use App\CmsPages;

use App\Category;
use App\Product;
use App\Banner;
use App\HomeImage;
use App\Brand;
use App\UserCartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\CustomHelper;

use App\Libraries\InstagramApi;

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

        $productsTrending = Product::where(['trending'=>1, 'status'=>1])->orderBy('sort_order')->limit(3)->get();
        $productsBestSeller = Product::where(['featured'=>1, 'status'=>1])->orderBy('sort_order')->limit(10)->get();
        $brands = Brand::where(['featured'=>1, 'status'=>1])->orderBy('sort_order')->limit(6)->get();
        $HomeImages = HomeImage::where(['status'=>1])->where('image', '!=', "")->whereNotNull('image')->orderBy('sort_order')->limit(6)->get();
        //pr($brands->toArray());
        $instaMedia = '';

        $insta = new InstagramApi();

        $instaMedia = $insta->userMedia();

        //prd($instaMedia['data']);

        $data['meta_title'] = 'Slumberjill';
        $data['banners'] = $banners;
        $data['productsTrending'] = $productsTrending;
        $data['productsBestSeller'] = $productsBestSeller;
        $data['brands'] = $brands;
        $data['HomeImages'] = $HomeImages;
        $data['instaMedia'] = $instaMedia;

        return view('home.index', $data);
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
                $email_subject = "Contact us From :: SlumberJill";
                $ADMIN_EMAIL = CustomHelper::WebsiteSettings('ADMIN_EMAIL');
                //$STORE_EMAIL = "ashishkb@ehostinguk.com"; 

                $emailData['name']= $name = $request->name;
                $emailData['email'] = $request->email;
                $emailData['phone'] = $request->phone;
                $emailData['subject'] = $request->subject;
                $emailData['msg'] = $request->message;

                /*$viewHtml = view('emails.contact', $emailData)->render();

                prd($viewHtml);*/

                $query_email = CustomHelper::sendEmail('emails.contact', $emailData, $ADMIN_EMAIL, $ADMIN_EMAIL, $ADMIN_EMAIL, $email_subject);

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


/* end of controller */
}
