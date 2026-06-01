<?php

declare(strict_types=1);

namespace Superscript\Monads\PHPStan;

use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ExtendedMethodReflection;
use PHPStan\Rules\RestrictedUsage\RestrictedMethodUsageExtension;
use PHPStan\Rules\RestrictedUsage\RestrictedUsage;

final class NoPanickingMonadUnwrapExtension implements RestrictedMethodUsageExtension
{
    private const BANNED = [
        'Superscript\\Monads\\Result\\Result' => [
            'unwrap' => "Result::unwrap() throws when the Result is an Err. Handle both cases with match(), document the invariant with expect('why this cannot fail'), or supply a fallback via unwrapOr()/unwrapOrElse().",
            'unwrapErr' => "Result::unwrapErr() throws when the Result is an Ok. Handle both cases with match(), or document the invariant with expectErr('why this must be an Err').",
        ],
        'Superscript\\Monads\\Result\\Err' => [
            'unwrap' => 'This value is statically known to be an Err, so unwrap() will always throw. Read the error with unwrapErr(), or handle both branches with match().',
        ],
        'Superscript\\Monads\\Result\\Ok' => [
            'unwrapErr' => 'This value is statically known to be an Ok, so unwrapErr() will always throw. Read the value with unwrap(), or handle both branches with match().',
        ],
        'Superscript\\Monads\\Option\\Option' => [
            'unwrap' => "Option::unwrap() throws when the Option is None. Supply a fallback via unwrapOr()/unwrapOrElse(), collapse both cases with mapOrElse(), or document the invariant with expect('why this cannot be None').",
        ],
        'Superscript\\Monads\\Option\\None' => [
            'unwrap' => 'This value is statically known to be None, so unwrap() will always throw. Guard with isSome() first, or supply a fallback via unwrapOr()/unwrapOrElse().',
        ],
    ];

    public function isRestrictedMethodUsage(ExtendedMethodReflection $methodReflection, Scope $scope): ?RestrictedUsage
    {
        $declaring = $methodReflection->getDeclaringClass()->getName();
        $name = $methodReflection->getName();

        $message = self::BANNED[$declaring][$name] ?? null;
        if ($message === null) {
            return null;
        }

        return RestrictedUsage::create(errorMessage: $message, identifier: 'monads.panickingUnwrap');
    }
}
