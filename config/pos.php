<?php

return [
    'currency_symbol' => '$',
    'tax_rate' => env('POS_TAX_RATE', 0.10), // 10%
    'service_charge' => env('POS_SERVICE_CHARGE', 0.05), // 5%
    'customer_session_lifetime' => env('POS_CUSTOMER_SESSION_LIFETIME', 60), // minutes
];
