<?php

declare(strict_types=1);

namespace Superscript\Monads\Result;

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
