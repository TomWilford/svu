<?php

declare(strict_types=1);

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Tools\DsnParser;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UploadedFileFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;
use Selective\BasePath\BasePathMiddleware;
use Slim\App;
use Slim\Interfaces\RouteParserInterface;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;
use Tuupola\Middleware\HttpBasicAuthentication;

return [
    'settings' => fn () => require dirname(__DIR__) . '/app/settings.php',

    App::class => function (ContainerInterface $container) {
        $app = Slim\Factory\AppFactory::createFromContainer($container);
        (require dirname(__DIR__) . '/app/routes.php')($app);
        (require dirname(__DIR__) . '/app/middleware.php')($app);

        return $app;
    },

    // HTTP factories
    ResponseFactoryInterface::class => function (ContainerInterface $container) {
        return $container->get(Psr17Factory::class);
    },

    ServerRequestFactoryInterface::class => function (ContainerInterface $container) {
        return $container->get(Psr17Factory::class);
    },

    StreamFactoryInterface::class => function (ContainerInterface $container) {
        return $container->get(Psr17Factory::class);
    },

    UploadedFileFactoryInterface::class => function (ContainerInterface $container) {
        return $container->get(Psr17Factory::class);
    },

    UriFactoryInterface::class => function (ContainerInterface $container) {
        return $container->get(Psr17Factory::class);
    },

    RouteParserInterface::class => function (ContainerInterface $container) {
        return $container->get(App::class)->getRouteCollector()->getRouteParser();
    },

    // Middleware
    BasePathMiddleware::class => function (ContainerInterface $container) {
        return new BasePathMiddleware($container->get(App::class));
    },

    TwigMiddleware::class => function (ContainerInterface $container) {
        return TwigMiddleware::createFromContainer(
            $container->get(App::class),
            Twig::class,
        );
    },

    HttpBasicAuthentication::class => function (ContainerInterface $container) {
        return new HttpBasicAuthentication($container->get('settings')['basic_auth']);
    },

    // Everything else
    Twig::class => function (ContainerInterface $container) {
        return Twig::create(
            $container->get('settings')['twig']['template_path'],
            $container->get('settings')['twig']['settings'],
        );
    },

    Connection::class => function (ContainerInterface $container) {
        $connectionParams = new DsnParser()->parse($container->get('settings')['database']['dsn']);

        return DriverManager::getConnection($connectionParams);
    },
];
