<?php
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Auth::routes();

Route::get('phpartisan', function(){
        //prd(request()->all());
    $cmd = request('cmd');
        //echo $cmd; die;
    if(!empty($cmd)){
        $exitCode = Artisan::call("$cmd");
    }
});


Route::group(['prefix' => 'processjobs', 'as' => 'processjobs'], function(){

    Route::get('getInventory', 'ProcessJobsController@getInventory');
    Route::get('cart_abondon', 'ProcessJobsController@cartAbondon');
    Route::get('update_order_status', 'ProcessJobsController@updateOrderStatus');    

});



$segments_arr = request()->segments();

//dd($routeCollection);

/*$home_slug_arr = ['about', 'returns', 'faq', 'terms', 'privacy'];

if(isset($segments_arr[0]) && in_array($segments_arr[0], $home_slug_arr)){
    Route::get('/{slug}', 'HomeController@cmsPage');
}*/

Route::get('/prideonme', 'PrideOnmeController@index');
Route::get('/cache_test', 'HomeController@cache_test');

Route::get('/', 'HomeController@index');
Route::get('/getUnicommerceAccessTokenSave', 'HomeController@tokentest');
Route::match(['get', 'post'], '/contact', 'HomeController@contact')->name('contact');
Route::match(['get', 'post'], '/enquiry-form', 'HomeController@enquiryForm')->name('enquiry-form');
Route::match(['get', 'post'], '/logout', 'HomeController@logout')->name('logout');
Route::match(['get', 'post'], '/track-order', 'HomeController@track_order')->name('track-order');

Route::redirect('account/', 'login', 301);
Route::redirect('login', 'account/login', 301);

Route::group(['prefix' => 'account', 'as' => 'account'], function(){

    Route::match(['get', 'post'], 'login', 'AccountController@login');
    Route::match(['get', 'post'], 'register', 'AccountController@register');
    Route::match(['get', 'post'], 'verify', 'AccountController@verify');
    Route::match(['get', 'post'], 'forgot', 'AccountController@forgot');
    Route::match(['get', 'post'], 'reset', 'AccountController@reset');
    
    Route::post('ajax_login', 'AccountController@ajaxLogin');
    Route::post('ajax_register', 'AccountController@ajaxRegister');
    Route::post('ajax_forgot', 'AccountController@ajaxForgot');

    Route::get('fblogin', 'AccountController@fbLogin')->name('.fbLogin');
    Route::get('fbcallback', 'AccountController@fbCallback')->name('.fbCallback');
    Route::get('glogin', 'AccountController@googleLogin')->name('.gLogin');
});

//Route::redirect('uo', 'users/orders/', 301);

Route::get('uo/{order_no?}', function($order_no=''){
    //prd($order_no);
    return redirect('users/orders/'.$order_no, 301);
});

Route::group(['prefix' => 'users', 'as' => 'users', 'middleware' => ['auth']], function(){

    Route::match(['get', 'post'], '/', 'UserController@profile');

    Route::match(['get', 'post'], 'profile', 'UserController@profile');
    Route::match(['get', 'post'], 'update', 'UserController@update');
    Route::get('addresses', 'UserController@addresses');

    Route::get('orders/{order_no?}', 'UserController@orders');
    Route::get('details', 'UserController@details');
    Route::get('wallet', 'UserController@wallet');
    Route::get('loyalty-points', 'UserController@loyaltyPoints');
    Route::get('wishlist', 'UserController@wishlist');

    Route::post('get_address_form', 'UserController@getAddressForm');
    Route::post('save_address', 'UserController@saveAddress');
    
    Route::post('add_to_wishlist', 'UserController@addToWishlist');
    Route::post('delete_from_wishlist', 'UserController@deleteFromWishlist');
    
    Route::post('notify_product_size', 'UserController@notifyProductSize');
    Route::post('get_order_cancel_form', 'UserController@getOrderCancelForm')->name('.get_order_cancel_form');
    Route::post('ajax_cancel_order', 'UserController@cancelOrder')->name('.ajax_cancel_order');

    Route::post('get_order_return_form', 'UserController@getOrderReturnForm')->name('.get_order_return_form');
    Route::post('ajax_return_order', 'UserController@returnOrder')->name('.ajax_return_order');
    
    Route::post('ajax_print_invoice', 'UserController@printInvoice')->name('.ajax_print_invoice');
});


Route::group(['prefix' => 'common', 'as' => 'common'], function(){
    
    Route::post('ajax_load_cities', 'CommonController@ajaxLoadCities');
    Route::post('ajax_regenerate_captcha', 'CommonController@ajaxRegenerateCaptcha');
    
    Route::post('ajax_set_currency', 'CommonController@ajaxSetCurrency');
    
    Route::post('ajax_check_pincode', 'CommonController@ajaxCheckPincode');

    Route::post('get_pincode_city_state', 'CommonController@getPincodeCityState');
    Route::match(['get', 'post'], 'newsletterSubscribe', 'CommonController@newsletterSubscribe');

});

// Product
Route::group(['prefix' => 'products', 'as' => 'products'], function() {
    Route::get('/', 'ProductController@index')->name('.list');
    Route::get('/{p2cat}', 'ProductController@index');
    Route::get('/details/{slug}', 'ProductController@details')->name('.details');

    Route::post('/ajax_get_list_by_search', 'ProductController@ajaxGetListBySearch')->name('.ajax_get_list_by_search');
    Route::post('load_more', 'ProductController@loadMore')->name('.loadMore');

    Route::post('/ajax_check_pincode', 'ProductController@ajaxCheckPincode')->name('.ajax_check_pincode');

    //save_review

    Route::group(['middleware' => ['auth']], function () {
        Route::post('save_review', 'ProductController@saveReview');
    });
});

// Collectons
Route::group(['prefix' => 'collections', 'as' => 'collections'], function() {
    Route::get('/{collection}', 'ProductController@index')->name('.list');
   // Route::get('/{p2cat}', 'ProductController@index');
    Route::get('/details/{slug}', 'ProductController@details')->name('.details');

    

   
});


Route::group(['prefix' => 'blogs', 'as' => 'blogs.'], function() {
    Route::get('/', 'BlogController@index');
    Route::get('/{slug}', 'BlogController@details')->name('details');
    //Route::get('/details/{slug}', 'BlogController@details')->name('details');
});



// Cart
Route::group(['prefix' => 'cart', 'as' => 'cart'], function() {

    Route::get('/', 'CartController@index');
    Route::get('/empty', 'CartController@empty'); 

    Route::match(['get', 'post'],'payment-method', 'CartController@payment_method')->name('payment-method');  
    
    Route::post('add', 'CartController@add');
    Route::post('update', 'CartController@update');
    Route::post('delete', 'CartController@delete');
    
    Route::post('ajax_get_size_qty', 'CartController@ajaxGetSizeQty');

    Route::group(['middleware' => ['auth']], function () {
        Route::match(['get', 'post'], 'address/{id?}', 'CartController@address');
        Route::match(['get', 'post'], 'checkout', 'CartController@checkout');
        Route::post('apply_coupon', 'CartController@applyCoupon');
        Route::post('remove_coupon', 'CartController@removeCoupon');

    });

});

// Order

   Route::get('order/success/{order_id}', 'OrderController@success');
   Route::get('order/failed', 'OrderController@failed');
   Route::match(['get', 'post'], 'order/response', 'OrderController@response');
   Route::match(['get', 'post'], 'order/payuresponse', 'OrderController@payuresponse');
    Route::match(['get', 'post'], 'order/paysuccess', 'OrderController@paysuccess');
    Route::match(['get', 'post'], 'order/payfail', 'OrderController@payfail');

Route::group(['prefix' => 'order', 'as' => 'order', 'middleware' => ['auth'] ], function() {
    Route::get('/', 'OrderController@index');
    Route::post('process', 'OrderController@process');
    //Route::match(['get', 'post'], 'response', 'OrderController@response');
    Route::get('confirmation', 'OrderController@confirmation');
    
    //Route::get('success', 'OrderController@success');
    //  Route::get('success/{order_id}', 'OrderController@success');

    // Route::get('failed', 'OrderController@failed');
});

 



Route::group(['prefix' => 'test', 'as' => 'test.'], function(){
    Route::any('/upload', 'TestController@upload');    
});


Route::match(['get', 'post'], 'admin/login', 'Admin\LoginController@index');
/*Route::match(['get', 'post'], 'admin/login', function(){
    echo 'adminj/login'; die;
});
Route::get('admin/login', 'Admin\LoginController@index');
Route::post('admin/login', 'Admin\LoginController@auth');*/

// Admin
Route::group(['namespace' => 'Admin', 'prefix' => 'admin', 'as' => 'admin.', 'middleware' => ['authadmin']], function() {
    

    // test - URL: /admin/test
    Route::any('test', 'TestController@index');

    // logout - URL: /admin/logout
    Route::post('/logout', 'LoginController@logout')->name('logout');

    Route::match(['get','post'],'change_password','AdminController@index')->name('change_password');
    

    // Dashboard - URL: /admin
    Route::get('/', 'HomeController@index')->name('home');
    Route::match(['get', 'post'], 'verify_password', 'HomeController@verify_password');

    Route::post('ck_upload', 'HomeController@ckUpload')->name('ck_upload');


    // Customers    
    Route::group(['prefix' => 'customers', 'as' => 'customers'], function() {

        Route::get('/', 'CustomerController@index')->name('.index');

        Route::match(['get', 'post'], 'add/', 'CustomerController@add')->name('.add');

        Route::match(['get', 'post'], 'edit/{customer_id}', 'CustomerController@add')->where(['customer_id'=>'[0-9]+'])->name('.edit');

        Route::post('search/', 'CustomerController@search')->name('.search')->middleware('permission:customers.search');

        Route::match(['get', 'post'], 'wallet/{user_id}', 'CustomerController@wallet')->where(['user_id' => '[0-9]+',])->name('.wallet');

        Route::match(['get', 'post'], 'loyalty-points/{user_id}', 'CustomerController@loyaltyPoints')->where(['user_id' => '[0-9]+',])->name('.loyalty-points');

        Route::match(['get', 'post'], 'customer_import', 'CustomerController@customerUpload')->name('.customer_upload');

        Route::match(['get', 'post'], 'test_import', 'CustomerController@test_import')->name('.test_import');


    });

    // Categories
    Route::group(['prefix' => 'categories', 'as' => 'categories'], function() {

        Route::get('/', 'CategoryController@index')->name('.index');

        Route::match(['get', 'post'], 'add/', 'CategoryController@add')->name('.add');

        Route::match(['get', 'post'], 'edit/{id}', 'CategoryController@add')->where(['id'=>'[0-9]+'])->name('.edit');
        
        Route::delete('delete/{id}', 'CategoryController@delete')->name('.delete');

    });


    //settings 
    Route::group(['prefix' => 'settings', 'as' => 'settings'], function() {

        Route::any('/', 'SettingsController@index')->name('.index');
        Route::any('/{setting_id}', 'SettingsController@index')->name('.index');
     }); 



    Route::group(['prefix' => 'newsletter', 'as' => 'newsletter'], function() {

         Route::get('/', 'NewsletterController@index')->name('.index');
         Route::post('/delete/{id}', 'NewsletterController@delete')->name('.delete');
       
    }); 

    // for countries 
    Route::group(['prefix' => 'countries', 'as' => 'countries'], function() {

        Route::get('/', 'CountryController@index')->name('.index');

        Route::match(['get', 'post'], '/save/{id?}', 'CountryController@save')->name('.save');
    });

    Route::group(['prefix' => 'states', 'as' => 'states'], function() {

        Route::get('/', 'StateController@index')->name('.index');

        Route::match(['get', 'post'], '/save/{id?}', 'StateController@save')->name('.save');
    });  

    Route::group(['prefix' => 'cities', 'as' => 'cities'], function() {

        Route::get('/', 'CityController@index')->name('.index');
        Route::match(['get', 'post'], '/save/{id?}', 'CityController@save')->name('.save');
        Route::match(['get', 'post'], 'import', 'CityController@import')->name('.import');
    });
      

    // Product
    Route::group(['prefix' => 'products', 'as' => 'products'], function() {

        Route::get('/', 'ProductController@index')->name('.index');
        
        Route::match(['get', 'post'], 'add', 'ProductController@add')->name('.add');
        Route::match(['get', 'post'], 'edit/{id}', 'ProductController@add')->name('.edit');

        Route::post('ajax_get_category_child', 'ProductController@ajaxGetCategoryChild')->name('.ajax_get_category_child');
        
        Route::post('ajax_get_category_attributes', 'ProductController@ajaxGetCategoryAttributes')->name('.ajax_get_category_attributes');
        Route::match(['get', 'post'], 'inventory/{product_id}/{inventory_id?}', 'ProductController@inventory')->name('.inventory');


        Route::post('ajax_remove_link', 'ProductController@ajaxRemoveLink')->name('.ajax_remove_link');

        Route::get('inventory_list', 'ProductController@inventoryList')->name('.inventory_list');

        // Product Inventory
        Route::group(['prefix' => '{product_id}', 'as' => '.product.'], function() {
            // Inventory
            Route::group(['prefix' => 'inventory', 'as' => 'inventory'], function() {
                // Create Inventory Item - URL: /admin/products/{product_id}/inventory/
                Route::match(['get', 'post'], '/{inventory_id?}', 'ProductController@inventory');
                Route::post('/{inventory_id}/delete', 'ProductController@deleteInventory');
            });
        });

        Route::match(['get', 'post'], 'upload', 'ProductController@upload')->name('.upload');
        Route::match(['get', 'post'], 'inventory_upload', 'ProductController@inventoryUpload')->name('.inventory_upload');


    });


    // Banners
    Route::group(['prefix' => 'banners', 'as' => 'banners'], function() {

        Route::get('/', 'BannerController@index')->name('.index');

        Route::match(['get', 'post'], 'add', 'BannerController@add')->name('.add');
        Route::match(['get', 'post'], 'edit/{banner_id}', 'BannerController@add')->name('.edit');

        Route::post('ajax_delete_image', 'BannerController@ajax_delete_image')->name('.ajax_delete_image');

        Route::post('delete/{banner_id}', 'BannerController@delete')->name('.delete');
    });


        // Size Chart
    Route::group(['prefix' => 'size_charts', 'as' => 'size_charts'], function() {

        Route::get('/', 'SizeChartController@index')->name('.index');

        Route::match(['get', 'post'], 'add', 'SizeChartController@add')->name('.add');
        Route::match(['get', 'post'], 'edit/{id}', 'SizeChartController@add')->name('.edit');

        Route::post('ajax_delete_image', 'SizeChartController@ajax_delete_image')->name('.ajax_delete_image');

        Route::post('delete/{id}', 'SizeChartController@delete')->name('.delete');
    });


    // Brands
    Route::group(['prefix' => 'brands', 'as' => 'brands'], function() {

        Route::get('/', 'BrandController@index')->name('.index');

        Route::match(['get', 'post'], 'add', 'BrandController@add')->name('.add');
        Route::match(['get', 'post'], 'edit/{id}', 'BrandController@add')->name('.edit');

        Route::post('ajax_delete_image', 'BrandController@ajax_delete_image')->name('.ajax_delete_image');

        Route::post('delete/{id}', 'BrandController@delete')->name('.delete');
    });

    // Customer Picture
    Route::group(['prefix' => 'customer-picture', 'as' => 'customer-picture'], function() {

        Route::get('/', 'CustomerPictureController@index')->name('.index');

        Route::match(['get', 'post'], 'add', 'CustomerPictureController@add')->name('.add');
        Route::match(['get', 'post'], 'edit/{id}', 'CustomerPictureController@add')->name('.edit');

        Route::post('ajax_delete_image', 'CustomerPictureController@ajax_delete_image')->name('.ajax_delete_image');

        Route::post('delete/{id}', 'CustomerPictureController@delete')->name('.delete');
    });

    // look book section
    Route::group(['prefix' => 'look-book', 'as' => 'look-book'], function() {

        Route::get('/', 'LookBookController@index')->name('.index');

        Route::match(['get', 'post'], 'add', 'LookBookController@add')->name('.add');
        Route::match(['get', 'post'], 'edit/{id}', 'LookBookController@add')->name('.edit');

        Route::post('ajax_delete_image', 'LookBookController@ajax_delete_image')->name('.ajax_delete_image');

        Route::post('delete/{id}', 'LookBookController@delete')->name('.delete');
    });

    // Colors
    Route::group(['prefix' => 'colors', 'as' => 'colors'], function() {

        Route::get('/', 'ColorController@index')->name('.index');

        Route::match(['get', 'post'], 'add/{id?}', 'ColorController@add')->name('.add');

        Route::delete('delete/{id}}', 'ColorController@delete')->where(['id'=>'[0-9]+'])->name('.delete');
    });


         // Home Images
    Route::group(['prefix' => 'home_images', 'as' => 'home_images'], function() {

        Route::get('/', 'HomeImageController@index')->name('.index');

        Route::match(['get', 'post'], 'add', 'HomeImageController@add')->name('.add');
        Route::match(['get', 'post'], 'edit/{id}', 'HomeImageController@add')->name('.edit');

        Route::post('ajax_delete_image', 'HomeImageController@ajax_delete_image')->name('.ajax_delete_image');

        Route::post('delete/{id}', 'HomeImageController@delete')->name('.delete');
    });

    //Size
    Route::group(['prefix' => 'sizes', 'as' => 'sizes'], function() {

        Route::get('/', 'SizeController@index')->name('.index');
        Route::match(['get', 'post'], '/{id?}', 'SizeController@index')->name('.index');
        Route::post('delete/{id}', 'SizeController@delete')->name('.delete');
     }); 

     //Pincodes
    Route::group(['prefix' => 'pincodes', 'as' => 'pincodes'], function() {

        Route::get('/', 'PincodeController@index')->name('.index');
        Route::match(['get', 'post'], 'import', 'PincodeController@import')->name('.import');
        Route::match(['get', 'post'], '/{id?}', 'PincodeController@index')->name('.index');
        Route::post('delete/{id}', 'PincodeController@delete')->name('.delete');
        
    });

    // Shipping Zones
    Route::group(['prefix' => 'shippingzones', 'as' => 'shippingzones'], function() {

        Route::get('/', 'ShippingZoneController@index')->name('.index');

        Route::match(['get', 'post'], 'add', 'ShippingZoneController@add')->name('.add');

        Route::match(['get', 'post'], 'edit/{id}', 'ShippingZoneController@edit')->name('.edit');

        Route::post('delete/{id}', 'ShippingZoneController@delete')->name('.delete');
    });  


    // Shipping Rate
    Route::group(['prefix' => 'shippingrates', 'as' => 'shippingrates'], function() {

        Route::match(['get', 'post'], '/{id?}', 'ShippingRateController@index')->name('.index');

        Route::post('delete/{id}', 'ShippingRateController@delete')->name('.delete');
    });
    

    // CMS Pages
    Route::group(['prefix' => 'cms', 'as' => 'cms'], function() {
        Route::get('/', 'CmsController@index')->name('.index');
        Route::match(['get', 'post'], 'add', 'CmsController@edit')->name('.add');
        Route::match(['get', 'post'],'edit/{cms_id?}', 'CmsController@edit')->where('cms_id', '[0-9]+')->name('.edit');

        Route::post('delete/{id}', 'CmsController@delete')->name('.delete');
    });


    // Coupon
    Route::group(['prefix' => 'coupons', 'as' => 'coupons'], function() {

        Route::get('/', 'CouponController@index')->name('.index');

        Route::match(['get', 'post'], 'add', 'CouponController@add')->name('.add');

        Route::match(['get', 'post'], 'edit/{id}', 'CouponController@edit')->name('.edit');

        Route::post('delete/{id}', 'CouponController@delete')->name('.delete');
    });

    // Blog Categories
    Route::group(['prefix' => 'blogs_categories', 'as' => 'blogs_categories'], function() {

        Route::get('/', 'BlogCategoryController@index')->name('.index');
        Route::match(['get', 'post'], 'add', 'BlogCategoryController@add')->name('.add');
        Route::match(['get', 'post'], 'edit/{id}', 'BlogCategoryController@add')->name('.edit');
        Route::post('ajax_delete_image', 'BlogCategoryController@ajax_delete_image')->name('.ajax_delete_image');
        Route::post('delete/{id}', 'BlogCategoryController@delete')->name('.delete');
    });

    // Blogs
    Route::group(['prefix' => 'blogs', 'as' => 'blogs'], function() {

        Route::get('/', 'BlogController@index')->name('.index');
        Route::match(['get', 'post'], 'add', 'BlogController@add')->name('.add');
        Route::match(['get', 'post'], 'edit/{id}', 'BlogController@add')->name('.edit');
        Route::post('ajax_delete_image', 'BlogController@ajax_delete_image')->name('.ajax_delete_image');
        Route::post('delete/{id}', 'BlogController@delete')->name('.delete');
    });

    
    // Orders
    Route::group(['prefix' => 'orders', 'as' => 'orders'], function() {

        Route::get('/', 'OrderController@index')->name('.index');

        Route::match(['get', 'post'], '/view/{id}', 'OrderController@view')->name('.view');

        Route::delete('delete/{id}}', 'OrderController@delete')->where(['id'=>'[0-9]+'])->name('.delete');

        Route::post('ajax_print_invoice', 'OrderController@printInvoice')->name('.ajax_print_invoice');
       
    });

    
    // Review
    Route::group(['prefix' => 'reviews', 'as' => 'reviews'], function() {

        Route::get('/', 'ReviewController@index')->name('.index');
        Route::get('/{id}', 'ReviewController@view')->name('.view');
        Route::post('ajax_view', 'ReviewController@ajaxView')->name('.ajax_view');
        Route::post('change_status', 'ReviewController@changeStatus')->name('.change_status');

        Route::post('delete/{id}', 'ReviewController@delete')->name('.delete');
       
    });

    
    // Cart
    Route::group(['prefix' => 'cart', 'as' => 'cart'], function() {

        Route::get('/', 'CartController@index')->name('.index');
        Route::post('ajax_get_items', 'CartController@ajaxGetItems')->name('.ajax_get_items');
       
    });

    // Colors
    Route::group(['prefix' => 'loyaltypoints', 'as' => 'loyaltypoints'], function() {

        Route::get('/', 'LoyaltyPointsMasterController@index')->name('.index');
        Route::match(['get', 'post'], 'add/{id?}', 'LoyaltyPointsMasterController@add')->name('.add');
        Route::delete('delete/{id}}', 'LoyaltyPointsMasterController@delete')->where(['id'=>'[0-9]+'])->name('.delete');
        
    });
/* End - Admin group*/
});

if(isset($segments_arr[0])){
    Route::get('/{slug}', 'HomeController@cmsPage');
}


 
