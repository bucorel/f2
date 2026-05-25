<?php

declare(strict_types=1);

namespace Bucorel\F2\Validator\Rules;

use Bucorel\F2\Validator\Contracts\RuleInterface;

class StrongPasswordRule implements RuleInterface
{
    public function validate(
        mixed $value,
        array $arguments = []
    ): bool {

        return preg_match(
            '/^(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9]).+$/',
            (string)$value
        ) === 1;
    }

    public function getErrorMessage(
        string $field,
        array $arguments = []
    ): string {

        return "{$field} must contain uppercase, lowercase and numbers.";
    }
}
