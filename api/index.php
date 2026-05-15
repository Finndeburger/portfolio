<?php

ini_set('display_errors', '1');
error_reporting(E_ALL);

// Catch fatal errors that try-catch misses
register_shutdown_function(function () {
    $err = error_get_last();
    if ($err && in_array($err['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        echo "\n\nFATAL: [{$err['type']}] {$err['message']} in {$err['file']}:{$err['line']}";
    } elseif ($err === null) {
        echo "\n\nSHUTDOWN: no error recorded";
    }
});

echo "START\n";

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

echo "DIRS OK\n";

try {
    require __DIR__ . '/../vendor/autoload.php';
    echo "AUTOLOAD OK\n";
    $app = require __DIR__ . '/../bootstrap/app.php';
    echo "APP OK: " . get_class($app) . "\n";
} catch (\Throwable $e) {
    echo 'CAUGHT: ' . $e->getMessage() . "\n" . $e->getTraceAsString();
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

