<?php
return [
    'paths' => ['api/*'], // Include sanctum/csrf-cookie for authentication
    'allowed_methods' => ['*'],
    'allowed_origins' => ['http://localhost:3000'],
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true, // Required for Sanctum or session-based auth
];