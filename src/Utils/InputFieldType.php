<?php
namespace Bucorel\F2\Utils;

enum InputFieldType: string{
	case TEXT     = 'text';
	case EMAIL    = 'email';
	case URL      = 'url';
	case IPV4     = 'ipv4';
	case IPV6     = 'ipv6';
	case DATE     = 'date';
	case TIME     = 'time';
	case INT      = 'integer';
	case BOOLEAN  = 'boolean';
	case SLUG     = 'slug';

	/**
	* Validates a value against the specific case rules.
	*/
	public function validate(mixed $value): bool{
		return match($this) {
			self::TEXT    => is_string($value) && strlen(trim($value)) > 0,
			self::EMAIL   => filter_var($value, FILTER_VALIDATE_EMAIL) !== false,
			self::URL     => filter_var($value, FILTER_VALIDATE_URL) !== false,
			self::IPV4    => filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false,
			self::IPV6    => filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) !== false,
			self::INT     => filter_var($value, FILTER_VALIDATE_INT) !== false,
			self::BOOLEAN => filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) !== null,
			self::DATE    => preg_match('/^\d{4}-\d{2}-\d{2}$/', $value) && strtotime($value) !== false,
			self::TIME    => preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/', $value),
			self::SLUG    => preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $value),
		};
	}

	/**
	* Cleans and casts the data to its proper PHP type.
	*/
	public function sanitize(mixed $value): mixed{
		return match($this) {
			self::EMAIL   => filter_var(trim($value), FILTER_SANITIZE_EMAIL),
			self::URL     => filter_var(trim($value), FILTER_SANITIZE_URL),
			self::INT     => (int) $value,
			self::BOOLEAN => filter_var($value, FILTER_VALIDATE_BOOLEAN),
			self::TEXT, 
			self::SLUG    => htmlspecialchars(trim((string)$value), ENT_QUOTES, 'UTF-8'),
			default       => trim((string)$value),
		};
	}
}
