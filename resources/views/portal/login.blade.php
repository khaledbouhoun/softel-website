@extends('portal.guest')
@section('heading', __('site.portal_login_title'))
@section('intro', __('site.portal_login_intro'))
@section('card')
  <form method="POST" action="{{ route('portal.login.store', $currentLocale) }}" class="space-y-4" novalidate>
    @csrf
    @foreach ($carry as $key => $val)
      <input type="hidden" name="{{ $key }}" value="{{ $val }}">
    @endforeach
    <x-form-field name="email" type="email" :label="__('site.portal_email')" required autocomplete="email" dir="ltr" />
    <x-form-field name="password" type="password" :label="__('site.portal_password')" required autocomplete="current-password" />
    <x-button class="w-full">{{ __('site.portal_sign_in') }}</x-button>
  </form>
@endsection
@section('footer')
  <p><a class="font-semibold text-[#009EE0] hover:underline" href="{{ route('portal.password.request', $currentLocale) }}">{{ __('site.portal_forgot_link') }}</a></p>
  <p class="mt-2">{{ __('site.portal_no_account') }}
    <a class="font-semibold text-[#009EE0] hover:underline" href="{{ route('portal.register', ['locale' => $currentLocale] + $carry) }}">{{ __('site.portal_sign_up') }}</a></p>
@endsection
