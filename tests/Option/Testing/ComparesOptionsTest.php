<?php

declare(strict_types=1);

use Superscript\Monads\Option\Testing\ComparesOptions;

use function Superscript\Monads\Option\None;
use function Superscript\Monads\Option\Some;

uses(ComparesOptions::class);

test('comparison', function (mixed $actual, mixed $expected) {
    expect($actual)->toEqual($expected);
})->with([
    [Some(1), 1],
    [Some(Some('foo')), 'foo'],
    [None(), null],
]);

test('inequal comparison', function (mixed $actual, mixed $expected) {
    expect($actual)->not->toEqual($expected);
})->with([
    [Some(1), 'foo'],
    [Some(1), null],
    [None(), 'foo'],
]);
