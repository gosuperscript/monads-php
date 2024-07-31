<?php

declare(strict_types=1);

namespace Superscript\Monads\Result;

use RuntimeException;
use Throwable;

class CannotUnwrapErr extends RuntimeException
{
    /**
     * @param  Err<mixed>  $err
     */
    public static function make(Err $err): self
    {
        $err = $err->unwrapErr();

        if (is_string($err)) {
            $message = "Err($err)";
        } elseif ($err instanceof Throwable) {
            $message = "Err({$err->getMessage()})";
        } else {
            $message = 'Err';
        }

        return new self('Unwrapped with the expectation of an Ok, but found ' . $message, previous: $err instanceof Throwable ? $err : null);
    }
}
