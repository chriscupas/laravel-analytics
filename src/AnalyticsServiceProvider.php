<?php

namespace AndreasElia\Analytics;

use AndreasElia\Analytics\Http\Middleware\Analytics;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AnalyticsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/analytics.php' => config_path('analytics.php'),
            ], 'analytics-config');

            $this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/analytics'),
            ], 'analytics-assets');

            $this->publishes([
                __DIR__.'/../database/migrations' => database_path('migrations'),
            ], 'analytics-migrations');
        }

        // Migrations
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        // Middleware
        Route::middlewareGroup('analytics', [
            Analytics::class,
        ]);

        // Routes
        Route::group($this->routeConfig(), function () {
            $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        });

        // Views
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'analytics');
    }

    protected function routeConfig(): array
    {
        $config = $this->buildRouteConfiguration();
        
        $config['middleware'] = $this->resolveMiddleware($config['middleware']);
        
        if ($this->app->environment('production')) {
            $config['middleware'][] = 'cache.headers:public;max_age=3600;etag';
        }
        
        if (config('analytics.rate_limit.enabled', false)) {
            $config['middleware'][] = $this->buildRateLimitMiddleware();
        }
        
        if (config('analytics.cors.enabled', false)) {
            $config['middleware'][] = $this->buildCorsMiddleware();
        }
        
        $config['middleware'] = array_values(array_unique(array_filter($config['middleware'])));
        
        return $config;
    }

    /**
     * Build the base route configuration array.
     */
    protected function buildRouteConfiguration(): array
    {
        $prefix = config('analytics.prefix', 'analytics');
        
        if (is_callable($prefix)) {
            $prefix = $prefix($this->app);
        }
        
        return [
            'namespace'  => 'AndreasElia\Analytics\Http\Controllers',
            'prefix'     => $prefix,
            'middleware' => config('analytics.middleware', ['web']),
            'domain'     => config('analytics.domain', null),
            'as'         => config('analytics.route_namespace', 'analytics.'),
        ];
    }

    /**
     * Resolve and merge middleware based on configuration.
     */
    protected function resolveMiddleware(array $baseMiddleware): array
    {
        $middleware = $baseMiddleware;

        if ($this->shouldProtectRoutes()) {
            $protectionMiddleware = $this->getProtectionMiddleware();
            $middleware = array_merge($middleware, $protectionMiddleware);
        }

        if ($customGroups = config('analytics.middleware_groups', [])) {
            foreach ($customGroups as $group) {
                if (is_string($group) && Route::hasMiddlewareGroup($group)) {
                    $middleware = array_merge($middleware, Route::getMiddlewareGroup($group));
                }
            }
        }

        if ($individualMiddleware = config('analytics.additional_middleware', [])) {
            $middleware = array_merge($middleware, $individualMiddleware);
        }

        return $middleware;
    }

    /**
     * Determine if routes should be protected.
     */
    protected function shouldProtectRoutes(): bool
    {
        $protected = config('analytics.protected', false);
        
        if (is_callable($protected)) {
            return $protected($this->app);
        }
        
        return $protected;
    }

    /**
     * Get protection middleware with fallbacks.
     */
    protected function getProtectionMiddleware(): array
    {
        $protectionMiddleware = config('analytics.protection_middleware', ['auth']);
        
        $validMiddleware = [];
        foreach ((array) $protectionMiddleware as $middleware) {
            if (class_exists($middleware) || Route::hasMiddleware($middleware)) {
                $validMiddleware[] = $middleware;
            } else {
                logger()->warning("Analytics protection middleware '{$middleware}' not found");
            }
        }
        
        return $validMiddleware;
    }

    /**
     * Build rate limiting middleware configuration.
     */
    protected function buildRateLimitMiddleware(): string
    {
        $rateLimit = config('analytics.rate_limit', []);
        $attempts = $rateLimit['attempts'] ?? 60;
        $decayMinutes = $rateLimit['decay_minutes'] ?? 1;
        
        return "throttle:{$attempts},{$decayMinutes}";
    }

    /**
     * Build CORS middleware configuration.
     */
    protected function buildCorsMiddleware(): string
    {
        $cors = config('analytics.cors', []);
        
        if (class_exists('Fruitcake\Cors\HandleCors')) {
            return 'Fruitcake\Cors\HandleCors';
        }
        
        if (class_exists('Illuminate\Http\Middleware\HandleCors')) {
            return 'Illuminate\Http\Middleware\HandleCors';
        }
        
        return 'cors';
    }

    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/analytics.php',
            'analytics'
        );
    }
}
