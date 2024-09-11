<?php

return [
    'pagbank_smart_button' => [
        'code'             => 'pagbank_smart_button',
        'title'            => 'PagBank Smart Button',
        'description'      => 'PagBank',
        'client_id'        => 'sb',
        'class'            => 'Webkul\PagBank\Payment\SmartButton',
        'sandbox'          => true,
        'active'           => true,
        'sort'             => 0,
    ],

    'pagbank_standard' => [
        'code'             => 'pagbank_standard',
        'title'            => 'PagBank Standard',
        'description'      => 'PagBank Standard',
        'class'            => 'Webkul\PagBank\Payment\Standard',
        'sandbox'          => true,
        'active'           => true,
        'business_account' => 'test@webkul.com',
        'sort'             => 3,
    ],
];
