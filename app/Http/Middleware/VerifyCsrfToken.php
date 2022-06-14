<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'order/response',
        'order/payuresponse',
        'order/paysuccess',
        'order/payfail',
        'order/success',
        'admin/ck_upload',
        'order/paytmCallBack',
    ];
}
