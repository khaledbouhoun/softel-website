@php
  $apptState = match ($appt['status'] ?? '') {
    'held' => 'awaiting', 'confirmed' => 'scheduled', 'proposed' => 'proposed', 'rejected' => 'rejected',
    'expired' => 'expired', 'cancelled' => 'cancelled', 'completed' => 'completed', 'no_show' => 'no_show', default => 'received',
  };
  $link = \App\Support\PortalFormat::safeUrl($appt['meeting_url'] ?? null);
  $resource = \App\Support\PortalFormat::safeUrl($appt['resource_url'] ?? null);
@endphp
<article class="rounded-2xl border border-[#E2E8F0] dark:border-[#26313C] bg-white dark:bg-[#111820] p-5">
  <div class="flex flex-wrap items-start justify-between gap-3">
    <div>
      <h3 class="font-bold text-[#111111] dark:text-[#F8FAFC]">{{ $appt['title'] ?? ($appt['type']['name'] ?? '') }}</h3>
      <p class="text-sm text-[#64748B] dark:text-[#94A3B8]">{{ $appt['type']['name'] ?? '' }}@if (! empty($appt['product']['name'])) · {{ $appt['product']['name'] }}@endif</p>
    </div>
    <x-status-badge :tone="\App\Support\PortalState::tone($apptState)" :label="__('site.portal_state_'.$apptState)" />
  </div>
  <p class="mt-3 text-sm font-semibold">{{ \App\Support\PortalFormat::range($appt['starts_at'] ?? null, $appt['ends_at'] ?? null) }}</p>
  @if (! empty($appt['message']))
    <p class="mt-2 text-sm text-[#334155] dark:text-[#CBD5E1]">{{ $appt['message'] }}</p>
  @endif
  <div class="mt-3 flex flex-wrap gap-3">
    @if ($link)
      <x-button :href="$link" target="_blank" rel="noopener noreferrer">{{ __('site.portal_join_meeting') }}</x-button>
    @endif
    @if ($resource)
      <x-button variant="secondary" :href="$resource" target="_blank" rel="noopener noreferrer">{{ __('site.portal_open_resource') }}</x-button>
    @endif
    @if (($appt['status'] ?? '') === 'proposed')
      <form method="POST" action="{{ route('portal.appointments.accept', ['locale' => $currentLocale, 'id' => $appt['id']]) }}">@csrf<x-button>{{ __('site.portal_accept') }}</x-button></form>
      <form method="POST" action="{{ route('portal.appointments.decline', ['locale' => $currentLocale, 'id' => $appt['id']]) }}">@csrf<x-button variant="danger">{{ __('site.portal_decline') }}</x-button></form>
    @endif
  </div>
</article>
