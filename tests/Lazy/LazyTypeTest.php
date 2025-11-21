<?php

declare(strict_types=1);

namespace Superscript\Monads\Tests\Lazy;

use PHPStan\Testing\TypeInferenceTestCase;

class LazyTypeTest extends TypeInferenceTestCase
{
    /**
     * @return array<array-key, mixed>
     */
    public static function providesTypeAssertions(): array
    {
        return self::gatherAssertTypes(__DIR__ . '/types.php');
    }

    /**
     * @dataProvider providesTypeAssertions
     */
    public function testFileAsserts(
        string $assertType,
        string $file,
        mixed ...$args,
    ): void {
        $this->assertFileAsserts($assertType, $file, ...$args);
    }
}
