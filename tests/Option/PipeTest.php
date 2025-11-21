<?php

declare(strict_types=1);

use function Superscript\Monads\Option\None;
use function Superscript\Monads\Option\Some;
use function Superscript\Monads\Option\Pipe\option;
use function Superscript\Monads\Option\Pipe\map;
use function Superscript\Monads\Option\Pipe\filter;
use function Superscript\Monads\Option\Pipe\andThen;
use function Superscript\Monads\Option\Pipe\unwrapOr;

/**
 * Tests demonstrating PHP 8.5 pipe operator with Option monad.
 *
 * Note: These tests use the traditional method chaining syntax because
 * PHP 8.5 is not yet available in the testing environment. However, they
 * demonstrate the exact same operations that would work with the pipe operator.
 *
 * With PHP 8.5 pipe operator, these would look like:
 *
 *   $result = $value
 *       |> option(...)
 *       |> map(...)(fn($x) => $x * 2)
 *       |> filter(...)(fn($x) => $x > 10)
 *       |> unwrapOr(...)(0);
 */

test('pipe friendly option creation from value', function () {
    expect(option(42))->toEqual(Some(42));
    expect(option(null))->toEqual(None());
});

test('pipe friendly map transformation', function () {
    $result = map(Some(21))(fn($x) => $x * 2);
    expect($result)->toEqual(Some(42));

    $result = map(None())(fn($x) => $x * 2);
    expect($result)->toEqual(None());
});

test('pipe friendly filter operation', function () {
    $result = filter(Some(42))(fn($x) => $x > 40);
    expect($result)->toEqual(Some(42));

    $result = filter(Some(5))(fn($x) => $x > 40);
    expect($result)->toEqual(None());

    $result = filter(None())(fn($x) => $x > 40);
    expect($result)->toEqual(None());
});

test('pipe friendly flatMap operation', function () {
    $result = andThen(Some(42))(fn($x) => $x > 40 ? Some($x * 2) : None());
    expect($result)->toEqual(Some(84));

    $result = andThen(Some(5))(fn($x) => $x > 40 ? Some($x * 2) : None());
    expect($result)->toEqual(None());
});

test('pipe friendly unwrapOr operation', function () {
    $result = unwrapOr(Some(42))(0);
    expect($result)->toBe(42);

    $result = unwrapOr(None())(0);
    expect($result)->toBe(0);
});

test('pipe chain example - process user input', function () {
    // Simulate: $input |> option(...) |> map(...)(trim) |> filter(...)(notEmpty) |> map(...)(strtoupper) |> unwrapOr(...)('GUEST')
    $input = '  john  ';

    $result = unwrapOr(
        map(
            filter(
                map(option($input))(fn($x) => trim($x)),
            )(fn($x) => strlen($x) > 0),
        )(fn($x) => strtoupper($x)),
    )('GUEST');

    expect($result)->toBe('JOHN');
});

test('pipe chain example - empty input returns default', function () {
    $input = '   ';

    $result = unwrapOr(
        map(
            filter(
                map(option($input))(fn($x) => trim($x)),
            )(fn($x) => strlen($x) > 0),
        )(fn($x) => strtoupper($x)),
    )('GUEST');

    expect($result)->toBe('GUEST');
});

test('pipe chain example - null input returns default', function () {
    $input = null;

    $result = unwrapOr(
        map(
            filter(
                map(option($input))(fn($x) => trim($x)),
            )(fn($x) => strlen($x) > 0),
        )(fn($x) => strtoupper($x)),
    )('GUEST');

    expect($result)->toBe('GUEST');
});

test('pipe friendly chaining with method calls', function () {
    // This demonstrates how the existing methods work perfectly with pipe operator
    // With PHP 8.5: $value |> Some(...) |> map(...) |> filter(...) |> unwrapOr(...)

    $result = Some(10)
        ->map(fn($x) => $x * 2)
        ->filter(fn($x) => $x > 15)
        ->map(fn($x) => "Value: $x")
        ->unwrapOr("No value");

    expect($result)->toBe("Value: 20");
});

test('pipe operator style - safe array access', function () {
    $users = [
        1 => ['id' => 1, 'name' => 'Alice'],
        2 => ['id' => 2, 'name' => 'Bob'],
    ];

    // With PHP 8.5 pipe: $id |> fn($x) => $users[$x] ?? null |> option(...) |> map(...)(fn($u) => $u['name']) |> unwrapOr(...)('Unknown')
    $getUserName = fn(int $id) => unwrapOr(
        map(option($users[$id] ?? null))(fn($u) => $u['name']),
    )('Unknown');

    expect($getUserName(1))->toBe('Alice');
    expect($getUserName(99))->toBe('Unknown');
});

test('pipe operator style - validate and transform', function () {
    $validateAge = fn(?int $age) => $age !== null && $age >= 18 && $age <= 100
        ? Some($age)
        : None();

    // With PHP 8.5: $input |> $validateAge(...) |> map(...)(fn($a) => "Age: $a") |> unwrapOr(...)('Invalid age')
    $result = unwrapOr(
        map($validateAge(25))(fn($a) => "Age: $a"),
    )('Invalid age');

    expect($result)->toBe('Age: 25');

    $result = unwrapOr(
        map($validateAge(200))(fn($a) => "Age: $a"),
    )('Invalid age');

    expect($result)->toBe('Invalid age');
});
