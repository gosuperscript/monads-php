<?php

declare(strict_types=1);

namespace Superscript\Monads\Writer;

use Closure;

/**
 * A Writer monad that carries a value alongside an accumulated log.
 *
 * The log is combined using a provided combiner function, allowing
 * flexible log types (arrays, strings, or any monoid-like type).
 *
 * @template W The log type
 * @template-covariant T The value type
 */
final readonly class Writer
{
    /**
     * @param T $value
     * @param W $log
     * @param Closure(W, W): W $combiner
     */
    private function __construct(
        private mixed $value,
        private mixed $log,
        private Closure $combiner,
    ) {}

    /**
     * Create a Writer with a value, initial log, and combiner function.
     *
     * @template WNew
     * @template TNew
     *
     * @param TNew $value
     * @param WNew $log
     * @param Closure(WNew, WNew): WNew $combiner
     * @return self<WNew, TNew>
     */
    public static function of(mixed $value, mixed $log, Closure $combiner): self
    {
        return new self($value, $log, $combiner);
    }

    /**
     * Returns the contained value.
     *
     * @return T
     */
    public function value(): mixed
    {
        return $this->value;
    }

    /**
     * Returns the accumulated log.
     *
     * @return W
     */
    public function log(): mixed
    {
        return $this->log;
    }

    /**
     * Returns both the value and the log as a tuple.
     *
     * @return array{T, W}
     */
    public function run(): array
    {
        return [$this->value, $this->log];
    }

    /**
     * Transforms the value by applying a function, leaving the log unchanged.
     *
     * @template U
     *
     * @param callable(T): U $f
     * @return self<W, U>
     */
    public function map(callable $f): self
    {
        return new self($f($this->value), $this->log, $this->combiner);
    }

    /**
     * Chains a computation that returns a Writer, combining the logs.
     *
     * Some languages call this operation flatmap or bind.
     *
     * @template U
     *
     * @param callable(T): self<W, U> $f
     * @return self<W, U>
     */
    public function andThen(callable $f): self
    {
        /** @var self<W, U> $result */
        $result = $f($this->value);

        return new self(
            $result->value,
            ($this->combiner)($this->log, $result->log),
            $this->combiner,
        );
    }

    /**
     * Appends an entry to the log without changing the value.
     *
     * @param W $entry
     * @return self<W, T>
     */
    public function tell(mixed $entry): self
    {
        return new self(
            $this->value,
            ($this->combiner)($this->log, $entry),
            $this->combiner,
        );
    }

    /**
     * Transforms the log by applying a function, leaving the value unchanged.
     *
     * @param callable(W): W $f
     * @return self<W, T>
     */
    public function mapLog(callable $f): self
    {
        return new self($this->value, $f($this->log), $this->combiner);
    }

    /**
     * Calls the provided closure with the contained value (for side effects).
     *
     * @param callable(T): void $f
     * @return self<W, T>
     */
    public function inspect(callable $f): self
    {
        $f($this->value);

        return $this;
    }

    /**
     * Resets the log to the given value, leaving the value unchanged.
     *
     * @param W $log
     * @return self<W, T>
     */
    public function reset(mixed $log): self
    {
        return new self($this->value, $log, $this->combiner);
    }

    /**
     * Accesses both the value and the log, returning a new Writer with the
     * callback's return value as the new value, and the log unchanged.
     *
     * @template U
     *
     * @param callable(T, W): U $f
     * @return self<W, U>
     */
    public function listen(callable $f): self
    {
        return new self($f($this->value, $this->log), $this->log, $this->combiner);
    }
}
