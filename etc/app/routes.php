<?php

declare(strict_types=1);

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\App;

return function (App $app) {
    $app->get('/ping', callable: function (ServerRequestInterface $request, ResponseInterface $response) {
        $responder = new \App\Application\Responder\JsonResponder();

        return $responder->item(
            $response,
            [
                'success' => "true",
                'timestamp' => new DateTimeImmutable()->format(DATE_ATOM),
            ]
        );
    });
};
