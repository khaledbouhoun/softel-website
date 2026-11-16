<?php

namespace App\Http\Controllers\Portal;

use App\Exceptions\CrmApiException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Portal\PortalForgotPasswordRequest;
use App\Http\Requests\Portal\PortalResetPasswordRequest;
use App\Mail\PortalPasswordResetMail;
use App\Services\CrmPortalClient;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class PasswordController extends Controller
{
    public function __construct(private readonly CrmPortalClient $crm) {}

    public function request(): View
    {
        return view('portal.forgot-password');
    }

    /**
     * The CRM hands the reset token to this server only; it reaches the browser solely inside the e-mailed link.
     * The visitor always sees the same neutral message, whether or not the account exists.
     */
    public function email(PortalForgotPasswordRequest $request, string $locale): RedirectResponse
    {
        $email = (string) $request->validated('email');

        try {
            $token = $this->crm->post('auth/forgot-password', ['email' => $email], false)['data']['reset_token'] ?? null;
        } catch (CrmApiException $e) {
            if ($e->status === 429) {
                return back()->withInput()->withErrors(['email' => __('site.portal_too_many_attempts')]);
            }
            throw $e;
        }

        if (filled($token)) {
            Mail::to($email)->send(new PortalPasswordResetMail(
                route('portal.password.reset', ['locale' => $locale, 'token' => $token]),
            ));
        }

        return redirect()->route('portal.password.request', $locale)->with('success', __('site.portal_forgot_neutral'));
    }

    public function reset(Request $request, string $locale, string $token): View
    {
        return view('portal.reset-password', ['token' => $token]);
    }

    public function update(PortalResetPasswordRequest $request, string $locale): RedirectResponse
    {
        try {
            $this->crm->post('auth/reset-password', $request->safe()->only(['token', 'password']), false);
        } catch (CrmApiException $e) {
            if (in_array($e->status, [422, 400, 404, 410], true)) {
                return back()->withErrors(['password' => __('site.portal_reset_invalid')]);
            }
            if ($e->status === 429) {
                return back()->withErrors(['password' => __('site.portal_too_many_attempts')]);
            }
            throw $e;
        }

        return redirect()->route('portal.login', $locale)->with('success', __('site.portal_reset_done'));
    }
}
