<?php

declare(strict_types=1);

/**
 * Example: Using PHP 8.5 Pipe Operator with Monads
 *
 * This file demonstrates how to use the pipe operator with the monads library.
 *
 * Note: This file requires PHP 8.5+ to run. It's provided as a reference for
 * how to use the pipe operator once PHP 8.5 is available.
 */

require __DIR__ . '/../vendor/autoload.php';

use function Superscript\Monads\Option\Pipe\{option, map, filter, unwrapOr};
use function Superscript\Monads\Result\Pipe\{andThen, matchResult};
use function Superscript\Monads\Result\{Ok, Err};

// Example 1: Clean user input with Option
echo "Example 1: Clean User Input\n";
echo "----------------------------\n";

$rawInputs = ['  john  ', '   ', null, 'ALICE'];

foreach ($rawInputs as $input) {
    // With PHP 8.5 pipe operator:
    // $username = $input
    //     |> option(...)
    //     |> map(...)(fn($x) => trim($x))
    //     |> filter(...)(fn($x) => strlen($x) > 0)
    //     |> map(...)(fn($x) => strtolower($x))
    //     |> unwrapOr(...)('guest');

    // For now, using nested style:
    $username = unwrapOr(
        map(
            filter(
                map(option($input))(fn($x) => trim($x)),
            )(fn($x) => strlen($x) > 0),
        )(fn($x) => strtolower($x)),
    )('guest');

    echo sprintf("Input: %s -> Username: %s\n", var_export($input, true), $username);
}

echo "\n";

// Example 2: Safe division with Result
echo "Example 2: Safe Division\n";
echo "------------------------\n";

$divide = fn(int $a, int $b) => $b === 0
    ? Err("Division by zero")
    : Ok($a / $b);

$calculations = [
    [100, 2, 5],
    [50, 5, 0],
    [120, 3, 4],
];

foreach ($calculations as [$start, $first, $second]) {
    // With PHP 8.5 pipe operator:
    // $result = $start
    //     |> fn($x) => $divide($x, $first)
    //     |> andThen(...)(fn($x) => $divide((int)$x, $second))
    //     |> matchResult(...)(
    //         fn($e) => "Error: $e",
    //         fn($v) => "Result: $v"
    //     );

    // For now, using nested style:
    $result = matchResult(
        andThen(
            $divide($start, $first),
        )(fn($x) => $divide((int) $x, $second)),
    )(
        fn($e) => "Error: $e",
        fn($v) => "Result: $v"
    );

    echo sprintf("%d / %d / %d = %s\n", $start, $first, $second, $result);
}

echo "\n";

// Example 3: Validate and process age
echo "Example 3: Validate Age\n";
echo "-----------------------\n";

$validateAge = fn(?int $age)
    => $age !== null && $age >= 18 && $age <= 100
        ? Ok($age)
        : Err($age === null ? "No age provided" : "Age must be between 18 and 100");

$formatAge = fn(int $age): string => "Age: $age years old";

$ages = [25, 200, null, 18, 17];

foreach ($ages as $age) {
    // With PHP 8.5 pipe operator:
    // $result = $age
    //     |> $validateAge(...)
    //     |> map(...)($formatAge)
    //     |> unwrapOr(...)('Invalid age');

    // For now, using the traditional method chaining (works in PHP 8.3+):
    $result = $validateAge($age)
        ->map($formatAge)
        ->unwrapOr('Invalid age');

    echo sprintf("Input: %s -> Result: %s\n", var_export($age, true), $result);
}

echo "\n";
echo "✓ All examples completed successfully!\n";
echo "\nNote: When PHP 8.5 is available, uncomment the pipe operator examples above.\n";
