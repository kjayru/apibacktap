<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => ['api/*'],

    'allowed_methods' => ['*'],

    // El sitio vive en FRONTEND_URL, pero durante el traspaso a producción conviven dos
    // direcciones —la definitiva y la de pruebas—, y con una sola permitida el navegador
    // bloquea todas las llamadas desde la otra: el catálogo se ve vacío y no hay error
    // visible, sólo avisos en la consola. CORS_EXTRA_ORIGINS admite las demás, separadas
    // por comas.
    'allowed_origins' => array_values(array_unique(array_filter(array_map(
        'trim',
        array_merge([(string) env('FRONTEND_URL')], explode(',', (string) env('CORS_EXTRA_ORIGINS', ''))),
    )))),

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    // Bearer token auth (Sanctum personal access tokens), no cookies involved.
    'supports_credentials' => false,

];
