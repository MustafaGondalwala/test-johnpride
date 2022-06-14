<?php

namespace App\Http\Controllers;

use App\Libraries\CaptchaSecurityImages as Captcha;

class CaptchaController extends Controller {

    public function index(){

    	//request
        $CaptchaObj = new Captcha;
        $CaptchaImg = $CaptchaObj->generateCaptcha(4);
    }

    /* End of controller */
}