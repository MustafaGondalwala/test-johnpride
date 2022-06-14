<?php
namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Country;
use App\State;
use App\City;

use Validator;
use Storage;

use App\Helpers\CustomHelper;

use Image;
use DB;

class CityController extends Controller{


    private $limit;

    public function __construct(){
        $this->limit = 100;
    }

    public function index(Request $request){

        $data = [];

        $limit = $this->limit;

        $name = (isset($request->name))?$request->name:'';
        $state_id = (isset($request->state))?$request->state:'';

        $status = (isset($request->status))?$request->status:'';
        $from = (isset($request->from))?$request->from:'';
        $to = (isset($request->to))?$request->to:'';

        $from_date = CustomHelper::DateFormat($from, 'Y-m-d', 'd/m/Y');
        $to_date = CustomHelper::DateFormat($to, 'Y-m-d', 'd/m/Y');


        $cityQuery = City::orderBy('name', 'asc');

        

        if(!empty($name)){
            $cityQuery->where(function($query) use($name){
                $query->where('name', 'like', $name.'%');
            });
        }

        if(is_numeric($state_id) && $state_id > 0){
            $cityQuery->where('state_id', $state_id);
        }

        if( strlen($status) > 0 ){
            $cityQuery->where('status', $status);
        }

        if(!empty($from_date)){
            $cityQuery->whereRaw('DATE(created_at) >= "'.$from_date.'"');
        }

        if(!empty($to_date)){
            $cityQuery->whereRaw('DATE(created_at) <= "'.$to_date.'"');
        }
        
        $cities = $cityQuery->paginate($limit);

        //DB::enableQueryLog();
        $states = State::where('status', 1)->orderBy('name')->get();
        //prd(DB::getQueryLog());
        


        $data['cities'] = $cities;
        $data['states'] = $states;

        //prd($states->toArray());
        
        $data['limit'] = $limit;

        return view('admin.cities.index', $data);

    }


    public function save(Request $request, $id= ''){
         $data= [];
         $page_heading= 'Add City';
         $state= array(); 
         $country_id=99;  
         if(!empty($id))
         {
            $page_heading= 'Edit City';

            $city= City::where(['id'=>$id])->first();
            $country_id= $city->cityState->country_id;
            
            $data['city']=   $city;

         } 

         $method= $request->method(); 
         if($method=='POST')
         { 
               $rules = [];
               $rules['name'] = 'required';
               $rules['country'] = 'required';
               $rules['state'] = 'required';
               $this->validate($request, $rules);
               
               $req_data['name']=$request->name;
               $req_data['state_id']=$request->state;
               $req_data['status']=(!empty($request->status))?$request->status:0;

               if(!empty($id))
               {

                   $req_data['updated_at']= date('Y-m-d H:i:s');
                   $isSaved = City::where('id',$id)->update($req_data);


               }
               else 
               {

                    $req_data['created_at']= date('Y-m-d H:i:s');
                    $req_data['updated_at']= date('Y-m-d H:i:s');
                    $isSaved = City::create($req_data);
                    $country_id = $isSaved->id;
 

               }


                if ($isSaved) 
                {
                    return redirect(url('admin/cities'))->with('alert-success', 'The city has been saved successfully.');
                } 
                else 
                {
                    return back()->with('alert-danger', 'The city cannot be saved, please try again or contact the administrator.');
                }
         
         }

         $data['page_heading']= $page_heading;
         $data['country']= Country::get();
         $state= State::where(['country_id'=>$country_id])->get();
         $data['state']=   $state;
         return view('admin.cities.form', $data);
    }


    

    

    


    




    

   



    /* end of controller */
}