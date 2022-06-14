<?php

namespace App\Http\Controllers\Admin;

use App\Category;
use App\Product;
use App\User;
use App\ProductImage;
use App\ColorsMaster;

use App\Newslettersubscriber;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Validator;
use Storage;

use App\Helpers\CustomHelper;

use Image;
use DB;

class NewslettersubscriberController extends Controller
{


    private $limit;

    public function __construct()
    {
        $this->limit = 20;
    }

    public function index(Request $request)
    {

        $data = [];

        $limit = $this->limit;
        $s_query = Newslettersubscriber::orderBy('id', 'desc');
        $res=$s_query->paginate($limit);
        $data['res'] = $res;     
        $data['limit'] = $limit;

        return view('admin.newslettersubscriber.list', $data);

    }


    

    public function delete(Request $request, $id='')
    {
        $model = Newslettersubscriber::find($id);
        $model->delete();
        return back()->with('alert-success', 'Newslettersubscriber deleted successfully.');
       
    }

    /* end of controller */
}