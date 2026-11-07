<?php

declare(strict_types=1);

namespace App\Domain\Responder\Pagination;

use JsonSerializable;

final readonly class PageLinks implements JsonSerializable
{
    public function __construct(
        private string $self,
        private ?string $next = null,
        private ?string $prev = null,
        private ?string $first = null,
        private ?string $last = null,
    ) {
    }

    /**
     * @return array{self: string, next?: string, prev?: string, first?: string, last?: string}
     */
    final public function toArray(): array
    {
        $array['self'] = $this->self;

        if ($this->next !== null) {
            $array['next'] = $this->next;
        }
        if ($this->prev !== null) {
            $array['prev'] = $this->prev;
        }
        if ($this->first !== null) {
            $array['first'] = $this->first;
        }
        if ($this->last !== null) {
            $array['last'] = $this->last;
        }

        return $array;
    }

    /**
     * @see self::toArray()
     *
     * @return array{self: string, next?: string, prev?: string, first?: string, last?: string}
     */
    final public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function getSelf(): string
    {
        return $this->self;
    }

    public function getNext(): ?string
    {
        return $this->next;
    }

    public function getPrev(): ?string
    {
        return $this->prev;
    }

    public function getFirst(): ?string
    {
        return $this->first;
    }

    public function getLast(): ?string
    {
        return $this->last;
    }
}
