<?php

namespace App\Http\Controllers;

use App\CmsPages;

use App\Category;
use App\Product;
use App\Banner;
use App\HomeImage;
use App\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\CustomHelper;
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

        $banners = Banner::where(['page'=>'home', 'status'=>1])->orderBy('sort_order')->limit($limit)->get();

        $productsTrending = Product::where(['trending'=>1, 'status'=>1])->orderBy('sort_order')->limit(3)->get();
        $productsBestSeller = Product::where(['featured'=>1, 'status'=>1])->orderBy('sort_order')->limit(10)->get();
        $brands = Brand::where(['featured'=>1, 'status'=>1])->orderBy('sort_order')->limit(6)->get();
        $HomeImages = HomeImage::where(['status'=>1])->where('image', '!=', "")->whereNotNull('image')->orderBy('sort_order')->limit(6)->get();
        //pr($brands->toArray());

        $data['meta_title'] = 'Slumberjill';
        $data['banners'] = $banners;
        $data['productsTrending'] = $productsTrending;
        $data['productsBestSeller'] = $productsBestSeller;
        $data['brands'] = $brands;
        $data['HomeImages'] = $HomeImages;

        return view('home.index', $data);
    }


    public function logout(Request $request){
        
        $method = $request->method();

        //if($method == 'POST'){
            Auth::logout();

            return redirect(url('account/login'))->with('alert-success', 'You have successfully logged out!');
        //}
    }

    public function contact(Request $request){
        //phpinfo(); die;
        $countries = DB::table('countries')->orderBy('name')->get();
        $data = [];

        $select_cols = '*';

        $page_name = 'contact_us';
        $cms_data = CustomHelper::GetCMSPage($page_name, $select_cols);

        $data = array_merge($data, $cms_data);


        if($request->method() == 'POST' || $request->method() == 'post'){
            $attributes['scode'] = 'Security Code';

            $rules['name'] = 'required';
            $rules['email'] = 'required|email';
            $rules['scode'] = 'required|captcha';

            $message['scode.captcha'] = "Invalid Captcha";

            $validator = Validator::make($request->all(), $rules, $message);

            $validator->setAttributeNames($attributes);

            if ($validator->fails())
            {
                return back()->withInput()->withErrors($validator);
            }
            else
            {  
                $email_subject = "Query From  :: Mushkis";
                $STORE_EMAIL = CustomHelper::WebsiteSettings('STORE_EMAIL');
                //$STORE_EMAIL = "ashishkb@ehostinguk.com"; 

                $data['name']= $name = $request->name;
                $data['email'] = $request->email;             
                $data['organization'] = $request->organization;             
                $data['state'] = $request->state;             
                $data['country'] = $request->country;
                $data['product'] = $request->product;             
                $data['company_url'] = $request->company_url;

                $query_email = CustomHelper::SendMail('emails.query', $data, $STORE_EMAIL, $STORE_EMAIL, $STORE_EMAIL, $email_subject);

                if($query_email){
                    return redirect(route('contact_us'))->with('alert_success', '<div class="alert alert-success"><b>Dear '.$name.'<br>Thanks for visiting and giving us an opportunity to serve you.<br>We will be back with the answer of your query with in next 24 business hours.<br>Thanx n warm regards<br>mushkis support team</b></div>');
                }else
                {
                    return redirect(route('contact_us'))->with('alert-warning', '<b>Opps! something went wrong. Your enquiry can not submit successfully.</b>');
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
