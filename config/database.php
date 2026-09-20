<?php

$hospitalDbEnabled = filter_var(env('HOSPITAL_DB_ENABLED', false), FILTER_VALIDATE_BOOLEAN);
$simrsConnectTimeout = (int) env('HOSPITAL_DB_CONNECT_TIMEOUT', env('SIMRS_DB_CONNECT_TIMEOUT', 3));

$simrsPdoOptions = [];
if (extension_loaded('pdo_mysql')) {
    if (defined('Pdo\Mysql::ATTR_CONNECT_TIMEOUT')) {
        $simrsPdoOptions[\Pdo\Mysql::ATTR_CONNECT_TIMEOUT] = $simrsConnectTimeout;
    } elseif (defined('PDO::ATTR_TIMEOUT')) {
        $simrsPdoOptions[PDO::ATTR_TIMEOUT] = $simrsConnectTimeout;
    }

    $simrsSslCa = env('SIMRS_MYSQL_ATTR_SSL_CA');
    if ($simrsSslCa) {
        $simrsPdoOptions[PDO::MYSQL_ATTR_SSL_CA] = $simrsSslCa;
    }
}

if ($hospitalDbEnabled) {
    $simrsHost = env('HOSPITAL_DB_HOST', '127.0.0.1');
    $simrsPort = env('HOSPITAL_DB_PORT', '3306');
    $simrsDatabase = env('HOSPITAL_DB_DATABASE', 'simsvbaru');
    $simrsUsername = env('HOSPITAL_DB_USERNAME', '');
    $simrsPassword = env('HOSPITAL_DB_PASSWORD', '');
    $simrsCharset = env('HOSPITAL_DB_CHARSET', 'utf8');
    $simrsCollation = env('HOSPITAL_DB_COLLATION', 'utf8_general_ci');
    $simrsStrict = false;
} else {
    $simrsHost = env('SIMRS_DB_HOST', '127.0.0.1');
    $simrsPort = env('SIMRS_DB_PORT', '3306');
    $simrsDatabase = env('SIMRS_DB_DATABASE', 'simsvbaru');
    $simrsUsername = env('SIMRS_DB_USERNAME', 'root');
    $simrsPassword = env('SIMRS_DB_PASSWORD', '');
    $simrsCharset = 'utf8mb4';
    $simrsCollation = 'utf8mb4_unicode_ci';
    $simrsStrict = true;
}

$simrsBaseConnection = [
    'driver' => 'mysql',
    'url' => env('SIMRS_DATABASE_URL'),
    'host' => $simrsHost,
    'port' => $simrsPort,
    'database' => $simrsDatabase,
    'username' => $simrsUsername,
    'password' => $simrsPassword,
    'unix_socket' => env('SIMRS_DB_SOCKET', ''),
    'charset' => $simrsCharset,
    'collation' => $simrsCollation,
    'prefix' => '',
    'prefix_indexes' => true,
    'strict' => $simrsStrict,
    'engine' => null,
    'options' => $simrsPdoOptions,
];

$simrsConnections = [
    'simrs' => $simrsBaseConnection,
    'hospital_sims' => $simrsBaseConnection,
];

return [
    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    */
    'default' => env('DB_CONNECTION', 'mysql'),

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    */
    'connections' => [
        'sqlite' => [
            'driver' => 'sqlite',
            'url' => env('DATABASE_URL'),
            'database' => env('DB_DATABASE', database_path('database.sqlite')),
            'prefix' => '',
            'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
        ],

        'mysql' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        'pgsql' => [
            'driver' => 'pgsql',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '5432'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
            'search_path' => 'public',
            'sslmode' => 'prefer',
        ],

        'sqlsrv' => [
            'driver' => 'sqlsrv',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST', 'localhost'),
            'port' => env('DB_PORT', '1433'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
        ],

        'simrs' => $simrsConnections['simrs'],
        'hospital_sims' => $simrsConnections['hospital_sims'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    */
    'migrations' => 'migrations',

    /*
    |--------------------------------------------------------------------------
    | Redis Databases
    |--------------------------------------------------------------------------
    */
    'redis' => [
        'client' => env('REDIS_CLIENT', 'phpredis'),
        'options' => [
            'cluster' => env('REDIS_CLUSTER', 'redis'),
            'prefix' => env('REDIS_PREFIX', ''),
        ],
        'default' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_DB', '0'),
        ],
        'cache' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_CACHE_DB', '1'),
        ],
    ],
];
