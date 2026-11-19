<?php

namespace App\Http\Controllers\Portal;

use App\Exceptions\CrmApiException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Portal\PortalLoginRequest;
use App\Http\Requests\Portal\PortalRegisterRequest;
use App\Mail\PortalEmailVerificationMail;
use App\Services\CrmPortalClient;
use App\Services\PortalSession;
use App\Support\PortalFormat;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class AuthController extends Controller
{
    private const SESSION_VERIFY_EMAIL = 'portal_verify_email';

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
            if ($e->is('GOOGLE_ACCOUNT_ONLY')) {
                return back()->withInput($request->only('email', 'product', 'type'))
                    ->withErrors(['email' => __('site.portal_google_only')]);
            }
            if ($e->status === 401 || $e->status === 422) {
                return back()->withInput($request->only('email', 'product', 'type'))
                    ->withErrors(['email' => __('site.portal_invalid_credentials')]);
            }
            if ($e->status === 429) {
                return back()->withInput($request->only('email'))->withErrors(['email' => __('site.portal_too_many_attempts')]);
            }
            throw $e;
        }

        $this->portal->start($request, (string) $result['token'], (array) $result['account'], isset($result['onboarding']) ? (array) $result['onboarding'] : null);

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

        $this->portal->start($request, (string) $result['token'], (array) $result['account'], isset($result['onboarding']) ? (array) $result['onboarding'] : null);

        $email = (string) ($result['account']['email'] ?? $payload['email']);
        $name = (string) ($result['account']['name'] ?? $payload['name']);

        if (filled($result['email_verification_code'] ?? null)) {
            $this->sendVerificationMail($name, (string) $result['email_verification_code'], $locale, $email);
        }

        // The e-mail is confirmed on its own screen; product/type survive via carry().
        $request->session()->put(self::SESSION_VERIFY_EMAIL, $email);

        return redirect()->route('portal.verify.email', ['locale' => $locale, 'email' => $email] + $this->carry($request));
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

    /* ----------------------------------------------------------------- Google OAuth */

    public function redirectToGoogle(Request $request, string $locale): RedirectResponse
    {
        // Product/type and the locale must survive the round trip through Google.
        $request->session()->put([
            'portal_google_locale' => $locale,
            'portal_carry' => $this->carry($request),
        ]);

        $redirectUri = route('portal.auth.google.callback', ['locale' => $locale]);

        return Socialite::driver('google')->redirectUrl($redirectUri)->redirect();
    }

    public function handleGoogleCallback(Request $request, string $locale): RedirectResponse
    {
        $locale = in_array($locale, ['en', 'fr', 'ar'], true)
            ? $locale
            : (string) $request->session()->get('portal_google_locale', config('app.locale', 'en'));

        $carry = (array) $request->session()->pull('portal_carry', []);
        $request->session()->forget('portal_google_locale');

        $loginUrl = fn (string $message) => redirect()
            ->route('portal.login', ['locale' => $locale] + $carry)
            ->with('portal_error', $message);

        // The visitor cancelled on Google's consent screen.
        if ($request->has('error')) {
            return $loginUrl(__('site.portal_google_failed'));
        }

        try {
            $redirectUri = route('portal.auth.google.callback', ['locale' => $locale]);
            $googleUser = Socialite::driver('google')->redirectUrl($redirectUri)->user();
        } catch (Throwable $e) {
            // InvalidStateException (expired/forged state) or a token-exchange failure: never log tokens or codes.
            Log::warning('portal.google.callback_failed', ['exception' => $e::class]);

            return $loginUrl(__('site.portal_google_failed'));
        }

        $raw = (array) $googleUser->user;
        $emailVerifiedByGoogle = filter_var($raw['email_verified'] ?? $raw['verified_email'] ?? false, FILTER_VALIDATE_BOOLEAN);

        // Linking by e-mail is only safe when Google itself vouches for the address.
        if (! filled($googleUser->getEmail()) || ! $emailVerifiedByGoogle) {
            return $loginUrl(__('site.portal_google_unverified'));
        }

        try {
            $result = $this->crm->googleAuth([
                'google_id' => (string) $googleUser->getId(),
                'email' => (string) $googleUser->getEmail(),
                'name' => (string) ($googleUser->getName() ?: $googleUser->getEmail()),
                'locale' => $locale,
                'avatar_url' => PortalFormat::avatarUrl($googleUser->getAvatar()),
            ]);
        } catch (CrmApiException $e) {
            if ($e->status === 429) {
                return $loginUrl(__('site.portal_too_many_attempts'));
            }
            if (in_array($e->status, [403, 409, 422], true)) {
                return $loginUrl(__('site.portal_google_failed'));
            }
            throw $e;
        }

        $this->portal->start($request, (string) $result['token'], (array) $result['account'], isset($result['onboarding']) ? (array) $result['onboarding'] : null);

        return $this->afterAuth($request, $locale, $carry);
    }

    /* ----------------------------------------------------------------- E-mail verification */

    public function showVerifyEmail(Request $request, string $locale): View|RedirectResponse
    {
        $account = $this->portal->account($request);

        if ($this->portal->check($request) && ($account['email_verified'] ?? false)) {
            return redirect()->route('portal.dashboard', $locale);
        }

        $email = $this->verifyEmailAddress($request);

        if ($email === null) {
            return redirect()->route('portal.login', ['locale' => $locale] + $this->carry($request));
        }

        return view('portal.verify-email', ['email' => $email, 'carry' => $this->carry($request)]);
    }

    public function verifyEmail(Request $request, string $locale): RedirectResponse
    {
        $data = $request->validate([
            'code' => ['required', 'digits:6'],
            'email' => ['nullable', 'email:rfc', 'max:191'],
        ]);

        $email = $this->verifyEmailAddress($request, $data['email'] ?? null);

        if ($email === null) {
            return redirect()->route('portal.login', ['locale' => $locale] + $this->carry($request));
        }

        $back = fn (string $field, string $message) => back()
            ->withInput($request->only('email', 'product', 'type'))
            ->withErrors([$field => $message]);

        try {
            $this->crm->verifyEmail($email, $data['code']);
        } catch (CrmApiException $e) {
            if ($e->is('VERIFICATION_ATTEMPTS_EXCEEDED')) {
                return $back('code', __('site.portal_verify_email_locked'));
            }
            if (in_array($e->status, [404, 422], true)) {
                return $back('code', __('site.portal_verify_email_invalid'));
            }
            if ($e->status === 429) {
                return $back('code', __('site.portal_too_many_attempts'));
            }
            throw $e;
        }

        $request->session()->forget(self::SESSION_VERIFY_EMAIL);

        if ($this->portal->check($request)) {
            $this->portal->remember($request, array_merge($this->portal->account($request), ['email_verified' => true]));

            return $this->afterAuth($request, $locale)->with('success', __('site.portal_verify_email_success'));
        }

        return redirect()->route('portal.login', ['locale' => $locale] + $this->carry($request))
            ->with('success', __('site.portal_verify_email_success'));
    }

    public function resendVerification(Request $request, string $locale): RedirectResponse
    {
        $data = $request->validate(['email' => ['required', 'email:rfc', 'max:191']]);

        try {
            $result = $this->crm->resendVerification($data['email']);
        } catch (CrmApiException $e) {
            if ($e->status === 429) {
                return back()->withErrors(['email' => __('site.portal_too_many_attempts')]);
            }
            throw $e;
        }

        // The CRM only returns a code for an existing, still-unverified account; the answer is neutral either way.
        if (filled($result['verification_code'] ?? null)) {
            $name = (string) ($this->portal->account($request)['name'] ?? '');
            $this->sendVerificationMail($name, (string) $result['verification_code'], $locale, $data['email']);
        }

        return redirect()
            ->route('portal.verify.email', ['locale' => $locale, 'email' => $data['email']] + $this->carry($request))
            ->with('success', __('site.portal_verify_email_resent'));
    }

    /** Mail problems must not break registration: the visitor can always press "Resend code". */
    private function sendVerificationMail(string $name, string $code, string $locale, string $email): void
    {
        try {
            Mail::to($email)->send(new PortalEmailVerificationMail($name, $code, $locale, $email));
        } catch (Throwable $e) {
            Log::error('portal.verify_mail_failed', ['exception' => $e::class]);
        }
    }

    /** Address being verified: explicit input, else query string, else session, else the signed-in account. */
    private function verifyEmailAddress(Request $request, ?string $explicit = null): ?string
    {
        $candidates = [
            $explicit,
            $request->query('email'),
            $request->session()->get(self::SESSION_VERIFY_EMAIL),
            $this->portal->account($request)['email'] ?? null,
        ];

        foreach ($candidates as $candidate) {
            if (is_string($candidate) && Validator::make(['e' => $candidate], ['e' => 'email:rfc|max:191'])->passes()) {
                return $candidate;
            }
        }

        return null;
    }

    /**
     * New customers go to the request form; otherwise the intended URL (it already carries ?product=&type=), else the carried product/type, else the dashboard.
     *
     * @param  array<string, string>|null  $carry  pre-restored product/type (Google callback); defaults to the request's
     */
    private function afterAuth(Request $request, string $locale, ?array $carry = null): RedirectResponse
    {
        $carry ??= $this->carry($request);

        // First sign-in: the CRM sends new customers straight to the request form (their chosen product/type is kept).
        if (($this->portal->onboarding($request)['redirect_to'] ?? null) === 'new_request') {
            return redirect()->route('portal.request.create', ['locale' => $locale] + $carry);
        }

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
