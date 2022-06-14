<?php

namespace App\Http\Controllers;

use App\Category;
use App\Product;
use App\Banner;
use App\CustomerPicture;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\CustomHelper;

//use App\Libraries\InstagramApi;

use DB;
use Validator;


class PrideOnmeController extends Controller {

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

        /*$isMobile = CustomHelper::isMobile();

        $bannerType = 'desktop';

        if($isMobile){
            $bannerType = 'mobile';
        }

        $bannerWhere = [];
        $bannerWhere['page'] = 'customer_picture';
        $bannerWhere['status'] = 1;
        $bannerWhere['device_type'] = $bannerType;

        $banners = Banner::where($bannerWhere)->orderBy('sort_order')->limit($limit)->get();*/

        $banners = [];

        
        $customerPictures = CustomerPicture::where(['status'=>1])->orderBy('sort_order')->get();
        
        $data['heading'] = '#PRIDEONME';
        $data['meta_title'] = 'PRIDEONME | Johnpride ';
        $data['meta_description'] = 'We are the leading online store in India for mens plus size tshirts, jeans and shirts. Get in touch';
        $data['banners'] = $banners;
        $data['customerPictures'] = $customerPictures;

        return view('customer_picture.index', $data);
    }


/* end of controller */
}
