<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

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
        // Sign-in, sign-up and password recovery: per IP + e-mail, plus a looser per-IP ceiling.
        RateLimiter::for('portal-auth', fn (Request $request) => [
            Limit::perMinute(5)->by($request->ip().'|'.mb_strtolower((string) $request->input('email'))),
            Limit::perMinute(20)->by($request->ip()),
        ]);
    }
}
