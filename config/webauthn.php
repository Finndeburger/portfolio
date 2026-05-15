<?php

$defaultHost = parse_url((string) env('APP_URL', 'http://localhost'), PHP_URL_HOST) ?: 'localhost';
$defaultOrigin = rtrim((string) env('APP_URL', 'http://localhost'), '/');

$allowedOrigins = array_filter(array_map(
    static fn (string $origin): string => trim($origin),
    explode(',', (string) env('WEBAUTHN_ALLOWED_ORIGINS', $defaultOrigin))
));

return [
    'rp_name' => env('WEBAUTHN_RP_NAME', 'LookAtMe'),
    'rp_id' => env('WEBAUTHN_RP_ID', $defaultHost),
    'allowed_origins' => array_values($allowedOrigins),
    'timeout' => (int) env('WEBAUTHN_TIMEOUT', 60000),
];
