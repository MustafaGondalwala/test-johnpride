<?php

return [
    'admin_email' => env('ADMIN_EMAIL'),
    'settings' => [
        'pay_later' => [
            'name' => 'Pay Later'
        ],
        'cash_on_delivery' => [
            'name' => 'Cash on Delivery'
        ]
    ],
    'timezone' => 'America/Los_Angeles',
    'tax' => 8,
    'states' => [
        'AL' => 'Alabama',
        'AK' => 'Alaska',
        'AZ' => 'Arizona',
        'AR' => 'Arkansas',
        'CA' => 'California',
        'CO' => 'Colorado',
        'CT' => 'Connecticut',
        'DE' => 'Delaware',
        'FL' => 'Florida',
        'GA' => 'Georgia',
        'HI' => 'Hawaii',
        'ID' => 'Idaho',
        'IL' => 'Illinois',
        'IN' => 'Indiana',
        'IA' => 'Iowa',
        'KS' => 'Kansas',
        'KY' => 'Kentucky',
        'LA' => 'Louisiana',
        'ME' => 'Maine',
        'MD' => 'Maryland',
        'MA' => 'Massachusetts',
        'MI' => 'Michigan',
        'MN' => 'Minnesota',
        'MS' => 'Mississippi',
        'MO' => 'Missouri',
        'MT' => 'Montana',
        'NE' => 'Nebraska',
        'NV' => 'Nevada',
        'NH' => 'New Hampshire',
        'NJ' => 'New Jersey',
        'NM' => 'New Mexico',
        'NY' => 'New York',
        'NC' => 'North Carolina',
        'ND' => 'North Dakota',
        'OH' => 'Ohio',
        'OK' => 'Oklahoma',
        'OR' => 'Oregon',
        'PA' => 'Pennsylvania',
        'RI' => 'Rhode Island',
        'SC' => 'South Carolina',
        'SD' => 'South Dakota',
        'TN' => 'Tennessee',
        'TX' => 'Texas',
        'UT' => 'Utah',
        'VT' => 'Vermont',
        'VA' => 'Virginia',
        'WA' => 'Washington',
        'WV' => 'West Virginia',
        'WI' => 'Wisconsin',
        'WY' => 'Wyoming',
    ],
    'order_status' => [
        'processing'       => 'Processing',
        'shipped'          => 'Shipped',
        'delivered'        => 'Delivered',
        'cancel_requested' => 'Cancel Requested',
        'canceled'         => 'Canceled',
        'refunded'         => 'Refunded'
    ],
    'payment_status' => [
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
    'checkout' => [
        'shipping' => [
            'carriers' => [
                'usps' => [
                    'name' => 'USPS',
                    'plans' => [
                        'standard' => [
                            'name' => 'Standard',
                            'plan' => 'USPS Priority Mail 1-2 Day',
                            'fee' => 7.15,
                        ],
                        'express'  => [
                            'name' => 'Express',
                            'plan'  => 'USPS Priority Mail Express 1-Day',
                            'fee' => 23.75
                        ]
                    ]
                ],
                'ups' => [
                    'name' => 'UPS',
                    'plans' => [
                        'standard' => [
                            'name' => 'Standard',
                            'plan' => 'UPS Ground',
                            'fee' => 9.08
                        ],
                        'express'  => [
                            'name' => 'Express',
                            'plan'  => 'UPS Next Day Air',
                            'fee' => 34.38
                        ]
                    ]
                ]
            ],
            'default' => ['usps', 'standard']
        ],
    ],
    'emails' => [
        'templates' => [
            'shipping_confirmation' => '',
            'order_confirmation'    => '',
            'new_order'             => ''
        ]
    ],

    'category_types' => [
        'fabric' => 'Fabric',
        'design' => 'Design'
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
        'selling_fast' => "Selling fast",
        'sold_out' => "Sold out",
        'free_shipping' => "Free-shipping"
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
    ]


    
];