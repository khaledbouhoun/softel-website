<?php

namespace App\Http\Middleware;

use App\Services\PortalSession;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Guests are sent to the sign-in page; the intended URL (including ?product=&type=) is kept in the session.
 * Signed-in accounts whose e-mail is not verified are sent to the verification screen instead.
 */
class PortalAuthenticate
{
    public function __construct(private readonly PortalSession $portal) {}

    public function handle(Request $request, Closure $next): Response
    {
        if (! $this->portal->check($request)) {
            $locale = (string) $request->route('locale', config('app.locale', 'en'));

            return redirect()->guest(route('portal.login', ['locale' => $locale]));
        }

        $locale = (string) $request->route('locale', config('app.locale', 'en'));
        $account = $this->portal->account($request);

        // Unverified e-mail: only the verification screen (and sign-out) is reachable.
        // Google accounts arrive pre-verified. The intended URL is kept so the visitor lands there after verifying.
        if (! ($account['email_verified'] ?? false) && $request->route()?->getName() !== 'portal.logout') {
            return redirect()->guest(route('portal.verify.email', array_filter([
                'locale' => $locale,
                'email' => $account['email'] ?? null,
            ])));
        }

        $response = $next($request);

        // Account pages are private: never let a shared cache or the back button keep them.
        $response->headers->set('Cache-Control', 'no-store, private');

        return $response;
    }
}
