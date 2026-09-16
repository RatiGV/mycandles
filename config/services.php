<?php

return [

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'payments' => [
        'tbc_base_url' => env('TBC_PAYMENT_BASE_URL', 'https://pay.flitt.com'),
        'tbc_create_endpoint' => env('TBC_CREATE_ENDPOINT', '/api/checkout/url'),
        'tbc_installment_base_url' => env('TBC_INSTALLMENT_BASE_URL', 'https://api.tbcbank.ge'),
        'tbc_installment_create_endpoint' => env('TBC_INSTALLMENT_CREATE_ENDPOINT', '/v1/online-installments/applications'),
        'tbc_oauth_token_url' => env('TBC_OAUTH_TOKEN_URL', 'https://api.tbcbank.ge/oauth/token'),
        'tbc_installment_redirect_base_url' => env('TBC_INSTALLMENT_REDIRECT_BASE_URL', 'https://tbcganvadeba.ge/auth'),
        'bog_base_url' => env('BOG_PAYMENT_BASE_URL', 'https://api.bog.ge'),
        'bog_create_endpoint' => env('BOG_CREATE_ENDPOINT', '/payments/v1/ecommerce/orders'),
        'bog_status_endpoint' => env('BOG_STATUS_ENDPOINT', '/payments/v1/receipt/{id}'),
        'flitt_base_url' => env('FLITT_PAYMENT_BASE_URL', 'https://pay.flitt.com'),
        'flitt_create_endpoint' => env('FLITT_CREATE_ENDPOINT', '/api/checkout/url'),
        'flitt_status_endpoint' => env('FLITT_STATUS_ENDPOINT', '/api/recurring/status/{id}'),
        'bog_oauth_token_url' => env('BOG_OAUTH_TOKEN_URL', 'https://oauth2.bog.ge/auth/realms/bog/protocol/openid-connect/token'),
        'bog_accept_language' => env('BOG_ACCEPT_LANGUAGE', 'en'),
        'ssl_verify' => env('PAYMENTS_SSL_VERIFY', true),
    ],

];
