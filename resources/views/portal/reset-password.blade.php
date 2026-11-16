@extends('portal.guest')
@section('heading', __('site.portal_reset_title'))
@section('card')
  <form method="POST" action="{{ route('portal.password.update', $currentLocale) }}" class="space-y-4" novalidate>
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">
    @error('token')<x-alert type="error">{{ $message }}</x-alert>@enderror
    <x-form-field name="password" type="password" :label="__('site.portal_new_password')" :hint="__('site.portal_password_hint')" required autocomplete="new-password" />
    <x-form-field name="password_confirmation" type="password" :label="__('site.portal_password_confirm')" required autocomplete="new-password" />
    <x-button class="w-full">{{ __('site.portal_reset_submit') }}</x-button>
  </form>
@endsection
@section('footer')
  <a class="font-semibold text-[#009EE0] hover:underline" href="{{ route('portal.password.request', $currentLocale) }}">{{ __('site.portal_reset_request_new') }}</a>
@endsection
