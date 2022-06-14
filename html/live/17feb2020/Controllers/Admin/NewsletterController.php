<?php

namespace App\Http\Controllers\Admin;

use App\Category;
use App\Product;
use App\User;
use App\ProductImage;
use App\ColorsMaster;

use App\NewsletterSubscriber;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Validator;
use Storage;

use App\Helpers\CustomHelper;

use Image;
use DB;

class NewsletterController extends Controller{


    private $limit;

    public function __construct(){
        $this->limit = 20;
    }

    public function index(Request $request){

        $data = [];

        $limit = $this->limit;

        $newsletterQuery = NewsletterSubscriber::orderBy('id', 'desc');

        $newsletters = $newsletterQuery->paginate($limit);

        $data['newsletters'] = $newsletters;
        $data['limit'] = $limit;

        return view('admin.newsletter.index', $data);

    }


    

    public function delete(Request $request){

        $method = $request->method();

        if($method == 'POST'){
            //prd($request->toArray());

            $id = (isset($request->id))?$request->id:0;

            if(is_numeric($id) && $id > 0){

                $newsletter = NewsletterSubscriber::find($id);

                if(isset($newsletter->id) && $newsletter->id == $id){
                    $newsletter->delete();
                    return back()->with('alert-success', 'Newsletter Subscriber deleted successfully.');
                }
            }
        }

        return back()->with('alert-danger', 'something went wrong, please try again...');
       
    }

    /* end of controller */
}