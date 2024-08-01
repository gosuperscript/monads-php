<?php

declare(strict_types=1);

namespace Superscript\Monads\Lazy;

use Closure;

/**
 * @template-covariant T
 */
final class Lazy
{
    private bool $evaluated = false;

    /** @var T */
    private mixed $value;

    private function __construct(
        /** @var Closure(): T */
        private readonly Closure $callable,
    ) {}

    /**
     * @template TReturn
     * @param Closure(): TReturn $callable
     * @return self<TReturn>
     */
    public static function of(Closure $callable): self
    {
        return new self($callable);
    }

    /**
     * @return T
     */
    public function evaluate(): mixed
    {
        if (! $this->evaluated) {
            $this->value = ($this->callable)();
            $this->evaluated = true;
        }

        return $this->value;
    }
}
