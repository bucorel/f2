# Bucorel F2 Validator

A lightweight, extensible, fluent PHP validation library.

---

# Features

- Fluent validation API
- Factory-based validator creation
- Automatic rule discovery
- Composer PSR-4 autoload support
- Custom validation rules
- Custom error messages
- SOLID-friendly architecture
- No configuration required for new rules
- Framework-independent
- Minimal and practical

---

# Installation

Add PSR-4 autoloading in your `composer.json`.

```json
{
    "autoload": {
        "psr-4": {
            "Bucorel\\F2\\Validator\\": "src/"
        }
    }
}
```

Then run:

```bash
composer dump-autoload
```

---

# Directory Structure

```text
src/
├── Validator.php
├── Contracts/
│   └── RuleInterface.php
├── Rules/
│   ├── RequiredRule.php
│   ├── EmailRule.php
│   ├── MinLengthRule.php
│   └── PhoneRule.php
└── Support/
    ├── RuleRegistry.php
    └── ErrorBag.php
```

---

# Basic Usage

```php
use Bucorel\F2\Validator\Validator;

$data = [
    'email' => 'john@example.com',
    'password' => 'secret123'
];

$validator = Validator::make($data)

    ->field('email')
        ->required()
        ->email()

    ->field('password')
        ->required()
        ->minLength(8);

if (!$validator->isValid()) {

    print_r(
        $validator->errors()
    );
}
```

---

# Validation Flow

Validation works in three steps:

1. Create validator
2. Select field
3. Apply rules

Example:

```php
Validator::make($data)
    ->field('email')
        ->required()
        ->email();
```

---

# Available Built-in Rules

| Rule | Example |
|---|---|
| required | `->required()` |
| email | `->email()` |
| minLength | `->minLength(8)` |
| phone | `->phone()` |

---

# Using Custom Error Messages

Every rule can receive a custom error message as the last argument.

Example:

```php
Validator::make($data)

    ->field('email')
        ->required('Email is required.')
        ->email('Please enter a valid email address.');
```

---

# Getting Errors

## All Errors

```php
$validator->errors();
```

Example output:

```php
[
    'email' => [
        'Invalid email address.'
    ],
    'password' => [
        'Password too short.'
    ]
]
```

---

## First Error

```php
$validator->firstError();
```

---

## First Error For Specific Field

```php
$validator->firstError('email');
```

---

# Validation Status

```php
$validator->isValid();
```

Returns:

```php
true
```

or

```php
false
```

---

# Creating Custom Rules

Creating custom rules is extremely simple.

Just create a new rule class inside:

```text
src/Rules/
```

Example:

```text
src/Rules/StrongPasswordRule.php
```

---

# Example Custom Rule

```php
<?php

declare(strict_types=1);

namespace Bucorel\F2\Validator\Rules;

use Bucorel\F2\Validator\Contracts\RuleInterface;

class StrongPasswordRule implements RuleInterface
{
    public function validate(
        mixed $value,
        array $arguments = []
    ): bool {

        return preg_match(
            '/^(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9]).+$/',
            (string)$value
        ) === 1;
    }

    public function getErrorMessage(
        string $field,
        array $arguments = []
    ): string {

        return "{$field} must contain uppercase, lowercase and numbers.";
    }
}
```

---

# Using Custom Rules

The validator automatically resolves rules using naming conventions.

Example:

```text
StrongPasswordRule
```

becomes:

```php
->strongPassword()
```

Usage:

```php
Validator::make($data)

    ->field('password')
        ->strongPassword();
```

No registration required.

No configuration required.

---

# Rule Naming Convention

Rules must follow this format:

```text
SomethingRule.php
```

Examples:

| Class | Method |
|---|---|
| RequiredRule | `->required()` |
| EmailRule | `->email()` |
| MinLengthRule | `->minLength()` |
| StrongPasswordRule | `->strongPassword()` |

---

# Rule Interface

Every rule must implement:

```php
RuleInterface
```

Methods:

```php
public function validate(
    mixed $value,
    array $arguments = []
): bool;

public function getErrorMessage(
    string $field,
    array $arguments = []
): string;
```

---

# Rule Arguments

Validation rules can receive arguments.

Example:

```php
->minLength(8)
```

Inside rule:

```php
$arguments[0]
```

contains:

```php
8
```

---

# Multiple Rule Arguments

Example:

```php
->between(5, 10)
```

Inside rule:

```php
$arguments[0] // 5
$arguments[1] // 10
```

---

# Custom Message Behavior

The last string argument is treated as a custom error message.

Example:

```php
->minLength(8, 'Password too short.')
```

Inside validator:

- `8` becomes validation argument
- `'Password too short.'` becomes custom message

---

# Example Complex Validation

```php
$data = [
    'name' => '',
    'email' => 'wrong-email',
    'password' => '123'
];

$validator = Validator::make($data)

    ->field('name')
        ->required()

    ->field('email')
        ->required()
        ->email()

    ->field('password')
        ->required()
        ->minLength(
            8,
            'Password must contain at least 8 characters.'
        );

if (!$validator->isValid()) {

    print_r(
        $validator->errors()
    );
}
```

---

# Example Error Output

```php
[
    'name' => [
        'name is required.'
    ],
    'email' => [
        'email must be a valid email address.'
    ],
    'password' => [
        'Password must contain at least 8 characters.'
    ]
]
```

---

# Architecture Overview

## Validator

Responsible for:

- field selection
- fluent chaining
- validation execution
- error collection

---

## RuleInterface

Defines validation contract for all rules.

---

## Rules

Contain actual validation logic.

Each rule validates only one thing.

---

## ErrorBag

Responsible only for storing validation errors.

---

## RuleRegistry

Responsible only for resolving rule classes dynamically.

---

# Design Principles

This module follows:

- Single Responsibility Principle
- Open/Closed Principle
- Strategy Pattern
- Factory Pattern
- Fluent Interface Pattern

---

# Performance Notes

- Uses Composer PSR-4 autoloading
- No filesystem scanning
- No reflection scanning
- No runtime bootstrapping
- Very lightweight

---

# Best Practices

## Keep Rules Small

One rule should validate only one thing.

Good:

```php
EmailRule
```

Bad:

```php
EmailAndPhoneAndPasswordRule
```

---

## Prefer Reusable Rules

Good:

```php
MinLengthRule
```

Bad:

```php
PasswordMinLengthRule
```

---

## Keep Rules Stateless

Rules should not store request-specific data internally.

---

# Future Expansion Ideas

Possible future improvements:

- nested validation
- array validation
- DTO validation
- localization support
- validation pipelines
- conditional validation
- async/database-backed rules
- rule groups
- stop-on-first-failure mode

---

# License

MIT
