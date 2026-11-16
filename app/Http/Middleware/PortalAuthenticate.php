<?php

namespace App\Http\Middleware;

use App\Services\PortalSession;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Guests are sent to the sign-in page; the intended URL (including ?product=&type=) is kept in the session.
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

        $response = $next($request);

        // Account pages are private: never let a shared cache or the back button keep them.
        $response->headers->set('Cache-Control', 'no-store, private');

        return $response;
    }
}
