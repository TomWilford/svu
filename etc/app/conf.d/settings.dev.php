<?php

declare(strict_types=1);

return function (array $settings): array {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');

    $settings['exception_middleware']['display_error_details'] = true;

    $settings['twig']['settings']['cache'] = false;
    $settings['twig']['settings']['debug'] = true;

    // password = password
    $settings['basic_auth']['users']['root'] = '$2y$10$D5/YikaVlW63fqufu0YtfewK6z7x0O3mZMWpY9P9Mo7NLCU40fSwW';

    return $settings;
};
