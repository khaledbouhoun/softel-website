@extends('portal.guest')
@section('heading', __('site.portal_register_title'))
@section('intro', __('site.portal_register_intro'))
@section('card')
  <form method="POST" action="{{ route('portal.register.store', $currentLocale) }}" class="space-y-4" novalidate>
    @csrf
    @foreach ($carry as $key => $val)
      <input type="hidden" name="{{ $key }}" value="{{ $val }}">
    @endforeach
    <x-form-field name="name" :label="__('site.portal_full_name')" required autocomplete="name" />
    <x-form-field name="email" type="email" :label="__('site.portal_email')" required autocomplete="email" dir="ltr" />
    <x-form-field name="phone" type="tel" :label="__('site.portal_phone')" autocomplete="tel" dir="ltr" />
    <x-form-field name="password" type="password" :label="__('site.portal_password')" :hint="__('site.portal_password_hint')" required autocomplete="new-password" />
    <x-form-field name="password_confirmation" type="password" :label="__('site.portal_password_confirm')" required autocomplete="new-password" />
    <x-button class="w-full">{{ __('site.portal_sign_up') }}</x-button>
  </form>
@endsection
@section('footer')
  <p>{{ __('site.portal_have_account') }}
    <a class="font-semibold text-[#009EE0] hover:underline" href="{{ route('portal.login', ['locale' => $currentLocale] + $carry) }}">{{ __('site.portal_sign_in') }}</a></p>
@endsection
