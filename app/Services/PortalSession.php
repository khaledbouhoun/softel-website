<?php

namespace App\Services;

use App\Support\PortalFormat;
use Illuminate\Http\Request;

/**
 * Customer sign-in state. Only the CRM bearer token and a minimal display profile live in the session;
 * the website database never stores customers.
 */
class PortalSession
{
    public const ACCOUNT = 'portal.account';

    public const ONBOARDING = 'portal.onboarding';

    /**
     * @param  array<string, mixed>  $account
     */
    public function start(Request $request, string $token, array $account, ?array $onboarding = null): void
    {
        $request->session()->regenerate();
        $request->session()->put(CrmPortalClient::SESSION_TOKEN, $token);
        $this->remember($request, $account);

        // Where the CRM wants a first-time customer to land (kept until the first request is sent).
        $onboarding === null ? $request->session()->forget(self::ONBOARDING) : $request->session()->put(self::ONBOARDING, $onboarding);
    }

    /** @return array<string, mixed> */
    public function onboarding(Request $request): array
    {
        return (array) $request->session()->get(self::ONBOARDING, []);
    }

    public function finishOnboarding(Request $request): void
    {
        $request->session()->forget(self::ONBOARDING);
    }

    /**
     * @param  array<string, mixed>  $account
     */
    public function remember(Request $request, array $account): void
    {
        $request->session()->put(self::ACCOUNT, [
            'id' => $account['id'] ?? null,
            'name' => (string) ($account['name'] ?? ''),
            'email' => (string) ($account['email'] ?? ''),
            'phone' => $account['phone'] ?? null,
            'avatar_url' => PortalFormat::avatarUrl($account['avatar_url'] ?? null),
            'locale' => $account['locale'] ?? null,
            'company' => $account['profile']['company'] ?? null,
            'is_customer' => (bool) ($account['is_customer'] ?? false),
            'email_verified' => (bool) ($account['email_verified'] ?? false),
        ]);
    }

    public function clear(Request $request): void
    {
        $request->session()->forget([CrmPortalClient::SESSION_TOKEN, self::ACCOUNT, self::ONBOARDING]);
        $request->session()->regenerateToken();
    }

    public function check(Request $request): bool
    {
        return filled($request->session()->get(CrmPortalClient::SESSION_TOKEN));
    }

    /**
     * @return array<string, mixed>
     */
    public function account(Request $request): array
    {
        return (array) $request->session()->get(self::ACCOUNT, []);
    }
}
