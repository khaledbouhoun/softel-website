@extends('portal.layout')
@section('heading', __('site.portal_profile_title'))

@php
  $p = $account['profile'] ?? [];
  $field = fn (string $key, $fallback = '') => old($key, $fallback);
  $accountType = $status['account_type'] ?? 'visitor';
@endphp

@section('portal')
  <div class="grid gap-8 lg:grid-cols-3">
    {{-- Identity card --}}
    <aside class="space-y-5">
      <div class="rounded-2xl border border-[#E2E8F0] dark:border-[#26313C] bg-white dark:bg-[#111820] p-6 text-center">
        <x-avatar :name="$account['name'] ?? ''" :url="$account['avatar_url'] ?? null" size="h-24 w-24 text-2xl" class="mx-auto" />
        <p class="mt-4 text-lg font-extrabold text-[#111111] dark:text-[#F8FAFC]">{{ $account['name'] ?? '' }}</p>
        <p class="text-sm text-[#64748B] dark:text-[#94A3B8]" dir="ltr">{{ $account['email'] ?? '' }}</p>
        @if (! empty($p['company']))<p class="mt-1 text-sm font-semibold">{{ $p['company'] }}</p>@endif
        <div class="mt-3"><x-status-badge :tone="$accountType === 'customer' ? 'success' : 'neutral'" :label="__('site.portal_account_type_'.$accountType)" /></div>
        <p class="mt-4 text-xs text-[#64748B] dark:text-[#94A3B8]">{{ __('site.portal_avatar_note') }}</p>
      </div>

      <dl class="rounded-2xl border border-[#E2E8F0] dark:border-[#26313C] bg-white dark:bg-[#111820] p-5 text-sm space-y-3">
        <div class="flex justify-between gap-3"><dt class="text-[#64748B] dark:text-[#94A3B8]">{{ __('site.portal_open_requests') }}</dt><dd class="font-extrabold">{{ (int) ($status['open_requests'] ?? 0) }}</dd></div>
        <div class="flex justify-between gap-3"><dt class="text-[#64748B] dark:text-[#94A3B8]">{{ __('site.portal_nav_products') }}</dt><dd class="font-extrabold">{{ (int) ($status['owned_products'] ?? 0) }}</dd></div>
        @if (! empty($status['latest_request']))
          @php $latest = $status['latest_request']; @endphp
          <div class="flex justify-between gap-3"><dt class="text-[#64748B] dark:text-[#94A3B8]">{{ __('site.portal_latest_request') }}</dt>
            <dd><a class="font-bold text-[#009EE0] hover:underline" href="{{ route('portal.requests.show', ['locale' => $currentLocale, 'id' => $latest['id']]) }}">#{{ $latest['id'] }}</a></dd></div>
        @endif
      </dl>
    </aside>

    <div class="lg:col-span-2 space-y-8">
      {{-- Details editor --}}
      <form method="POST" action="{{ route('portal.profile.update', $currentLocale) }}" class="space-y-5 rounded-2xl border border-[#E2E8F0] dark:border-[#26313C] bg-white dark:bg-[#111820] p-5 sm:p-6" novalidate id="profile-form">
        @csrf
        @method('PATCH')
        <h2 class="text-lg font-extrabold">{{ __('site.portal_profile_details') }}</h2>
        <div class="grid gap-5 sm:grid-cols-2">
          <x-form-field name="name" :label="__('site.portal_full_name')" :value="$account['name'] ?? ''" required autocomplete="name" />
          <x-form-field name="phone" type="tel" :label="__('site.portal_phone')" :value="$account['phone'] ?? ''" autocomplete="tel" dir="ltr" />
          <x-form-field name="company" :label="__('site.portal_company')" :value="$p['company'] ?? ''" autocomplete="organization" />
          <x-form-field name="contact_name" :label="__('site.portal_contact_name')" :value="$p['contact_name'] ?? ''" />
          <x-form-field name="job_title" :label="__('site.portal_job_title')" :value="$p['job_title'] ?? ''" autocomplete="organization-title" />
          <x-form-field name="business_activity" :label="__('site.portal_business_activity')" :value="$p['business_activity'] ?? ''" />
          <x-wilaya-field name="wilaya" :label="__('site.portal_wilaya')" :wilayas="$wilayas" :value="$p['wilaya'] ?? ''" />
          <x-form-field name="city" :label="__('site.portal_city')" :value="$p['city'] ?? ''" autocomplete="address-level2" />
          <x-form-field as="select" name="locale" :label="__('site.portal_language')" required>
            @foreach (['fr', 'en', 'ar'] as $l)
              <option value="{{ $l }}" @selected(old('locale', $account['locale'] ?? $currentLocale) === $l)>{{ __('site.portal_language_'.$l) }}</option>
            @endforeach
          </x-form-field>
          <div>
            <span class="mb-1.5 block text-sm font-semibold">{{ __('site.portal_email') }}</span>
            <p class="rounded-lg border border-[#E2E8F0] dark:border-[#26313C] bg-gray-50 dark:bg-[#0e141b] px-3 py-2.5 text-sm text-[#64748B] dark:text-[#94A3B8]" dir="ltr">{{ $account['email'] ?? '' }}</p>
          </div>
        </div>
        <x-button>{{ __('site.portal_save_changes') }}</x-button>
      </form>

      {{-- Request history and status --}}
      <section aria-labelledby="history-title">
        <div class="flex flex-wrap items-center justify-between gap-3">
          <h2 id="history-title" class="text-lg font-extrabold">{{ __('site.portal_request_history') }}</h2>
          <x-button variant="secondary" :href="route('portal.request.create', $currentLocale)">{{ __('site.portal_nav_new_request') }}</x-button>
        </div>
        @if (empty($requests))
          <x-empty-state class="mt-3" :title="__('site.portal_no_requests')" :text="__('site.portal_no_requests_text')" />
        @else
          <div class="mt-3 overflow-x-auto rounded-2xl border border-[#E2E8F0] dark:border-[#26313C] bg-white dark:bg-[#111820]">
            <table class="w-full min-w-[34rem] text-sm">
              <thead class="text-start text-xs uppercase tracking-wider text-[#64748B] dark:text-[#94A3B8]">
                <tr><th scope="col" class="px-4 py-3 text-start">{{ __('site.portal_col_request') }}</th><th scope="col" class="px-4 py-3 text-start">{{ __('site.portal_col_date') }}</th><th scope="col" class="px-4 py-3 text-start">{{ __('site.portal_col_status') }}</th><th scope="col" class="px-4 py-3"><span class="sr-only">{{ __('site.portal_details') }}</span></th></tr>
              </thead>
              <tbody class="divide-y divide-[#E2E8F0] dark:divide-[#26313C]">
                @foreach ($requests as $req)
                  @php $state = \App\Support\PortalState::forRequest($req); @endphp
                  <tr>
                    <td class="px-4 py-3 font-semibold">{{ __('site.portal_choice_'.(['demo' => 'presentation', 'trial' => 'demo_version'][$req['type'] ?? ''] ?? 'quotation')) }} - {{ $req['product']['name'] ?? '' }}</td>
                    <td class="px-4 py-3 text-[#64748B] dark:text-[#94A3B8]">{{ \App\Support\PortalFormat::date($req['created_at'] ?? null) }}</td>
                    <td class="px-4 py-3"><x-status-badge :tone="\App\Support\PortalState::tone($state)" :label="__('site.portal_state_'.$state)" /></td>
                    <td class="px-4 py-3 text-end"><a class="font-bold text-[#009EE0] hover:underline" href="{{ route('portal.requests.show', ['locale' => $currentLocale, 'id' => $req['id']]) }}">{{ __('site.portal_details') }}</a></td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @endif
      </section>
    </div>
  </div>
@endsection
