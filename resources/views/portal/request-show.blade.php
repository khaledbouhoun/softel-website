@extends('portal.layout')
@php
  $state = \App\Support\PortalState::forRequest($req);
  $appt = $req['appointment'] ?? null;
  $customerMessage = $req['message'] ?? ($appt['message'] ?? null);
  $retryQuery = ['locale' => $currentLocale, 'type' => $req['type'] ?? 'demo', 'product_id' => $req['product']['id'] ?? null];
@endphp
@section('heading', __('site.portal_type_'.($req['type'] ?? 'demo')).' - '.($req['product']['name'] ?? ''))

@section('portal')
  <div class="max-w-3xl space-y-5">
    <div class="flex flex-wrap items-center gap-3">
      <x-status-badge :tone="\App\Support\PortalState::tone($state)" :label="__('site.portal_state_'.$state)" />
      <span class="text-sm text-[#64748B] dark:text-[#94A3B8]">#{{ $req['id'] }} · {{ \App\Support\PortalFormat::dateTime($req['created_at'] ?? null) }}</span>
    </div>

    @if ($state === 'awaiting')
      <x-alert type="info">
        {{ __('site.portal_awaiting_text') }}
        @if (! empty($appt['hold_expires_at'])) {{ __('site.portal_hold_until', ['time' => \App\Support\PortalFormat::dateTime($appt['hold_expires_at'])]) }} @endif
      </x-alert>
    @elseif ($state === 'scheduled')
      <x-alert type="success">{{ __('site.portal_scheduled_text') }}</x-alert>
    @elseif ($state === 'proposed')
      <x-alert type="warning">{{ __('site.portal_proposed_text') }}</x-alert>
    @elseif ($state === 'rejected')
      <x-alert type="error">{{ __('site.portal_rejected_text') }}</x-alert>
    @elseif ($state === 'expired')
      <x-alert type="warning">{{ __('site.portal_expired_text') }}</x-alert>
    @elseif ($state === 'received')
      <x-alert type="info">{{ __('site.portal_received_text') }}</x-alert>
    @endif

    @if ($customerMessage)
      <div class="rounded-2xl border border-[#E2E8F0] dark:border-[#26313C] bg-white dark:bg-[#111820] p-5">
        <p class="text-xs font-bold uppercase tracking-wider text-[#64748B] dark:text-[#94A3B8]">{{ __('site.portal_message_from_team') }}</p>
        <p class="mt-1 text-sm whitespace-pre-line">{{ $customerMessage }}</p>
      </div>
    @endif

    @if (is_array($appt))
      @include('portal.partials.appointment-card', ['appt' => $appt])
    @endif

    <div class="flex flex-wrap gap-3">
      @if (in_array($state, ['rejected', 'cancelled', 'expired'], true))
        <x-button :href="route('portal.request.create', array_filter($retryQuery))">
          {{ $state === 'rejected' && ($req['type'] ?? '') === 'demo' ? __('site.portal_choose_another_time') : __('site.portal_request_again') }}
        </x-button>
      @endif
      @if (in_array($req['status'] ?? '', ['pending', 'scheduled'], true) && in_array($state, ['awaiting', 'received', 'scheduled', 'proposed'], true))
        <form method="POST" action="{{ route('portal.requests.cancel', ['locale' => $currentLocale, 'id' => $req['id']]) }}" onsubmit="return confirm(@js(__('site.portal_cancel_confirm')))">
          @csrf
          <x-button variant="danger">{{ __('site.portal_cancel_request') }}</x-button>
        </form>
      @endif
      <x-button variant="secondary" :href="route('portal.requests.index', $currentLocale)">{{ __('site.portal_back') }}</x-button>
    </div>
  </div>
@endsection
