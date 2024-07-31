<?php

declare(strict_types=1);

use Superscript\Monads\Result\CannotUnwrapErr;
use Superscript\Monads\Result\Err;
use Superscript\Monads\Result\Ok;
use Superscript\Monads\Result\Result;

use function Superscript\Monads\Option\None;
use function Superscript\Monads\Option\Some;
use function Superscript\Monads\Result\Err;
use function Superscript\Monads\Result\Ok;

test('is ok', function (Result $result, mixed $expected) {
    expect($result->isOk())->toEqual($expected);
})->with([
    [Ok(-3), true],
    [Err(new Exception('Some error message')), false],
]);

test('is err', function (Result $result, mixed $expected) {
    expect($result->isErr())->toEqual($expected);
})->with([
    [Ok(-3), false],
    [Err(new Exception('Some error message')), true],
]);

test('ok', function (Result $result, mixed $expected) {
    expect($result->ok())->toEqual($expected);
})->with([
    [Ok(-3), Some(-3)],
    [Err(new Exception('Some error message')), None()],
]);

test('err', function (Result $result, mixed $expected) {
    expect($result->err())->toEqual($expected);
})->with([
    [Ok(-3), None()],
    [Err(new Exception('Some error message')), Some(new Exception('Some error message'))],
]);

test('expect', function () {
    expect(Ok(2)->expect('should be there'))->toEqual(2);

    expect(fn() => Err('error')->expect('should be there'))->toThrow(RuntimeException::class);
    expect(fn() => Err('err')->expect(new InvalidArgumentException()))->toThrow(InvalidArgumentException::class);
});

test('expect err', function () {
    expect(fn() => Ok(2)->expectErr('should have failed'))->toThrow(RuntimeException::class);

    expect(Err('error')->expectErr('should have failed'))->toEqual('error');
});

test('map', function (Result $result, mixed $expected) {
    expect($result->map(fn($i) => $i * 2))->toEqual($expected);
})->with([
    [Ok(1), Ok(2)],
    [Err(new Exception('Some error message')), Err(new Exception('Some error message'))],
]);

test('map or', function (Result $result, mixed $expected) {
    expect($result->mapOr(42, fn($v) => strlen($v)))->toEqual($expected);
})->with([
    [Ok('foo'), 3],
    [Err(new Exception('bar')), 42],
]);

test('map or else', function (Result $result, mixed $expected) {
    expect($result->mapOrElse(fn($err) => 21 * 2, fn($v) => strlen($v)))->toEqual($expected);
})->with([
    [Ok('foo'), 3],
    [Err(new Exception('bar')), 42],
]);

test('match', function (Result $result, mixed $expected) {
    expect($result->match(fn($err) => 21 * 2, strlen(...)))->toEqual($expected);
})->with([
    [Ok('foo'), 3],
    [Err(new Exception('bar')), 42],
]);

test('map err', function (Result $result, mixed $expected) {
    expect($result->mapErr(fn(Exception $err) => new Exception('The error: ' . $err->getMessage())))->toEqual($expected);
})->with([
    [Ok(2), Ok(2)],
    [Err(new Exception('Error!')), Err(new Exception('The error: Error!'))],
]);

test('inspect', function (Result $result, mixed $expected) {
    $inspected = null;

    $result->inspect(function ($value) use (&$inspected) {
        $inspected = $value;
    });

    expect($inspected)->toEqual($expected);
})->with([
    [Ok(4), 4],
    [Err(new Exception('Some error message')), null],
]);

test('inspect err', function (Result $result, mixed $expected) {
    $inspected = null;

    $result->inspectErr(function ($err) use (&$inspected) {
        $inspected = $err;
    });

    expect($inspected)->toEqual($expected);
})->with([
    [Ok(4), null],
    [Err(new Exception('Some error message')), new Exception('Some error message')],
]);

test('unwrap', function () {
    $result = Ok(2);
    expect($result->unwrap())->toEqual(2);

    $result = Err('Some error message');
    expect(fn() => $result->unwrap())->toThrow(new CannotUnwrapErr('Unwrapped with the expectation of an Ok, but found Err(Some error message)'));

    $result = Err(new InvalidArgumentException('Some error message'));
    expect(fn() => $result->unwrap())->toThrow(new CannotUnwrapErr('Unwrapped with the expectation of an Ok, but found Err(Some error message)', previous: new InvalidArgumentException('Some error message')));

    $result = Err(new stdClass());
    expect(fn() => $result->unwrap())->toThrow(new CannotUnwrapErr('Unwrapped with the expectation of an Ok, but found Err'));
});

test('unwrap err', function () {
    $result = Ok(2);
    expect(fn() => $result->unwrapErr())->toThrow(RuntimeException::class);

    $result = Err(new Exception('Some error message'));
    expect($result->unwrapErr())->toEqual(new Exception('Some error message'));
});

test('and', function (Result $x, Result $y, mixed $expected) {
    expect($x->and($y))->toEqual($expected);
})->with([
    [Ok(4), Err('late error'), Err('late error')],
    [Err('early error'), Ok(2), Err('early error')],
    [Err('not a 2'), Err('late error'), Err('not a 2')],
    [Ok(2), Ok('different result type'), Ok('different result type')],
]);

test('and then', function (Result $x, mixed $expected) {
    $perfectSquareRoot = function ($x) {
        $sqrt = sqrt($x);

        if (round($sqrt) === $sqrt) {
            return Ok($sqrt);
        }

        return Err('not a perfect square');
    };

    expect($x->andThen(fn($x) => $perfectSquareRoot($x)->map(fn($x) => (string) $x)))->toEqual($expected);
})->with([
    [Ok(16), Ok('4')],
    [Ok(15), Err('not a perfect square')],
    [Err('not a number'), Err('not a number')],
]);

test('or', function (Result $x, Result $y, mixed $expected) {
    expect($x->or($y))->toEqual($expected);
})->with([
    [Ok(2), Err('late error'), Ok(2)],
    [Err('early error'), Ok(2), Ok(2)],
    [Err('not a 2'), Err('late error'), Err('late error')],
    [Ok(2), Ok(100), Ok(2)],
]);

test('or else', function () {
    $sq = function ($x) {
        return Ok($x * $x);
    };
    $err = function ($x) {
        return Err($x);
    };

    expect(Ok(2)->orElse($sq)->orElse($err))->toEqual(Ok(2));
    expect(Ok(2)->orElse($err)->orElse($sq))->toEqual(Ok(2));
    expect(Err(3)->orElse($sq)->orElse($err))->toEqual(Ok(9));
    expect(Err(3)->orElse($err)->orElse($err))->toEqual(Err(3));
});

test('unwrap or', function (Result $result, mixed $other, mixed $expected) {
    expect($result->unwrapOr($other))->toEqual($expected);
})->with([
    [Ok(9), 2, 9],
    [Err('err'), 2, 2]
]);

test('unwrap or else', function (Result $result, callable $op, mixed $expected) {
    expect($result->unwrapOrElse($op))->toEqual($expected);
})->with([
    [Ok(2), strlen(...), 2],
    [Err('foo'), strlen(...), 3]
]);

test('unwrap either', function (Result $result, mixed $expected) {
    expect($result->unwrapEither())->toEqual($expected);
})->with([
    [Ok(42), 42],
    [Err('foo'), 'foo']
]);

test('into ok', function (Ok $ok, mixed $expected) {
    expect($ok->intoOk())->toEqual($expected);
})->with([
    [Ok(2), 2],
]);

test('into err', function (Err $err, mixed $expected) {
    expect($err->intoErr())->toEqual($expected);
})->with([
    [Err('error'), 'error'],
]);

test('collect', function (array $items, Result $expected) {
    expect(Result::collect($items))->toEqual($expected);
})->with([
    [[Ok(1), Ok(2)], Ok([1, 2])],
    [[Err('error')], Err('error')]
]);
