<?php

declare(strict_types=1);

namespace Superscript\Monads\Option;

use RuntimeException;

final class CannotUnwrapNone extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Called `Option::unwrap` on a `None` value');
    }
}
