<?php
namespace App;
use DB;
use Illuminate\Database\Eloquent\Model;
class Setting extends Model
{
    protected $table = 'website_settings';

    protected $guarded = ['id'];

    public $timestamps = false;

    protected $fillable = [];



   public static function get_setting($setting_key= '')
   {
   	  $val='';
       
   	  if(!empty($setting_key))
   	  {
   	  	   
           $res= DB::table('website_settings')->select(['value'])->where(['name'=>$setting_key])->first();
          
   	  	   if(!empty($res))
   	  	   {
   	  	   	   $val=$res->value;  

   	  	   }

   	  }
      return $val; 
   	 

   }


}