<?php

declare(strict_types=1);

namespace Superscript\Monads\Writer;

/**
 * Create a Writer with an array-based log.
 *
 * The log entries are combined using array spread (array_merge-like behavior).
 *
 * @template T
 *
 * @param T $value
 * @param list<mixed> $log
 * @return Writer<list<mixed>, T>
 */
function Writer(mixed $value, array $log = []): Writer
{
    $combiner = /** @param list<mixed> $a @param list<mixed> $b @return list<mixed> */ fn(array $a, array $b): array => array_values([...$a, ...$b]);

    return Writer::of($value, $log, $combiner);
}
