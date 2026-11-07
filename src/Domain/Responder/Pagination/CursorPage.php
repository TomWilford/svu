<?php

declare(strict_types=1);

namespace App\Domain\Responder\Pagination;

use InvalidArgumentException;
use JsonSerializable;

final class CursorPage implements JsonSerializable
{
    private int $size;

    public function __construct(
        /** @var positive-int $size */
        int $size,
        private readonly ?string $before = null,
        private readonly ?string $after = null,
    ) {
        if ($size <= 0) {
            throw new InvalidArgumentException('Size must be greater than zero');
        }
        if ($before !== null && $after !== null) {
            throw new InvalidArgumentException('Before and after cannot both be set');
        }

        $this->size = $size;
    }

    /**
     * @return array{size: int, before?: string, after?: string}
     */
    final public function toArray(): array
    {
        $array['size'] = $this->size;

        if ($this->before !== null) {
            $array['before'] = $this->before;
        }
        if ($this->after !== null) {
            $array['after'] = $this->after;
        }

        return $array;
    }

    /**
     * @see self::toArray()
     *
     * @return array{size: int, before?: string, after?: string}
     */
    final public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function getBefore(): ?string
    {
        return $this->before;
    }

    public function getAfter(): ?string
    {
        return $this->after;
    }
}
