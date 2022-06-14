<?php
namespace App\Libraries;

class InstagramApi{

	/*private $username = 'playclan';
	private $accessToken = '412922297.1677ed0.c903fc002f994d50a6e33c5f8a0146b4';*/

    private $username = 'slumberjill_women';
    private $accessToken = '8584999632.a40682a.aab9b0c44d92470a9c436dc8b4a61f65';

	public function __construct(){
        
    }


    function userID(){
    	$username = strtolower($this->username); // sanitization
    	$username = urlencode($username); // sanitization
	    $token = $this->accessToken;
	    
	    $token = urlencode($token);

	    //$url = "https://api.instagram.com/v1/users/self/?access_token=".$token;
	    $url = 'https://api.instagram.com/v1/users/self/?access_token='.$token;
	    //$url = "https://api.instagram.com/v1/users/search?q=".$username."&access_token=".$token;

	    $arrContextOptions=array(
           "ssl"=>array(
             "verify_peer"=>false,
             "verify_peer_name"=>false,
          ),
        );

        $get = file_get_contents($url, false, stream_context_create($arrContextOptions));
	    
	    //$get = file_get_contents($url);
	    $json = json_decode($get);

	    foreach($json->data as $user){
	        if($user->username == $username){
	            return $user->id;
	        }
	    }

	    return '00000000'; // return this if nothing is found
    }

    function userMedia($count=0){

    	$token = $this->accessToken;
	    
	    $token = urlencode($token);

    	$url =  'https://api.instagram.com/v1/users/self/media/recent/?access_token='.$token;

    	if(is_numeric($count) && $count > 0){
    		$url .=  "&count={$count}";
    	}

    	//$content = file_get_contents($url);

    	/*$arrContextOptions=array(
           "ssl"=>array(
             "verify_peer"=>false,
             "verify_peer_name"=>false,
          ),
        );

        $json = file_get_contents($url, false, stream_context_create($arrContextOptions));*/

        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => $url,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "GET",
          CURLOPT_HTTPHEADER => array(
            "cache-control: no-cache",
        ),
      ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        $json = '';

        if ($err) {
            //echo "cURL Error #:" . $err;
        } else {
            $json = $response;
        }




        //return $obj = json_decode($json, true);
        return $obj = json_decode($json, true, 512, JSON_BIGINT_AS_STRING);
    }

}
?>