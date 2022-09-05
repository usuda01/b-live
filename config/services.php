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

    'admin_user_id' => env('ADMIN_USER_ID'),

    'guest_user_id' => env('GUEST_USER_ID'),

    'event' => [
        'is_active' => env('IS_EVENT_ACTIVE'),
        'start_date' => env('EVENT_START_DATE'),
        'end_date' => env('EVENT_END_DATE'),
    ],

    'event2' => [
        'is_active' => env('IS_EVENT2_ACTIVE'),
        'start_date' => env('EVENT2_START_DATE'),
        'end_date' => env('EVENT2_END_DATE'),
    ],

    'event3' => [
        'is_active' => env('IS_EVENT3_ACTIVE'),
        'start_date' => env('EVENT3_START_DATE'),
        'end_date' => env('EVENT3_END_DATE'),
    ],

    'event4' => [
        'is_active' => env('IS_EVENT4_ACTIVE'),
        'start_date' => env('EVENT4_START_DATE'),
        'end_date' => env('EVENT4_END_DATE'),
    ],

    'event5' => [
        'is_active' => env('IS_EVENT5_ACTIVE'),
        'start_date' => env('EVENT5_START_DATE'),
        'end_date' => env('EVENT5_END_DATE'),
    ],

    'event6' => [
        'is_active' => env('IS_EVENT6_ACTIVE'),
        'start_date' => env('EVENT6_START_DATE'),
        'end_date' => env('EVENT6_END_DATE'),
    ],

    'event7' => [
        'is_active' => env('IS_EVENT7_ACTIVE'),
        'start_date' => env('EVENT7_START_DATE'),
        'end_date' => env('EVENT7_END_DATE'),
    ],

    'event8' => [
        'is_active' => env('IS_EVENT8_ACTIVE'),
        'start_date' => env('EVENT8_START_DATE'),
        'end_date' => env('EVENT8_END_DATE'),
    ],

    'facebook' => [
        'client_id' => env('FACEBOOK_APP_ID'),
        'client_secret' => env('FACEBOOK_APP_SECRET'),
        'redirect' =>  env('FACEBOOK_CALLBACK_URL'),
    ],

    'line' => [
        'client_id'=> env('LINE_CHANNEL_ID'),
        'client_secret' => env('LINE_CHANNEL_SECRET'),
        'redirect'=> env('LINE_REDIRECT'),
    ],

    'line_message' => [
        'access_token' => env('LINE_ACCESS_TOKEN'),
    ],

    'rtmp_url' => env('RTMP_URL'),

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'max_liver' => env('MAX_LIVER'),

    'max_movie_upload' => env('MAX_MOVIE_UPLOAD'),

    'max_movie_upload_seconds' => env('MAX_MOVIE_UPLOAD_SECONDS'),

    'max_movie_upload_size' => env('MAX_MOVIE_UPLOAD_SIZE'),

    'ncmb' => [
        'applicationkey' => env('NCMB_APPLICATIONKEY'),
        'clientkey' => env('NCMB_CLIENTKEY'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'twitter' => [
        'client_id' => env('TWITTER_API_KEY', ''),
        'client_secret' => env('TWITTER_API_SECRET_KEY', ''),
        'redirect' => env('TWITTER_CALLBACK_URL', ''),
    ],

    'sign_in_with_apple' => [
        'login' => env("SIGN_IN_WITH_APPLE_LOGIN"),
        'redirect' => env("SIGN_IN_WITH_APPLE_REDIRECT"),
        'client_id' => env("SIGN_IN_WITH_APPLE_CLIENT_ID"),
        'client_secret' => env("SIGN_IN_WITH_APPLE_CLIENT_SECRET"),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'stripe' => [
        'publishable_key' => env('STRIPE_PUBLISHABLE_KEY'),
        'secret_key' => env('STRIPE_SECRET_KEY'),
        'plan1_id' => env('STRIPE_PLAN1_ID'),
    ],

    'wowza' => [
        'api_key' => env('WOWZA_API_KEY'),
        'access_key' => env('WOWZA_ACCESS_KEY'),
        'host' => env('WOWZA_HOST'),
        'username' => env('WOWZA_USERNAME'),
        'password' => env('WOWZA_PASSWORD'),
    ],

];
