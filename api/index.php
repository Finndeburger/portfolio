<?php

// Force PHP to recognise this as an HTTPS request
// (Vercel terminates TLS at the edge and proxies as HTTP internally)
$_SERVER['HTTPS'] = 'on';
$_SERVER['SERVER_PORT'] = '443';

// Capture PHP errors to output in debug mode
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// Create required temp directories for serverless environment
$dirs = [
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/logs',
    '/tmp/storage/app',
];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

require __DIR__ . '/../public/index.php';
