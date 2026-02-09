<?php

declare(strict_types=1);

use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use Superscript\Monads\Result\Testing\ComparesResults;

use function Superscript\Monads\Result\Err;
use function Superscript\Monads\Result\Ok;

class ResultAssertionsTestCase extends TestCase
{
    use ComparesResults;
}

test('assertErr passes with Err result', function () {
    $errResult = Err('error message');

    ResultAssertionsTestCase::assertErr($errResult);

    expect(true)->toBeTrue(); // If we get here, the assertion passed
});

test('assertErr fails with Ok result', function () {
    $okResult = Ok('success value');

    expect(function () {
        ResultAssertionsTestCase::assertErr($okResult);
    })->toThrow(ExpectationFailedException::class);
});

test('assertErr fails with non-Result value', function () {
    expect(function () {
        ResultAssertionsTestCase::assertErr('not a result');
    })->toThrow(ExpectationFailedException::class);
});

test('assertOk passes with Ok result', function () {
    $okResult = Ok('success value');

    ResultAssertionsTestCase::assertOk($okResult);

    expect(true)->toBeTrue(); // If we get here, the assertion passed
});

test('assertOk fails with Err result', function () {
    $errResult = Err('error message');

    expect(function () {
        ResultAssertionsTestCase::assertOk($errResult);
    })->toThrow(ExpectationFailedException::class);
});

test('assertOk fails with non-Result value', function () {
    expect(function () {
        ResultAssertionsTestCase::assertOk('not a result');
    })->toThrow(ExpectationFailedException::class);
});

test('assertErr with custom message', function () {
    $okResult = Ok('success value');

    try {
        ResultAssertionsTestCase::assertErr($okResult, 'Custom error message');
        expect(false)->toBeTrue(); // Should not reach here
    } catch (ExpectationFailedException $e) {
        expect($e->getMessage())->toContain('Custom error message');
    }
});

test('assertOk with custom message', function () {
    $errResult = Err('error message');

    try {
        ResultAssertionsTestCase::assertOk($errResult, 'Custom error message');
        expect(false)->toBeTrue(); // Should not reach here
    } catch (ExpectationFailedException $e) {
        expect($e->getMessage())->toContain('Custom error message');
    }
});

test('isErr constraint returns IsErr instance', function () {
    $constraint = ResultAssertionsTestCase::isErr();

    expect($constraint)->toBeInstanceOf(\Superscript\Monads\Result\Testing\IsErr::class);
});

test('isOk constraint returns IsOk instance', function () {
    $constraint = ResultAssertionsTestCase::isOk();

    expect($constraint)->toBeInstanceOf(\Superscript\Monads\Result\Testing\IsOk::class);
});
