<?php

return [
    'hospital_db' => [
        'enabled' => filter_var(env('HOSPITAL_DB_ENABLED', false), FILTER_VALIDATE_BOOLEAN),
        'connection' => env('HOSPITAL_DB_CONNECTION', 'hospital_sims'),
        'legacy_connection' => 'simrs',
        'connect_timeout' => (int) env('HOSPITAL_DB_CONNECT_TIMEOUT', env('SIMRS_DB_CONNECT_TIMEOUT', 3)),
        'availability_cache_seconds' => (int) env('HOSPITAL_DB_AVAILABILITY_CACHE_SECONDS', 30),
    ],
];
