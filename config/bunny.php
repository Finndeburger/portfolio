<?php

return [
    // Public base URL of your pull zone, used when rendering CDN asset URLs.
    'cdn_url' => rtrim((string) env('BUNNY_CDN_URL', ''), '/'),

    // High-level identifiers for future API use.
    'pull_zone' => env('BUNNY_PULL_ZONE'),
    'storage_zone' => env('BUNNY_STORAGE_ZONE'),

    // Storage endpoint format example: https://storage.bunnycdn.com
    'storage_endpoint' => env('BUNNY_STORAGE_ENDPOINT'),
];
