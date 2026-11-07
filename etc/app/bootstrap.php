<?php

declare(strict_types=1);

use DI\ContainerBuilder;
use Slim\App;

require_once dirname(__DIR__, 2) . '/vendor/autoload.php';

$container = new ContainerBuilder()
    ->addDefinitions(dirname(__DIR__) . '/app/container.php')
    ->build();

return $container->get(App::class);
