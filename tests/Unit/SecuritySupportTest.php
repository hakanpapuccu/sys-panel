<?php

namespace Tests\Unit;

use App\Support\TwoFactorService;
use App\Support\UserAgentParser;
use Tests\TestCase;

class SecuritySupportTest extends TestCase
{
    public function test_user_agent_parser_returns_human_readable_label(): void
    {
        $label = UserAgentParser::summarize('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/121.0.0.0 Safari/537.36');

        $this->assertSame('Windows / Chrome', $label);
    }

    public function test_two_factor_service_verifies_current_totp_code(): void
    {
        $service = new TwoFactorService();
        $secret = $service->generateSecret();
        $code = $this->currentTotpCode($secret);

        $this->assertTrue($service->verifyCode($secret, $code));
        $this->assertFalse($service->verifyCode($secret, '000000'));
    }

    public function test_recovery_code_is_consumed_once(): void
    {
        $service = new TwoFactorService();
        $codes = $service->generateRecoveryCodes(3);
        $hashed = $service->hashRecoveryCodes($codes);

        $remaining = $service->consumeRecoveryCode($hashed, $codes[0]);
        $this->assertIsArray($remaining);
        $this->assertCount(2, $remaining);
        $this->assertNull($service->consumeRecoveryCode($remaining, $codes[0]));
    }

    private function currentTotpCode(string $secret): string
    {
        $secretKey = $this->base32Decode($secret);
        $counter = (int) floor(time() / 30);
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

    private function base32Decode(string $input): string
    {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $cleanInput = strtoupper(preg_replace('/[^A-Z2-7]/', '', $input) ?? '');
        $binaryString = '';

        foreach (str_split($cleanInput) as $char) {
            $position = strpos($alphabet, $char);
            if ($position === false) {
                continue;
            }

            $binaryString .= str_pad(decbin($position), 5, '0', STR_PAD_LEFT);
        }

        $decoded = '';
        foreach (str_split($binaryString, 8) as $byte) {
            if (strlen($byte) < 8) {
                continue;
            }

            $decoded .= chr(bindec($byte));
        }

        return $decoded;
    }
}
