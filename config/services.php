<?php

// Resolved once so the basemap and its keyless fallback cannot drift apart.
$cartoBasemapKey = env('CARTO_BASEMAP_KEY');

$openStreetMapBasemap = [
    'url' => 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
    'subdomains' => 'abc',
    'attribution' => '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
    'max_zoom' => 19,
];

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

    'maps' => [
        /*
         * The basemap every Leaflet map in the admin panel draws.
         *
         * CARTO Positron looks better, but its raster tiles need a paid API
         * key. Without a valid one every tile comes back 403 and the map
         * renders empty — which is exactly how the maps broke in Oct 2026,
         * when the key we had stopped being accepted. (The unkeyed CARTO URL
         * is no help either: it now serves a blank "API KEY REQUIRED"
         * placeholder with no map on it.)
         *
         * So CARTO is opt-in: set CARTO_BASEMAP_KEY to a valid key and it is
         * used, otherwise the maps draw on OpenStreetMap, which needs no key
         * and is already what the Vaccination Records map uses.
         *
         * `subdomains` has to travel with the URL rather than being hardcoded
         * in the views: CARTO serves a-d, OpenStreetMap only a-c, and
         * d.tile.openstreetmap.org does not resolve at all — so sharing one
         * 'abcd' across both providers silently fails a quarter of the tiles.
         */
        'basemap' => $cartoBasemapKey ? [
            'url' => 'https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png?key='.$cartoBasemapKey,
            'subdomains' => 'abcd',
            'attribution' => '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
            'max_zoom' => 20,
        ] : $openStreetMapBasemap,

        /*
         * Keyless provider the page switches to if the primary stops serving
         * tiles. A lapsed key then degrades the map's styling instead of
         * blanking it — see clmAddBasemap() in layouts/partials/scripts.
         */
        'fallback' => $openStreetMapBasemap,
    ],

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

];
