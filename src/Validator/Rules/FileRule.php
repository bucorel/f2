<?php

declare(strict_types=1);

namespace Bucorel\F2\Validator\Rules;

use Bucorel\F2\Validator\Contracts\RuleInterface;

class FileRule implements RuleInterface
{
    public function validate(
        mixed $value,
        array $arguments = []
    ): bool {

        /**
         * Optional field
         */
        if ($value === null) {
            return true;
        }

        /**
         * Must be upload array
         */
        if (!is_array($value)) {
            return false;
        }

        /**
         * Required keys
         */
        $requiredKeys = [
            'tmp_name',
            'error',
            'size'
        ];

        foreach ($requiredKeys as $key) {

            if (!array_key_exists($key, $value)) {
                return false;
            }
        }

        /**
         * Upload error
         */
        if ($value['error'] !== UPLOAD_ERR_OK) {
            return false;
        }

        /**
         * File existence
         */
        return is_uploaded_file(
            $value['tmp_name']
        );
    }

    public function getErrorMessage(
        string $field,
        array $arguments = []
    ): string {

        return "{$field} must be a valid uploaded file.";
    }
}
