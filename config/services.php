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

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],
    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),        // Your Google Client ID
        'client_secret' => env('GOOGLE_CLIENT_SECRET'), // Your Google Client Secret
        'redirect' => env('GOOGLE_REDIRECT_URL'),      // Your Google Redirect URL
    ],
    'firebase' => [
        // 'apiKey' => "AIzaSyD8Y_XS1rIobvwT1yimbB5RmVtaTKNchjQ",
        // 'authDomain' => "astha-jyotish-e79cc.firebaseapp.com",
        // 'databaseURL' => "https://astroguru-75d26-default-rtdb.firebaseio.com",
        // 'projectId' => "astha-jyotish-e79cc",
        // 'storageBucket' => "astha-jyotish-e79cc.appspot.com",
        // 'messagingSenderId' => "736238700309",
        // 'appId' => "1:736238700309:web:e534252db116a6b373cb1c",
        // 'measurementId' => "G-4MVQX2R6C0",
        
        'apiKey' => "AIzaSyAbVv-H2kbOH1REQ2ggNc7xxg0Bh9LfT28",
        'authDomain' => "realtionship-849b1.firebaseapp.com",
        'databaseURL' => "https://realtionship-849b1-default-rtdb.firebaseio.com",
        // https://realtionship-849b1-default-rtdb.firebaseio.com/
        'projectId' => "realtionship-849b1",
        'storageBucket' => "realtionship-849b1.firebasestorage.app",
        'messagingSenderId' => "884911350693",
        'appId' => "1:884911350693:web:86f89b3a009d9fe8823e4c",
        'measurementId' => "G-2QD0G6R41N",

        // apiKey: "AIzaSyAbVv-H2kbOH1REQ2ggNc7xxg0Bh9LfT28",
        // authDomain: "realtionship-849b1.firebaseapp.com",
        // projectId: "realtionship-849b1",
        // storageBucket: "realtionship-849b1.firebasestorage.app",
        // messagingSenderId: "884911350693",
        // appId: "1:884911350693:web:86f89b3a009d9fe8823e4c",
        // measurementId: "G-2QD0G6R41N"

    ],
];
