<?php

declare(strict_types=1);

namespace Bucorel\F2\Validator\Rules;

use Bucorel\F2\Validator\Contracts\RuleInterface;

class RequiredRule implements RuleInterface
{
    public function validate(
        mixed $value,
        array $arguments = []
    ): bool {

        /**
         * Must not be an array
         */
        if (is_array($value)) {
            return false;
        }
        
        if (is_string($value)) {
            $value = trim($value);
        }

        return !empty($value)
            || $value === '0'
            || $value === 0;
    }

    public function getErrorMessage(
        string $field,
        array $arguments = []
    ): string {

        return "{$field} is required.";
    }
}
