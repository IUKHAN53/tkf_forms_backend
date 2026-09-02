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

    'carto' => [
        /*
         * CARTO basemaps now require an API key on their raster (PNG) tiles;
         * without one the tiles render with an "API key required" watermark.
         * The key travels in the tile URL and is therefore visible to anyone
         * loading a map — it is a public, per-domain key, not a secret. It
         * lives here so it can differ per environment and be rotated in one
         * place rather than in each view that builds a map.
         */
        'basemap_key' => env('CARTO_BASEMAP_KEY'),

        /*
         * Single source of truth for the tile URL. Every Leaflet map reads
         * this, so the style and the key are set once. Falls back to the
         * unkeyed URL (watermarked, but still functional) when no key is set.
         */
        'basemap_url' => 'https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png'
            .(env('CARTO_BASEMAP_KEY') ? '?key='.env('CARTO_BASEMAP_KEY') : ''),
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
