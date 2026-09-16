<?php

return [
    /*
    |--------------------------------------------------------------------------
    | ADN WEB -> ADN APP
    |--------------------------------------------------------------------------
    */

    'app_url' =>
        env(
            'ADN_APP_URL',
            'http://127.0.0.1:8000'
        ),

    'secret' =>
        env(
            'ADN_APP_INTEGRATION_SECRET'
        ),

    'endpoint' =>
        env(
            'ADN_APP_INTEGRATION_ENDPOINT',
            '/integrations/adn-web/leads'
        ),

    /*
    |--------------------------------------------------------------------------
    | HTTP
    |--------------------------------------------------------------------------
    */

    'timeout' =>
        (int) env(
            'ADN_APP_INTEGRATION_TIMEOUT',
            5
        ),

    'connect_timeout' =>
        (int) env(
            'ADN_APP_INTEGRATION_CONNECT_TIMEOUT',
            2
        ),

    /*
    |--------------------------------------------------------------------------
    | SEGURIDAD
    |--------------------------------------------------------------------------
    */

    'max_clock_skew' =>
        (int) env(
            'ADN_INTEGRATION_MAX_CLOCK_SKEW',
            300
        ),
];