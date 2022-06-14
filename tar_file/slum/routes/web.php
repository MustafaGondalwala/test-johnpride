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



$segments_arr = request()->segments();

//dd($routeCollection);

$slug_arr = ['about', 'returns', 'faq', 'terms', 'privacy'];

if(isset($segments_arr[0]) && in_array($segments_arr[0], $slug_arr)){
    Route::get('/{slug}', 'HomeController@cmsPage');
}


Route::get('/', 'HomeController@index');
Route::get('/contact', 'HomeController@contact')->name('contact');
Route::match(['get', 'post'], '/logout', 'HomeController@logout')->name('logout');

Route::redirect('account/', 'login', 301);
Route::redirect('login', 'account/login', 301);

Route::group(['prefix' => 'account', 'as' => 'account'], function(){

    Route::match(['get', 'post'], 'login', 'AccountController@login');
    Route::match(['get', 'post'], 'register', 'AccountController@register');
    Route::match(['get', 'post'], 'verify', 'AccountController@verify');
    Route::match(['get', 'post'], 'forgot', 'AccountController@forgot');
    Route::match(['get', 'post'], 'reset', 'AccountController@reset');
});

Route::group(['prefix' => 'users', 'as' => 'users', 'middleware' => ['auth']], function(){

    Route::match(['get', 'post'], '/', 'UserController@profile');

    Route::match(['get', 'post'], 'profile', 'UserController@profile');
    Route::match(['get', 'post'], 'update', 'UserController@update');

    Route::get('orders', 'UserController@orders');
    Route::get('details', 'UserController@details');
    Route::get('wallet', 'UserController@wallet');
    Route::get('wishlist', 'UserController@wishlist');

    Route::post('get_address_form', 'UserController@getAddressForm');
    Route::post('save_address', 'UserController@saveAddress');
    
    Route::post('add_to_wishlist', 'UserController@addToWishlist');
    Route::post('delete_from_wishlist', 'UserController@deleteFromWishlist');
});


Route::group(['prefix' => 'common', 'as' => 'common'], function(){
    
    Route::post('ajax_load_cities', 'CommonController@ajax_load_cities');
    Route::post('ajax_regenerate_captcha', 'CommonController@ajax_regenerate_captcha');
    Route::match(['get', 'post'],'/ajax_load_product', 'CommonController@ajax_load_product');

    Route::post('ajax_set_currency', 'CommonController@ajax_set_currency');
});

/*Route::group(['prefix' => 'login', 'as' => 'login'], function() {
    Route::match(['get', 'post'], '/', 'LoginController@index');
    Route::match(['get', 'post'], 'forgot_password', 'LoginController@forgot_password');
    Route::match(['get', 'post'], 'reset_password', 'LoginController@reset_password');
});*/



// Product

Route::group(['prefix' => 'products', 'as' => 'products'], function() {
    Route::get('/', 'ProductController@index')->name('.list');
    Route::get('/details/{slug}', 'ProductController@details')->name('.details');

    Route::post('/ajax_get_list_by_search', 'ProductController@ajaxGetListBySearch')->name('.ajax_get_list_by_search');

    Route::post('/ajax_check_pincode', 'ProductController@ajaxCheckPincode')->name('.ajax_check_pincode');
});

Route::group(['prefix' => 'blogs', 'as' => 'blogs.'], function() {
    Route::get('/', 'BlogController@index');
    Route::get('/details/{slug}', 'BlogController@details')->name('details');
});



// Cart

Route::group(['prefix' => 'cart', 'as' => 'cart'], function() {

    Route::get('/', 'CartController@index');
    Route::get('/empty', 'CartController@empty');
    Route::match(['get', 'post'], 'address/{id?}', 'CartController@address');
    Route::get('checkout', 'CartController@checkout');
    
    Route::post('add', 'CartController@add');
    Route::post('delete', 'CartController@delete');

    Route::post('add_to_cart', 'CartController@add_to_cart');
    Route::post('swatchbooks_addtocart', 'CartController@swatchbooks_addtocart');
    Route::post('remove/{id?}', 'CartController@remove');



    Route::post('apply_coupon', 'CartController@apply_coupon');
    Route::post('remove_coupon', 'CartController@remove_coupon');

    Route::post('use_wallet_amount', 'CartController@use_wallet_amount');


    Route::post('get_product_price', 'CartController@get_product_price');
});



// Order

Route::group(['prefix' => 'order', 'as' => 'order'], function() {
    Route::get('/', 'OrderController@index');
    Route::get('/order/confirmation', 'OrderController@confirmation');
    Route::get('/order/success', 'OrderController@success');
    Route::get('/order/failed', 'OrderController@failed');
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


    // Customers    
    Route::group(['prefix' => 'customers', 'as' => 'customers'], function() {

        Route::get('/', 'CustomerController@index')->name('.index');

        Route::match(['get', 'post'], 'add/', 'CustomerController@add')->name('.add');

        Route::match(['get', 'post'], 'edit/{customer_id}', 'CustomerController@add')->where(['customer_id'=>'[0-9]+'])->name('.edit');

        Route::post('search/', 'CustomerController@search')->name('.search')->middleware('permission:customers.search');

        Route::match(['get', 'post'], 'wallet/{user_id}', 'CustomerController@wallet')->where(['user_id' => '[0-9]+',])->name('.wallet');

    });

    // Categories
    Route::group(['prefix' => 'categories', 'as' => 'categories'], function() {

        Route::get('/', 'CategoryController@index')->name('.index');

        Route::match(['get', 'post'], 'add/', 'CategoryController@add')->name('.add');

        Route::match(['get', 'post'], 'edit/{id}', 'CategoryController@add')->where(['id'=>'[0-9]+'])->name('.edit');
        
        Route::delete('delete/{id}', 'CategoryController@delete')->name('.delete');

    });

    // Designers    
    Route::group(['prefix' => 'designers', 'as' => 'designers'], function() {

        Route::get('/', 'DesignerController@index')->name('.index');

        Route::match(['get', 'post'], 'add/', 'DesignerController@add')->name('.add');

        Route::match(['get', 'post'], 'edit/{designer_id}', 'DesignerController@add')->where(['customer_id'=>'[0-9]+'])->name('.edit');
        
        Route::match(['get', 'post'], 'designs/{designer_id}', 'DesignerController@designs')->where(['customer_id'=>'[0-9]+'])->name('.designs');

        Route::post('search/', 'DesignerController@search')->name('.search')->middleware('permission:customers.search');

        Route::match(['get', 'post'], 'view_design/{designer_id}', 'DesignerController@view_design')->name('.view_design');
       
        Route::match(['get', 'post'], 'edit_design/{id}', 'DesignerController@edit_design')->name('.edit_design');

    });



    // SwatchBook  
    Route::group(['prefix' => 'swatchbooks', 'as' => 'swatchbooks'], function() {

        Route::get('/', 'SwatchbooksController@index')->name('.index');

        Route::match(['get', 'post'], 'add', 'SwatchbooksController@add')->name('.add');
        Route::match(['get', 'post'], 'edit/{id}', 'SwatchbooksController@add')->name('.edit');
    }); 


    //settings 
    Route::group(['prefix' => 'settings', 'as' => 'settings'], function() {

        Route::get('/', 'SettingsController@index')->name('.index');
        Route::any('/{setting_id}', 'SettingsController@index')->name('.index');
     }); 



    Route::group(['prefix' => 'newslettersubscriber', 'as' => 'newslettersubscriber'], function() {

         Route::get('/', 'NewslettersubscriberController@index')->name('.index');
         Route::get('/delete/{id}', 'NewslettersubscriberController@delete')->name('.index');
       
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
    });  
   




      

    // Product
    Route::group(['prefix' => 'products', 'as' => 'products'], function() {

        Route::get('/', 'ProductController@index')->name('.index');
        
        Route::match(['get', 'post'], 'add', 'ProductController@add')->name('.add');
        Route::match(['get', 'post'], 'edit/{id}', 'ProductController@add')->name('.edit');

        Route::post('ajax_get_category_child', 'ProductController@ajaxGetCategoryChild')->name('.ajax_get_category_child');
        
        Route::post('ajax_get_category_attributes', 'ProductController@ajaxGetCategoryAttributes')->name('.ajax_get_category_attributes');

    });


    // Banners
    Route::group(['prefix' => 'banners', 'as' => 'banners'], function() {

        Route::get('/', 'BannersController@index')->name('.index');

        Route::match(['get', 'post'], 'add', 'BannersController@add')->name('.add');
        Route::match(['get', 'post'], 'edit/{banner_id}', 'BannersController@add')->name('.edit');

        Route::post('ajax_delete_image', 'BannersController@ajax_delete_image')->name('.ajax_delete_image');

        Route::post('delete/{banner_id}', 'BannersController@delete')->name('.delete');
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
        Route::match(['get', 'post'], '/{id?}', 'PincodeController@index')->name('.index');
        Route::post('delete/{id}', 'PincodeController@delete')->name('.delete');
    }); 

    // Discounts
    Route::group(['prefix' => 'discounts', 'as' => 'discounts'], function() {

        Route::get('/', 'DiscountController@index')->name('.index');

        Route::match(['get', 'post'], 'add/{discount_id?}', 'DiscountController@add')->name('.add');

        Route::post('delete/{discount_id}}', 'DiscountController@delete')->where(['discount_id'=>'[0-9]+'])->name('.delete');
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
        Route::match(['get', 'post'],'edit/{cms_id?}', 'CmsController@edit')->where('cms_id', '[0-9]+')->name('.edit');
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

    // Testimonials
    Route::group(['prefix' => 'testimonials', 'as' => 'testimonials'], function() {

        Route::get('/', 'TestimonialController@index')->name('.index');
        Route::match(['get', 'post'], 'add', 'TestimonialController@add')->name('.add');
        Route::match(['get', 'post'], 'edit/{id}', 'TestimonialController@add')->name('.edit');

        Route::post('delete/{id}', 'TestimonialController@delete')->name('.delete');
    });

    // Usages
    Route::group(['prefix' => 'usages', 'as' => 'usages'], function() {

        Route::get('/', 'UsageController@index')->name('.index');

        Route::match(['get', 'post'], 'add/{id?}', 'UsageController@add')->name('.add');

        Route::delete('delete/{id}}', 'UsageController@delete')->where(['id'=>'[0-9]+'])->name('.delete');

        Route::post('ajax_delete_image', 'UsageController@ajax_delete_image')->name('.ajax_delete_image');
    });

    // Properties
    Route::group(['prefix' => 'properties', 'as' => 'properties'], function() {

        Route::get('/', 'PropertyController@index')->name('.index');

        Route::match(['get', 'post'], 'add/{id?}', 'PropertyController@add')->name('.add');

        Route::delete('delete/{id}}', 'PropertyController@delete')->where(['id'=>'[0-9]+'])->name('.delete');

        Route::post('ajax_delete_image', 'PropertyController@ajax_delete_image')->name('.ajax_delete_image');
    });

    // for orders

    Route::group(['prefix' => 'orders', 'as' => 'orders'], function() {

        Route::get('/', 'OrdersController@index')->name('.index');

        Route::match(['get', 'post'], '/view_order/{order_id}', 'OrdersController@view_order')->name('.view_order');

        Route::delete('delete/{id}}', 'OrdersController@delete')->where(['id'=>'[0-9]+'])->name('.delete');
       
    });




/* End - Admin group*/
});


 
