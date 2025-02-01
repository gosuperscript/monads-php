<?php

declare(strict_types=1);

namespace Superscript\Monads\Option;

use RuntimeException;
use Superscript\Monads\Result\Result;

use Throwable;

use function Superscript\Monads\Result\Err;
use function Superscript\Monads\Result\Ok;

/**
 * @extends Option<never>
 */
final readonly class None extends Option
{
    public function and(Option $other): Option
    {
        return $this;
    }

    public function andThen(callable $f): Option
    {
        return $this;
    }

    public function expect(string|Throwable $message): mixed
    {
        throw $message instanceof Throwable ? $message : new RuntimeException($message);
    }

    public function filter(callable $f): Option
    {
        return $this;
    }

    /**
     * @return $this
     */
    public function inspect(callable $f): self
    {
        return $this;
    }

    public function isNone(): bool
    {
        return true;
    }

    public function isSome(): bool
    {
        return false;
    }

    public function isSomeAnd(callable $f): bool
    {
        return false;
    }

    public function map(callable $f): Option
    {
        return $this;
    }

    public function mapOr(mixed $default, callable $f): mixed
    {
        return $default;
    }

    public function mapOrElse(callable $default, callable $f): mixed
    {
        return $default();
    }

    public function okOr(mixed $err): Result
    {
        return Err($err);
    }

    public function okOrElse(callable $err): Result
    {
        return Err($err());
    }

    public function or(Option $other): Option
    {
        return $other;
    }

    public function orElse(callable $other): Option
    {
        return $other();
    }

    public function unwrap(): mixed
    {
        throw new CannotUnwrapNone();
    }

    public function unwrapOr(mixed $default): mixed
    {
        return $default;
    }

    public function unwrapOrElse(callable $default): mixed
    {
        return $default();
    }

    public function xor(Option $other): Option
    {
        return $other;
    }

    public function transpose(): Result
    {
        return Ok(new self());
    }
}
