<?php

declare(strict_types=1);

namespace Superscript\Monads\Option\Testing;

use LogicException;
use PHPUnit\Framework\Attributes\Before;
use PHPUnit\Framework\TestCase;

trait ComparesOptions
{
    #[Before]
    protected function setUpComparesOptions(): void
    {
        if (! $this instanceof TestCase) {
            throw new LogicException('This trait should only be used on PHPUnit TestCase');
        }

        $this->registerComparator(new OptionComparator());
    }
}
