<?php

declare(strict_types=1);

namespace App\Application\Responder;

use App\Domain\Responder\Pagination\CursorPage;
use App\Domain\Responder\Pagination\PageLinks;
use App\Domain\Responder\Problem\ProblemDetailsInterface;
use Psr\Http\Message\ResponseInterface;
use SineMacula\Http\Enums\HttpStatus;

class JsonResponder implements ApiResponderInterface
{
    /**
     * {@inheritDoc}
     */
    public function item(
        ResponseInterface $response,
        object|array $data,
        HttpStatus $status = HttpStatus::OK,
        array $headers = [],
    ): ResponseInterface {
        return $this->send($response, $headers, $status, ['data' => $data]);
    }

    /**
     * {@inheritDoc}
     */
    public function collection(
        ResponseInterface $response,
        array|object $data,
        CursorPage $page,
        ?PageLinks $links = null,
        HttpStatus $status = HttpStatus::OK,
        array $headers = [],
    ): ResponseInterface {
        if ($links !== null) {
            $body['links'] = $links;
        }
        $body = [
            'data' => $data,
            'meta' => [
                'page' => $page,
            ],
        ];

        return $this->send($response, $headers, $status, $body);
    }

    /**
     * {@inheritDoc}
     */
    public function problem(
        ResponseInterface $response,
        ProblemDetailsInterface $problemDetails,
        array $headers = [],
    ): ResponseInterface {
        $response = $response->withHeader('Content-Type', 'application/problem+json');
        $response = $this->applyHeaders($headers, $response);

        $this->writeBody($response, $problemDetails);

        return $response->withStatus($problemDetails->getStatus()->value);
    }

    /**
     * {@inheritDoc}
     */
    public function empty(
        ResponseInterface $response,
        HttpStatus $status = HttpStatus::NO_CONTENT,
        array $headers = [],
    ): ResponseInterface {
        return $this->send($response, $headers, $status);
    }

    /**
     * @param array<string, string|list<string>> $headers
     * @param ResponseInterface $response
     *
     * @return ResponseInterface
     */
    private function applyHeaders(array $headers, ResponseInterface $response): ResponseInterface
    {
        foreach ($headers as $header => $value) {
            $response = $response->withHeader($header, $value);
        }

        return $response;
    }

    /**
     * @param ResponseInterface $response
     * @param array<string, mixed>|object $data
     *
     * @return void
     */
    private function writeBody(ResponseInterface $response, object|array $data): void
    {
        $response->getBody()->write(
            (string)json_encode(
                $data,
                JSON_UNESCAPED_SLASHES | JSON_PARTIAL_OUTPUT_ON_ERROR
            )
        );
    }

    /**
     * @param ResponseInterface $response
     * @param array<string, string|list<string>> $headers
     * @param HttpStatus $status
     * @param array<string, mixed>|object|null $body
     *
     * @return ResponseInterface
     */
    private function send(
        ResponseInterface $response,
        array $headers,
        HttpStatus $status,
        array|object|null $body = null,
    ): ResponseInterface {
        $response = $response->withHeader('Content-Type', 'application/json');
        $response = $this->applyHeaders($headers, $response);

        if ($body !== null) {
            $this->writeBody($response, $body);
        }

        return $response->withStatus($status->value);
    }
}
