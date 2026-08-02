<?php

declare(strict_types=1);

use Sqids\Sqids;

error_reporting(0);
ini_set('display_errors', '0');
ini_set('display_startup_errors', '0');
date_default_timezone_set('Europe/London');
setlocale(LC_ALL, 'en_GB.UTF-8', 'en_GB');

$settings = [];

$settings['root_dir'] = dirname(__DIR__, 2);

$settings['exception_middleware'] = [
    'display_error_details' => false,
];

$settings['database'] = [
    'dsn' => 'pdo-sqlite:///' . dirname(__DIR__, 2) . '/var/lib/database.sqlite',
];

$settings['doctrine']['migrations'] = [
    'table_storage' => [
        'table_name' => 'doctrine_migration_versions',
        'version_column_name' => 'version',
        'version_column_length' => 191,
        'executed_at_column_name' => 'executed_at',
        'execution_time_column_name' => 'execution_time',
    ],
    'migrations_paths' => [
        'App\Database\Migrations' => dirname(__DIR__, 2) . '/src/Database/Migrations',
    ],
    'all_or_nothing' => true,
    'transactional' => true,
    'check_database_platform' => true,
    'organize_migrations' => 'none',
    'connection' => null,
    'em' => null,
];

$settings['twig']['template_path'] = [
    dirname(__DIR__, 2) . '/usr/share/views'
];
$settings['twig']['settings'] = [
    'cache' => dirname(__DIR__, 2) . '/var/cache/twig',
    'debug' => false,
];

$settings['basic_auth'] = [
    'path' => ['/api', '/admin'],
    'realm' => 'Protected',
    'secure' => true,
    'relaxed' => ['localhost'],
];

$settings['sqids'] = [
    'alphabet' => Sqids::DEFAULT_ALPHABET,
    'minLength' => Sqids::DEFAULT_MIN_LENGTH,
    'blockList' => Sqids::DEFAULT_BLOCKLIST,
];

return $settings;
