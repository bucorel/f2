<?php

declare(strict_types=1);

namespace Bucorel\F2\Validator\Rules;

use Bucorel\F2\Validator\Contracts\RuleInterface;

class IntegerRule implements RuleInterface
{
    public function validate(
        mixed $value,
        array $arguments = []
    ): bool {

        /**
         * Skip empty optional values
         */
        if (
            $value === null
            || $value === ''
        ) {
            return true;
        }

        return filter_var(
            $value,
            FILTER_VALIDATE_INT
        ) !== false;
    }

    public function getErrorMessage(
        string $field,
        array $arguments = []
    ): string {

        return "{$field} must be a valid integer.";
    }
}
