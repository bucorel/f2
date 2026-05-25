<?php

declare(strict_types=1);

namespace Bucorel\F2\Validator\Rules;

use Bucorel\F2\Validator\Contracts\RuleInterface;

class BooleanRule implements RuleInterface
{
    protected array $allowed = [
        true,
        false,
        1,
        0,
        '1',
        '0',
        'true',
        'false',
        'yes',
        'no',
        'on',
        'off'
    ];

    public function validate(
        mixed $value,
        array $arguments = []
    ): bool {

        return in_array(
            is_string($value)
                ? strtolower(trim($value))
                : $value,
            $this->allowed,
            true
        );
    }

    public function getErrorMessage(
        string $field,
        array $arguments = []
    ): string {

        return "{$field} must be a valid boolean value.";
    }
}
