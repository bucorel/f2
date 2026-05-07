<?php
/*
 * @copyright Business Computing Research Laboratory <www.bucorel.com>
 * @author Pushpendra Singh Thakur <thakurpsr@gmail.com>
 */

namespace Bucorel\F2\Utils;

class Mask{

	/**
	* Masks sensitive strings like emails and phone numbers.
	* 
	* @param string $string The input to mask.
	* @param string $type   'email' or 'phone'.
	* @return string        The masked result.
	*/
	public static function maskSensitiveData($string, $type = 'email') {
		if ( empty($string) ) {
			return "";
		}

		if ($type === 'email') {
			// Split into [name] and [domain.com]
			$parts = explode("@", $string);
			$name = $parts[0];
			$domain = $parts[1] ?? '';

			// Length-based masking for the name part
			$len = strlen($name);
			if ($len <= 2) {
				return $name[0] . "*@" . $domain;
			}

			// Shows first 2 chars and last 1 char: "jo*******n@gmail.com"
			return substr($name, 0, 2) . str_repeat('*', $len - 3) . substr($name, -1) . "@" . $domain;
		}

		if ($type === 'phone') {
			// Strip non-numeric characters for processing
			$clean = preg_replace('/[^0-9]/', '', $string);
			$len = strlen($clean);

			// Shows the last 4 digits: "******1234"
			if ($len > 4) {
				return str_repeat('*', $len - 4) . substr($clean, -4);
			}
			return str_repeat('*', $len);
		}

		return $string;
	}
}
?>
