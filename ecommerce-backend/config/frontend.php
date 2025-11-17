<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Frontend URLs
    |--------------------------------------------------------------------------
    |
    | These URLs define the frontend application URLs for different user roles.
    | Used for password reset links, email verification, etc.
    |
    */

    'buyer_url' => env('BUYER_FRONTEND_URL', 'http://localhost:8081'),
    'seller_url' => env('SELLER_FRONTEND_URL', 'http://localhost:5173'),

];