<?php

declare(strict_types=1);

namespace Superscript\Monads\Result\Pipe;

use Superscript\Monads\Result\Result;

use function Superscript\Monads\Result\Err;
use function Superscript\Monads\Result\Ok;

/**
 * Wrap a value in Ok.
 * Pipe-friendly version for creating successful results.
 *
 * @template T
 * @param T $value
 * @return Result<T, mixed>
 */
function toOk(mixed $value): Result
{
    return Ok($value);
}

/**
 * Wrap an error in Err.
 * Pipe-friendly version for creating error results.
 *
 * @template E
 * @param E $error
 * @return Result<mixed, E>
 */
function toErr(mixed $error): Result
{
    return Err($error);
}

/**
 * Map over a Result value.
 * Pipe-friendly helper for chaining transformations.
 *
 * @template T
 * @template E
 * @template U
 * @param Result<T, E> $result
 * @return callable(callable(T): U): Result<U, E>
 * @phpstan-return callable(callable(T): U): Result<U, E>
 */
function map(Result $result): callable
{
    /** @phpstan-ignore-next-line */
    return fn(callable $f): Result => $result->map($f);
}

/**
 * Map over a Result error.
 * Pipe-friendly helper for transforming errors.
 *
 * @template T
 * @template E
 * @template F
 * @param Result<T, E> $result
 * @return callable(callable(E): F): Result<T, F>
 * @phpstan-return callable(callable(E): F): Result<T, F>
 */
function mapErr(Result $result): callable
{
    /** @phpstan-ignore-next-line */
    return fn(callable $f): Result => $result->mapErr($f);
}

/**
 * FlatMap over a Result value.
 * Pipe-friendly helper for chaining operations that return Results.
 *
 * @template T
 * @template E
 * @template U
 * @template F
 * @param Result<T, E> $result
 * @return callable(callable(T): Result<U, F>): Result<U, E|F>
 * @phpstan-return callable(callable(T): Result<U, F>): Result<U, E|F>
 */
function andThen(Result $result): callable
{
    /** @phpstan-ignore-next-line */
    return fn(callable $f): Result => $result->andThen($f);
}

/**
 * Unwrap a Result with a default value.
 * Pipe-friendly helper for extracting values safely.
 *
 * @template T
 * @template E
 * @template U
 * @param Result<T, E> $result
 * @return callable(U): (T|U)
 */
function unwrapOr(Result $result): callable
{
    return fn(mixed $default): mixed => $result->unwrapOr($default);
}

/**
 * Match on a Result value.
 * Pipe-friendly helper for handling both Ok and Err cases.
 *
 * @template T
 * @template E
 * @template U
 * @param Result<T, E> $result
 * @return callable(callable(E): U, callable(T): U): U
 */
function matchResult(Result $result): callable
{
    return fn(callable $err, callable $ok): mixed => $result->match($err, $ok);
}
