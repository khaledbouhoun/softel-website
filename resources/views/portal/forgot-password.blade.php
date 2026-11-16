@extends('portal.guest')
@section('heading', __('site.portal_forgot_title'))
@section('intro', __('site.portal_forgot_intro'))
@section('card')
  <form method="POST" action="{{ route('portal.password.email', $currentLocale) }}" class="space-y-4" novalidate>
    @csrf
    <x-form-field name="email" type="email" :label="__('site.portal_email')" required autocomplete="email" dir="ltr" />
    <x-button class="w-full">{{ __('site.portal_forgot_send') }}</x-button>
  </form>
@endsection
@section('footer')
  <a class="font-semibold text-[#009EE0] hover:underline" href="{{ route('portal.login', $currentLocale) }}">{{ __('site.portal_back_to_sign_in') }}</a>
@endsection
