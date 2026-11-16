<?php

use App\Exceptions\CrmApiException;
use App\Http\Middleware\PortalAuthenticate;
use App\Http\Middleware\SetLocale;
use App\Services\PortalSession;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'set.locale' => SetLocale::class,
            'portal.auth' => PortalAuthenticate::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Fallback for any CRM failure a controller did not handle itself.
        $exceptions->render(function (CrmApiException $e, Request $request) {
            $locale = in_array($request->route('locale'), ['en', 'fr', 'ar'], true) ? $request->route('locale') : config('app.locale', 'en');
            app()->setLocale($locale);

            if ($e->isUnauthenticated()) {
                app(PortalSession::class)->clear($request);

                return redirect()->guest(route('portal.login', ['locale' => $locale]))
                    ->with('portal_error', __('site.portal_session_expired'));
            }

            if ($e->status === 404) {
                abort(404);
            }

            if ($e->isUnreachable()) {
                view()->share('currentLocale', $locale);
                view()->share('isRtl', $locale === 'ar');

                return response()->view('portal.unavailable', [], 503);
            }

            $back = url()->previous() ?: route('portal.dashboard', ['locale' => $locale]);

            return redirect($back)->with('portal_error', __('site.portal_generic_error'));
        });

        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
