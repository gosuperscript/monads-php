<?php

use Superscript\Monads\Writer\Writer;

use function PHPStan\Testing\assertType;
use function Superscript\Monads\Writer\Writer;

$writer = Writer(42);
assertType('Superscript\Monads\Writer\Writer<list<mixed>, int>', $writer);
assertType('int', $writer->value());
assertType('list<mixed>', $writer->log());
assertType('array{int, list<mixed>}', $writer->run());

$mapped = Writer(42)->map(fn(int $x): string => (string) $x);
assertType('Superscript\Monads\Writer\Writer<list<mixed>, string>', $mapped);
assertType('string', $mapped->value());

$chained = Writer(42)->andThen(fn(int $x) => Writer($x * 2, ['doubled']));
assertType('Superscript\Monads\Writer\Writer<list<mixed>, int>', $chained);

$told = Writer(42)->tell(['entry']);
assertType('Superscript\Monads\Writer\Writer<list<mixed>, int>', $told);

$inspected = Writer(42)->inspect(fn(int $x) => null);
assertType('Superscript\Monads\Writer\Writer<list<mixed>, int>', $inspected);

$listened = Writer(42)->listen(fn(int $value, $log) => 'result');
assertType('Superscript\Monads\Writer\Writer<list<mixed>, string>', $listened);

$custom = Writer::of('hello', '', fn(string $a, string $b): string => $a . $b);
assertType('Superscript\Monads\Writer\Writer<string, string>', $custom);
assertType('string', $custom->value());
assertType('string', $custom->log());
