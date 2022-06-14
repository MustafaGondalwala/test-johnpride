<?php
namespace App\Libraries;

class CurlFunctions{

function send_request($method='GET', $url, $request=''){

    //echo $url; die;

    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => $url,
      CURLOPT_RETURNTRANSFER => false,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => $method,
      //CURLOPT_POSTFIELDS => $request,
      CURLOPT_HTTPHEADER => array(
        "cache-control: no-cache"
        ),
      ));


    ob_start();
    $response = curl_exec($curl);
    $err = curl_error($curl);
    $cache = ob_get_contents();
    ob_end_clean();

    curl_close($curl);
   /* if ($err) {
      return "cURL Error #:" . $err;
    } else {
      //return $response;
      //echo $cache = $response;
      return $cache;
    }*/

    return $cache;

  }

  function send_sms($url){
    return $this->send_request('GET', $url);
  }


  
  function check_send_status($url){
    return $this->send_request('GET', $url);
  }

  }
?>