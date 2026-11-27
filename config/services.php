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
    'zkbio' => [
        'url'      => env('ZKBIO_URL', 'http://biotime.local'),
        'username' => env('ZKBIO_USERNAME', 'admin'),
        'password' => env('ZKBIO_PASSWORD', 'password'),
        'department' => env('ZKBIO_DEPARTMENT_ID'),
        'area'       => env('ZKBIO_AREA_ID'),
        'company'    => env('ZKBIO_COMPANY_ID'),
    ],

    'remote' => [
    'secret' => env('REMOTE_API_SECRET'),
    'base'   => env('REMOTE_API_URL'),
    ],

    
];
