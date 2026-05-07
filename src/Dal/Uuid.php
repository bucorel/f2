<?php
namespace Bucorel\F2\Dal;

class Uuid {
	public static function v4(): string {
		$data = random_bytes(16);
		$data[6] = chr((ord($data[6]) & 0x0f) | 0x40);
		$data[8] = chr((ord($data[8]) & 0x3f) | 0x80);

		return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
	}
	
    public static function v5(string $namespace, string $name): string {
        // hash-based (e.g., for consistent IDs from email, etc.)
        $ns_bin = self::fromStringToBinary($namespace);
        $hash = sha1($ns_bin . $name);
        return self::formatUuid($hash, 5);
    }

    private static function fromStringToBinary(string $uuid): string {
        return pack('H*', str_replace('-', '', $uuid));
    }

    private static function formatUuid(string $hash, int $version): string {
        return sprintf('%08s-%04s-%04x-%04x-%12s',
            substr($hash, 0, 8),
            substr($hash, 8, 4),
            (hexdec(substr($hash, 12, 4)) & 0x0fff) | ($version << 12),
            (hexdec(substr($hash, 16, 4)) & 0x3fff) | 0x8000,
            substr($hash, 20, 12)
        );
    }

	public static function v7(): string {
		// 48-bit Unix timestamp in milliseconds
		$msec = (int) floor(microtime(true) * 1000);

		// Convert to big-endian 6 bytes
		$timeHex = str_pad(dechex($msec), 12, '0', STR_PAD_LEFT);

		// 10 random bytes (80 bits)
		$rand = bin2hex(random_bytes(10));

		// time_low (32 bits)
		$time_low = substr($timeHex, 0, 8);

		// time_mid (16 bits)
		$time_mid = substr($timeHex, 8, 4);

		// time_high_and_version (16 bits)
		$time_hi = substr($rand, 0, 4);
		$time_high_and_version = sprintf(
		    '%04x',
		    (hexdec($time_hi) & 0x0fff) | 0x7000
		);

		// clock_seq_and_variant (16 bits)
		$clock_seq = substr($rand, 4, 4);
		$clock_seq_and_variant = sprintf(
		    '%04x',
		    (hexdec($clock_seq) & 0x3fff) | 0x8000
		);

		// node (48 bits)
		$node = substr($rand, 8, 12);

		return sprintf(
		    '%s-%s-%s-%s-%s',
		    $time_low,
		    $time_mid,
		    $time_high_and_version,
		    $clock_seq_and_variant,
		    $node
		);
	}
	
	public static function isValidV7(string $uuid): bool {
		return (bool) preg_match(
			'/^[0-9a-f]{8}-[0-9a-f]{4}-7[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i',
			$uuid
		);
	}
	
	function isValidForPg(mixed $uuid): bool {
		if (!is_string($uuid) || strlen($uuid) !== 36) {
			return false;
		}

		// Postgres accepts any hex digit in any position as long as the format is correct.
		// It is case-insensitive, so we use the 'i' flag.
		$pattern = '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i';

		return preg_match($pattern, $uuid) === 1;
	}
}
?>
