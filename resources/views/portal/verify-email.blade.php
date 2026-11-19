@extends('portal.guest')
@section('heading', __('site.portal_verify_email_heading'))
@section('intro', __('site.portal_verify_email_instruction', ['email' => $email]))
@section('card')
  @if ($errors->has('email'))
    <x-alert type="error" class="mb-4">{{ $errors->first('email') }}</x-alert>
  @endif
  <form method="POST" action="{{ route('portal.verify.email.store', $currentLocale) }}" class="space-y-4" novalidate>
    @csrf
    <input type="hidden" name="email" value="{{ $email }}">
    @foreach ($carry as $key => $val)
      <input type="hidden" name="{{ $key }}" value="{{ $val }}">
    @endforeach
    <x-form-field name="code" :label="__('site.portal_verify_email_code_label')" required inputmode="numeric" pattern="[0-9]{6}" maxlength="6" autocomplete="one-time-code" dir="ltr" />
    <x-button class="w-full">{{ __('site.portal_verify_email_submit') }}</x-button>
  </form>
@endsection
@section('footer')
  <form method="POST" action="{{ route('portal.verify.resend', $currentLocale) }}">
    @csrf
    <input type="hidden" name="email" value="{{ $email }}">
    @foreach ($carry as $key => $val)
      <input type="hidden" name="{{ $key }}" value="{{ $val }}">
    @endforeach
    <button type="submit" class="font-semibold text-[#009EE0] hover:underline">{{ __('site.portal_verify_email_resend') }}</button>
  </form>
@endsection
