<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        /*
    |--------------------------------------------------------------------------
    | Authentication
    |--------------------------------------------------------------------------
    */

    RateLimiter::for('login', function (Request $request) {
        $email = strtolower((string) $request->input('email'));

        return Limit::perMinute(5)
            ->by($request->ip() . '|' . $email);
    });


    /*
    |--------------------------------------------------------------------------
    | Public / General API
    |--------------------------------------------------------------------------
    */

    RateLimiter::for('public-api', function (Request $request) {
        return Limit::perMinute(60)
            ->by($request->ip());
    });


    /*
    |--------------------------------------------------------------------------
    | Vehicle Read Operations
    |--------------------------------------------------------------------------
    */

    RateLimiter::for('vehicle-read', function (Request $request) {
        return Limit::perMinute(60)
            ->by($request->user()?->id ?: $request->ip());
    });


    /*
    |--------------------------------------------------------------------------
    | Vehicle Write Operations
    |--------------------------------------------------------------------------
    */

    RateLimiter::for('vehicle-write', function (Request $request) {
        return Limit::perMinute(30)
            ->by($request->user()?->id ?: $request->ip());
    });


    /*
    |--------------------------------------------------------------------------
    | Admin Read Operations
    |--------------------------------------------------------------------------
    */

    RateLimiter::for('admin-read', function (Request $request) {
        return Limit::perMinute(120)
            ->by($request->user()?->id ?: $request->ip());
    });


    /*
    |--------------------------------------------------------------------------
    | Admin Write Operations
    |--------------------------------------------------------------------------
    */

    RateLimiter::for('admin-write', function (Request $request) {
        return Limit::perMinute(30)
            ->by($request->user()?->id ?: $request->ip());
    });


    /*
    |--------------------------------------------------------------------------
    | File Uploads
    |--------------------------------------------------------------------------
    */

    RateLimiter::for('upload', function (Request $request) {
        return Limit::perMinute(10)
            ->by($request->user()?->id ?: $request->ip());
    });


    /*
    |--------------------------------------------------------------------------
    | Inquiries
    |--------------------------------------------------------------------------
    */

    RateLimiter::for('inquiry', function (Request $request) {
        return Limit::perMinute(20)
            ->by($request->user()?->id ?: $request->ip());
    });


    /*
    |--------------------------------------------------------------------------
    | CTA Interactions
    |--------------------------------------------------------------------------
    */

    RateLimiter::for('cta', function (Request $request) {
        return Limit::perMinute(30)
            ->by($request->user()?->id ?: $request->ip());
    });


    /*
    |--------------------------------------------------------------------------
    | Logout
    |--------------------------------------------------------------------------
    */

    RateLimiter::for('logout', function (Request $request) {
        return Limit::perMinute(10)
            ->by($request->user()?->id ?: $request->ip());
    });
    }
}
