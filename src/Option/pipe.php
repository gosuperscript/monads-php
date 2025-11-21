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
 * @param callable(T): U $f
 * @return callable(Option<T>): Option<U>
 */
function map(callable $f): callable
{
    return fn(Option $option): Option => $option->map($f);
}

/**
 * Filter an Option value.
 * Pipe-friendly helper for conditional filtering.
 *
 * @template T
 * @param callable(T): bool $predicate
 * @return callable(Option<T>): Option<T>
 */
function filter(callable $predicate): callable
{
    return fn(Option $option): Option => $option->filter($predicate);
}

/**
 * FlatMap over an Option value.
 * Pipe-friendly helper for chaining operations that return Options.
 *
 * @template T
 * @template U
 * @param callable(T): Option<U> $f
 * @return callable(Option<T>): Option<U>
 */
function andThen(callable $f): callable
{
    return fn(Option $option): Option => $option->andThen($f);
}

/**
 * Unwrap an Option with a default value.
 * Pipe-friendly helper for extracting values safely.
 *
 * @template T
 * @template U
 * @param U $default
 * @return callable(Option<T>): (T|U)
 */
function unwrapOr(mixed $default): callable
{
    return fn(Option $option): mixed => $option->unwrapOr($default);
}

/**
 * Check if Option is Some and satisfies predicate.
 * Pipe-friendly helper for conditional checks.
 *
 * @template T
 * @param callable(T): bool $predicate
 * @return callable(Option<T>): bool
 */
function isSomeAnd(callable $predicate): callable
{
    return fn(Option $option): bool => $option->isSomeAnd($predicate);
}
