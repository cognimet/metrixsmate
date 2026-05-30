<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI', '/auth/google/callback'),
        'places_api_key' => env('GOOGLE_PLACES_API_KEY'),
    ],

    // Legacy UPI configuration has been disabled in favor of PayPal and Razorpay.
    // 'upi' => [
    //     'merchant_id' => env('UPI_MERCHANT_ID', 'metrixsmate@upi'),
    //     'merchant_name' => env('UPI_MERCHANT_NAME', 'MetrixsMate'),
    // ],

    'paypal' => [
        'client_id' => env('PAYPAL_CLIENT_ID'),
        'client_secret' => env('PAYPAL_CLIENT_SECRET'),
        'mode' => env('PAYPAL_MODE', 'sandbox'),
        'currency' => env('PAYPAL_CURRENCY', 'USD'),
        'webhook_id' => env('PAYPAL_WEBHOOK_ID'),
        'return_url' => env('PAYPAL_RETURN_URL', env('APP_URL') . '/payments'),
        'cancel_url' => env('PAYPAL_CANCEL_URL', env('APP_URL') . '/payments'),
    ],

    'razorpay' => [
        'key_id' => env('RAZORPAY_KEY_ID'),
        'key_secret' => env('RAZORPAY_KEY_SECRET'),
        'webhook_secret' => env('RAZORPAY_WEBHOOK_SECRET'),
    ],

    'groq' => [
        'api_key' => env('GROQ_API_KEY'),
    ],

    'gemini' => [
        'api_key' => env('GEMINI_API_KEY'),
        'model'   => env('GEMINI_MODEL', 'gemini-2.0-flash'),
        'base_url' => 'https://generativelanguage.googleapis.com/v1beta/models',
    ],

];
