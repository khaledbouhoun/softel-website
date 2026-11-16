<?php

namespace App\Http\Controllers\Portal;

use App\Exceptions\CrmApiException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Portal\PortalLoginRequest;
use App\Http\Requests\Portal\PortalRegisterRequest;
use App\Services\CrmPortalClient;
use App\Services\PortalSession;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(private readonly CrmPortalClient $crm, private readonly PortalSession $portal) {}

    public function showLogin(Request $request, string $locale): View|RedirectResponse
    {
        if ($this->portal->check($request)) {
            return redirect()->route('portal.dashboard', $locale);
        }

        return view('portal.login', ['carry' => $this->carry($request)]);
    }

    public function login(PortalLoginRequest $request, string $locale): RedirectResponse
    {
        try {
            $result = $this->crm->post('auth/login', $request->safe()->only(['email', 'password']), false)['data'];
        } catch (CrmApiException $e) {
            if ($e->status === 401 || $e->status === 422) {
                return back()->withInput($request->only('email', 'product', 'type'))
                    ->withErrors(['email' => __('site.portal_invalid_credentials')]);
            }
            if ($e->status === 429) {
                return back()->withInput($request->only('email'))->withErrors(['email' => __('site.portal_too_many_attempts')]);
            }
            throw $e;
        }

        $this->portal->start($request, (string) $result['token'], (array) $result['account']);

        return $this->afterAuth($request, $locale);
    }

    public function showRegister(Request $request, string $locale): View|RedirectResponse
    {
        if ($this->portal->check($request)) {
            return redirect()->route('portal.dashboard', $locale);
        }

        return view('portal.register', ['carry' => $this->carry($request)]);
    }

    public function register(PortalRegisterRequest $request, string $locale): RedirectResponse
    {
        $payload = $request->safe()->only(['name', 'email', 'phone', 'password']);
        $payload['locale'] = $locale;

        try {
            $result = $this->crm->post('auth/register', array_filter($payload, fn ($v) => $v !== null && $v !== ''), false)['data'];
        } catch (CrmApiException $e) {
            if ($e->status === 422) {
                return back()->withInput($request->except('password', 'password_confirmation'))
                    ->withErrors($e->fieldErrors() ?: ['email' => __('site.portal_generic_error')]);
            }
            if ($e->status === 429) {
                return back()->withInput($request->only('name', 'email', 'phone'))->withErrors(['email' => __('site.portal_too_many_attempts')]);
            }
            throw $e;
        }

        $this->portal->start($request, (string) $result['token'], (array) $result['account']);

        return $this->afterAuth($request, $locale);
    }

    public function logout(Request $request, string $locale): RedirectResponse
    {
        try {
            $this->crm->post('auth/logout');
        } catch (CrmApiException) {
            // The local session is cleared regardless; the CRM token simply expires.
        }

        $this->portal->clear($request);

        return redirect()->route('home', $locale)->with('success', __('site.portal_signed_out'));
    }

    /**
     * Intended URL first (it already carries ?product=&type=), else the carried product/type, else the dashboard.
     */
    private function afterAuth(Request $request, string $locale): RedirectResponse
    {
        $carry = $this->carry($request);
        $fallback = $carry !== []
            ? route('portal.request.create', ['locale' => $locale] + $carry)
            : route('portal.dashboard', $locale);

        return redirect()->intended($fallback);
    }

    /**
     * Product/request-type selection preserved across the sign-in / sign-up screens.
     *
     * @return array<string, string>
     */
    private function carry(Request $request): array
    {
        $carry = [];
        $product = (string) $request->input('product', '');
        $type = (string) $request->input('type', '');

        if ($product !== '' && preg_match('/^[a-z0-9-]{1,100}$/i', $product)) {
            $carry['product'] = $product;
        }
        if (in_array($type, ['demo', 'quotation', 'trial'], true)) {
            $carry['type'] = $type;
        }

        return $carry;
    }
}
