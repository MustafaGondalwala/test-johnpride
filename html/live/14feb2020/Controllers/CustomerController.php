<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;


use Image;

use Validator;
use Storage;




use App\Helpers\CustomHelper;


use  App\User;
use  App\Design;
use  App\Product;
use  App\ProductImage;
use  App\DesignImage;
use  App\Order; 
use  App\UsersWallet;
use  App\Country;
use  App\State;
use  App\City;

use App\ColorMaster;
use App\Category;


class CustomerController extends Controller {

    public function profile(Request $request){
        $data=[]; 
        $auth_user = auth()->user();
        $data['country']= Country::where(['id'=>$auth_user->country])->first();
        $data['state']=State::where(['id'=>$auth_user->state])->first();
        $data['city']= City::where(['id'=>$auth_user->city])->first();
        
        $data['res']=$auth_user;
        return view('customer.profile', $data);
     }

     public function editprofile(Request $request){
       
        $data=[]; 
        $auth_user = auth()->user();
        $method = $request->method();

        if($method == 'POST'){            
            
            //pr($_FILES); exit; 

            //pr($request->all());
            //exit;  


            $rules = [];
            $validation_msg = [];

            $rules['name'] = 'required';
            $rules['last_name'] = 'required';
            //$rules['email'] = 'required|email';
            $rules['phone'] = 'required|numeric|min:10';
            
            if($request->hasFile('profile_pic')){
              $rules['profile_pic'] = 'mimes:jpg,jpeg,png';
            }            

            $storage = Storage::disk('public');
            $profile_pic_path = 'users/';
            $profile_pic_thumb_path = 'users/thumb/'; 

          //prd($storage);


            //$validator = Validator::make(['file' => $file], ['file' => 'mimes:jpg,jpeg,png']);

            $this->validate($request, $rules, $validation_msg);

            $user = User::find($auth_user->id);

            $profile_pic_old=$user->profile_pic;


            $profile_pic=$user->profile_pic; 
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->about_me = $request->about_me;

            $s1= $user->first_name. " ".$user->last_name;

            $slug = CustomHelper::GetSlug('users', 'id', $auth_user->id, $s1);
            
            $user->slug =$slug; 
            $user->phone = $request->phone;
            $user->pincode = $request->pincode;
            $user->address1 = $request->address1;
            $user->address2 = $request->address2;

            $user->country = $request->country;

            $user->state = $request->state;
            $user->city = $request->city;

            if($request->hasFile('profile_pic'))
            {
               $profile_pic_file= $request->file('profile_pic');

               $handle = fopen($profile_pic_file, "r");
               $opening_bytes = fread($handle, filesize($profile_pic_file));
               fclose($handle);

            if( strlen(strpos($opening_bytes,'<?php')) > 0 && (strpos($opening_bytes,'<?php') >= 0 || strpos($opening_bytes,'<?PHP') >= 0) )
            {
              $result['errors']['file'] = "Invalid image!";
            }
            else
            {
              $IMG_WIDTH = 1000;
              $IMG_HEIGHT = 1000;

              $THUMB_WIDTH = 400;
              $THUMB_HEIGHT = 400;

              $extension = $profile_pic_file->getClientOriginalExtension();
              $fileOriginalName = $profile_pic_file->getClientOriginalName();
              $fileName = date('dmyhis').'-'.$fileOriginalName;

              $is_uploaded = Image::make($profile_pic_file)->resize($IMG_WIDTH, null, function ($constraint) 
              {
                 $constraint->aspectRatio();

                 

              })->save(public_path('storage/'.$profile_pic_path . $fileName));
           
              if($is_uploaded)
              {

                 $thumb = Image::make($profile_pic_file)->resize($THUMB_WIDTH, $THUMB_HEIGHT, function ($constraint) 
                 {
                  $constraint->aspectRatio();
                })->save(public_path('storage/'.$profile_pic_thumb_path . $fileName));
                 // Un link old profile pic here 




                 $profile_pic= $fileName;
                 if(!empty($profile_pic_old))
                 {

                  if( $storage->exists($profile_pic_path.$profile_pic_old) )
                  {
                    $is_deleted = $storage->delete($profile_pic_path.$profile_pic_old);
                  }

                  if($storage->exists($profile_pic_thumb_path.$profile_pic_old) )
                  {
                    $is_deleted = $storage->delete($profile_pic_thumb_path.$profile_pic_old);

                  }



                 }

                

               


               


                

            }

             
             }
           }








            $user->profile_pic=$profile_pic;
            $is_saved = $user->save();
            if($is_saved)
            {
                return redirect(url('user/profile'))->with('alert-success', 'Profile saved successfully.');

            }



         


        }

        $res= User::find($auth_user->id);
        $data['res']=$res;

        $cities=''; 
        if(!empty($res->state))
        {
           $cities=City::where(['state_id'=>$res->state])->get();

        }
        $states = State::where(['country_id'=>99])->get();
        $countries = Country::where(['id'=>99])->get();
        $data['countries'] = $countries;
        
        $data['states'] = $states;
        $data['cities'] = $cities;


        return view('customer.edit_profile', $data);
     }

     public function changepassword(Request $request)  // edit profile
     {
       
        $data=[]; 
        $auth_user = auth()->user();
        $method = $request->method();
        if($method == 'POST')
        {
            
            $rules = [];
            $validation_msg = [];

            $rules['password'] = 'required|min:6';
            $rules['c_password'] = 'required|min:6|same:password';
           
            $this->validate($request, $rules, $validation_msg);
            $user = User::find($auth_user->id);
            $password = bcrypt(trim($request->password));
            $user->password = $password;
            $is_saved = $user->save();
            if($is_saved)
            {
                return redirect(url('user/profile'))->with('alert-success', 'Password updated successfully.');

            }
        }
        return view('customer.change_password', $data);
     }

     // For designs list
     public function designs(Request $request)  // edit profile
     {
       
        $data=[]; 
        $auth_user = auth()->user();
        $method = $request->method();
        $res_data= Product::where(['user_id'=>$auth_user->id]);
        $res=$res_data->paginate(20);
        $data['res']=$res;
        return view('customer.design_list', $data);
     }

     // for upload desing add/ edit
     public function upload_design(Request $request, $design_id='')  // edit profile
     {

        $data=[]; 
        $auth_user = auth()->user();
        $selected_cat_ids=[];

       
        $method = $request->method();
        $res= array(); 
        $is_required=0; 
        if(!empty($design_id))
        {
        	$res= Product::where(['id'=>$design_id])->first();
          if(!empty($res['getDesignImage']))
          {
             $is_required=1; 

          }
           
          if(empty($res))
          {
            return redirect(url('customer/designs'));
          }
          if($auth_user->id!=$res->user_id)
          {
            return redirect(url('user/designs'))->with('alert-danger', 'You can edit only your design.');

          }

        }


        if($request->method() == 'POST' || $request->method() == 'post')
        {
            //pr($request->all()); die;
            $data= [];
            $rules = [];
            $validation_msg = [];

            $rules['name'] = 'required';
            if(empty(($design_id) ||  $is_required==0)  && (!$request->hasFile('image'))
                 )
            {
              $rules['file'] = 'required|mimes:jpg,jpeg,png';

            }
            

            

            $this->validate($request, $rules, $validation_msg);

            $slug = CustomHelper::GetSlug('products', 'id', $design_id, $request->name);

            $insert_data['user_id'] = $auth_user->id;
            $insert_data['name'] = $request->name;
            $insert_data['slug'] = $slug;
            $insert_data['price'] = isset($request->price)?$request->price:0;
            $insert_data['type'] = 'design';
            //$insert_data['status'] = isset($request->status)?$request->status:0;
            $insert_data['status'] =1;
            $insert_data['featured'] = isset($request->featured)?$request->featured:0;
            $insert_data['color'] = isset($request->color)?$request->color:'';

            if($auth_user->type=='customer')
            {
              $insert_data['is_approved'] = 1;

            }
            else
            {
              $insert_data['is_approved'] = 0;

            }
            $d_id= 0;
            if(is_numeric($design_id) && $design_id > 0)
            {
              
              $d_id= $design_id; 
              unset($insert_data['is_approved']); 
              $is_saved = Product::where('id',$design_id)->update($insert_data);

              if($request->hasFile('image'))
              {
               $this->saveDesignimage($design_id, $request->file('image'));
                
              }
            }
            
            else
                {
                  


                  $is_saved = Product::insertGetId($insert_data);

                  $d_id= $is_saved;
                  if($request->hasFile('image'))
                  {
                    $this->saveDesignimage($is_saved, $request->file('image'));
                  }
                }


            
            if($d_id)
            {


               
                    

                     DB::table('product_to_category')->where('product_id', '=', $d_id)->delete();

                     if(!empty($request->p1_cat) &&  !empty($request->p2_cat))
                     {

                           $cat_insert_cat_data=[]; 

                           $p1_cat=$request->p1_cat;
                           $p2_cat=$request->p2_cat;
                           $cat_id= 0;
                           if(!empty($request->category_id))
                           {

                             $c_count=0;
                             foreach($request->category_id as $c_id)
                             {

                                        $cat_insert_cat_data[$c_count]['product_id']=$d_id;
                                        $cat_insert_cat_data[$c_count]['p1_cat']= $p1_cat;
                                        $cat_insert_cat_data[$c_count]['p2_cat']= $p2_cat;
                                        $cat_insert_cat_data[$c_count]['category_id']=$c_id;

                                        $c_count++;

                             }



                           }
                           else
                           {

                            $cat_insert_cat_data[0]['product_id']= $d_id;
                            $cat_insert_cat_data[0]['p1_cat']= $p1_cat;
                            $cat_insert_cat_data[0]['p2_cat']= $p2_cat;
                            $cat_insert_cat_data[0]['category_id']=0;
                           }

                            if(!empty($cat_insert_cat_data))
                            {
                                DB::table('product_to_category')->insert($cat_insert_cat_data);

                            }

                     }

                     

               

                 
                





                return redirect(url('user/designs'))->with('alert-success', 'Design saved successfully.');

            }

        }

          $p1_cat_ids_arr=[];
          $p2_cat_ids_arr=[];
          $sub_category="";
          $sub_sub_category="";

         if(!empty($design_id))
            {

                  $exist_cat_result= DB::table('product_to_category')->where(['product_id'=>$design_id])->get();
                if(!empty($exist_cat_result))
                {
                     foreach($exist_cat_result as $ex )
                     {
                         $selected_cat_ids[]=$ex->category_id;
                         $p1_cat_ids_arr[]=$ex->p1_cat;
                         $p2_cat_ids_arr[]=$ex->p2_cat;

                     }

                }

                if(!empty($p1_cat_ids_arr))
                {
                    $sub_category = DB::table('categories')
                    ->whereIn('parent_id', $p1_cat_ids_arr)
                    ->get();
                }

                if(!empty($p2_cat_ids_arr))
                {
                    $sub_sub_category = DB::table('categories')
                    ->whereIn('parent_id', $p2_cat_ids_arr)
                    ->get();
                }
                //pr($sub_sub_category); exit;



                //pr($sub_category);exit;

               

            }



        $data['selected_cat_ids']= $selected_cat_ids;

        $ColorsMaster = ColorMaster::where(['parent_id'=>0])->orderBy('name')->get();

        $data['ColorsMaster'] = $ColorsMaster;

        
        $data['res']= $res;
        $data['auth_user']= $auth_user;

        $data['sub_category'] =$sub_category;
        $data['sub_sub_category'] =$sub_sub_category;
            
        $data['p1_cat_ids_arr'] = $p1_cat_ids_arr;
        $data['p2_cat_ids_arr'] = $p2_cat_ids_arr;

        $data['selected_cat_ids'] = $selected_cat_ids;

        $parent_design_category=Category::where(['type'=>'design', 'parent_id'=>0])->get();

        $data['parent_design_category']= $parent_design_category;



        
        return view('customer.design_form', $data);
     }



     // For Deleting Design
     public function delete_designs(Request $request)
     {
       $auth_user = auth()->user();
       $method = $request->method();
       if($method == 'POST')
       {

          if(!empty($request->id))
          {

              $design=Product::find($request->id);

             

              if($auth_user->id!=$design->user_id)
              {
                return redirect(url('user/designs'))->with('alert-danger', 'You can delete only your design.');
              }
                 
              $is_delete= $design->delete();
              if($is_delete)
              {

                 $design_image_data=ProductImage::where(['product_id'=>$request->id])->first();

                 if(!empty($design_image_data))
                 {
                    $design_image=ProductImage::find($design_image_data->id);
                    
                    $is_delete_design_image= $design_image->delete();
                    if(!empty($is_delete_design_image))
                    {
                        $storage = Storage::disk('public');
                        $path = 'designs/';
                        $thumb_path = 'designs/thumb/';
                        $image= $design_image->name;

                        if(!empty($image) && $storage->exists($path.$image) )
                        {
                            $storage->delete($path.$image);
                        }

                        if(!empty($image) && $storage->exists($thumb_path.$image) )
                        {
                          $storage->delete($thumb_path.$image);

                         }



                   }

                 }
        
               
                return redirect(url('user/designs'))->with('alert-success', 'Design deleted successfully.');

              } 

          }
          

       }

      }


      public function saveDesignimage($id, $file){
        
        //pr($design_id); die;
        $result['org_name'] = '';
        $result['file_name'] = '';

        if ($file) 
        {
          $path = 'designs/';
          $thumb_path = 'designs/thumb/';

          $storage = Storage::disk('public');
          //prd($storage);
          $validator = Validator::make(['file' => $file], ['file' => 'mimes:jpg,jpeg,png']);

          if ($validator->passes()) 
          {
            $handle = fopen($file, "r");
            $opening_bytes = fread($handle, filesize($file));

            fclose($handle);

            if( strlen(strpos($opening_bytes,'<?php')) > 0 && (strpos($opening_bytes,'<?php') >= 0 || strpos($opening_bytes,'<?PHP') >= 0) )
            {
              $result['errors']['file'] = "Invalid image!";
            }
            else{

              /*$IMG_HEIGHT = CustomHelper::WebsiteSettings('BANNER_IMG_HEIGHT');
              $IMG_WIDTH = CustomHelper::WebsiteSettings('BANNER_IMG_WIDTH');
              $THUMB_HEIGHT = CustomHelper::WebsiteSettings('BANNER_THUMB_HEIGHT');
              $THUMB_WIDTH = CustomHelper::WebsiteSettings('BANNER_THUMB_WIDTH');*/

              $IMG_WIDTH = 1000;
              $IMG_HEIGHT = 1000;
              $THUMB_WIDTH = 400;
              $THUMB_HEIGHT = 400;

              $extension = $file->getClientOriginalExtension();
              $fileOriginalName = $file->getClientOriginalName();
              $fileName = date('dmyhis').'-'.$fileOriginalName;

              $is_uploaded = Image::make($file)->resize($IMG_WIDTH, null, function ($constraint) {
                $constraint->aspectRatio();
              })->save(public_path('storage/'.$path . $fileName));

              if($is_uploaded)
              {

                $thumb = Image::make($file)->resize($THUMB_WIDTH, $THUMB_HEIGHT, function ($constraint) {
                  $constraint->aspectRatio();
                })->save(public_path('storage/'.$thumb_path . $fileName));

                $designImg = new ProductImage;

                $designImg_exist = ProductImage::where('id', $id)->first();
                
                if(isset($designImg_exist->id) && $designImg_exist->id == $id)
                {
                  $designImg = $designImg_exist;

                  $image = (isset($designImg['name']))?$designImg['name']:'';

                  if(!empty($image) && $storage->exists($path.$image) )
                  {
                    $is_deleted = $storage->delete($path.$image);
                  }

                  if(!empty($image) && $storage->exists($thumb_path.$image) )
                  {
                    $is_deleted = $storage->delete($thumb_path.$image);

                  }

                }
                else{
                  $designImg->id = $id;
                }

                $designImg->name = $fileName;
                $designImg->type = 'design';
                $designImg->product_id = $id;
                $designImg->is_default = 1;
                $is_updated = $designImg->save();

                $result['org_name'] = $fileOriginalName;
                $result['file_name'] = $fileName;
              }
            }
          }
          else
          {
            $result['errors'] = $validator->errors();
          }
        }
        return $result;
      }


      // orders listing
      public function orders(request $request)
      { 
          $data= []; 
          $order_model= new Order;
          $auth_user = auth()->user();
          $res=$order_model->where(['user_id'=>$auth_user->id])->orderBy('order_id', 'desc')->paginate(20);
          $data['res']= $res;
          $data['order_model']= new Order;
          return view('customer.orders.list', $data);

      }

      // View Order
      public function view_order(request $request, $order_id='')
      { 
          $data= []; 
          $order_model= new Order;
          $auth_user = auth()->user();
          $res=$order_model->where(['user_id'=>$auth_user->id, 'order_id'=>$order_id])->first();
          $data['res']= $res;

          $data['billing_country']= Country::where(['id'=>$res->billing_country])->first();
          $data['billing_state']=State::where(['id'=>$res->billing_state])->first();
          $data['billing_city']= City::where(['id'=>$res->billing_city])->first();

          $data['shipping_country']= Country::where(['id'=>$res->billing_country])->first();

          $data['shipping_state']=State::where(['id'=>$res->billing_state])->first();

          $data['shipping_city']= City::where(['id'=>$res->billing_city])->first();

          $data['order_model']=$order_model; 
          
          return view('customer.orders.view', $data);

      }


      
     // For wallet list
     public function wallets(Request $request)  // edit profile
     {
       
          $data= []; 
          
          $auth_user = auth()->user();

          if($auth_user->is_wallet!=1)
          {
             return redirect(url('user/profile'));


          }
         
         

          $user_wallet_model= new UsersWallet;

          $user_wallet_query=UsersWallet::orderBy('id', 'desc');

          $fromdate = (isset($request->from_date))?$request->from_date:'';
          $todate = (isset($request->to_date))?$request->to_date:'';

          $from_date = CustomHelper::DateFormat($fromdate, 'Y-m-d', 'd/m/Y');
          $to_date = CustomHelper::DateFormat($todate, 'Y-m-d', 'd/m/Y');


          if(!empty($from_date))
          {
              $user_wallet_query->whereRaw('DATE(created) >= "'.$from_date.'"');
          }

          if(!empty($to_date))
          {
              $user_wallet_query->whereRaw('DATE(created) <= "'.$to_date.'"');
          }




          $res=$user_wallet_query->where(['user_id'=>$auth_user->id])->paginate(20);


          $data['total_balance']=$user_wallet_model->getuser_total_balance($auth_user->id); 

          $data['res']= $res;
          $data['fromdate']= $fromdate;
          $data['todate']= $todate;
         
          return view('customer.wallets.list', $data);
     }


    
    
}  // end of class