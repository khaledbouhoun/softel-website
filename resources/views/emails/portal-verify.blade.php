<!DOCTYPE html>
<html lang="{{ $mailLocale }}" dir="{{ $mailLocale === 'ar' ? 'rtl' : 'ltr' }}">
<body style="font-family: Arial, sans-serif; color: #111111; line-height: 1.6;">
  <h2 style="color: #009EE0;">SOFTEL</h2>
  @if ($name !== '')
    <p>{{ __('site.portal_verify_mail_hello', ['name' => $name]) }}</p>
  @endif
  <p>{{ __('site.portal_verify_mail_intro') }}</p>
  <p dir="ltr" style="text-align: center; font-size: 32px; font-weight: bold; letter-spacing: 8px; color: #009EE0; margin: 24px 0;">{{ $code }}</p>
  <p>{{ __('site.portal_verify_mail_expiry', ['minutes' => 10]) }}</p>
  <p style="font-size: 13px; color: #555555;">{{ __('site.portal_reset_mail_ignore') }}</p>
</body>
</html>
