<?php

declare(strict_types=1);

namespace Bucorel\F2\Validator\Contracts;

/**
 * =========================================================
 * RULE INTERFACE
 * =========================================================
 */
interface RuleInterface
{
    public function validate(
        mixed $value,
        array $arguments = []
    ): bool;

    public function getErrorMessage(
        string $field,
        array $arguments = []
    ): string;
}
