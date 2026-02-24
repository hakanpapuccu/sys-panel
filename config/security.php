<?php

return [
    'force_https' => env('FORCE_HTTPS', false),

    'headers' => [
        'x_frame_options' => env('SECURITY_X_FRAME_OPTIONS', 'SAMEORIGIN'),
        'x_content_type_options' => env('SECURITY_X_CONTENT_TYPE_OPTIONS', 'nosniff'),
        'referrer_policy' => env('SECURITY_REFERRER_POLICY', 'strict-origin-when-cross-origin'),
        'permissions_policy' => env(
            'SECURITY_PERMISSIONS_POLICY',
            'camera=(), microphone=(), geolocation=(), payment=(), usb=()'
        ),
        'content_security_policy' => env(
            'SECURITY_CONTENT_SECURITY_POLICY',
            "default-src 'self'; img-src 'self' data: blob: https:; media-src 'self' blob:; script-src 'self' 'unsafe-inline' 'unsafe-eval' https:; style-src 'self' 'unsafe-inline' https:; font-src 'self' data: https:; connect-src 'self' https: ws: wss:; frame-ancestors 'self'; base-uri 'self'; form-action 'self'"
        ),
        'cross_origin_opener_policy' => env('SECURITY_COOP', 'same-origin'),
        'cross_origin_resource_policy' => env('SECURITY_CORP', 'same-origin'),
    ],

    'hsts' => [
        'enabled' => env('SECURITY_HSTS_ENABLED', true),
        'max_age' => env('SECURITY_HSTS_MAX_AGE', 31536000),
        'include_subdomains' => env('SECURITY_HSTS_INCLUDE_SUBDOMAINS', true),
        'preload' => env('SECURITY_HSTS_PRELOAD', false),
    ],

    'slow_query_threshold_ms' => env('SLOW_QUERY_THRESHOLD_MS', 500),
];
