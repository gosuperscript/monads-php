<?php

declare(strict_types=1);

namespace Superscript\Monads\Result\Testing;

use InvalidArgumentException;
use SebastianBergmann\Comparator\Comparator;
use SebastianBergmann\Comparator\ComparisonFailure;
use SebastianBergmann\Exporter\Exporter;
use Superscript\Monads\Result\CannotUnwrapErr;
use Superscript\Monads\Result\Result;

final class ResultComparator extends Comparator
{
    public function accepts(mixed $expected, mixed $actual): bool
    {
        return ! $expected instanceof Result && $actual instanceof Result;
    }

    public function assertEquals(mixed $expected, mixed $actual, float $delta = 0.0, bool $canonicalize = false, bool $ignoreCase = false): void
    {
        if (! $actual instanceof Result) {
            throw new InvalidArgumentException();
        }

        try {
            $unwrapped = $actual->unwrap();
        } catch (CannotUnwrapErr) {
            throw new ComparisonFailure(
                $expected,
                $actual,
                (new Exporter())->export($expected),
                'Err',
            );
        }

        $comparator = $this->factory()->getComparatorFor($expected, $unwrapped);

        $comparator->assertEquals($expected, $unwrapped, $delta, $canonicalize, $ignoreCase);
    }
}
