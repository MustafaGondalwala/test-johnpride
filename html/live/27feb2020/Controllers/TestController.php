<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Helpers\CustomHelper;

use Validator;
use Storage;
use Image;

class TestController extends Controller 
{

     public function upload(Request $request)
     {
     	if($request->upload)
     	{
     		//echo 'here'; die;
     		//echo '<pre>'; print_r($request->image); die;
			if($request->image) 
	        {
	        	$file = $request->image;
	        	//echo '<pre>'; print_r($file); die;

	            $path = 'test/';
	            $thumb_path = 'test/thumb/';

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
	                else
	                {

	                    $IMG_HEIGHT = CustomHelper::WebsiteSettings('BANNER_IMG_HEIGHT');
	                    $IMG_WIDTH = CustomHelper::WebsiteSettings('BANNER_IMG_WIDTH');
	                    $THUMB_HEIGHT = CustomHelper::WebsiteSettings('BANNER_THUMB_HEIGHT');
	                    $THUMB_WIDTH = CustomHelper::WebsiteSettings('BANNER_THUMB_WIDTH');

	                    $IMG_WIDTH = (!empty($IMG_WIDTH))?$IMG_WIDTH:1900;
	                    $IMG_HEIGHT = (!empty($IMG_HEIGHT))?$IMG_HEIGHT:1900;
	                    $THUMB_WIDTH = (!empty($THUMB_WIDTH))?$THUMB_WIDTH:400;
	                    $THUMB_HEIGHT = (!empty($THUMB_HEIGHT))?$IMG_WIDTH:400;

	                    $extension = $file->getClientOriginalExtension();
	                    $fileOriginalName = $file->getClientOriginalName();
	                    $fileName = date('dmyhis').'-'.$fileOriginalName;

	                    $is_uploaded = Image::make($file)->resize($IMG_WIDTH, $IMG_HEIGHT, function ($constraint) {
	                            $constraint->aspectRatio();
	                        })->save(public_path('storage/'.$path . $fileName));

	                    if($is_uploaded)
	                    {
	                        $thumb = Image::make($file)->resize($THUMB_WIDTH, $THUMB_HEIGHT, function ($constraint) {
	                            $constraint->aspectRatio();
	                        })->save(public_path('storage/'.$thumb_path . 'tile-'.$fileName));
	                        //echo '<pre>'; print_r($thumb); die;

	                        $img_width = Image::make(public_path('storage/'.$thumb_path . 'tile-'.$fileName))->width();
	                        $img_height = Image::make(public_path('storage/'.$thumb_path . 'tile-'.$fileName))->height();
	                        //echo $img_width.' = '.$img_width; die;


							//make brick image.
							/*******************************************/
							$img = Image::canvas(($img_width*2), ($img_height*2), '#ccc');
							$img->insert(public_path('storage/'.$thumb_path . 'tile-'.$fileName),'top-left');
							$img->insert(public_path('storage/'.$thumb_path . 'tile-'.$fileName),'top-right');
							$img->insert(public_path('storage/'.$thumb_path . 'tile-'.$fileName),'bottom-left');
							$img->insert(public_path('storage/'.$thumb_path . 'tile-'.$fileName),'bottom-right');
							$img->save(public_path('storage/'.$thumb_path . 'brick-'.$fileName));
							//echo 'Done'; die;
							/*******************************************/


							//make mirror image.
							/*******************************************/
							// flip image vertically
							$img1 = Image::make(public_path('storage/'.$thumb_path . 'tile-'.$fileName));
							$img1->flip('v');
							$img1->save(public_path('storage/'.$thumb_path . 'm1-'.$fileName));
							//join vertically
							$img2 = Image::canvas($img_width, ($img_height*2), '#ccc');
							$img2->insert(public_path('storage/'.$thumb_path . 'tile-'.$fileName),'top');
							$img2->insert(public_path('storage/'.$thumb_path . 'm1-'.$fileName),'bottom');
							$img2->save(public_path('storage/'.$thumb_path . 'm2-'.$fileName));
							//echo 'Done'; die;
							// flip image horizontally
							$img3 = Image::make(public_path('storage/'.$thumb_path . 'm2-'.$fileName));
							$img3->flip('h');
							$img3->save(public_path('storage/'.$thumb_path . 'm3-'.$fileName));
							//join horizontally
							$img4 = Image::canvas(($img_width*2), ($img_height*2), '#ccc');
							$img4->insert(public_path('storage/'.$thumb_path . 'm2-'.$fileName),'left');
							$img4->insert(public_path('storage/'.$thumb_path . 'm3-'.$fileName),'right');
							$img4->save(public_path('storage/'.$thumb_path . 'mirror-'.$fileName));
							/*******************************************/
							$storage->delete($thumb_path . 'm1-'.$fileName);
							$storage->delete($thumb_path . 'm2-'.$fileName);
							$storage->delete($thumb_path . 'm3-'.$fileName);
							echo 'Done'; die;


	                        /*$BannerImage = new BlogImage;
	                        $BannerImage->blog_id = $blog_id;
	                        $BannerImage->image = $fileName;
	                        $BannerImage->save();*/

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
     	}       
        $data=[]; 
        $auth_user = auth()->user();
        $data['res']=$auth_user;
        return view('test.upload', $data);
     }

    
    
}