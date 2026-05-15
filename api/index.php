<?php

ini_set('display_errors', '1');
error_reporting(E_ALL);

$_SERVER['HTTPS'] = 'on';
$_SERVER['SERVER_PORT'] = '443';

// Diagnose: show critical env vars before Laravel boots
echo '<pre>';
echo 'APP_KEY set: ' . (empty(getenv('APP_KEY')) ? 'NO - THIS IS THE PROBLEM' : 'yes') . "\n";
echo 'APP_ENV: ' . getenv('APP_ENV') . "\n";
echo 'VIEW_COMPILED_PATH: ' . getenv('VIEW_COMPILED_PATH') . "\n";
echo '</pre>';
die();
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

