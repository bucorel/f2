<?php

declare(strict_types=1);

namespace Bucorel\F2\Validator\Support;

/**
 * =========================================================
 * ERROR BAG
 * =========================================================
 */
class ErrorBag
{
    private array $errors = [];

    public function add(
        string $field,
        string $message
    ): void {

        $this->errors[$field][] = $message;
    }

    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }

    public function all(): array
    {
        return $this->errors;
    }

    public function first(
        ?string $field = null
    ): ?string {

        if ($field !== null) {
            return $this->errors[$field][0] ?? null;
        }

        foreach ($this->errors as $messages) {
            return $messages[0];
        }

        return null;
    }
}
