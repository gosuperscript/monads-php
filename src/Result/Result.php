<?php

declare(strict_types=1);

namespace Superscript\Monads\Result;

use Superscript\Monads\Option\Option;
use Throwable;

/**
 * @template-covariant T
 * @template-covariant E
 */
abstract readonly class Result
{
    /**
     * @template U
     * @template F
     *
     * @param list<Result<U, F>> $items
     * @return Result<list<U>, F>
     */
    final public static function collect(array $items): self
    {
        $carry = [];

        foreach ($items as $result) {
            if ($result->isErr()) {
                return $result;
            }

            $carry[] = $result->intoOk();
        }

        return Ok($carry);
    }

    /**
     * Returns res if the result is `Ok`, otherwise returns the `Err` value of self.
     *
     * Arguments passed to `and` are eagerly evaluated; if you are passing the result of a
     * function call, it is recommended to use `andThen`, which is lazily evaluated.
     *
     * @template U
     * @template F
     *
     * @param Result<U, F> $other
     * @return Result<U, E|F>
     */
    abstract public function and(Result $other): Result;

    /**
     * Calls op if the result is `Ok`, otherwise returns the `Err` value of self.
     *
     * This function can be used for control flow based on `Result` values.
     *
     * @template U
     * @template F
     *
     * @param callable(T): Result<U, F> $other
     * @return Result<U, E|F>
     */
    abstract public function andThen(callable $other): Result;

    /**
     * Converts from Result<T, E> to Option<E>.
     *
     * Converts self into an Option<E>, consuming self, and discarding the success value, if any.
     *
     * @return Option<E>
     */
    abstract public function err(): Option;

    /**
     * Returns the contained `Ok` value.
     *
     * Because this function may panic, its use is generally discouraged. Instead, prefer to use
     * matching and handle the `Err` case explicitly, or call `unwrapOr` or `unwrapOrElse`.
     *
     * @return T
     */
    abstract public function expect(string|Throwable $message): mixed;

    /**
     * Returns the contained `Err` value, consuming the `self` value.
     *
     * @param string|Throwable $message
     *
     * @return E
     */
    abstract public function expectErr(string|Throwable $message): mixed;

    /**
     * Calls the provided closure with a reference to the contained value (if `Ok`).
     *
     * @param callable(T): mixed $f
     * @return Result<T, E>
     */
    abstract public function inspect(callable $f): self;

    /**
     * Calls the provided closure with a reference to the contained error (if `Err`).
     *
     * @param callable(E): mixed $f
     * @return Result<T, E>
     */
    abstract public function inspectErr(callable $f): self;

    /**
     * Returns true is the result is Err.
     *
     * @phpstan-assert-if-true Err<E> $this
     * @phpstan-assert-if-false Ok<T> $this
     */
    abstract public function isErr(): bool;

    /**
     * Returns true if the result is `Ok`.
     *
     * @phpstan-assert-if-true Ok<T> $this
     * @phpstan-assert-if-false Err<E> $this
     */
    abstract public function isOk(): bool;

    /**
     * Maps a `Result<T, E>` to `Result<U, E>` by applying a function to a
     * contained `Ok` value, leaving an `Err` value untouched.
     *
     * This function can be used to compose the results of two functions.
     *
     * @template U
     *
     * @param callable(T): U $op
     * @return Result<U, E>
     */
    abstract public function map(callable $op): Result;

    /**
     * Maps a `Result<T, E>` to `Result<T, F>` by applying a function to a contained
     * `Err` value, leaving an `Ok` value untouched.
     *
     * This function can be used to pass through a successful result while handling an error.
     *
     * @template F
     *
     * @param callable(E): F $op
     * @return Result<T, F>
     */
    abstract public function mapErr(callable $op): Result;

    /**
     * Returns the provided default (if `Err`), or applies a function to the contained value (if `Ok`),
     * Arguments passed to `mapOr` are eagerly evaluated; if you are passing the result of a
     * function call, it is recommended to use `mapOrElse`, which is lazily evaluated.
     *
     * @template U
     *
     * @param U $default
     * @param callable(T): U $f
     * @return U
     */
    abstract public function mapOr(mixed $default, callable $f): mixed;

    /**
     * Maps a `Result<T, E>` to `U` by applying fallback function default to a contained `Err` value,
     * or function `f` to a contained `Ok` value.
     *
     * This function can be used to unpack a successful result while handling an error.
     *
     * @template U
     *
     * @param callable(E): U $default
     * @param callable(T): U $f
     * @return U
     */
    abstract public function mapOrElse(callable $default, callable $f): mixed;

    /**
     * Alias for `mapOrElse`.
     *
     * @template U
     *
     * @param callable(E): U $err
     * @param callable(T): U $ok
     * @return U
     */
    final public function match(callable $err, callable $ok): mixed
    {
        return $this->mapOrElse($err, $ok);
    }

    /**
     * Converts from `Result<T, E>` to `Option<T>`.
     * Converts `self` into an `Option<T>`, and discarding the error, if any.
     *
     * @return Option<T>
     */
    abstract public function ok(): Option;

    /**
     * Returns `other` if the result is `Err`, otherwise returns the `Ok` value of `self`.
     * Arguments passed to `or` are eagerly evaluated; if you are passing the result of a
     * function call, it is recommended to use `orElse`, which is lazily evaluated.
     *
     * @template U
     * @template F
     *
     * @param Result<U, F> $other
     * @return Result<T|U, F>
     */
    abstract public function or(Result $other): Result;

    /**
     * Calls `op` if the result is `Err`, otherwise returns the `Ok` value of `self`.
     *
     * @template U
     * @template F
     *
     * @param callable(E): Result<U, F> $op
     * @return Result<T|U, F>
     */
    abstract public function orElse(callable $op): Result;

    /**
     * Returns the contained `Ok` value, consuming the `self` value.
     *
     * Because this function may panic, its use is generally discouraged. Instead, prefer to use pattern
     * matching and handle the `Err` case explicitly, or call `unwrapOr` or `unwrapOrElse`.
     *
     * @return T
     */
    abstract public function unwrap(): mixed;

    /**
     * Returns the contained `Ok` or `Err` value.
     *
     * @return T|E
     */
    abstract public function unwrapEither(): mixed;

    /**
     * Returns the contained `Err` value, consuming the `self` value.
     * Throws if the value is an `Ok`.
     *
     * @return E
     */
    abstract public function unwrapErr(): mixed;

    /**
     * Returns the contained `Ok` value or a provided default.
     * Arguments passed to `unwrapOr` are eagerly evaluated; if you are passing the result of a
     * function call, it is recommended to use `unwrapOrElse`, which is lazily evaluated.
     *
     * @template U
     *
     * @param U $default
     * @return T|U
     */
    abstract public function unwrapOr(mixed $default): mixed;

    /**
     * Returns the contained `Ok` value or computes it from a closure.
     *
     * @template U
     *
     * @param callable(E): U $op
     * @return T|U
     */
    abstract public function unwrapOrElse(callable $op): mixed;
}
