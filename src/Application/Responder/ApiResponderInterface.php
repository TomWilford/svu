<?php

declare(strict_types=1);

namespace App\Application\Responder;

use App\Domain\Responder\Pagination\CursorPage;
use App\Domain\Responder\Pagination\PageLinks;
use App\Domain\Responder\Problem\ProblemDetailsInterface;
use Psr\Http\Message\ResponseInterface;
use SineMacula\Http\Enums\HttpStatus;

interface ApiResponderInterface
{
    /**
     * @param ResponseInterface $response
     * @param array<string, mixed>|object $data
     * @param HttpStatus $status
     * @param array<string, string|list<string>> $headers
     *
     * @return ResponseInterface
     */
    public function item(
        ResponseInterface $response,
        array|object $data,
        HttpStatus $status = HttpStatus::OK,
        array $headers = [],
    ): ResponseInterface;

    /**
     * @param ResponseInterface $response
     * @param array<string, mixed>|object $data
     * @param CursorPage $page
     * @param ?PageLinks $links
     * @param HttpStatus $status
     * @param array<string, string|list<string>> $headers
     *
     * @return ResponseInterface
     */
    public function collection(
        ResponseInterface $response,
        array|object $data,
        CursorPage $page,
        ?PageLinks $links = null,
        HttpStatus $status = HttpStatus::OK,
        array $headers = [],
    ): ResponseInterface;

    /**
     * @param ResponseInterface $response
     * @param ProblemDetailsInterface $problemDetails
     * @param array<string, string|list<string>> $headers
     *
     * @return ResponseInterface
     */
    public function problem(
        ResponseInterface $response,
        ProblemDetailsInterface $problemDetails,
        array $headers = [],
    ): ResponseInterface;

    /**
     * @param ResponseInterface $response
     * @param HttpStatus $status
     * @param array<string, string|list<string>> $headers
     *
     * @return ResponseInterface
     */
    public function empty(
        ResponseInterface $response,
        HttpStatus $status = HttpStatus::NO_CONTENT,
        array $headers = [],
    ): ResponseInterface;
}
