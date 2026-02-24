<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TwoFactorService
{
    private const BASE32_ALPHABET = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';

    public function generateSecret(int $byteLength = 20): string
    {
        return $this->base32Encode(random_bytes($byteLength));
    }

    public function otpAuthUrl(User $user, string $secret): string
    {
        $issuer = rawurlencode((string) config('app.name', 'OIDB Panel'));
        $label = rawurlencode(sprintf('%s:%s', config('app.name', 'OIDB Panel'), $user->email));

        return sprintf(
            'otpauth://totp/%s?secret=%s&issuer=%s&algorithm=SHA1&digits=6&period=30',
            $label,
            rawurlencode($secret),
            $issuer
        );
    }

    public function verifyCode(string $secret, string $code, int $window = 1): bool
    {
        $normalizedCode = preg_replace('/\D+/', '', $code ?? '');
        if (! $normalizedCode || strlen($normalizedCode) !== 6) {
            return false;
        }

        $counter = (int) floor(time() / 30);
        for ($offset = -$window; $offset <= $window; $offset++) {
            if (hash_equals($this->hotp($secret, $counter + $offset), $normalizedCode)) {
                return true;
            }
        }

        return false;
    }

    public function generateRecoveryCodes(int $count = 8): array
    {
        $codes = [];
        for ($i = 0; $i < $count; $i++) {
            $codes[] = Str::upper(Str::random(5)).'-'.Str::upper(Str::random(5));
        }

        return $codes;
    }

    public function hashRecoveryCodes(array $codes): array
    {
        return array_map(static fn (string $code): string => Hash::make($code), $codes);
    }

    public function consumeRecoveryCode(array $hashedCodes, string $inputCode): ?array
    {
        $normalized = Str::upper(trim($inputCode));
        if ($normalized === '') {
            return null;
        }

        foreach ($hashedCodes as $index => $hashedCode) {
            if (Hash::check($normalized, (string) $hashedCode)) {
                unset($hashedCodes[$index]);

                return array_values($hashedCodes);
            }
        }

        return null;
    }

    private function hotp(string $secret, int $counter): string
    {
        $secretKey = $this->base32Decode($secret);
        if ($secretKey === '') {
            return str_repeat('0', 6);
        }

        $counterBytes = pack('N*', 0).pack('N*', $counter);
        $hash = hash_hmac('sha1', $counterBytes, $secretKey, true);
        $offset = ord(substr($hash, -1)) & 0x0f;
        $truncatedHash = (
            ((ord($hash[$offset]) & 0x7f) << 24) |
            ((ord($hash[$offset + 1]) & 0xff) << 16) |
            ((ord($hash[$offset + 2]) & 0xff) << 8) |
            (ord($hash[$offset + 3]) & 0xff)
        );

        return str_pad((string) ($truncatedHash % 1000000), 6, '0', STR_PAD_LEFT);
    }

    private function base32Encode(string $data): string
    {
        if ($data === '') {
            return '';
        }

        $binaryString = '';
        foreach (str_split($data) as $char) {
            $binaryString .= str_pad(decbin(ord($char)), 8, '0', STR_PAD_LEFT);
        }

        $chunks = str_split($binaryString, 5);
        $encoded = '';
        foreach ($chunks as $chunk) {
            $chunk = str_pad($chunk, 5, '0', STR_PAD_RIGHT);
            $encoded .= self::BASE32_ALPHABET[bindec($chunk)];
        }

        return $encoded;
    }

    private function base32Decode(string $input): string
    {
        $cleanInput = strtoupper(preg_replace('/[^A-Z2-7]/', '', $input) ?? '');
        if ($cleanInput === '') {
            return '';
        }

        $binaryString = '';
        foreach (str_split($cleanInput) as $char) {
            $position = strpos(self::BASE32_ALPHABET, $char);
            if ($position === false) {
                continue;
            }

            $binaryString .= str_pad(decbin($position), 5, '0', STR_PAD_LEFT);
        }

        $bytes = str_split($binaryString, 8);
        $decoded = '';
        foreach ($bytes as $byte) {
            if (strlen($byte) < 8) {
                continue;
            }

            $decoded .= chr(bindec($byte));
        }

        return $decoded;
    }
}
