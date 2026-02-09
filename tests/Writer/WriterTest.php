<?php

declare(strict_types=1);

use Superscript\Monads\Writer\Writer;

use function Superscript\Monads\Writer\Writer;

test('value', function () {
    expect(Writer(42)->value())->toBe(42);
    expect(Writer('hello')->value())->toBe('hello');
});

test('log', function () {
    expect(Writer(42)->log())->toBe([]);
    expect(Writer(42, ['created'])->log())->toBe(['created']);
});

test('run', function () {
    expect(Writer(42, ['created'])->run())->toBe([42, ['created']]);
});

test('map', function () {
    $writer = Writer(10, ['initial'])
        ->map(fn($x) => $x * 2);

    expect($writer->value())->toBe(20);
    expect($writer->log())->toBe(['initial']);
});

test('and then', function () {
    $writer = Writer(10, ['start'])
        ->andThen(fn($x) => Writer($x * 2, ['doubled']))
        ->andThen(fn($x) => Writer($x + 1, ['incremented']));

    expect($writer->value())->toBe(21);
    expect($writer->log())->toBe(['start', 'doubled', 'incremented']);
});

test('tell', function () {
    $writer = Writer(42, ['initial'])
        ->tell(['extra entry']);

    expect($writer->value())->toBe(42);
    expect($writer->log())->toBe(['initial', 'extra entry']);
});

test('map log', function () {
    $writer = Writer(42, ['a', 'b', 'c'])
        ->mapLog(fn($log) => array_map('strtoupper', $log));

    expect($writer->value())->toBe(42);
    expect($writer->log())->toBe(['A', 'B', 'C']);
});

test('inspect', function () {
    $inspected = null;

    $writer = Writer(42, ['log'])
        ->inspect(function ($value) use (&$inspected) {
            $inspected = $value;
        });

    expect($inspected)->toBe(42);
    expect($writer->value())->toBe(42);
    expect($writer->log())->toBe(['log']);
});

test('reset', function () {
    $writer = Writer(42, ['a', 'b', 'c'])
        ->reset([]);

    expect($writer->value())->toBe(42);
    expect($writer->log())->toBe([]);
});

test('listen', function () {
    $writer = Writer(42, ['a', 'b'])
        ->listen(fn($value, $log) => [$value, count($log)]);

    expect($writer->value())->toBe([42, 2]);
    expect($writer->log())->toBe(['a', 'b']);
});

test('chaining preserves immutability', function () {
    $original = Writer(10, ['start']);
    $mapped = $original->map(fn($x) => $x * 2);

    expect($original->value())->toBe(10);
    expect($original->log())->toBe(['start']);
    expect($mapped->value())->toBe(20);
    expect($mapped->log())->toBe(['start']);
});

test('of with custom combiner', function () {
    $writer = Writer::of('hello', '', fn(string $a, string $b): string => $a . $b)
        ->tell(' world')
        ->andThen(fn($v) => Writer::of(strtoupper($v), '!', fn(string $a, string $b): string => $a . $b));

    expect($writer->value())->toBe('HELLO');
    expect($writer->log())->toBe(' world!');
});

test('pipeline with logging', function () {
    $addTax = fn($price) => Writer(
        $price * 1.2,
        [sprintf('Added tax: %.2f -> %.2f', $price, $price * 1.2)],
    );

    $applyDiscount = fn($price) => Writer(
        $price * 0.9,
        [sprintf('Applied 10%% discount: %.2f -> %.2f', $price, $price * 0.9)],
    );

    $writer = Writer(100.0, ['Starting price: 100.00'])
        ->andThen($addTax)
        ->andThen($applyDiscount);

    expect($writer->value())->toBe(108.0);
    expect($writer->log())->toHaveCount(3);
});
