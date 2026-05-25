<?php

declare(strict_types=1);

namespace Bucorel\F2\Validator\Rules;

use Bucorel\F2\Validator\Contracts\RuleInterface;

class AllowedValuesRule implements RuleInterface
{
    public function validate(
        mixed $value,
        array $arguments = []
    ): bool {

        /**
         * Allowed values
         */
        $allowed = $arguments[0] ?? [];

        if (!is_array($allowed)) {
            return false;
        }

        return in_array(
            $value,
            $allowed,
            true
        );
    }

    public function getErrorMessage(
        string $field,
        array $arguments = []
    ): string {

        $allowed = $arguments[0] ?? [];

        $list = implode(', ', $allowed);

        return "{$field} must be one of: {$list}.";
    }
}
