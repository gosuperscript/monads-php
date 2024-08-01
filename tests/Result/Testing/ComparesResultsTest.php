<?php

declare(strict_types=1);

use Superscript\Monads\Result\Testing\ComparesResults;

use function Superscript\Monads\Result\Err;
use function Superscript\Monads\Result\Ok;

uses(ComparesResults::class);

test('comparison', function (mixed $actual, mixed $expected) {
    expect($actual)->toEqual($expected);
})->with([
    [Ok(1), 1],
    [Ok(Ok('foo')), 'foo'],
]);

test('inequal comparison', function (mixed $actual, mixed $expected) {
    expect($actual)->not->toEqual($expected);
})->with([
    [Ok(1), 'foo'],
    [Ok(1), null],
    [Err('foo'), 'foo'],
]);
