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
 * @param callable(T): U $f
 * @return callable(Result<T, E>): Result<U, E>
 */
function map(callable $f): callable
{
    return fn(Result $result): Result => $result->map($f);
}

/**
 * Map over a Result error.
 * Pipe-friendly helper for transforming errors.
 *
 * @template T
 * @template E
 * @template F
 * @param callable(E): F $f
 * @return callable(Result<T, E>): Result<T, F>
 */
function mapErr(callable $f): callable
{
    return fn(Result $result): Result => $result->mapErr($f);
}

/**
 * FlatMap over a Result value.
 * Pipe-friendly helper for chaining operations that return Results.
 *
 * @template T
 * @template E
 * @template U
 * @template F
 * @param callable(T): Result<U, F> $f
 * @return callable(Result<T, E>): Result<U, E|F>
 */
function andThen(callable $f): callable
{
    return fn(Result $result): Result => $result->andThen($f);
}

/**
 * Unwrap a Result with a default value.
 * Pipe-friendly helper for extracting values safely.
 *
 * @template T
 * @template E
 * @template U
 * @param U $default
 * @return callable(Result<T, E>): (T|U)
 */
function unwrapOr(mixed $default): callable
{
    return fn(Result $result): mixed => $result->unwrapOr($default);
}

/**
 * Match on a Result value.
 * Pipe-friendly helper for handling both Ok and Err cases.
 *
 * @template T
 * @template E
 * @template U
 * @param callable(E): U $err
 * @param callable(T): U $ok
 * @return callable(Result<T, E>): U
 */
function matchResult(callable $err, callable $ok): callable
{
    return fn(Result $result): mixed => $result->match($err, $ok);
}
