<?php

declare(strict_types=1);

namespace App\Application\Responder;

use App\Application\Responder\ResponderInterface;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use SineMacula\Http\Enums\HttpStatus;

class JsonResponder implements ResponderInterface
{
    public function respond(ResponseInterface $response, array $data): ResponseInterface
    {
        $response = $response->withHeader('Content-Type', 'application/json');

        $response->getBody()->write(
            (string) json_encode(
                $data,
                JSON_UNESCAPED_SLASHES | JSON_PARTIAL_OUTPUT_ON_ERROR
            )
        );

        return $response;
    }

    public function respondWithStatus(
        ResponseInterface $response,
        array $data,
        HttpStatus $status = HttpStatus::OK
    ): ResponseInterface {
        $response = $this->respond(
            $response, array_merge(
                [
                    'success' => $status->isSuccess()
                ],
                $data
            )
        );

        return $response->withStatus($status->value);
    }
}
