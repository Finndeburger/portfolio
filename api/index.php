<?php

ini_set('display_errors', '1');
error_reporting(E_ALL);

$_SERVER['HTTPS'] = 'on';
$_SERVER['SERVER_PORT'] = '443';

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

try {
    require __DIR__ . '/../public/index.php';
} catch (\Throwable $e) {
    http_response_code(500);
    echo '<pre>' . $e->getMessage() . "\n" . $e->getTraceAsString() . '</pre>';
}

