<?php

declare(strict_types=1);

namespace Superscript\Monads\Result;

use RuntimeException;
use Superscript\Monads\Option\None;
use Superscript\Monads\Option\Option;
use Superscript\Monads\Option\Some;
use Throwable;
use function Superscript\Monads\Option\Some;

/**
 * @template E
 *
 * @extends Result<never, E>
 */
final readonly class Err extends Result
{
    /**
     * @param  E  $err
     */
    public function __construct(
        private mixed $err,
    ) {}

    public function and(Result $other): Result
    {
        return $this;
    }

    public function andThen(callable $other): Result
    {
        return $this;
    }

    public function err(): Option
    {
        return new Some($this->err);
    }

    public function expect(string|Throwable $message): mixed
    {
        if ($message instanceof Throwable) {
            throw $message;
        }

        if ($this->err instanceof Throwable) {
            throw new RuntimeException($message, previous: $this->err);
        }

        throw new RuntimeException($message);
    }

    public function expectErr(string|Throwable $message): mixed
    {
        return $this->err;
    }

    public function inspect(callable $f): Result
    {
        return $this;
    }

    public function inspectErr(callable $f): Result
    {
        $f($this->err);

        return $this;
    }

    /**
     * @return E
     */
    public function intoErr(): mixed
    {
        return $this->err;
    }

    public function isErr(): bool
    {
        return true;
    }

    public function isOk(): bool
    {
        return false;
    }

    public function map(callable $op): Result
    {
        return $this;
    }

    public function mapErr(callable $op): Result
    {
        return new self($op($this->err));
    }

    public function mapOr(mixed $default, callable $f): mixed
    {
        return $default;
    }

    public function mapOrElse(callable $default, callable $f): mixed
    {
        return $default($this->err);
    }

    public function ok(): Option
    {
        return new None();
    }

    public function or(Result $other): Result
    {
        return $other;
    }

    public function orElse(callable $op): Result
    {
        return $op($this->err);
    }

    public function unwrap(): mixed
    {
        throw CannotUnwrapErr::make($this);
    }

    public function unwrapEither(): mixed
    {
        return $this->err;
    }

    public function unwrapErr(): mixed
    {
        return $this->err;
    }

    public function unwrapOr(mixed $default): mixed
    {
        return $default;
    }

    public function unwrapOrElse(callable $op): mixed
    {
        return $op($this->err);
    }

    public function transpose(): Option
    {
        return Some(new self($this->err));
    }
}
