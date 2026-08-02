<?php

declare(strict_types=1);

use DI\ContainerBuilder;
use Slim\App;
use Sqids\Sqids;
use TomWilford\SlimSqids\GlobalSqidConfiguration;

require_once dirname(__DIR__, 2) . '/vendor/autoload.php';

$container = new ContainerBuilder()
    ->addDefinitions(dirname(__DIR__) . '/app/container.php')
    ->build();

GlobalSqidConfiguration::set($container->get(Sqids::class));

return $container->get(App::class);
