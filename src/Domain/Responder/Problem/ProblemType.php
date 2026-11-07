<?php

declare(strict_types=1);

namespace App\Domain\Responder\Problem;

enum ProblemType: string
{
    case UNKNOWN = 'about:blank';
    case RESOURCE_NOT_FOUND = 'resource-not-found';
    case VALIDATION_ERROR = 'validation-error';

    public function explanation(): string
    {
        return match ($this) {
            self::VALIDATION_ERROR => 'One or more fields failed validation.',
            self::RESOURCE_NOT_FOUND =>
                'The requested resource could not be found. Please verify the URI and try again.',
            self::UNKNOWN => 'No further explanation available.',
        };
    }

    public function uri(string $baseUri): string
    {
        if ($this === self::UNKNOWN) {
            return $this->value;
        }

        return rtrim($baseUri, '/') . '/problem/' . $this->value;
    }
}
