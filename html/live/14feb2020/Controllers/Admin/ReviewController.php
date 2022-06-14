<?php

namespace App\Http\Controllers\Admin;

use App\Review;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Helpers\CustomHelper;

use Validator;
use Storage;

use Image;
use DB;

class ReviewController extends Controller{

    private $limit;

    public function __construct(){
        $this->limit = 20;
    }

    public function index(Request $request){

        $data = [];

        $limit = $this->limit;

        $customer = (isset($request->customer))?$request->customer:'';
        $product = (isset($request->product))?$request->product:'';

        $rating = (isset($request->rating))?$request->rating:'';
        $status = (isset($request->status))?$request->status:'';

        $from = (isset($request->from))?$request->from:'';
        $to = (isset($request->to))?$request->to:'';

        $from_date = CustomHelper::DateFormat($from, 'Y-m-d', 'd/m/Y');
        $to_date = CustomHelper::DateFormat($to, 'Y-m-d', 'd/m/Y');

        $reviewsQuery = Review::orderBy('id', 'desc');

        if(!empty($customer)){
            $reviewsQuery->whereHas('reviewUser', function ($query) use ($customer) {
                $query->where('name', 'like', '%'.$customer.'%');
            });
        }

        if(!empty($product)){
            $reviewsQuery->whereHas('reviewProduct', function ($query) use ($product) {
                $query->where('name', 'like', '%'.$product.'%');
            });
        }

        if( is_numeric($rating) > 0 && $rating > 0){
            $reviewsQuery->where('rating', $rating);
        }

        if( strlen($status) > 0 ){
            $reviewsQuery->where('status', $status);
        }

        if(!empty($from_date)){
            $reviewsQuery->whereRaw('DATE(created_at) >= "'.$from_date.'"');
        }

        if(!empty($to_date)){
            $reviewsQuery->whereRaw('DATE(created_at) <= "'.$to_date.'"');
        }

        $reviews = $reviewsQuery->paginate($limit);

        $data['reviews'] = $reviews;

        $data['limit'] = $limit;

        return view('admin.reviews.index', $data);

    }

    public function view(Request $request){

        //prd($request->toArray());

        $data = [];

        $id = (isset($request->id))?$request->id:0;

        if(is_numeric($id) && $id > 0){

            $review = Review::find($id);

            if(isset($review->id) && $review->id == $id){

                $data['review'] = $review;

                return view('admin.reviews.view', $data);
            }

        }

        return redirect(url('admin/reviews'));

    }

    /* ajax_view */
    public function ajaxView(Request $request){

        //prd($request->toArray());

        $response = [];
        $response['success'] = false;

        $id = (isset($request->id))?$request->id:0;

        if(is_numeric($id) && $id > 0){

            $review = Review::find($id);

            if(isset($review->id) && $review->id == $id){

                $viewData = [];

                $viewData['review'] = $review;

                $reviewHtml = view('admin.reviews._view', $viewData)->render();

                $response['reviewHtml'] = $reviewHtml;

                $response['success'] = true;
            }

        }

        return response()->json($response);

    }

    public function changeStatus(Request $request){

        $response['success'] = false;

        $method = $request->method();

        if($method == 'POST'){
            //prd($request->toArray());

            $rules = [];
            $validation_msg = [];

            $rules['id'] = 'required|numeric';
            $rules['type'] = 'required|in:approved,status';
            $rules['status'] = 'required';

            //$this->validate($request, $rules, $validation_msg);

            $validator = Validator::make($request->all(), $rules, $validation_msg);

            if($validator->fails()){
                $response['errors'] = $validator->errors();
            }
            else{

                $id = $request->id;
                $type = $request->type;
                $oldStatus = $request->status;

                $newStatus = 1;

                if($oldStatus == 1 || $oldStatus == '1'){
                    $newStatus = 0;
                }

                $statusData = [];
                $statusData[$type] = $newStatus;

                $isSaved = Review::where('id', $id)->update($statusData);

                //prd($isSaved->toArray());

                if($isSaved){

                    session()->flash('alert-success', ucfirst($type).' has been changed.');

                    $response['success'] = true;
                }

            }

        }

        return response()->json($response);
    }


    public function delete(Request $request){
        //prd($request->toArray());
        $method = $request->method();
        $is_deleted = 0;

        if($method == "POST"){
            $id = $request->id;
            if(is_numeric($id) && $id > 0){
                $is_deleted = Review::where('id', $id)->delete();
            }
        }
        
        if($is_deleted){
            return redirect(url('admin/reviews'))->with('alert-success', 'The Review has been deleted successfully.');
        }
        else{
            return back()->with('alert-danger', 'something went wrong, please try again.');
        }

    }


    /* end of controller */
}