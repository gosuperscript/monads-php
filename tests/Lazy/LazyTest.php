<?php

declare(strict_types=1);

use Superscript\Monads\Lazy\Lazy;

it('evaluates lazy', function () {
    $isEvaluated = false;

    $lazy = Lazy::of(function () use (&$isEvaluated) {
        $isEvaluated = true;
    });

    expect($isEvaluated)->toBeFalse();

    $lazy->evaluate();

    expect($isEvaluated)->toBeTrue();
});

it('evaluates once', function () {
    $evaluations = 0;

    $lazy = Lazy::of(function () use (&$evaluations) {
        $evaluations++;
    });

    expect($evaluations)->toBe(0);
    $lazy->evaluate();
    expect($evaluations)->toBe(1);
    $lazy->evaluate();
    expect($evaluations)->toBe(1);
});
