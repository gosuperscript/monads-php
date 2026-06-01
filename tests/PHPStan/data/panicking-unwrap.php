<?php

declare(strict_types=1);

namespace Superscript\Monads\Tests\PHPStan\data;

use Superscript\Monads\Option\None;
use Superscript\Monads\Option\Option;
use Superscript\Monads\Option\Some;
use Superscript\Monads\Result\Err;
use Superscript\Monads\Result\Ok;
use Superscript\Monads\Result\Result;

/** @var Result<int, string> $result */
$result->unwrap();    // flagged: Result::unwrap()
$result->unwrapErr(); // flagged: Result::unwrapErr()
$result->match(fn($e) => $e, fn($v) => $v); // NOT flagged

/** @var Ok<int> $ok */
$ok->unwrap();        // NOT flagged: Ok::unwrap() is safe
fn () => $ok->unwrapErr();     // flagged: narrowed Ok

/** @var Err<string> $err */
fn () => $err->unwrap();       // flagged: narrowed Err

/** @var Option<int> $option */
$option->unwrap();    // flagged: Option::unwrap()
$option->unwrapOr(0); // NOT flagged

/** @var Some<int> $some */
$some->unwrap();      // NOT flagged: Some::unwrap() is safe

/** @var None $none */
fn () => $none->unwrap();      // flagged: narrowed None
