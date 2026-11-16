@extends('portal.layout')
@section('heading', __('site.portal_welcome', ['name' => $overview['account']['name'] ?? '']))

@section('portal')
  <div class="grid gap-4 sm:grid-cols-3">
    <div class="rounded-2xl border border-[#E2E8F0] dark:border-[#26313C] bg-white dark:bg-[#111820] p-5">
      <p class="text-sm text-[#64748B] dark:text-[#94A3B8]">{{ __('site.portal_open_requests') }}</p>
      <p class="mt-1 text-3xl font-extrabold">{{ (int) ($overview['open_requests'] ?? 0) }}</p>
    </div>
    <div class="rounded-2xl border border-[#E2E8F0] dark:border-[#26313C] bg-white dark:bg-[#111820] p-5">
      <p class="text-sm text-[#64748B] dark:text-[#94A3B8]">{{ __('site.portal_nav_products') }}</p>
      <p class="mt-1 text-3xl font-extrabold">{{ (int) ($overview['products_count'] ?? 0) }}</p>
    </div>
    <div class="rounded-2xl border border-[#E2E8F0] dark:border-[#26313C] bg-white dark:bg-[#111820] p-5">
      <p class="text-sm text-[#64748B] dark:text-[#94A3B8]">{{ __('site.portal_nav_trials') }}</p>
      <p class="mt-1 text-3xl font-extrabold">{{ count($overview['active_trials'] ?? []) }}</p>
    </div>
  </div>

  <div class="mt-8 grid gap-8 lg:grid-cols-2">
    <div>
      <h2 class="text-lg font-extrabold">{{ __('site.portal_upcoming') }}</h2>
      <div class="mt-3 space-y-3">
        @forelse (($overview['upcoming_appointments'] ?? []) as $appt)
          @include('portal.partials.appointment-card', ['appt' => $appt])
        @empty
          <x-empty-state :title="__('site.portal_no_upcoming')" :text="__('site.portal_no_upcoming_text')">
            <x-button :href="route('portal.request.create', $currentLocale)">{{ __('site.portal_nav_new_request') }}</x-button>
          </x-empty-state>
        @endforelse
      </div>
    </div>
    <div>
      <h2 class="text-lg font-extrabold">{{ __('site.portal_active_trials') }}</h2>
      <div class="mt-3 space-y-3">
        @forelse (($overview['active_trials'] ?? []) as $trial)
          @include('portal.partials.trial-card', ['trial' => $trial])
        @empty
          <x-empty-state :title="__('site.portal_no_trials')" />
        @endforelse
      </div>
    </div>
  </div>
@endsection
