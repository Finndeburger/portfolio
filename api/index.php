<?php

header('Content-Type: text/plain');

// Test 1: PHP is running
echo "Step 1: PHP running\n";
echo "DIR: " . __DIR__ . "\n";
echo "public/index.php exists: " . (file_exists(__DIR__ . '/../public/index.php') ? 'yes' : 'no') . "\n";
echo "vendor/autoload.php exists: " . (file_exists(__DIR__ . '/../vendor/autoload.php') ? 'yes' : 'no') . "\n";
echo "bootstrap/app.php exists: " . (file_exists(__DIR__ . '/../bootstrap/app.php') ? 'yes' : 'no') . "\n";

