<?php

namespace App\Support;

class UserAgentParser
{
    public static function summarize(?string $userAgent): string
    {
        if (! $userAgent) {
            return 'Bilinmiyor';
        }

        $os = self::detectOs($userAgent);
        $browser = self::detectBrowser($userAgent);

        return trim($os.' / '.$browser, ' /');
    }

    private static function detectOs(string $userAgent): string
    {
        $ua = strtolower($userAgent);

        return match (true) {
            str_contains($ua, 'windows') => 'Windows',
            str_contains($ua, 'android') => 'Android',
            str_contains($ua, 'iphone'), str_contains($ua, 'ipad'), str_contains($ua, 'ios') => 'iOS',
            str_contains($ua, 'mac os'), str_contains($ua, 'macintosh') => 'macOS',
            str_contains($ua, 'linux') => 'Linux',
            default => 'Bilinmeyen OS',
        };
    }

    private static function detectBrowser(string $userAgent): string
    {
        $ua = strtolower($userAgent);

        return match (true) {
            str_contains($ua, 'edg/') => 'Edge',
            str_contains($ua, 'opr/'), str_contains($ua, 'opera') => 'Opera',
            str_contains($ua, 'chrome/') && ! str_contains($ua, 'edg/') => 'Chrome',
            str_contains($ua, 'firefox/') => 'Firefox',
            str_contains($ua, 'safari/') && ! str_contains($ua, 'chrome/') => 'Safari',
            default => 'Bilinmeyen Tarayıcı',
        };
    }
}
