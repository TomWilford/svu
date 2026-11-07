<?php

declare(strict_types=1);

namespace App\Domain\Responder\Problem;

use SineMacula\Http\Enums\HttpStatus;

interface ProblemDetailsInterface
{
    public function getStatus(): HttpStatus;

    public function getType(): string;

    /** @return array<string, mixed> */
    public function toArray(): array;
}
