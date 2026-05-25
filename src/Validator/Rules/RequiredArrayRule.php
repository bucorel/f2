<?php

declare(strict_types=1);

namespace Bucorel\F2\Validator\Rules;

use Bucorel\F2\Validator\Contracts\RuleInterface;

class RequiredArrayRule implements RuleInterface
{
    public function validate(
        mixed $value,
        array $arguments = []
    ): bool {

        /**
         * Must be an array
         */
        if (!is_array($value)) {
            return false;
        }

        /**
         * Must not be empty
         */
        if (empty($value)) {
            return false;
        }

        /**
         * Optional:
         * Remove empty string values
         */

        $filtered = array_filter(
            $value,
            function ($item) {

                if (is_string($item)) {
                    $item = trim($item);
                }

                return $item !== ''
                    && $item !== null;
            }
        );

        return !empty($filtered);
    }

    public function getErrorMessage(
        string $field,
        array $arguments = []
    ): string {

        return "{$field} must contain at least one value.";
    }
}
