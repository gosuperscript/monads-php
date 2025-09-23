<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Superscript\Monads\Result\Testing\ComparesResults;

use function Superscript\Monads\Result\Err;
use function Superscript\Monads\Result\Ok;

class DemoTest extends TestCase
{
    use ComparesResults;

    public function testAssertOkDemo(): void
    {
        $successResult = Ok('success');
        
        // This should pass
        self::assertOk($successResult);
    }

    public function testAssertErrDemo(): void
    {
        $errorResult = Err('error message');
        
        // This should pass
        self::assertErr($errorResult);
    }
}