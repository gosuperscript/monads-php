<?php

declare(strict_types=1);

namespace Superscript\Monads\Result;

use Throwable;

/**
 * @template T
 *
 * @param  T  $value
 * @return Ok<T>
 */
function Ok(mixed $value): Ok
{
    return new Ok($value);
}

/**
 * @template E
 *
 * @param  E  $err
 * @return Err<E>
 */
function Err(mixed $err): Err
{
    return new Err($err);
}

/**
 * @template T
 * @template E of Throwable
 * @param callable(): T $f
 * @return Result<T, E>
 */
function attempt(callable $f): Result
{
    try {
        return Ok($f());
    } catch (Throwable $e) {
        return Err($e);
    }
}
