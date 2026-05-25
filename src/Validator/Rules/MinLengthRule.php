<?php

declare(strict_types=1);

namespace Bucorel\F2\Validator\Rules;

use Bucorel\F2\Validator\Contracts\RuleInterface;

class MinLengthRule implements RuleInterface
{
    public function validate(
        mixed $value,
        array $arguments = []
    ): bool {

        $min = $arguments[0] ?? 0;

        return mb_strlen(
            (string)$value
        ) >= $min;
    }

    public function getErrorMessage(
        string $field,
        array $arguments = []
    ): string {

        $min = $arguments[0] ?? 0;

        return "{$field} must contain at least {$min} characters.";
    }
}
