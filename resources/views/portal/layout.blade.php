@extends('layouts.app')

@section('title', $__env->yieldContent('heading', __('site.portal_title')))

@php
  $portalAccount = session(\App\Services\PortalSession::ACCOUNT, []);
  $tabs = [
    ['portal.dashboard', 'portal_nav_overview', 'portal.dashboard'],
    ['portal.request.create', 'portal_nav_new_request', 'portal.request.*'],
    ['portal.requests.index', 'portal_nav_requests', 'portal.requests.*'],
    ['portal.appointments.index', 'portal_nav_appointments', 'portal.appointments.*'],
    ['portal.trials', 'portal_nav_trials', 'portal.trials'],
    ['portal.products', 'portal_nav_products', 'portal.products'],
    ['portal.interventions.index', 'portal_nav_support', 'portal.interventions.*'],
    ['portal.updates', 'portal_nav_updates', 'portal.updates'],
  ];
@endphp

@section('content')
  <section class="bg-gray-50 dark:bg-[#0e141b] border-b border-[#E2E8F0] dark:border-[#26313C]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8">
      <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
          <p class="text-xs font-bold uppercase tracking-wider text-[#009EE0]">{{ __('site.portal_title') }}</p>
          <p class="text-lg font-extrabold text-[#111111] dark:text-[#F8FAFC]">{{ $portalAccount['name'] ?? '' }}</p>
        </div>
        <form method="POST" action="{{ route('portal.logout', $currentLocale) }}">
          @csrf
          <x-button variant="secondary">{{ __('site.portal_sign_out') }}</x-button>
        </form>
      </div>
      <nav class="mt-5 -mb-px flex gap-1 overflow-x-auto" aria-label="{{ __('site.portal_title') }}">
        @foreach ($tabs as [$route, $label, $pattern])
          <a href="{{ route($route, $currentLocale) }}" @if (request()->routeIs($pattern)) aria-current="page" @endif
            class="whitespace-nowrap border-b-2 px-3 py-3 text-sm font-semibold transition-colors {{ request()->routeIs($pattern) ? 'border-[#009EE0] text-[#009EE0]' : 'border-transparent text-[#475569] dark:text-[#94A3B8] hover:text-[#009EE0]' }}">
            {{ __('site.'.$label) }}
          </a>
        @endforeach
      </nav>
    </div>
  </section>

  <section class="py-8 lg:py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <h1 class="text-2xl sm:text-3xl font-extrabold text-[#111111] dark:text-[#F8FAFC]">@yield('heading')</h1>
      <div class="mt-5">@include('portal.partials.flash')</div>
      <div class="mt-6">@yield('portal')</div>
      @include('portal.partials.ai-assistant-slot')
    </div>
  </section>

  @include('portal.partials.once-script')
@endsection
