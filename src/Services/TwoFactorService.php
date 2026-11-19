<?php

namespace Veltra\VeltraLaravel\Services;

class TwoFactorService
{
    /**
     * Standard RFC 3548 / 4648 Base32 alphabet.
     */
    protected const BASE32_CHARS = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';

    /**
     * Generate a new random Base32 secret key.
     */
    public function generateSecretKey(int $length = 16): string
    {
        $secret = '';
        $alphabet = self::BASE32_CHARS;
        $max = strlen($alphabet) - 1;

        for ($i = 0; $i < $length; $i++) {
            $secret .= $alphabet[random_int(0, $max)];
        }

        return $secret;
    }

    /**
     * Calculate 6-digit TOTP code for a secret and timestamp.
     */
    public function calculateCode(string $secret, ?int $timestamp = null): string
    {
        $timestamp = $timestamp ?? time();
        $timeSlice = (int) floor($timestamp / 30);

        $secretBinary = $this->base32Decode($secret);
        $timePacked = pack('J', $timeSlice);

        $hash = hash_hmac('sha1', $timePacked, $secretBinary, true);

        // Dynamic truncation (RFC 4226)
        $offset = ord(substr($hash, -1)) & 0x0F;
        $hashPart = substr($hash, $offset, 4);
        $value = unpack('N', $hashPart)[1] & 0x7FFFFFFF;

        $otp = $value % 1000000;

        return str_pad((string) $otp, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Verify a 6-digit TOTP code with time drift window tolerance (±30s).
     */
    public function verifyCode(string $secret, string $code, int $window = 1): bool
    {
        $code = trim($code);
        if (strlen($code) !== 6 || ! ctype_digit($code)) {
            return false;
        }

        $currentTime = time();
        for ($i = -$window; $i <= $window; $i++) {
            $sliceTime = $currentTime + ($i * 30);
            if (hash_equals($this->calculateCode($secret, $sliceTime), $code)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Generate a collection of random recovery codes.
     *
     * @return array<int, string>
     */
    public function generateRecoveryCodes(int $count = 8): array
    {
        $codes = [];
        for ($i = 0; $i < $count; $i++) {
            $codes[] = sprintf(
                '%04s-%04s',
                bin2hex(random_bytes(2)),
                bin2hex(random_bytes(2))
            );
        }

        return $codes;
    }

    /**
     * Verify a recovery code and remove it from the list if valid.
     *
     * @param array<int, string> $recoveryCodes
     */
    public function verifyRecoveryCode(array &$recoveryCodes, string $code): bool
    {
        $normalizedCode = str_replace(['-', ' '], '', strtolower(trim($code)));

        foreach ($recoveryCodes as $index => $storedCode) {
            $normalizedStored = str_replace(['-', ' '], '', strtolower($storedCode));
            if (hash_equals($normalizedStored, $normalizedCode)) {
                unset($recoveryCodes[$index]);
                $recoveryCodes = array_values($recoveryCodes);
                return true;
            }
        }

        return false;
    }

    /**
     * Generate the standard otpauth:// URL for authenticator apps.
     */
    public function getOtpauthUrl(string $company, string $account, string $secret): string
    {
        $label = rawurlencode($company) . ':' . rawurlencode($account);
        $issuer = rawurlencode($company);

        return "otpauth://totp/{$label}?secret={$secret}&issuer={$issuer}";
    }

    /**
     * Generate a QR code image URL for scanning.
     */
    public function getQrCodeUrl(string $company, string $account, string $secret): string
    {
        $otpauth = $this->getOtpauthUrl($company, $account, $secret);

        return 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&format=svg&data=' . rawurlencode($otpauth);
    }

    /**
     * Decode a Base32 string into its binary representation.
     */
    public function base32Decode(string $b32): string
    {
        $b32 = strtoupper(trim($b32));
        $alphabet = self::BASE32_CHARS;
        $buffer = 0;
        $bitsLeft = 0;
        $binary = '';

        for ($i = 0; $i < strlen($b32); $i++) {
            $char = $b32[$i];
            if ($char === '=' || $char === ' ') {
                continue;
            }

            $val = strpos($alphabet, $char);
            if ($val === false) {
                continue;
            }

            $buffer = ($buffer << 5) | $val;
            $bitsLeft += 5;

            if ($bitsLeft >= 8) {
                $bitsLeft -= 8;
                $binary .= chr(($buffer >> $bitsLeft) & 0xFF);
            }
        }

        return $binary;
    }
}
