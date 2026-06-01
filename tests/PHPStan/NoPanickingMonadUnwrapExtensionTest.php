<?php

declare(strict_types=1);

namespace Superscript\Monads\Tests\PHPStan;

use PHPStan\Rules\RestrictedUsage\RestrictedMethodUsageRule;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * @extends RuleTestCase<RestrictedMethodUsageRule>
 */
final class NoPanickingMonadUnwrapExtensionTest extends RuleTestCase
{
    protected function getRule(): Rule
    {
        // The rule reads RestrictedMethodUsageExtension services from the container by tag;
        // getAdditionalConfigFiles() below loads extension.neon so ours is registered.
        return new RestrictedMethodUsageRule(self::getContainer(), self::createReflectionProvider());
    }

    public static function getAdditionalConfigFiles(): array
    {
        return [__DIR__ . '/../../extension.neon'];
    }

    public function testFlagsPanickingUnwraps(): void
    {
        $this->analyse([__DIR__ . '/data/panicking-unwrap.php'], [
            [
                "Result::unwrap() throws when the Result is an Err. Handle both cases with match(), document the invariant with expect('why this cannot fail'), or supply a fallback via unwrapOr()/unwrapOrElse().",
                15,
            ],
            [
                "Result::unwrapErr() throws when the Result is an Ok. Handle both cases with match(), or document the invariant with expectErr('why this must be an Err').",
                16,
            ],
            [
                'This value is statically known to be an Ok, so unwrapErr() will always throw. Read the value with unwrap(), or handle both branches with match().',
                30,
            ],
            [
                'This value is statically known to be an Err, so unwrap() will always throw. Read the error with unwrapErr(), or handle both branches with match().',
                33,
            ],
            [
                "Option::unwrap() throws when the Option is None. Supply a fallback via unwrapOr()/unwrapOrElse(), collapse both cases with mapOrElse(), or document the invariant with expect('why this cannot be None').",
                36,
            ],
            [
                'This value is statically known to be None, so unwrap() will always throw. Guard with isSome() first, or supply a fallback via unwrapOr()/unwrapOrElse().',
                47,
            ],
        ]);
    }
}
