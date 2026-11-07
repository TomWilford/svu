<?php

declare(strict_types=1);

namespace App\Domain\Responder\Problem;

use JsonSerializable;
use SineMacula\Http\Enums\HttpStatus;

/**
 * @phpstan-type ProblemArray array{
 *     type: string,
 *     status: int,
 *     title?: string,
 *     detail?: string,
 *     instance?: string,
 *     ...<string, mixed>
 * }
 */
readonly class AbstractProblemDetails implements ProblemDetailsInterface, JsonSerializable
{
    public function __construct(
        private HttpStatus $status,
        private ProblemType $type = ProblemType::UNKNOWN,
        private ?string $title = null,
        private ?string $detail = null,
        private ?string $instance = null,
        private string $baseUri = 'https://example.com',
    ) {
    }

    public function getStatus(): HttpStatus
    {
        return $this->status;
    }

    public function getType(): string
    {
        return $this->type->uri($this->baseUri);
    }

    /**
     * @return array<string, mixed>
     */
    protected function extensions(): array
    {
        return [];
    }

    /**
     * @return ProblemArray
     */
    final public function toArray(): array
    {
        $reservedKeys = [
            'type' => $this->getType(),
            'status' => $this->status,
        ];

        if ($this->title !== null) {
            $reservedKeys['title'] = $this->title;
        }
        if ($this->detail !== null) {
            $reservedKeys['detail'] = $this->detail;
        }
        if ($this->instance !== null) {
            $reservedKeys['instance'] = $this->instance;
        }

        /** @var ProblemArray $result */
        $result = array_merge($this->extensions(), $reservedKeys);

        return $result;
    }

    /**
     * @see self::toArray()
     *
     * @return ProblemArray
     */
    final public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
