<?php

ini_set('display_errors', '1');
error_reporting(E_ALL);

$_SERVER['HTTPS'] = 'on';
$_SERVER['SERVER_PORT'] = '443';

foreach ([
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/logs',
    '/tmp/storage/app',
] as $dir) {
    if (!is_dir($dir)) mkdir($dir, 0755, true);
}

try {
    require __DIR__ . '/../vendor/autoload.php';
    echo "autoload OK\n";
    $app = require __DIR__ . '/../bootstrap/app.php';
    echo "app boot OK: " . get_class($app) . "\n";
} catch (\Throwable $e) {
    echo 'ERROR: ' . $e->getMessage() . "\n" . $e->getTraceAsString();
}


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

