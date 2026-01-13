<?php

declare(strict_types=1);

use function Superscript\Monads\Result\Ok;
use function Superscript\Monads\Result\Err;
use function Superscript\Monads\Result\Pipe\toOk;
use function Superscript\Monads\Result\Pipe\toErr;
use function Superscript\Monads\Result\Pipe\map;
use function Superscript\Monads\Result\Pipe\mapErr;
use function Superscript\Monads\Result\Pipe\andThen;
use function Superscript\Monads\Result\Pipe\unwrapOr;
use function Superscript\Monads\Result\Pipe\matchResult;

/**
 * Tests demonstrating PHP 8.5 pipe operator with Result monad.
 *
 * Note: These tests use the traditional function call syntax because
 * PHP 8.5 is not yet available in the testing environment. However, they
 * demonstrate the exact same operations that would work with the pipe operator.
 *
 * With PHP 8.5 pipe operator, these would look like:
 *
 *   $result = $value
 *       |> toOk(...)
 *       |> map(fn($x) => $x * 2)
 *       |> andThen(validateValue)
 *       |> unwrapOr(0);
 */

test('pipe friendly ok creation', function () {
    expect(toOk(42))->toEqual(Ok(42));
});

test('pipe friendly err creation', function () {
    expect(toErr('error'))->toEqual(Err('error'));
});

test('pipe friendly map transformation', function () {
    $result = map(fn($x) => $x * 2)(Ok(21));
    expect($result)->toEqual(Ok(42));

    $result = map(fn($x) => $x * 2)(Err('error'));
    expect($result)->toEqual(Err('error'));
});

test('pipe friendly mapErr transformation', function () {
    $result = mapErr(fn($e) => "Wrapped: $e")(Ok(42));
    expect($result)->toEqual(Ok(42));

    $result = mapErr(fn($e) => "Wrapped: $e")(Err('error'));
    expect($result)->toEqual(Err('Wrapped: error'));
});

test('pipe friendly flatMap operation', function () {
    $divide = fn(int $a, int $b): mixed => $b === 0
        ? Err("Division by zero")
        : Ok($a / $b);

    $result = andThen(fn($x) => $divide($x, 2))(Ok(10));
    expect($result)->toEqual(Ok(5));

    $result = andThen(fn($x) => $divide($x, 0))(Ok(10));
    expect($result)->toEqual(Err("Division by zero"));

    $result = andThen(fn($x) => $divide($x, 2))(Err('previous error'));
    expect($result)->toEqual(Err('previous error'));
});

test('pipe friendly unwrapOr operation', function () {
    $result = unwrapOr(0)(Ok(42));
    expect($result)->toBe(42);

    $result = unwrapOr(0)(Err('error'));
    expect($result)->toBe(0);
});

test('pipe friendly match operation', function () {
    $result = matchResult(
        fn($e) => "Error: $e",
        fn($v) => "Value: $v",
    )(Ok(42));
    expect($result)->toBe('Value: 42');

    $result = matchResult(
        fn($e) => "Error: $e",
        fn($v) => "Value: $v",
    )(Err('oops'));
    expect($result)->toBe('Error: oops');
});

test('pipe chain example - validate and process number', function () {
    // Simulate: $input |> toOk(...) |> map(parseInt) |> andThen(validate) |> map(double) |> unwrapOr(0)

    $parseInt = fn(string $s): int => (int) $s;
    $validate = fn(int $x): mixed => $x > 0 ? Ok($x) : Err("Must be positive");
    $double = fn(int $x): int => $x * 2;

    $process = fn(string $input) => unwrapOr(0)(
        map($double)(
            andThen($validate)(
                map($parseInt)(toOk($input))
            )
        )
    );

    expect($process("5"))->toBe(10);
    expect($process("-5"))->toBe(0);
    expect($process("0"))->toBe(0);
});

test('pipe operator style - safe division chain', function () {
    $divide = fn(int $a, int $b): mixed => $b === 0
        ? Err("Division by zero")
        : Ok($a / $b);

    // With PHP 8.5: 100 |> fn($x) => $divide($x, 2) |> andThen(fn($x) => $divide((int)$x, 5)) |> unwrapOr(0)
    $result = unwrapOr(0)(
        andThen(fn($x) => $divide((int) $x, 5))(
            $divide(100, 2)
        )
    );

    expect($result)->toBe(10);

    // Test with division by zero
    $result = unwrapOr(0)(
        andThen(fn($x) => $divide((int) $x, 5))(
            $divide(100, 0)
        )
    );

    expect($result)->toBe(0);
});

test('pipe operator style - error recovery', function () {
    // With PHP 8.5: $value |> toOk(...) |> map(risky) |> mapErr(recover) |> unwrapOr('fallback')

    $risky = fn(int $x): int => $x < 0 ? throw new Exception("Negative!") : $x * 2;
    $recover = fn($e): string => "Recovered from error";

    $result = unwrapOr('fallback')(
        mapErr($recover)(
            map($risky)(Ok(5))
        )
    );

    expect($result)->toBe(10);
});

test('pipe friendly chaining with method calls', function () {
    // This demonstrates how the existing methods work perfectly with pipe operator
    // With PHP 8.5: $value |> Ok(...) |> map(...) |> andThen(...) |> unwrapOr(...)

    $result = Ok(10)
        ->map(fn($x) => $x * 2)
        ->andThen(fn($x) => $x > 15 ? Ok($x) : Err("too small"))
        ->map(fn($x) => "Result: $x")
        ->unwrapOr("Failed");

    expect($result)->toBe("Result: 20");

    $result = Ok(5)
        ->map(fn($x) => $x * 2)
        ->andThen(fn($x) => $x > 15 ? Ok($x) : Err("too small"))
        ->map(fn($x) => "Result: $x")
        ->unwrapOr("Failed");

    expect($result)->toBe("Failed");
});

test('pipe operator style - parse and validate JSON', function () {
    $parseJson = function (string $json): mixed {
        try {
            return Ok(json_decode($json, true, 512, JSON_THROW_ON_ERROR));
        } catch (Exception $e) {
            return Err("Invalid JSON");
        }
    };

    $validateUser = fn(array $data): mixed
        => isset($data['name']) && isset($data['email'])
            ? Ok($data)
            : Err("Missing required fields");

    $extractName = fn(array $user): string => $user['name'];

    // With PHP 8.5: $json |> $parseJson(...) |> andThen(validateUser) |> map(extractName) |> unwrapOr('Unknown')
    $process = fn(string $json) => unwrapOr('Unknown')(
        map($extractName)(
            andThen($validateUser)($parseJson($json))
        )
    );

    $validJson = '{"name":"Alice","email":"alice@example.com"}';
    expect($process($validJson))->toBe('Alice');

    $invalidJson = '{"name":"Bob"}';
    expect($process($invalidJson))->toBe('Unknown');

    $malformedJson = '{invalid}';
    expect($process($malformedJson))->toBe('Unknown');
});
