<?php

declare(strict_types=1);

namespace Bucorel\F2\Validator;

use Bucorel\F2\Validator\Support\ErrorBag;
use Bucorel\F2\Validator\Support\RuleRegistry;

/**
 * =========================================================
 * VALIDATOR
 * =========================================================
 */
class Validator
{
    private string $currentField = '';

    private ErrorBag $errors;

    private function __construct(
        private array $data
    ) {
        $this->errors = new ErrorBag();
    }

    /**
     * Factory
     */
    public static function make(
        array $data
    ): self {

        return new self($data);
    }

    /**
     * Select field
     */
    public function field(
        string $field
    ): self {

        $this->currentField = $field;

        return $this;
    }

    /**
     * Dynamic rule calls
     */
    public function __call(
        string $method,
        array $arguments
    ): self {

        $rule = RuleRegistry::resolve($method);

        $value = $this->data[$this->currentField] ?? null;

        /**
         * Extract custom message
         */
        $customMessage = null;

        if (
            !empty($arguments)
            && is_string(end($arguments))
        ) {
            $customMessage = array_pop($arguments);
        }

        /**
         * Validate
         */
        if (
            !$rule->validate(
                $value,
                $arguments
            )
        ) {

            $message = $customMessage
                ?? $rule->getErrorMessage(
                    $this->currentField,
                    $arguments
                );

            $this->errors->add(
                $this->currentField,
                $message
            );
        }

        return $this;
    }

    /**
     * Validation status
     */
    public function isValid(): bool
    {
        return !$this->errors->hasErrors();
    }

    /**
     * Get all errors
     */
    public function errors(): array
    {
        return $this->errors->all();
    }

    /**
     * Get first error
     */
    public function firstError(
        ?string $field = null
    ): ?string {

        return $this->errors->first($field);
    }
}
