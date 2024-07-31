<?php

declare(strict_types=1);

use Superscript\Monads\Option\None;
use Superscript\Monads\Option\Option;
use Superscript\Monads\Option\Some;
use Superscript\Monads\Result\Result;

use function PHPStan\Testing\assertType;

/** @var Option<int> $x */
/** @var Option<string> $y */
assertType(Option::class . '<string>', $x->and($y));
assertType(Option::class . '<string>', $x->andThen(fn() => $y));

/** @var Option<string> $x */
assertType('string', $x->expect('value should not be empty'));

/** @var Option<string> $x */
$x->inspect(fn($value) => assertType('string', $value));

/** @var Option<int> $x */
assertType('bool', $x->isNone());
assertType('bool', $x->isSome());
assertType('bool', $x->isSomeAnd(fn(int $x) => $x > 1));

/** @var Option<int> $x */
if ($x->isSome()) {
    assertType(Some::class . '<int>', $x);
} else {
    assertType(None::class . '<int>', $x);
}

/** @var Option<int> $x */
if ($x->isNone()) {
    assertType(None::class . '<int>', $x);
} else {
    assertType(Some::class . '<int>', $x);
}

/** @var Option<string> $x */
assertType(Option::class . '<int>', $x->map(strlen(...)));
assertType('int', $x->mapOr(42, strlen(...)));
assertType('int', $x->mapOrElse(fn() => 42, strlen(...)));

/** @var Option<string> $x */
assertType(Result::class . '<string, int>', $x->okOr(0));
assertType(Result::class . '<string, int>', $x->okOrElse(fn() => 0));

/** @var Option<int> $x */
/** @var Option<int> $y */
assertType(Option::class . '<int>', $x->or($y));
assertType(Option::class . '<int>', $x->orElse(fn() => $y));

/** @var Option<string> $x */
assertType('string', $x->unwrap());
assertType('string', $x->unwrapOr('default'));
assertType('string', $x->unwrapOrElse(fn() => 'default'));

/** @var Option<int> $x */
/** @var Option<int> $y */
assertType(Option::class . '<int>', $x->xor($y));

/** @var list<Option<int>> $items */
assertType(Option::class . '<array<int, int>>', Option::collect($items));
