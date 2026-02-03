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

    /*
    |--------------------------------------------------------------------------
    | CORS Paths
    |--------------------------------------------------------------------------
    |
    | Paths that should have CORS headers applied. Include both API routes
    | and the Sanctum CSRF cookie endpoint for stateful authentication.
    |
    */
    'paths' => ['api/*', 'sanctum/csrf-cookie', 'login', 'logout'],

    /*
    |--------------------------------------------------------------------------
    | Allowed Methods
    |--------------------------------------------------------------------------
    |
    | HTTP methods that are allowed for CORS requests.
    |
    */
    'allowed_methods' => ['*'],

    /*
    |--------------------------------------------------------------------------
    | Allowed Origins
    |--------------------------------------------------------------------------
    |
    | Origins that are allowed to make CORS requests. When using credentials
    | (supports_credentials = true), you cannot use wildcard '*'.
    | Specify exact origins or use allowed_origins_patterns for flexibility.
    |
    */
    'allowed_origins' => array_filter(explode(',', env('CORS_ALLOWED_ORIGINS', 'http://localhost,http://localhost:3000,http://localhost:5173,http://127.0.0.1,http://127.0.0.1:8000,http://127.0.0.1:3000,http://127.0.0.1:5173'))),

    /*
    |--------------------------------------------------------------------------
    | Allowed Origins Patterns
    |--------------------------------------------------------------------------
    |
    | Patterns to match allowed origins using regex.
    | Example: 'https://*.example.com'
    |
    */
    'allowed_origins_patterns' => [],

    /*
    |--------------------------------------------------------------------------
    | Allowed Headers
    |--------------------------------------------------------------------------
    |
    | Headers that are allowed in CORS requests.
    |
    */
    'allowed_headers' => ['*'],

    /*
    |--------------------------------------------------------------------------
    | Exposed Headers
    |--------------------------------------------------------------------------
    |
    | Headers that should be exposed to the browser.
    |
    */
    'exposed_headers' => [],

    /*
    |--------------------------------------------------------------------------
    | Max Age
    |--------------------------------------------------------------------------
    |
    | The maximum time (in seconds) that the results of a preflight request
    | can be cached. Set to 0 to disable caching.
    |
    */
    'max_age' => 0,

    /*
    |--------------------------------------------------------------------------
    | Supports Credentials
    |--------------------------------------------------------------------------
    |
    | CRITICAL: Must be true for session-based authentication to work!
    | When true, the Access-Control-Allow-Credentials header is sent,
    | allowing cookies and authorization headers in cross-origin requests.
    |
    */
    'supports_credentials' => true,

];
