<?php

use Superscript\Monads\Option\Option;
use Superscript\Monads\Result\Err;
use Superscript\Monads\Result\Ok;
use Superscript\Monads\Result\Result;

use function PHPStan\Testing\assertType;

/** @var Result<int, string> $result */
if ($result->isOk()) {
    assertType(Ok::class . '<int>', $result);
} else {
    assertType(Err::class . '<string>', $result);
}

/** @var Result<int, string> $result */
if ($result->isErr()) {
    assertType(Err::class . '<string>', $result);
} else {
    assertType(Ok::class . '<int>', $result);
}

/** @var Result<int, mixed> $result */
assertType('Superscript\Monads\Result\Result<non-empty-string, mixed>', $result->map(fn(int $value): string => random_bytes($value)));

/** @var Result<string, mixed> $result */
assertType('int<0, max>', $result->mapOr(42, fn(string $v) => strlen($v)));
assertType('int<0, max>', $result->mapOrElse(fn() => 21 * 2, fn(string $v) => strlen($v)));
assertType('int<0, max>', $result->match(fn() => 21 * 2, fn(string $v) => strlen($v)));

/** @var Result<mixed, int> $result */
assertType('Superscript\Monads\Result\Result<mixed, RuntimeException>', $result->mapErr(fn(int $err) => new RuntimeException(code: $err)));

/** @var Result<int, Throwable> $result */
assertType('Superscript\Monads\Result\Result<int, Throwable>', $result->inspect(fn() => null));
assertType('Superscript\Monads\Result\Result<int, Throwable>', $result->inspectErr(fn() => null));

/** @var Result<int, string> $result */
assertType('int', $result->unwrap());
assertType('string', $result->unwrapErr());

/** @var Result<int, RuntimeException> $x */
/** @var Result<string, LogicException> $y */
assertType('Superscript\Monads\Result\Result<string, LogicException|RuntimeException>', $x->and($y));
assertType('Superscript\Monads\Result\Result<string, LogicException|RuntimeException>', $x->andThen(fn(int $v) => $y));

assertType('Superscript\Monads\Result\Result<int|string, LogicException>', $x->or($y));
assertType('Superscript\Monads\Result\Result<int|string, LogicException>', $x->orElse(fn(Throwable $v) => $y));

/** @var Result<int, string> $x */
assertType('int', $x->unwrapOr(2));
assertType('int', $x->unwrapOrElse(fn() => 2));

/** @var Result<Option<int>, Throwable> $x */
assertType(Option::class . '<'.Result::class.'<int, '.Throwable::class.'>>', $x->transpose());
