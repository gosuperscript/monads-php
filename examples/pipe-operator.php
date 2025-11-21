<?php

declare(strict_types=1);

/**
 * PHP 8.5 Pipe Operator Examples
 * 
 * This file demonstrates the actual usage of PHP 8.5's pipe operator with the monad library.
 * 
 * Run this file with PHP 8.5:
 *   php examples/pipe-operator.php
 * 
 * These examples show the real pipe operator syntax (|>) in action.
 */

require __DIR__ . '/../vendor/autoload.php';

use function Superscript\Monads\Option\{Some, None};
use function Superscript\Monads\Option\Pipe\{option, map, filter, andThen, unwrapOr};
use function Superscript\Monads\Result\{Ok, Err};
use function Superscript\Monads\Result\Pipe\{toOk, matchResult};
use function Superscript\Monads\Result\Pipe\map as resultMap;
use function Superscript\Monads\Result\Pipe\andThen as resultAndThen;

// Check PHP version
if (PHP_VERSION_ID < 80500) {
    echo "PHP 8.5+ is required to run pipe operator examples.\n";
    echo "Current version: " . PHP_VERSION . "\n";
    echo "\nThis file contains actual pipe operator syntax which will cause parse errors on PHP < 8.5.\n";
    exit(1);
}

echo "✓ Running PHP 8.5 Pipe Operator Examples\n\n";

// Example 1: Option - String processing pipeline
echo "=== Example 1: String Processing with Option ===\n";
$input = '  Hello World  ';
$result = $input
    |> option(...)
    |> map(fn($x) => trim($x))
    |> map(fn($x) => strtolower($x))
    |> filter(fn($x) => strlen($x) > 5)
    |> unwrapOr('default');

echo "Input: '{$input}'\n";
echo "Result: '{$result}'\n\n";

// Example 2: Option - Null handling
echo "=== Example 2: Null Handling with Option ===\n";
$nullInput = null;
$result = $nullInput
    |> option(...)
    |> map(fn($x) => $x * 2)
    |> unwrapOr(99);

echo "Input: null\n";
echo "Result: {$result}\n\n";

// Example 3: Option - Value transformation
echo "=== Example 3: Value Transformation with Option ===\n";
$number = 21;
$result = $number
    |> option(...)
    |> map(fn($x) => $x * 2)
    |> filter(fn($x) => $x > 40)
    |> unwrapOr(0);

echo "Input: {$number}\n";
echo "Result: {$result}\n\n";

// Example 4: Result - Safe division
echo "=== Example 4: Safe Division with Result ===\n";
$divide = fn(int $a, int $b) => $b === 0
    ? Err("Division by zero")
    : Ok($a / $b);

$result1 = 100
    |> fn($x) => $divide($x, 2)
    |> resultAndThen(fn($x) => $divide((int)$x, 5))
    |> unwrapOr(0);

echo "100 / 2 / 5 = {$result1}\n";

$result2 = 100
    |> fn($x) => $divide($x, 0)
    |> resultAndThen(fn($x) => $divide((int)$x, 5))
    |> unwrapOr(-1);

echo "100 / 0 / 5 = {$result2} (error handled)\n\n";

// Example 5: Result - Number validation
echo "=== Example 5: Number Validation with Result ===\n";
$parseInt = fn(string $s): int => (int) $s;
$validate = fn(int $x) => $x > 0 ? Ok($x) : Err("Must be positive");
$double = fn(int $x): int => $x * 2;

$result = "5"
    |> toOk(...)
    |> resultMap($parseInt)
    |> resultAndThen($validate)
    |> resultMap($double)
    |> unwrapOr(0);

echo "Input: '5'\n";
echo "Result after parse->validate->double: {$result}\n\n";

// Example 6: Result - Error handling with match
echo "=== Example 6: Error Handling with Match ===\n";
$result = 42
    |> toOk(...)
    |> resultMap(fn($x) => $x * 2)
    |> matchResult(
        fn($e) => "Error: $e",
        fn($v) => "Value: $v"
    );

echo "Result: {$result}\n\n";

// Example 7: Option - FlatMap with andThen
echo "=== Example 7: FlatMap with andThen ===\n";
$validatePositive = fn($x) => $x > 0 ? Some($x) : None();

$result1 = 42
    |> option(...)
    |> andThen($validatePositive)
    |> map(fn($x) => $x * 2)
    |> unwrapOr(0);

$result2 = -5
    |> option(...)
    |> andThen($validatePositive)
    |> map(fn($x) => $x * 2)
    |> unwrapOr(0);

echo "42 validated and doubled: {$result1}\n";
echo "-5 validated and doubled: {$result2} (failed validation)\n\n";

// Example 8: Clean user input
echo "=== Example 8: Clean User Input ===\n";
$rawInputs = ['  john  ', '   ', null, 'ALICE'];

foreach ($rawInputs as $input) {
    $username = $input
        |> option(...)
        |> map(fn($x) => trim($x))
        |> filter(fn($x) => strlen($x) > 0)
        |> map(fn($x) => strtolower($x))
        |> unwrapOr('guest');

    echo sprintf("Input: %s -> Username: %s\n", var_export($input, true), $username);
}

echo "\n✓ All examples completed successfully!\n";
