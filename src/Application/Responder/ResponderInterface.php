<?php

declare(strict_types=1);

namespace App\Application\Responder;

use Psr\Http\Message\ResponseInterface;
use SineMacula\Http\Enums\HttpStatus;

interface ResponderInterface
{
    public function respond(ResponseInterface $response, array $data): ResponseInterface;

    public function respondWithStatus(
        ResponseInterface $response,
        array $data,
        HttpStatus $status = HttpStatus::OK)
    : ResponseInterface;
}
