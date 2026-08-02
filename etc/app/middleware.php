<?php

declare(strict_types=1);

use Selective\BasePath\BasePathMiddleware;
use Slim\App;
use Slim\Views\TwigMiddleware;
use TomWilford\SlimSqids\SqidsMiddleware;
use Tuupola\Middleware\HttpBasicAuthentication;

return function (App $app) {
    $app->addBodyParsingMiddleware();
    $app->add(SqidsMiddleware::class);
    $app->addRoutingMiddleware();
    $app->add(BasePathMiddleware::class);
    $app->add(TwigMiddleware::class);
    $app->add(HttpBasicAuthentication::class);
};