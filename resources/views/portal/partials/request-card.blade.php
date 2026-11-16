@php
  $state = \App\Support\PortalState::forRequest($req);
  $appt = $req['appointment'] ?? null;
@endphp
<article class="rounded-2xl border border-[#E2E8F0] dark:border-[#26313C] bg-white dark:bg-[#111820] p-5">
  <div class="flex flex-wrap items-start justify-between gap-3">
    <div>
      <h2 class="font-bold text-[#111111] dark:text-[#F8FAFC]">{{ __('site.portal_type_'.($req['type'] ?? 'demo')) }} - {{ $req['product']['name'] ?? '' }}</h2>
      <p class="text-xs text-[#64748B] dark:text-[#94A3B8]">#{{ $req['id'] }} · {{ \App\Support\PortalFormat::date($req['created_at'] ?? null) }}</p>
    </div>
    <x-status-badge :tone="\App\Support\PortalState::tone($state)" :label="__('site.portal_state_'.$state)" />
  </div>
  @if (is_array($appt) && ! empty($appt['starts_at']))
    <p class="mt-3 text-sm text-[#334155] dark:text-[#CBD5E1]"><span class="font-semibold">{{ __('site.portal_when') }}:</span> {{ \App\Support\PortalFormat::range($appt['starts_at'], $appt['ends_at'] ?? null) }}</p>
  @endif
  <div class="mt-4">
    <a class="text-sm font-bold text-[#009EE0] hover:underline" href="{{ route('portal.requests.show', ['locale' => $currentLocale, 'id' => $req['id']]) }}">{{ __('site.portal_details') }}</a>
  </div>
</article>
