<?php

$sip = '';

/*$sip=request()->server('SERVER_ADDR');

if($sip==""){
    $sip=request()->server('LOCAL_ADDR');
}*/

//echo 'sip='.$sip; die;

$sip = env('SERVER_IP_ADDR');

if($sip == "192.168.1.20" || $sip == "192.168.1.95")
{
	//define('OWNER_NAME','KC APP');
	//define('SUPPORT_EMAIL','ramji@indiaint.com');
	//define('INFO_EMAIL','ramji@indiaint.com');
	//define('CAREER_EMAIL','ramji@indiaint.com');
}
else if($sip == '45.118.134.240')
{

}
//define('ASSETS', asset('assets').'/');
//echo url('assets'); die;
//echo 'assests';
/*define('DIR_FS_CATALOG', url('').'/');
define('DIR_WS_CATALOG', base_path('').'/');


define('CATEGORY_WS_IMG', DIR_WS_CATALOG.'assets/images/categories/'); 
define('CATEGORY_FS_IMG', DIR_FS_CATALOG.'assets/images/categories/');

define('CATEGORY_WS_IMG_SMALL', DIR_WS_CATALOG.'assets/images/categories/small/'); 
define('CATEGORY_FS_IMG_SMALL', DIR_FS_CATALOG.'assets/images/categories/small/');*/