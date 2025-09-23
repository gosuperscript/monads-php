<?php

declare(strict_types=1);

namespace Superscript\Monads\Result\Testing;

use LogicException;
use PHPUnit\Framework\Attributes\Before;
use PHPUnit\Framework\Assert;
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

    public static function assertErr($actual, string $message = ''): void
    {
        Assert::assertThat($actual, static::isErr(), $message);
    }

    public static function isErr(): IsErr
    {
        return new IsErr();
    }

    public static function assertOk($actual, string $message = ''): void
    {
        Assert::assertThat($actual, static::isOk(), $message);
    }

    public static function isOk(): IsOk
    {
        return new IsOk();
    }
}
