<?php

declare(strict_types=1);

namespace Superscript\Monads\Result\Testing;

use PHPUnit\Framework\Constraint\Constraint;
use Superscript\Monads\Result\Result;

final class IsErr extends Constraint
{
    public function toString(): string
    {
        return 'is an Err Result';
    }

    public function matches(mixed $other): bool
    {
        return $other instanceof Result && $other->isErr();
    }

    protected function failureDescription(mixed $other): string
    {
        return sprintf('%s %s', $this->exporter()->export($other), $this->toString());
    }
}
