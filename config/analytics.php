<?php

return [

    'enabled' => env('ANALYTICS_ENABLED', true),

    /**
     * Analytics Dashboard.
     *
     * The prefix and middleware for the analytics dashboard.
     */
    'prefix' => 'analytics',

    /**
     * Domain.
     *
     * The domain (optional) for the analytics dashboard.
     */
    'domain' => null,

    'middleware' => [
        'web',
    ],

    /**
     * Exclude.
     *
     * The routes excluded from page view tracking.
     */
    'exclude' => [
        '/analytics',
        '/analytics/*',
    ],

    /**
     * Determine if traffic from robots should be tracked.
     */
    'ignoreRobots' => false,

    /**
     * Ignored IP addresses.
     *
     * The IP addresses excluded from page view tracking.
     */
    'ignoredIPs' => [
        // '192.168.1.1',
    ],

    /**
     * Mask.
     *
     * Mask routes so they are tracked together.
     */
    'mask' => [
        // '/users/*',
    ],

    /**
     * Ignore methods.
     *
     * The HTTP verbs/methods that should be excluded from page view tracking.
     */
    'ignoreMethods' => [
        // 'OPTIONS', 'POST',
    ],

    /**
     * Columns that won't be tracked.
     *
     * List the columns you want to ignore from the page view tracking.
     */
    'ignoredColumns' => [
        // 'source',
        // 'country',
        // 'browser',
        // 'device',
        // 'host',
        // 'utm_source',
        // 'utm_medium',
        // 'utm_campaign',
        // 'utm_term',
        // 'utm_content',
    ],

    'session' => [
        'provider' => \AndreasElia\Analytics\RequestSessionProvider::class,
    ],

    /**
     * Dashboard Protection.
     *
     * Enable authentication protection for analytics dashboard.
     */
    'protected' => env('ANALYTICS_PROTECTED', true),

    /**
     * Protection Middleware.
     *
     * Middleware to apply when analytics dashboard is protected.
     */
    'protection_middleware' => [
        'auth',
    ],

    /**
     * Route Namespace.
     *
     * The namespace for route names (e.g., 'analytics.').
     */
    'route_namespace' => 'analytics.',

    /**
     * Additional Middleware.
     *
     * Additional middleware to apply to analytics routes.
     */
    'additional_middleware' => [
        // 'api',
        // 'verified',
    ],

    /**
     * Middleware Groups.
     *
     * Laravel middleware groups to include.
     */
    'middleware_groups' => [
        // 'api',
        // 'web',
    ],

    /**
     * Rate Limiting.
     *
     * Configure rate limiting for analytics routes.
     */
    'rate_limit' => [
        'enabled' => false,
        'attempts' => 60,
        'decay_minutes' => 1,
    ],

    /**
     * CORS Configuration.
     *
     * Configure CORS headers for analytics routes.
     */
    'cors' => [
        'enabled' => false,
        'allowed_origins' => ['*'],
        'allowed_methods' => ['GET', 'POST'],
        'allowed_headers' => ['*'],
    ],
];
