<?php

namespace App;

use Illuminate\Support\Facades\Facade;

class CustomCartFacade extends Facade{
    protected static function getFacadeAccessor() {
        return 'Cart';
    }
}