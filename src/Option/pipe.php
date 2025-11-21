<?php

declare(strict_types=1);

namespace Superscript\Monads\Option\Pipe;

use Superscript\Monads\Option\Option;

use function Superscript\Monads\Option\None;
use function Superscript\Monads\Option\Some;

/**
 * Wrap a value in a Some, or None if null.
 * Pipe-friendly version of Option::from().
 *
 * @template T
 * @param T|null $value
 * @return Option<T>
 */
function option(mixed $value): Option
{
    return Option::from($value);
}

/**
 * Map over an Option value.
 * Pipe-friendly helper for chaining transformations.
 *
 * @template T
 * @template U
 * @param Option<T> $option
 * @return callable(callable(T): U): Option<U>
 * @phpstan-return callable(callable(T): U): Option<U>
 */
function map(Option $option): callable
{
    /** @phpstan-ignore-next-line */
    return fn(callable $f): Option => $option->map($f);
}

/**
 * Filter an Option value.
 * Pipe-friendly helper for conditional filtering.
 *
 * @template T
 * @param Option<T> $option
 * @return callable(callable(T): bool): Option<T>
 */
function filter(Option $option): callable
{
    return fn(callable $f): Option => $option->filter($f);
}

/**
 * FlatMap over an Option value.
 * Pipe-friendly helper for chaining operations that return Options.
 *
 * @template T
 * @template U
 * @param Option<T> $option
 * @return callable(callable(T): Option<U>): Option<U>
 * @phpstan-return callable(callable(T): Option<U>): Option<U>
 */
function andThen(Option $option): callable
{
    /** @phpstan-ignore-next-line */
    return fn(callable $f): Option => $option->andThen($f);
}

/**
 * Unwrap an Option with a default value.
 * Pipe-friendly helper for extracting values safely.
 *
 * @template T
 * @template U
 * @param Option<T> $option
 * @return callable(U): (T|U)
 */
function unwrapOr(Option $option): callable
{
    return fn(mixed $default): mixed => $option->unwrapOr($default);
}

/**
 * Check if Option is Some and satisfies predicate.
 * Pipe-friendly helper for conditional checks.
 *
 * @template T
 * @param Option<T> $option
 * @return callable(callable(T): bool): bool
 */
function isSomeAnd(Option $option): callable
{
    return fn(callable $predicate): bool => $option->isSomeAnd($predicate);
}
