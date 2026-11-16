@extends('portal.layout')
@section('heading', __('site.portal_nav_support'))
@section('portal')
  <div class="mb-5"><x-button :href="route('portal.interventions.create', $currentLocale)">{{ __('site.portal_new_intervention') }}</x-button></div>
  <div class="grid gap-4 md:grid-cols-2">
    @forelse ($items as $item)
      <article class="rounded-2xl border border-[#E2E8F0] dark:border-[#26313C] bg-white dark:bg-[#111820] p-5">
        <div class="flex flex-wrap items-start justify-between gap-3">
          <div>
            <h2 class="font-bold">{{ $item['title'] }}</h2>
            <p class="text-xs text-[#64748B] dark:text-[#94A3B8]">{{ $item['reference'] ?? '' }} · {{ $item['type']['name'] ?? '' }}@if (! empty($item['product']['name'])) · {{ $item['product']['name'] }}@endif</p>
          </div>
          <x-status-badge :tone="! empty($item['status']['is_closed']) ? 'success' : (! empty($item['status']['awaits_customer']) ? 'warning' : 'neutral')" :label="$item['status']['label'] ?? ''" />
        </div>
        <p class="mt-2 text-xs text-[#64748B] dark:text-[#94A3B8]">{{ \App\Support\PortalFormat::dateTime($item['updated_at'] ?? null) }}</p>
        @if (! empty($item['status']['awaits_customer']))
          <p class="mt-2 text-sm font-semibold text-amber-700 dark:text-amber-300">{{ __('site.portal_awaits_you') }}</p>
        @endif
        <a class="mt-3 inline-block text-sm font-bold text-[#009EE0] hover:underline" href="{{ route('portal.interventions.show', ['locale' => $currentLocale, 'id' => $item['id']]) }}">{{ __('site.portal_details') }}</a>
      </article>
    @empty
      <x-empty-state class="md:col-span-2" :title="__('site.portal_no_interventions')" :text="__('site.portal_no_interventions_text')" />
    @endforelse
  </div>
@endsection
