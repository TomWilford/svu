<?php

declare(strict_types=1);

$env = $_ENV['APP_ENV'] ??= $_SERVER['APP_ENV'] ?? 'dev';

$settings = require dirname(__DIR__) . '/app/settings.default.php';

$configFiles = [
    dirname(__DIR__) . sprintf('/app/conf.d/settings.%s.php', $env),
    dirname(__DIR__, 2) . '/.env.php'
];

foreach ($configFiles as $config) {
    if (!file_exists($config)) {
        continue;
    }

    $override = require $config;

    if (is_callable($override)) {
        $settings = $override($settings);
    } elseif (is_array($override)) {
        $settings = array_replace_recursive($settings, $override);
    }
}

return $settings;
