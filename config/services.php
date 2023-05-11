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
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'firebase' => [
        'apiKey' => 'AIzaSyBC92PdmZt6iytdT5-dRIEsNJ6x5j-kO1I',
        'authDomain' => 'community-ae306.firebaseapp.com',
        'projectId' => 'community-ae306',
        'storageBucket' => 'community-ae306.appspot.com',
        'messagingSenderId' => '799061137300',
        'appId' => '1:799061137300:web:0db6e4cb9011c1f4d92181',
        'measurementId' => 'G-0R389ML4QG',
        'database_url' => '',
        'storage_bucket' => '',
    ],

];
