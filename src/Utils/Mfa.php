<?php

namespace Bucorel\F2\Utils;

class Mfa {

    private string $encryptionKey;
    private string $cipher = 'aes-256-gcm';

    private int $digits = 6;
    private int $period = 30;
    private string $algo = 'sha1';

    public function __construct(string $key) {
        $this->encryptionKey = hash('sha256', $key, true); // 32-byte key
    }

    /**
     * Generate MFA setup data
     */
    public function newSetup(): array {

        $secret = $this->generateSecret();

        $backups = [];

        for ($i = 0; $i < 8; $i++) {
            $backups[] = $this->generateBackupCode();
        }

        return [
            'secret' => $secret,
            'backups' => $backups
        ];
    }

    /**
     * Generate RFC-compatible Base32 secret
     */
    private function generateSecret(int $length = 32): string {

        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';

        $secret = '';

        for ($i = 0; $i < $length; $i++) {
            $secret .= $alphabet[random_int(0, 31)];
        }

        return $secret;
    }

    /**
     * Human-friendly recovery code
     */
    private function generateBackupCode(): string {

        $alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

        $code = '';

        for ($i = 0; $i < 8; $i++) {
            $code .= $alphabet[random_int(0, strlen($alphabet) - 1)];
        }

        return substr($code, 0, 4) . '-' . substr($code, 4);
    }

    /**
     * Encrypt secret before DB storage
     */
    public function encryptSecret(string $secret): string {

        $iv = random_bytes(
            openssl_cipher_iv_length($this->cipher)
        );

        $tag = '';

        $ciphertext = openssl_encrypt(
            $secret,
            $this->cipher,
            $this->encryptionKey,
            OPENSSL_RAW_DATA,
            $iv,
            $tag
        );

        return base64_encode(
            $iv . $tag . $ciphertext
        );
    }

    /**
     * Decrypt stored secret
     */
    public function decryptSecret(string $payload): string {

        $data = base64_decode($payload);

        $ivLength = openssl_cipher_iv_length($this->cipher);

        $iv = substr($data, 0, $ivLength);

        $tag = substr($data, $ivLength, 16);

        $ciphertext = substr($data, $ivLength + 16);

        $secret = openssl_decrypt(
            $ciphertext,
            $this->cipher,
            $this->encryptionKey,
            OPENSSL_RAW_DATA,
            $iv,
            $tag
        );

        if ($secret === false) {
            throw new \RuntimeException('Failed to decrypt MFA secret');
        }

        return $secret;
    }

    /**
     * Generate provisioning URI for QR
     */
    public function provisioningUri(
        string $issuer,
        string $account,
        string $secret
    ): string {

        return sprintf(
            'otpauth://totp/%s:%s?secret=%s&issuer=%s&algorithm=%s&digits=%d&period=%d',
            rawurlencode($issuer),
            rawurlencode($account),
            $secret,
            rawurlencode($issuer),
            strtoupper($this->algo),
            $this->digits,
            $this->period
        );
    }

    /**
     * Generate TOTP code
     */
    public function calculateTOTP(
        string $secret,
        int $timeWindow
    ): string {

        $secretBinary = $this->base32Decode($secret);

        $timeBinary =
            pack('N*', 0) .
            pack('N*', $timeWindow);

        $hash = hash_hmac(
            $this->algo,
            $timeBinary,
            $secretBinary,
            true
        );

        $offset = ord($hash[19]) & 0x0F;

        $binary =
            ((ord($hash[$offset]) & 0x7F) << 24) |
            ((ord($hash[$offset + 1]) & 0xFF) << 16) |
            ((ord($hash[$offset + 2]) & 0xFF) << 8) |
            (ord($hash[$offset + 3]) & 0xFF);

        $otp = $binary % (10 ** $this->digits);

        return str_pad(
            (string)$otp,
            $this->digits,
            '0',
            STR_PAD_LEFT
        );
    }

    /**
     * Verify TOTP token
     *
     * Returns:
     * - false if invalid
     * - time window integer if valid
     */
    public function verifyToken(
        string $encryptedSecret,
        string $userInput,
        ?int $lastUsedWindow = null
    ): int|false {

        $userInput = $this->sanitizeCode($userInput);

        if (!preg_match('/^\d{6}$/', $userInput)) {
            return false;
        }

        $secret = $this->decryptSecret($encryptedSecret);

        $currentWindow = floor(time() / $this->period);

        for ($i = -1; $i <= 1; $i++) {

            $window = $currentWindow + $i;

            // replay protection
            if (
                $lastUsedWindow !== null &&
                $window <= $lastUsedWindow
            ) {
                continue;
            }

            $expected = $this->calculateTOTP(
                $secret,
                $window
            );

            if (hash_equals($expected, $userInput)) {
                return $window;
            }
        }

        return false;
    }

    /**
     * Verify recovery code
     */
    public function verifyRecoveryCode(
        string $input,
        array $storedHashes
    ): int|false {

        $input = strtoupper(
            $this->sanitizeCode($input)
        );

        foreach ($storedHashes as $index => $hash) {

            if (password_verify($input, $hash)) {
                return $index;
            }
        }

        return false;
    }

    /**
     * Format backup code for UI
     */
    public function codeForDisplay(
        string $rawCode
    ): string {

        $rawCode = strtoupper(
            $this->sanitizeCode($rawCode)
        );

        return substr($rawCode, 0, 4)
            . '-'
            . substr($rawCode, 4);
    }

    /**
     * Remove spaces/dashes/etc
     */
    public function sanitizeCode(
        string $userInput
    ): string {

        return preg_replace(
            '/[^A-Za-z0-9]/',
            '',
            strtoupper($userInput)
        );
    }

    /**
     * Base32 decode
     */
    private function base32Decode(
        string $base32
    ): string {

        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';

        $base32 = strtoupper($base32);

        $bits = '';

        foreach (str_split($base32) as $char) {

            $position = strpos($alphabet, $char);

            if ($position === false) {
                continue;
            }

            $bits .= str_pad(
                decbin($position),
                5,
                '0',
                STR_PAD_LEFT
            );
        }

        $binary = '';

        foreach (str_split($bits, 8) as $byte) {

            if (strlen($byte) !== 8) {
                continue;
            }

            $binary .= chr(bindec($byte));
        }

        return $binary;
    }
}
