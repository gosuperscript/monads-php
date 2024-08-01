<?php

use Superscript\Monads\Lazy\Lazy;

use function PHPStan\Testing\assertType;

$lazy = Lazy::of(fn() => 'foo');
assertType('string', $lazy->evaluate());
