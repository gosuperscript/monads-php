# Monads for PHP

[![Latest Version on Packagist](https://img.shields.io/packagist/v/gosuperscript/monads.svg?style=flat-square)](https://packagist.org/packages/gosuperscript/monads)
[![Tests](https://img.shields.io/github/actions/workflow/status/gosuperscript/monads-php/ci.yaml?branch=main&label=tests&style=flat-square)](https://github.com/gosuperscript/monads-php/actions)
[![License](https://img.shields.io/packagist/l/gosuperscript/monads?style=flat-square)](https://packagist.org/packages/gosuperscript/monads)

A collection of useful monads for PHP 8.3+. Inspired by Rust's powerful type system and functional programming patterns.

## Features

- 🦀 **Rust-inspired API** - Familiar methods for those coming from Rust
- 🔒 **Type-safe** - Full PHPStan level 9 support with generics
- 🧪 **Well-tested** - Comprehensive test suite
- 📦 **Zero dependencies** - Lightweight and focused
- 🎯 **Three core monads**:
  - **`Option<T>`** - Represent optional values without null
  - **`Result<T, E>`** - Handle errors without exceptions
  - **`Lazy<T>`** - Defer computation until needed

## Installation

You can install the package via Composer:

```bash
composer require gosuperscript/monads
```

## Requirements

- PHP 8.3 or higher

## Usage

### Option Monad

The `Option` type represents an optional value: every `Option` is either `Some` and contains a value, or `None`, and does not. This is a safer alternative to using `null`.

```php
use function Superscript\Monads\Option\{Some, None};

// Create an Option
$some = Some(42);
$none = None();

// Check if value exists
$some->isSome(); // true
$none->isNone(); // true

// Transform the value
$doubled = Some(21)->map(fn($x) => $x * 2); // Some(42)
$empty = None()->map(fn($x) => $x * 2);    // None

// Provide default values
Some(42)->unwrapOr(0);  // 42
None()->unwrapOr(0);    // 0

// Chain operations
Some(10)
    ->filter(fn($x) => $x > 5)
    ->map(fn($x) => $x * 2)
    ->unwrapOr(0); // 20

// Convert to Result
Some(42)->okOr("error");  // Ok(42)
None()->okOr("error");    // Err("error")
```

#### Key Option Methods

- `isSome()` / `isNone()` - Check if the option contains a value
- `isSomeAnd(callable $predicate)` - Check if Some and matches predicate
- `map(callable $f)` - Transform the contained value
- `filter(callable $f)` - Filter based on a predicate
- `and(Option $other)` / `or(Option $other)` - Combine options
- `andThen(callable $f)` - Chain operations (flatMap)
- `unwrap()` - Get the value (throws if None)
- `unwrapOr($default)` - Get the value or a default
- `unwrapOrElse(callable $f)` - Get the value or compute a default
- `expect(string|Throwable $message)` - Unwrap with custom error message

### Result Monad

`Result<T, E>` is the type used for returning and propagating errors. It is either `Ok(T)`, representing success and containing a value, or `Err(E)`, representing error and containing an error value.

```php
use function Superscript\Monads\Result\{Ok, Err, attempt};

// Create Results
$ok = Ok(42);
$err = Err("something went wrong");

// Check the result
$ok->isOk();   // true
$err->isErr(); // true

// Transform success values
$doubled = Ok(21)->map(fn($x) => $x * 2); // Ok(42)
$stillErr = Err("error")->map(fn($x) => $x * 2); // Err("error")

// Transform error values
$recovered = Err("error")->mapErr(fn($e) => "recovered"); // Err("recovered")

// Handle both cases
$result = Ok(10)->match(
    err: fn($e) => "Error: $e",
    ok: fn($x) => "Success: $x"
); // "Success: 10"

// Chain operations
Ok(10)
    ->map(fn($x) => $x * 2)
    ->andThen(fn($x) => $x > 15 ? Ok($x) : Err("too small"))
    ->unwrapOr(0); // 20

// Convert to Option
Ok(42)->ok();   // Some(42)
Err("e")->ok(); // None()

// Safely execute code that might throw
$result = attempt(fn() => json_decode($json, flags: JSON_THROW_ON_ERROR));
// Returns: Result<mixed, Throwable>
```

#### Key Result Methods

- `isOk()` / `isErr()` - Check if the result is success or error
- `map(callable $f)` - Transform the success value
- `mapErr(callable $f)` - Transform the error value
- `mapOr($default, callable $f)` - Transform or provide default
- `mapOrElse(callable $default, callable $f)` - Transform or compute default
- `match(callable $err, callable $ok)` - Handle both cases
- `and(Result $other)` / `or(Result $other)` - Combine results
- `andThen(callable $f)` - Chain operations (flatMap)
- `unwrap()` - Get the success value (throws if Err)
- `unwrapErr()` - Get the error value (throws if Ok)
- `unwrapOr($default)` - Get the value or a default
- `unwrapOrElse(callable $f)` - Get the value or compute a default
- `expect(string|Throwable $message)` - Unwrap with custom error message

### Lazy Monad

The `Lazy` type allows you to defer the execution of a computation until its result is actually needed.

```php
use Superscript\Monads\Lazy\Lazy;

// Create a lazy computation
$lazy = Lazy::of(fn() => expensiveComputation());

// The computation hasn't run yet...

// Evaluate when needed (memoized)
$result = $lazy->evaluate(); // Runs the computation
$cached = $lazy->evaluate(); // Returns cached result

// Practical example: lazy database query
$users = Lazy::of(fn() => DB::query("SELECT * FROM users"));

if ($needUsers) {
    $data = $users->evaluate(); // Query runs only if needed
}
```

### Collection Operations

Both `Option` and `Result` support collecting arrays of values:

```php
use function Superscript\Monads\Option\{Some, None};
use function Superscript\Monads\Result\{Ok, Err};

// Collect Options - returns first None or Some(array)
Option::collect([Some(1), Some(2), Some(3)]); // Some([1, 2, 3])
Option::collect([Some(1), None(), Some(3)]);  // None()

// Collect Results - returns first Err or Ok(array)
Result::collect([Ok(1), Ok(2), Ok(3)]);     // Ok([1, 2, 3])
Result::collect([Ok(1), Err("e"), Ok(3)]);  // Err("e")
```

### Practical Examples

#### Safe Array Access

```php
use Superscript\Monads\Option\Option;

function getUser(int $id): Option {
    $user = DB::find('users', $id);
    return Option::from($user); // Returns None if null
}

$username = getUser(123)
    ->map(fn($user) => $user->name)
    ->unwrapOr('Guest');
```

#### Error Handling Without Exceptions

```php
use function Superscript\Monads\Result\{Ok, Err, attempt};

function divide(int $a, int $b): Result {
    return $b === 0 
        ? Err("Division by zero")
        : Ok($a / $b);
}

$result = divide(10, 2)
    ->map(fn($x) => $x * 2)
    ->unwrapOr(0); // 10

$error = divide(10, 0)
    ->map(fn($x) => $x * 2)
    ->unwrapOr(0); // 0
```

#### Pipeline Processing

```php
use function Superscript\Monads\Result\{Ok, Err};

function processData(array $data): Result {
    return Ok($data)
        ->andThen(fn($d) => validateData($d))
        ->andThen(fn($d) => transformData($d))
        ->andThen(fn($d) => saveData($d));
}

$result = processData($input)->match(
    err: fn($e) => response()->json(['error' => $e], 400),
    ok: fn($d) => response()->json(['data' => $d], 200)
);
```

## Testing

The package uses [Pest](https://pestphp.com/) for testing:

```bash
# Run tests
vendor/bin/pest

# Run type checking
vendor/bin/phpstan

# Run code style fixer
vendor/bin/pint
```

## PHPStan Integration

This library provides full PHPStan support with generic types. The testing utilities include:

```php
use Superscript\Monads\Result\Testing\ComparesResults;
use Superscript\Monads\Option\Testing\ComparesOptions;

class MyTest extends TestCase {
    use ComparesResults;
    use ComparesOptions;
    
    public function test_example() {
        // Custom assertions
        $this->assertOk(Ok(42));
        $this->assertErr(Err("error"));
        $this->assertSome(Some(42));
        $this->assertNone(None());
        
        // PHPUnit constraints
        $this->assertThat(Ok(42), $this->isOk());
        $this->assertThat(Err("e"), $this->isErr());
    }
}
```

## Why Monads?

Monads help you write more predictable and maintainable code by:

1. **Making errors explicit** - No hidden nulls or uncaught exceptions
2. **Enabling composition** - Chain operations cleanly with `map` and `andThen`
3. **Improving type safety** - Let PHPStan catch errors at analysis time
4. **Reducing boilerplate** - Less null checking and try-catch blocks

## Inspiration

This library is heavily inspired by Rust's [`Option`](https://doc.rust-lang.org/std/option/) and [`Result`](https://doc.rust-lang.org/std/result/) types, bringing similar patterns to PHP.

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
