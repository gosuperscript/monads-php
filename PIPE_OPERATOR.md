# PHP 8.5 Pipe Operator Support

This document demonstrates how to use PHP 8.5's new pipe operator (`|>`) with the monads library.

## What is the Pipe Operator?

The pipe operator (`|>`) allows you to chain function calls in a more readable, left-to-right manner. Instead of deeply nested function calls or intermediate variables, you can write:

```php
$result = $value
    |> trim(...)
    |> strtoupper(...)
    |> fn($x) => str_replace(' ', '-', $x);
```

This is equivalent to:
```php
$result = str_replace(' ', '-', strtoupper(trim($value)));
```

## Using the Pipe Operator with Monads

### Option Monad with Pipe Operator

The library provides pipe-friendly helper functions in the `Superscript\Monads\Option\Pipe` namespace:

```php
use function Superscript\Monads\Option\Pipe\{option, map, filter, andThen, unwrapOr};

// Process user input with validation
$username = $rawInput
    |> option(...)                              // Wrap in Option
    |> map(fn($x) => trim($x))                 // Trim whitespace
    |> filter(fn($x) => strlen($x) > 0)        // Filter empty strings
    |> map(fn($x) => strtolower($x))           // Convert to lowercase
    |> unwrapOr('guest');                      // Provide default

// Safe array access
$users = [
    1 => ['name' => 'Alice'],
    2 => ['name' => 'Bob'],
];

$userName = $userId
    |> fn($id) => $users[$id] ?? null
    |> option(...)
    |> map(fn($u) => $u['name'])
    |> unwrapOr('Unknown');

// Validation with flatMap
$validateAge = fn(?int $age) => 
    $age !== null && $age >= 18 && $age <= 100 
        ? Some($age) 
        : None();

$result = $userAge
    |> $validateAge(...)
    |> map(fn($a) => "Age: $a years")
    |> unwrapOr('Invalid age');
```

### Result Monad with Pipe Operator

The library provides pipe-friendly helper functions in the `Superscript\Monads\Result\Pipe` namespace:

```php
use function Superscript\Monads\Result\Pipe\{toOk, map, andThen, mapErr, unwrapOr, matchResult};

// Validate and process numbers
$parseInt = fn(string $s): int => (int) $s;
$validate = fn(int $x) => $x > 0 ? Ok($x) : Err("Must be positive");
$double = fn(int $x): int => $x * 2;

$result = $input
    |> toOk(...)
    |> map($parseInt)
    |> andThen($validate)
    |> map($double)
    |> unwrapOr(0);

// Safe division chain
$divide = fn(int $a, int $b) => 
    $b === 0 ? Err("Division by zero") : Ok($a / $b);

$result = 100
    |> fn($x) => $divide($x, 2)
    |> andThen(fn($x) => $divide((int)$x, 5))
    |> unwrapOr(0);

// Error handling with match
$result = $value
    |> toOk(...)
    |> map(fn($x) => $x * 2)
    |> matchResult(
        fn($e) => "Error: $e",
        fn($v) => "Success: $v"
    );

// Parse JSON with validation
$parseJson = function (string $json) {
    try {
        return Ok(json_decode($json, true, 512, JSON_THROW_ON_ERROR));
    } catch (Exception $e) {
        return Err("Invalid JSON");
    }
};

$validateUser = fn(array $data) => 
    isset($data['name']) && isset($data['email'])
        ? Ok($data)
        : Err("Missing required fields");

$extractName = fn(array $user): string => $user['name'];

$userName = $jsonString
    |> $parseJson(...)
    |> andThen($validateUser)
    |> map($extractName)
    |> unwrapOr('Unknown');
```

## Method Chaining (Works in PHP 8.3+)

The monads already work great with the pipe operator because they support method chaining:

```php
// Option chaining with pipe
$result = $value
    |> Some(...)
    |> fn($opt) => $opt->map(fn($x) => $x * 2)
    |> fn($opt) => $opt->filter(fn($x) => $x > 10)
    |> fn($opt) => $opt->unwrapOr(0);

// Or using the traditional method chaining (PHP 8.3+)
$result = Some($value)
    ->map(fn($x) => $x * 2)
    ->filter(fn($x) => $x > 10)
    ->unwrapOr(0);
```

## Real-World Examples

### Example 1: Processing Form Input

```php
use function Superscript\Monads\Option\Pipe\{option, map, filter, unwrapOr};

// Clean and validate email
$cleanEmail = $formData['email'] ?? null
    |> option(...)
    |> map(fn($e) => trim($e))
    |> filter(fn($e) => filter_var($e, FILTER_VALIDATE_EMAIL))
    |> map(fn($e) => strtolower($e))
    |> unwrapOr(null);

if ($cleanEmail === null) {
    throw new ValidationException('Invalid email');
}
```

### Example 2: Database Query with Error Handling

```php
use function Superscript\Monads\Result\Pipe\{toOk, andThen, map, unwrapOr};

$findUser = fn(int $id) => 
    DB::find('users', $id) 
        ? Ok(DB::find('users', $id)) 
        : Err("User not found");

$validateUser = fn($user) => 
    $user['active'] 
        ? Ok($user) 
        : Err("User is inactive");

$formatUser = fn($user) => [
    'id' => $user['id'],
    'name' => $user['name'],
    'email' => $user['email'],
];

$userData = $userId
    |> $findUser(...)
    |> andThen($validateUser)
    |> map($formatUser)
    |> unwrapOr(null);
```

### Example 3: Configuration Loading

```php
use function Superscript\Monads\Result\Pipe\{toOk, map, andThen, matchResult};

$loadConfig = function(string $path) {
    if (!file_exists($path)) {
        return Err("Config file not found");
    }
    
    try {
        $content = file_get_contents($path);
        $config = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
        return Ok($config);
    } catch (Exception $e) {
        return Err("Invalid config format");
    }
};

$validateConfig = fn(array $config) =>
    isset($config['app_name']) && isset($config['version'])
        ? Ok($config)
        : Err("Missing required config keys");

$config = $configPath
    |> $loadConfig(...)
    |> andThen($validateConfig)
    |> matchResult(
        fn($err) => throw new ConfigException($err),
        fn($cfg) => $cfg
    );
```

## Helper Functions Reference

### Option Pipe Helpers

- `option($value)` - Wrap value in Option (Some if non-null, None if null)
- `map($fn)` - Transform the contained value
- `filter($predicate)` - Filter based on predicate
- `andThen($fn)` - FlatMap operation
- `unwrapOr($default)` - Extract value or return default
- `isSomeAnd($predicate)` - Check if Some and satisfies predicate

### Result Pipe Helpers

- `toOk($value)` - Wrap value in Ok
- `toErr($error)` - Wrap error in Err
- `map($fn)` - Transform the success value
- `mapErr($fn)` - Transform the error value
- `andThen($fn)` - FlatMap operation
- `unwrapOr($default)` - Extract value or return default
- `matchResult($errFn, $okFn)` - Handle both cases
- `matchResult($result)($errFn, $okFn)` - Handle both cases

## Why Pipe-Friendly Helpers?

While the monads already have great method chaining APIs, the pipe-friendly helpers make it easier to compose operations in a functional style:

1. **Clearer data flow** - Read operations left-to-right
2. **Better composition** - Easier to build reusable pipelines
3. **Functional style** - Matches the pipe operator's functional paradigm
4. **Point-free style** - Can pass functions directly without wrapping

## Migration Guide

If you're already using the library, you don't need to change anything! The traditional method chaining still works:

```php
// Traditional (still works great)
$result = Some($value)
    ->map(fn($x) => $x * 2)
    ->filter(fn($x) => $x > 10)
    ->unwrapOr(0);

// With pipe operator (PHP 8.5+)
$result = $value
    |> Some(...)
    |> fn($opt) => $opt->map(fn($x) => $x * 2)
    |> fn($opt) => $opt->filter(fn($x) => $x > 10)
    |> fn($opt) => $opt->unwrapOr(0);

// With pipe helpers (most functional)
$result = $value
    |> option(...)
    |> map(fn($x) => $x * 2)
    |> filter(fn($x) => $x > 10)
    |> unwrapOr(0);
```

Choose the style that best fits your codebase and preferences!
