<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Resend, Postmark, AWS, and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
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


        // SMS gateway (TextBee: an Android phone with a SIM). Leave the key empty to only queue alerts.
    'textbee' => [
        'url'    => env('TEXTBEE_URL', 'https://api.textbee.dev'),
        'key'    => env('TEXTBEE_API_KEY'),
        'device' => env('TEXTBEE_DEVICE_ID'),
        'sim'    => env('TEXTBEE_SIM_SLOT'), // 0 = SIM 1, 1 = SIM 2, empty = let TextBee choose
    ],



];
