@php $trialUrl = \App\Support\PortalFormat::safeUrl($trial['url'] ?? null); @endphp
<article class="rounded-2xl border border-[#E2E8F0] dark:border-[#26313C] bg-white dark:bg-[#111820] p-5">
  <div class="flex flex-wrap items-start justify-between gap-3">
    <h3 class="font-bold">{{ $trial['product']['name'] ?? '' }}</h3>
    <x-status-badge :tone="\App\Support\PortalState::tone($trial['state'] ?? '')" :label="__('site.portal_trial_'.($trial['state'] ?? 'upcoming'))" />
  </div>
  <p class="mt-2 text-sm text-[#64748B] dark:text-[#94A3B8]">
    {{ \App\Support\PortalFormat::date($trial['starts_at'] ?? null) }}@if (! empty($trial['ends_at'])) - {{ \App\Support\PortalFormat::date($trial['ends_at']) }}@endif
  </p>
  @if ($trialUrl)
    <div class="mt-3"><x-button :href="$trialUrl" target="_blank" rel="noopener noreferrer">{{ __('site.portal_open_trial') }}</x-button></div>
  @endif
</article>
