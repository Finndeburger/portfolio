<?php

return [

    /*
    |--------------------------------------------------------------------------
    | View Storage Paths
    |--------------------------------------------------------------------------
    */

    'paths' => [
        resource_path('views'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Compiled View Path
    |--------------------------------------------------------------------------
    |
    | On Vercel (serverless), the app filesystem is read-only.
    | Set VIEW_COMPILED_PATH=/tmp/storage/framework/views in env vars.
    |
    */

    'compiled' => env('VIEW_COMPILED_PATH', storage_path('framework/views')),

];
