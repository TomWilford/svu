<?php

declare(strict_types=1);

use Sqids\Sqids;

return function (array $settings): array {
    $settings['exception_middleware']['display_error_details'] = true;

    $settings['database']['dsn'] = 'pdo-sqlite:///:memory:';

    $settings['twig']['settings']['cache'] = false;
    $settings['twig']['settings']['debug'] = true;

    $settings['basic_auth']['secure'] = false;
    $settings['basic_auth']['users']['test'] = 'test';

    $settings['sqids']['alphabet'] = Sqids::DEFAULT_ALPHABET;
    $settings['sqids']['minLength'] = Sqids::DEFAULT_MIN_LENGTH;
    $settings['sqids']['blockList'] = Sqids::DEFAULT_BLOCKLIST;

    return $settings;
};
