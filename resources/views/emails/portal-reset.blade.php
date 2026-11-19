<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<body style="font-family: Arial, sans-serif; color: #111111; line-height: 1.6;">
  <h2 style="color: #009EE0;">SOFTEL</h2>
  <p>{{ __('site.portal_reset_mail_intro') }}</p>
  <p><a href="{{ $resetUrl }}"
      style="display:inline-block;background:#009EE0;color:#ffffff;padding:10px 18px;border-radius:6px;text-decoration:none;font-weight:bold;">{{ __('site.portal_reset_mail_button') }}</a>
  </p>
  <p style="font-size: 13px; color: #555555;">{{ __('site.portal_reset_mail_ignore') }}</p>
</body>

</html>
