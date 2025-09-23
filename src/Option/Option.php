<?php

declare(strict_types=1);

namespace Superscript\Monads\Option;

use Superscript\Monads\Result\Result;
use Throwable;

/**
 * @template-covariant T
 */
abstract readonly class Option
{
    /**
     * @template TItem
     * @param list<self<TItem>> $items
     * @return self<list<TItem>>
     */
    final public static function collect(array $items): self
    {
        $carry = [];

        foreach ($items as $item) {
            if ($item->isNone()) {
                return $item;
            }

            $carry[] = $item->unwrap();
        }

        return Some($carry);
    }

    /**
     * @template TParam
     *
     * @param self<TParam>|TParam|null $value
     * @return self<TParam>
     */
    final public static function from(mixed $value): self
    {
        if ($value instanceof self) {
            return $value;
        }

        return $value === null ? new None() : new Some($value);
    }

    /**
     * Returns None if the option is `None`, otherwise returns `other`.
     *
     * Arguments passed to and are eagerly evaluated; if you are passing the result of
     * a function call, it is recommended to use `andThen`, which is lazily evaluated.
     *
     * @template TOther
     *
     * @param Option<TOther> $other
     * @return Option<TOther>
     */
    abstract public function and(Option $other): Option;

    /**
     * Returns `None` if the option is `None`, otherwise calls `$f` with the wrapped
     * value and returns the result.
     *
     * Some languages call this operation flatmap.
     *
     * @template TOther
     *
     * @param callable(T): Option<TOther> $f
     * @return Option<TOther>
     */
    abstract public function andThen(callable $f): Option;

    /**
     * Returns the contained `Some` value.
     *
     * Throws if the value is a `None` with a custom message provided by `message`.
     *
     * @return T
     */
    abstract public function expect(string|Throwable $message): mixed;

    /**
     * Returns `None` if the option is `None`, otherwise calls `predicate` with the wrapped value and returns.
     *
     * `Some<T> if `predicate` returns `true`, and
     * None if `predicate` returns `false`.
     *
     * @param callable(T): bool $f
     * @return self<T>
     */
    abstract public function filter(callable $f): self;

    /**
     * Calls the provided closure with a reference to the contained value (if Some).
     *
     * @param callable(T): void $f
     * @return self<T>
     */
    abstract public function inspect(callable $f): self;

    /**
     * Returns `true` if the option is a `None` value.
     *
     * @phpstan-assert-if-true None $this
     * @phpstan-assert-if-false Some<T> $this
     */
    abstract public function isNone(): bool;

    /**
     * Returns `true` if the option is a `Some` value.
     *
     * @phpstan-assert-if-true Some<T> $this
     * @phpstan-assert-if-false None $this
     */
    abstract public function isSome(): bool;

    /**
     * Returns true if the option is a `Some` and the value inside of it matches a predicate.
     *
     * @param callable(T): bool $f
     * @phpstan-assert-if-true Some<T> $this
     */
    abstract public function isSomeAnd(callable $f): bool;

    /**
     * Maps an Option<T> to Option<TMap> by applying a function to a contained value (if `Some`) or returns `None` (if `None`).
     *
     * @template TMap
     *
     * @param callable(T): TMap $f
     * @return Option<TMap>
     */
    abstract public function map(callable $f): self;

    /**
     * @template TDefault
     * @template TMap
     *
     * @param TDefault $default
     * @param callable(T): TMap $f
     * @return TMap|TDefault
     */
    abstract public function mapOr(mixed $default, callable $f): mixed;

    /**
     * Computes a default function result (if none), or applies a different function to the contained value (if any).
     *
     * @template TDefault
     * @template TMap
     *
     * @param callable(): TDefault $default
     * @param callable(T): TMap $f
     * @return TMap|TDefault
     */
    abstract public function mapOrElse(callable $default, callable $f): mixed;

    /**
     * @template E
     *
     * @param E $err
     * @return Result<T, E>
     */
    abstract public function okOr(mixed $err): Result;

    /**
     * @template E
     *
     * @param callable(): E $err
     * @return Result<T, E>
     */
    abstract public function okOrElse(callable $err): Result;

    /**
     * Returns the option if it contains a value, otherwise returns `other`.
     *
     * Arguments passed to or are eagerly evaluated; if you are passing the result of a
     * function call, it is recommended to use orElse, which is lazily evaluated.
     *
     * @template TOther
     *
     * @param Option<TOther> $other
     * @return Option<T|TOther>
     */
    abstract public function or(self $other): self;

    /**
     * Returns the option if it contains a value, otherwise calls callable and returns the result.
     *
     * @template U
     *
     * @param callable(): Option<U> $other
     * @return Option<T|U>
     */
    abstract public function orElse(callable $other): Option;

    /**
     * Returns the contained `Some` value.
     *
     * Because this method may throw, its use is generally discouraged. Instead, prefer to use
     * match to handle the `None` case explicitly, or call `unwrapOr` or `unwrapOrElse`.
     *
     * @return T
     *
     * @throws CannotUnwrapNone
     */
    abstract public function unwrap(): mixed;

    /**
     * Returns the contained `Some` value or a provided default.
     *
     * Arguments passed to unwrap_or are eagerly evaluated; if you are passing the result of a
     * function call, it is recommended to use `unwrapOrElse`, which is lazily evaluated.
     *
     * @template TDefault
     *
     * @param TDefault $default
     * @return T|TDefault
     */
    abstract public function unwrapOr(mixed $default): mixed;

    /**
     * Returns the contained `Some` value or computes it from a closure.
     *
     * @template U
     *
     * @param callable():U $default
     * @return T|U
     */
    abstract public function unwrapOrElse(callable $default): mixed;

    /**
     * Returns `Some` if exactly one of `self`, `other` is Some, otherwise returns `None`.
     *
     * @template U
     *
     * @param Option<U> $other
     * @return Option<T>|Option<U>
     */
    abstract public function xor(Option $other): Option;

    /**
     * Transposes an Option of a Result into a Result of an Option.
     *
     * None will be mapped to Ok(None). Some(Ok(_)) and Some(Err(_)) will be mapped to Ok(Some(_)) and Err(_).
     *
     * @phpstan-return Result<Option<template-type<T, Result, 'T'>>, template-type<T, Result, 'E'>>
     */
    abstract public function transpose();
}
