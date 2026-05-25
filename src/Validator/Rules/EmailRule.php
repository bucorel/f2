<?php

declare(strict_types=1);

namespace Bucorel\F2\Validator\Rules;

use Bucorel\F2\Validator\Contracts\RuleInterface;

class EmailRule implements RuleInterface
{
    public function validate(
        mixed $value,
        array $arguments = []
    ): bool {

        if (empty($value)) {
            return true;
        }

        return filter_var(
            $value,
            FILTER_VALIDATE_EMAIL
        ) !== false;
    }

    public function getErrorMessage(
        string $field,
        array $arguments = []
    ): string {

        return "{$field} must be a valid email address.";
    }
}
