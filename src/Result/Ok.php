<?php

declare(strict_types=1);

namespace Superscript\Monads\Result;

use RuntimeException;
use Superscript\Monads\Option\None;
use Superscript\Monads\Option\Option;
use Superscript\Monads\Option\Some;
use Throwable;

/**
 * @template T
 *
 * @extends Result<T, never>
 */
final readonly class Ok extends Result
{
    /**
     * @param  T  $value
     */
    public function __construct(
        private mixed $value,
    ) {}

    public function and(Result $other): Result
    {
        return $other;
    }

    public function andThen(callable $other): Result
    {
        return $other($this->value);
    }

    public function err(): Option
    {
        return new None();
    }

    public function expect(string|Throwable $message): mixed
    {
        return $this->value;
    }

    public function expectErr(Throwable|string $message): mixed
    {
        throw $message instanceof Throwable ? $message : new RuntimeException($message);
    }

    public function inspect(callable $f): Result
    {
        $f($this->value);

        return $this;
    }

    public function inspectErr(callable $f): Result
    {
        return $this;
    }

    /**
     * @return T
     */
    public function intoOk(): mixed
    {
        return $this->value;
    }

    public function isErr(): bool
    {
        return false;
    }

    public function isOk(): bool
    {
        return true;
    }

    public function map(callable $op): Result
    {
        return new self($op($this->value));
    }

    public function mapErr(callable $op): Result
    {
        return $this;
    }

    public function mapOr(mixed $default, callable $f): mixed
    {
        return $f($this->value);
    }

    public function mapOrElse(callable $default, callable $f): mixed
    {
        return $f($this->value);
    }

    public function ok(): Option
    {
        return new Some($this->value);
    }

    public function or(Result $other): Result
    {
        return $this;
    }

    public function orElse(callable $op): Result
    {
        return $this;
    }

    public function unwrap(): mixed
    {
        return $this->value;
    }

    public function unwrapEither(): mixed
    {
        return $this->value;
    }

    public function unwrapErr(): mixed
    {
        throw new RuntimeException('Unwrapped with the expectation of an Err, but found Ok');
    }

    public function unwrapOr(mixed $default): mixed
    {
        return $this->value;
    }

    public function unwrapOrElse(callable $op): mixed
    {
        return $this->value;
    }
}
