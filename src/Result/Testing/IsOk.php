<?php

declare(strict_types=1);

namespace Superscript\Monads\Result\Testing;

use PHPUnit\Framework\Constraint\Constraint;
use Superscript\Monads\Result\Result;

final class IsOk extends Constraint
{
    public function toString(): string
    {
        return 'is an Ok Result';
    }

    public function matches(mixed $other): bool
    {
        return $other instanceof Result && $other->isOk();
    }

    protected function failureDescription(mixed $other): string
    {
        return sprintf('%s %s', $this->exporter()->export($other), $this->toString());
    }
}
