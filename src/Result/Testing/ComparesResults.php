<?php

declare(strict_types=1);

namespace Superscript\Monads\Result\Testing;

use LogicException;
use PHPUnit\Framework\Attributes\Before;
use PHPUnit\Framework\TestCase;

trait ComparesResults
{
    #[Before]
    protected function setUpComparesResults(): void
    {
        if (! $this instanceof TestCase) {
            throw new LogicException('This trait should only be used on PHPUnit TestCase');
        }

        $this->registerComparator(new ResultComparator());
    }
}
