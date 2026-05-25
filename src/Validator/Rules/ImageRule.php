<?php

declare(strict_types=1);

namespace Bucorel\F2\Validator\Rules;

use Bucorel\F2\Validator\Contracts\RuleInterface;

class ImageRule implements RuleInterface
{
    protected array $allowedMimeTypes = [
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp'
    ];

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
         * Upload must succeed
         */
        if (
            !isset($value['tmp_name'])
            || !isset($value['error'])
            || $value['error'] !== UPLOAD_ERR_OK
        ) {
            return false;
        }

        /**
         * Must be uploaded file
         */
        if (
            !is_uploaded_file(
                $value['tmp_name']
            )
        ) {
            return false;
        }

        /**
         * Detect actual mime type
         */
        $mime = mime_content_type(
            $value['tmp_name']
        );

        if (
            !in_array(
                $mime,
                $this->allowedMimeTypes,
                true
            )
        ) {
            return false;
        }

        /**
         * Verify image content
         */
        return getimagesize(
            $value['tmp_name']
        ) !== false;
    }

    public function getErrorMessage(
        string $field,
        array $arguments = []
    ): string {

        return "{$field} must be a valid image.";
    }
}
