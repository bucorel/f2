<?php

declare(strict_types=1);

namespace Bucorel\F2\Validator\Rules;

use Bucorel\F2\Validator\Contracts\RuleInterface;

class LatitudeRule implements RuleInterface
{
    public function validate(
        mixed $value,
        array $arguments = []
    ): bool {

        /**
         * Optional field
         */
        if (
            $value === null
            || $value === ''
        ) {
            return true;
        }

        if (!is_numeric($value)) {
            return false;
        }

        $value = (float)$value;

        return $value >= -90
            && $value <= 90;
    }

    public function getErrorMessage(
        string $field,
        array $arguments = []
    ): string {

        return "{$field} must be a valid latitude.";
    }
}
