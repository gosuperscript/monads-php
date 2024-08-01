<?php

declare(strict_types=1);

namespace Superscript\Monads\Option\Testing;

use InvalidArgumentException;
use SebastianBergmann\Comparator\Comparator;
use SebastianBergmann\Comparator\ComparisonFailure;
use SebastianBergmann\Exporter\Exporter;
use Superscript\Monads\Option\CannotUnwrapNone;
use Superscript\Monads\Option\Option;

final class OptionComparator extends Comparator
{
    public function accepts(mixed $expected, mixed $actual): bool
    {
        return ! $expected instanceof Option && $actual instanceof Option;
    }

    public function assertEquals(mixed $expected, mixed $actual, float $delta = 0.0, bool $canonicalize = false, bool $ignoreCase = false): void
    {
        if (! $actual instanceof Option) {
            throw new InvalidArgumentException();
        }

        try {
            $unwrapped = $actual->unwrap();
        } catch (CannotUnwrapNone) {
            if (is_null($expected)) {
                return;
            }

            throw new ComparisonFailure(
                $expected,
                $actual,
                (new Exporter())->export($expected),
                'None',
            );
        }

        $comparator = $this->factory()->getComparatorFor($expected, $unwrapped);

        $comparator->assertEquals($expected, $unwrapped, $delta, $canonicalize, $ignoreCase);
    }
}
