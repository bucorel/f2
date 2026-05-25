<?php

declare(strict_types=1);

namespace Bucorel\F2\Validator\Rules;

use Bucorel\F2\Validator\Contracts\RuleInterface;

class MinRule implements RuleInterface
{
    public function validate(
        mixed $value,
        array $arguments = []
    ): bool {

        $min = $arguments[0] ?? null;

        if ($min === null) {
            return false;
        }

        $value = $this->normalize($value);
        $min = $this->normalize($min);

        if ($value === null || $min === null) {
            return false;
        }

        return $value >= $min;
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
