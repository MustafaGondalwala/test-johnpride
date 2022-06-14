<?php

return [
    'admin_email' => env('ADMIN_EMAIL'),
    'order_prefix' => 'JP',

    //'unicommerce_api_mode' => 'DEMO',
    'unicommerce_api_mode' => 'LIVE',
    'unicommerce_api_url' => 'https://stgbloomexim.unicommerce.com/',
    'unicommerce_username' => 'sanyogita%40ehostinguk.com',
    'unicommerce_password' => 'jpnew2021',
    'unicommerce_facility' => 'Warehouse',

    /*'unicommerce_username' => 'slumberjill%40fwacziarg.com',
    'unicommerce_password' => 'Fwfpl12345',
    'unicommerce_facility' => 'fwfpl989898',*/

    'unicommerce_demo_api_url' => '​​​https://demostaging.unicommerce.com/',
    'unicommerce_demo_username' => 'anand%40ehostinguk.com',//anand@ehostinguk.com
    'unicommerce_demo_password' => 'indiaint%40123',//indiaint@123
    'unicommerce_demo_facility' => 'ABHI',

    'payumoney_key' => 'b8Trf7',
    'payumoney_salt' => '1zqySucK',

    // 'payumoney_key' => 'gtKFFx',
    // 'payumoney_salt' => 'eCwWELxi',

    'payumoney_base_url' => 'https://secure.payu.in/_payment',
    'payumoney_response_url' => 'http://jpnew.ehostinguk.com/order/payuresponse',
    // 'payumoney_success_url' => 'http://jpnew.ehostinguk.com/order/paysuccess',
    // 'payumoney_fail_url' => 'http://jpnew.ehostinguk.com/order/payfail',

     'payumoney_success_url' => 'http://jpnew.ehostinguk.com/order/payuresponse',
    'payumoney_fail_url' => 'http://jpnew.ehostinguk.com/order/payuresponse',

    'intagram_token' => 'IGQVJWc19kNHp0ZA0RvSmN3czVEZAjNCNWpQemYxbTVDSEZA4WTJkZA1A4UVpZAT0dlR2lIMWI2dDNDbWtZAUVMxWGZAMaEdiYUJJNTMxbkcxWENVdTFLcmVTQTNOQ0NjRGFlVDFNVUZAJZAzFB',
    'insta_user_id' => '17841402387258457',
    'image_path' => 'http://jpnewstatic.ehostinguk.com/',

    'order_status_arr' => [
        'pending' => 'Pending',
        'placed' => 'Placed',
        'confirmed' => 'Confirmed',
        'cancelled' => 'Cancelled',
        'shipped' => 'Shipped',
        'delivered' => 'Delivered',
        'failed' => 'Failed',
        //'success' => 'Success',
        'return' => 'Return',
        'partially_cancelled' => 'Partially cancelled',

    ],
    'payment_status_arr' => [
        'not_paid' => 'Not Paid',
        'paid'     => 'Paid',
        'refunded' => 'Refunded',
        'free'     => 'Free',
    ],
    'payment_method' => [
        'card' => 'Card',
        'paypal' => 'PayPal',
        'cash' => 'Cash',
    ],

    'img_extension' => ['jpg', 'jpeg', 'gif', 'png', 'JPG', 'JPEG', 'GIF', 'PNG'],

    'compare_scope' => [
        '=' => '=',
        '>' => '>',
        '<' => '<',
        '>=' => '>=',
        '<=' => '<='
    ],

    'currency_arr' => [
        'USD' => 'USD',
        'EUR' => 'EUR',
        'INR' => 'INR',
        'AUD' => 'AUD',
        'GBP' => 'GBP'
    ],

    'currency_symbol_arr' => [
        'USD' => "&#36;",
        'EUR' => "&#128;",
        'INR' => "&#x20B9;",
        'AUD' => "A&#36;",
        'GBP' => "&#163;"
    ],

    'product_stamps_arr' => [
        'new' => "New",
        'trending' => "Trending",
        'popular' => "Popular"
    ],

    'device_types_arr' => [
        'desktop' => "Desktop",
        'mobile' => "Mobile"
    ],

    'products_sort_by_arr' => [
        'price_high_low' => 'Price: High to Low',
        'price_low_high' => 'Price: Low to High',
        'new' => 'What\'s new',
        'popularity' => 'Popularity',
        'discount' => 'Discount',
    ],

    'gst_arr' => [
        '5' => '5%',
        '12' => '12%',
        '18' => '18%',
    ],

    'reason_order_return_arr' => [
        'ORDERED_WRONG_ITEM' => 'I accidentally ordered the wrong item',
        'FOUND_BETTER_PRICE' => 'I found better prices elsewhere',
        'NO_REASON_GIVEN' => "No reason—I just don't want the product any more",
        'QUALITY_UNACCEPTABLE' => "Product performance/quality is not up to my expectations",
        'MISSED_ESTIMATED_DELIVERY' => "Item took too long to arrive; I don't want it any more",
        'MISSING_PARTS' => "Delivery was missing items or accessories",
        'DAMAGED_BY_CARRIER' => "Product was damaged/defective on arrival",
        'RECEIVED_WRONG_ITEM' => "Received the wrong item",
        'DEFECTIVE' => "Item is defective",
        'EXTRA_ITEM' => "Extra item included in delivery",
        'APPAREL_TOO_SMALL' => "Apparel: Product was too small",
        'APPAREL_TOO_LARGE' => "Apparel: Product was too large",
        'APPAREL_STYLE' => "Apparel: Did not like style of garment",
        'MISORDERED' => "Ordered wrong style/size/colour",
        'remark' => "Not as described on website",
        //'remark' => 'Other remark',
    ],


    'reason_order_cancel_arr' => [
        'ORDERED_WRONG_ITEM' => 'I accidentally ordered the wrong item',
        'FOUND_BETTER_PRICE' => 'I found better prices elsewhere',
        'NO_REASON_GIVEN' => "No reason—I just don't want the product any more",
        'MISSED_ESTIMATED_DELIVERY' => "Item took too long to arrive; I don't want it any more",
        'MISORDERED' => "Ordered wrong style/size/colour",
        'remark' => 'Other remark',
    ],

    'facilities_arr' => [
        '1' => 'Free shipping',
        //'2' => 'Extra Discount',        
        '3' => 'Instant Refund after Product Pickup',
        '4' => 'Instant Refund',
    ],


    
];