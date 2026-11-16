<?php

namespace App\Services;

use Illuminate\Http\Request;

/**
 * Customer sign-in state. Only the CRM bearer token and a minimal display profile live in the session;
 * the website database never stores customers.
 */
class PortalSession
{
    public const ACCOUNT = 'portal.account';

    /**
     * @param  array<string, mixed>  $account
     */
    public function start(Request $request, string $token, array $account): void
    {
        $request->session()->regenerate();
        $request->session()->put(CrmPortalClient::SESSION_TOKEN, $token);
        $this->remember($request, $account);
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
            'is_customer' => (bool) ($account['is_customer'] ?? false),
        ]);
    }

    public function clear(Request $request): void
    {
        $request->session()->forget([CrmPortalClient::SESSION_TOKEN, self::ACCOUNT]);
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
