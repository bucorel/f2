<?php

declare(strict_types=1);

namespace Bucorel\F2\Validator\Rules;

use Bucorel\F2\Validator\Contracts\RuleInterface;

class PhoneRule implements RuleInterface
{
	public function validate(
		mixed $value,
		array $arguments = []
	): bool {

		if (!is_string($value)) {
		    return false;
		}

		/**
		 * Keep only digits
		 */
		$normalized = preg_replace(
		    '/[^0-9]/',
		    '',
		    $value
		);

		/**
		 * Typical phone lengths:
		 * 7 to 15 digits
		 */

		$length = strlen($normalized);

		return $length >= 7
		    && $length <= 15;
	}

    public function getErrorMessage(
        string $field,
        array $arguments = []
    ): string {

        return "{$field} is not a valid phone number.";
    }
}
