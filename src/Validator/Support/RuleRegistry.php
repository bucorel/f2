<?php

declare(strict_types=1);

namespace Bucorel\F2\Validator\Support;

use BadMethodCallException;
use Bucorel\F2\Validator\Contracts\RuleInterface;

/**
 * =========================================================
 * RULE REGISTRY
 * =========================================================
 */
class RuleRegistry
{
    protected static string $baseNamespace =
        'Bucorel\\F2\\Validator\\Rules\\';

    public static function resolve(
        string $rule
    ): RuleInterface {

        /**
         * Convert:
         * required => RequiredRule
         * email => EmailRule
         * minLength => MinLengthRule
         */

        $class = self::$baseNamespace .
            str_replace(
                ' ',
                '',
                ucwords(
                    preg_replace(
                        '/([a-z])([A-Z])/',
                        '$1 $2',
                        $rule
                    )
                )
            ) .
            'Rule';

        if (!class_exists($class)) {
            throw new BadMethodCallException(
                "Validation rule '{$rule}' not found."
            );
        }

        return new $class();
    }
}
