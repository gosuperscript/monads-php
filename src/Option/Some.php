<?php

declare(strict_types=1);

namespace Superscript\Monads\Option;

use InvalidArgumentException;
use Superscript\Monads\Result\Result;
use Throwable;

use function Superscript\Monads\Result\Ok;

/**
 * @template-covariant TValue
 *
 * @extends Option<TValue>
 */
final readonly class Some extends Option
{
    /**
     * @param  TValue  $value
     */
    public function __construct(public mixed $value) {}

    public function and(Option $other): Option
    {
        return $other;
    }

    public function andThen(callable $f): Option
    {
        return $f($this->value);
    }

    public function expect(string|Throwable $message): mixed
    {
        return $this->value;
    }

    public function filter(callable $f): Option
    {
        if ($f($this->value)) {
            return $this;
        }

        return None();
    }

    /**
     * @return self<TValue>
     */
    public function inspect(callable $f): self
    {
        $f($this->value);

        return $this;
    }

    public function isNone(): bool
    {
        return false;
    }

    public function isSome(): bool
    {
        return true;
    }

    public function isSomeAnd(callable $f): bool
    {
        return $f($this->value);
    }

    public function map(callable $f): Option
    {
        return new self($f($this->value));
    }

    public function mapOr(mixed $default, callable $f): mixed
    {
        return $f($this->value);
    }

    public function mapOrElse(callable $default, callable $f): mixed
    {
        return $f($this->value);
    }

    public function okOr(mixed $err): Result
    {
        return Ok($this->value);
    }

    public function okOrElse(callable $err): Result
    {
        return Ok($this->value);
    }

    public function or(Option $other): Option
    {
        return $this;
    }

    public function orElse(callable $other): Option
    {
        return $this;
    }

    public function unwrap(): mixed
    {
        return $this->value;
    }

    public function unwrapOr(mixed $default): mixed
    {
        return $this->value;
    }

    public function unwrapOrElse(callable $default): mixed
    {
        return $this->value;
    }

    public function xor(Option $other): Option
    {
        return $other->isNone() ? $this : None();
    }

    public function transpose(): Result
    {
        if (! $this->value instanceof Result) {
            throw new InvalidArgumentException('Cannot transpose a Some value that is not a Result');
        }

        return $this->value->andThen(fn($value) => Ok(new self($value)));
    }
}
