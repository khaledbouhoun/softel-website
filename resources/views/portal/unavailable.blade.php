@extends('layouts.app')
@section('title', __('site.portal_unavailable_title'))
@section('content')
  <section class="py-16">
    <div class="max-w-xl mx-auto px-4 text-center">
      <h1 class="text-2xl sm:text-3xl font-extrabold text-[#111111] dark:text-[#F8FAFC]">{{ __('site.portal_unavailable_title') }}</h1>
      <p class="mt-3 text-[#475569] dark:text-[#94A3B8]">{{ __('site.portal_unavailable_text') }}</p>
      <div class="mt-6 flex flex-wrap justify-center gap-3">
        <x-button variant="primary" :href="url()->current()">{{ __('site.portal_try_again') }}</x-button>
        <x-button variant="secondary" :href="route('contact.create', $currentLocale)">{{ __('site.nav_contact') }}</x-button>
      </div>
    </div>
  </section>
@endsection
