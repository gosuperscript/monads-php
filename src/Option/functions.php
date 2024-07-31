<?php

declare(strict_types=1);

namespace Superscript\Monads\Option;

/**
 * @template T
 *
 * @param T $value
 * @return Some<T>
 */
function Some(mixed $value): Some
{
    return new Some($value);
}

function None(): None
{
    return new None();
}
