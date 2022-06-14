<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Libraries\Cart;

class CartServiceProvider extends ServiceProvider{
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register(){
        $this->app->bind('Cart', function(){
            return new \App\Libraries\Cart;
        });
    }
}