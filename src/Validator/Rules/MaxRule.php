<?php

declare(strict_types=1);

namespace Bucorel\F2\Validator\Rules;

use Bucorel\F2\Validator\Contracts\RuleInterface;

class MaxRule implements RuleInterface
{
    public function validate(
        mixed $value,
        array $arguments = []
    ): bool {

        $max = $arguments[0] ?? null;

        if ($max === null) {
            return false;
        }

        $value = $this->normalize($value);
        $max = $this->normalize($max);

        if ($value === null || $max === null) {
            return false;
        }

        return $value <= $max;
    }

    protected function normalize(
        mixed $value
    ): int|float|null {

        /**
         * Integer / Float
         */
        if (
            is_int($value)
            || is_float($value)
        ) {
            return $value;
        }

        /**
         * Numeric string
         */
        if (is_numeric($value)) {
            return (float)$value;
        }

        /**
         * Date / Time
         */
        if (is_string($value)) {

            $timestamp = strtotime($value);

            if ($timestamp !== false) {
                return $timestamp;
            }
        }

        return null;
    }

    public function getErrorMessage(
        string $field,
        array $arguments = []
    ): string {

        return "{$field} must be greater than or equal to {$arguments[0]}.";
    }
}
