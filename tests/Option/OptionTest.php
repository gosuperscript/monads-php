<?php

declare(strict_types=1);

use Superscript\Monads\Option\CannotUnwrapNone;
use Superscript\Monads\Option\Option;

use Superscript\Monads\Result\Result;

use function Superscript\Monads\Option\None;
use function Superscript\Monads\Option\Some;
use function Superscript\Monads\Result\Err;
use function Superscript\Monads\Result\Ok;

test('collect', function (array $items, Option $expected) {
    expect(Option::collect($items))->toEqual($expected);
})->with([
    [[Some(1), Some(2), Some(3)], Some([1, 2, 3])],
    [[Some(1), None()], None()],
    [[], Some([])],
]);

test('is some', function (Option $option, bool $expected) {
    expect($option->isSome())->toBe($expected);
})->with([
    [Some(2), true],
    [None(), false],
]);

test('is some and', function (Option $option, callable $f, bool $expected) {
    expect($option->isSomeAnd($f))->toBe($expected);
})->with([
    [Some(2), fn(int $x) => $x > 1, true],
    [Some(0), fn(int $x) => $x > 1, false],
    [None(), fn(int $x) => $x > 1, false],
]);

test('is none', function (Option $option, bool $expected) {
    expect($option->isNone())->toBe($expected);
})->with([
    [Some(2), false],
    [None(), true],
]);

test('and', function (Option $x, Option $y, Option $expected) {
    expect($x->and($y))->toEqual($expected);
})->with([
    [Some(2), None(), None()],
    [None(), Some('foo'), None()],
    [Some(2), Some('foo'), Some('foo')],
    [None(), None(), None()],
]);

test('and then', function (Option $option, Option $expected) {
    $checked_mul = static function (int $x, int $y) {
        return ($result = $x * $y) > (2 * 32) ? None() : Some($result);
    };

    expect($option->andThen(fn($x) => $checked_mul($x, $x)->map(strval(...))))->toEqual($expected);
})->with([
    [Some(2), Some('4')],
    [Some(1_000_000), None()],
    [None(), None()],
]);

test('expect', function () {
    expect(Some(2)->expect('should be there'))->toEqual(2);

    expect(fn() => None()->expect('should be there'))->toThrow(RuntimeException::class);
});

test('filter', function (Option $option, Option $expected) {
    $is_even = static fn(int $x) => $x % 2 === 0;

    expect($option->filter($is_even))->toEqual($expected);
})->with([
    [None(), None()],
    [Some(3), None()],
    [Some(4), Some(4)],
]);

test('inspect', function (Option $result, mixed $expected) {
    $inspected = null;

    $result->inspect(function ($value) use (&$inspected) {
        $inspected = $value;
    });

    expect($inspected)->toEqual($expected);
})->with([
    [Some(4), 4],
    [None(), null],
]);

test('map', function (Option $option, Option $expected) {
    expect($option->map(strlen(...)))->toEqual($expected);
})->with([
    [Some('Hello, world!'), Some(13)],
    [None(), None()],
]);

test('map or', function (Option $option, mixed $expected) {
    expect($option->mapOr(42, strlen(...)))->toEqual($expected);
})->with([
    [Some('foo'), 3],
    [None(), 42],
]);

test('map or else', function (Option $option, mixed $expected) {
    expect($option->mapOrElse(fn() => 42, strlen(...)))->toEqual($expected);
})->with([
    [Some('foo'), 3],
    [None(), 42],
]);

test('ok or', function (Option $result, Result $expected) {
    expect($result->okOr(0))->toEqual($expected);
})->with([
    [Some('foo'), Ok('foo')],
    [None(), Err(0)],
]);

test('ok or else', function (Option $result, Result $expected) {
    expect($result->okOrElse(fn() => 0))->toEqual($expected);
})->with([
    [Some('foo'), Ok('foo')],
    [None(), Err(0)],
]);

test('or', function (Option $x, Option $y, Option $expected) {
    expect($x->or($y))->toEqual($expected);
})->with([
    [Some(2), None(), Some(2)],
    [None(), Some(100), Some(100)],
    [Some(2), Some(100), Some(2)],
    [None(), None(), None()],
]);

test('or else', function (Option $option, Option $other, Option $expected) {
    expect($option->orElse(fn() => $other))->toEqual($expected);
})->with([
    [Some('barbarians'), Some('vikings'), Some('barbarians')],
    [None(), Some('barbarians'), Some('barbarians')],
    [None(), None(), None()],
]);

test('unwrap', function () {
    expect(Some('foo')->unwrap())->toEqual('foo');

    expect(fn() => None()->unwrap())->toThrow(CannotUnwrapNone::class);
});

test('unwrap or', function () {
    expect(Some('car')->unwrapOr('bike'))->toEqual('car');
    expect(None()->unwrapOr('bike'))->toEqual('bike');
});

test('unwrap or else', function () {
    expect(Some(4)->unwrapOrElse(fn() => 20))->toEqual(4);
    expect(None()->unwrapOrElse(fn() => 20))->toEqual(20);
});

test('xor', function (Option $option, Option $other, Option $expected) {
    expect($option->xor($other))->toEqual($expected);
})->with([
    [Some(2), None(), Some(2)],
    [None(), Some(2), Some(2)],
    [Some(2), Some(2), None()],
    [None(), None(), None()],
]);

test('from', function (mixed $value, Option $expected) {
    expect(Option::from($value))->toEqual($expected);
})->with([
    ['foo', Some('foo')],
    [Some('foo'), Some('foo')],
    [null, None()],
    [None(), None()],
]);
