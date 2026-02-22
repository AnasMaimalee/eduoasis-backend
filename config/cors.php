<?php

return [
    'paths' => [
        'api/*',
        'broadcasting/auth',
    ],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'http://localhost:5173',
        'https://www.codebridge.com.ng',
        'https://maimalee.netlify.app',
        'http://localhost:3000',
        'http://127.0.0.1:3000',
        'https://money-frontend-swart.vercel.app',
        'https://eduoasis.vercel.app',
        'https://www.eduoasis.com.ng',
        'https://eduoasis.com.ng',
        'https://prayer-referrals-collecting-ago.trycloudflare.com'
    ],

    'allowed_origins_patterns' => [
        '/^https:\/\/.*\.trycloudflare\.com$/',
    ],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,
];
