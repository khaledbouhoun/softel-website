@extends('portal.layout')
@section('heading', $item['title'])

@php
  $locked = array_key_exists('can_reply', $item) ? ! $item['can_reply'] : false;
  // English visitors get the CRM's own sentence; other languages the translated one.
  $closedNotice = $currentLocale === 'en' ? ($item['closed_notice'] ?? __('site.portal_closed_notice')) : __('site.portal_closed_notice');
@endphp

@section('portal')
  <div class="grid gap-8 lg:grid-cols-3">
    <div class="lg:col-span-2 space-y-5">
      <div class="flex flex-wrap items-center gap-3">
        <x-status-badge :tone="! empty($item['status']['is_closed']) ? 'success' : (! empty($item['status']['awaits_customer']) ? 'warning' : 'neutral')" :label="$item['status']['label'] ?? ''" />
        <span class="text-sm text-[#64748B] dark:text-[#94A3B8]">{{ $item['reference'] ?? '' }} · {{ $item['type']['name'] ?? '' }}@if (! empty($item['product']['name'])) · {{ $item['product']['name'] }}@endif</span>
      </div>

      @if (! empty($item['status']['awaits_customer']) && ! $locked)
        <x-alert type="warning">{{ __('site.portal_awaits_you_text') }}</x-alert>
      @endif

      @if (! empty($item['description']))
        <div class="rounded-2xl border border-[#E2E8F0] dark:border-[#26313C] bg-white dark:bg-[#111820] p-5">
          <p class="text-xs font-bold uppercase tracking-wider text-[#64748B] dark:text-[#94A3B8]">{{ __('site.portal_description') }}</p>
          <p class="mt-1 text-sm whitespace-pre-line">{{ $item['description'] }}</p>
        </div>
      @endif

      @if (! empty($item['resolution']))
        <x-alert type="success"><strong>{{ __('site.portal_resolution') }}:</strong> {{ $item['resolution'] }}</x-alert>
      @endif

      <section aria-labelledby="conversation-title">
        <h2 id="conversation-title" class="text-lg font-extrabold">{{ __('site.portal_conversation') }}</h2>
        <ol class="mt-3 space-y-3" id="conversation">
          @forelse (($item['messages'] ?? []) as $m)
            @php
              $role = $m['sender']['role'] ?? (($m['author'] ?? '') === 'you' ? 'client' : (($m['author'] ?? '') === 'system' ? 'system' : 'employee'));
              $event = $m['event'] ?? null;
              $when = \App\Support\PortalFormat::dateTime($m['created_at'] ?? ($event['at'] ?? null));
            @endphp

            @if ($role === 'system')
              {{-- Timeline event: status change or reopening, in the middle of the conversation. --}}
              <li class="flex justify-center">
                <p class="max-w-full rounded-full bg-gray-100 dark:bg-[#1b2530] px-4 py-1.5 text-center text-xs text-[#475569] dark:text-[#CBD5E1]">
                  @if (($event['type'] ?? $m['kind'] ?? '') === 'reopened')
                    {{ __('site.portal_event_reopened', ['actor' => ($event['actor']['role'] ?? '') === 'client' ? __('site.portal_you') : __('site.portal_support_badge')]) }}
                  @else
                    {{ __('site.portal_event_status_changed', ['status' => $event['new_status']['name'] ?? ($m['status_label'] ?? '')]) }}
                  @endif
                  · <time>{{ $when }}</time>
                </p>
              </li>
            @else
              @php $mine = $role === 'client'; @endphp
              {{-- Employees on the right in the brand colour with a Support badge; the client on the left, neutral. --}}
              <li class="flex gap-3 {{ $mine ? '' : 'flex-row-reverse' }}">
                <x-avatar :name="$mine ? ($m['sender']['name'] ?? '') : 'S'" :url="$m['sender']['avatar_url'] ?? null" size="h-9 w-9" />
                <div class="min-w-0 max-w-[85%] rounded-2xl p-4 text-sm {{ $mine ? 'border border-[#E2E8F0] dark:border-[#26313C] bg-gray-50 dark:bg-[#111820]' : 'bg-[#009EE0] text-white' }}">
                  <div class="flex flex-wrap items-center justify-between gap-2 text-xs {{ $mine ? 'text-[#64748B] dark:text-[#94A3B8]' : 'text-white/85' }}">
                    <span class="flex items-center gap-2 font-bold">
                      {{ $mine ? __('site.portal_you') : __('site.portal_support_team') }}
                      @unless ($mine)<span class="rounded-full bg-white/20 px-2 py-0.5 text-[10px] font-extrabold uppercase tracking-wide">{{ __('site.portal_support_badge') }}</span>@endunless
                    </span>
                    <time>{{ $when }}</time>
                  </div>
                  <p class="mt-1 whitespace-pre-line" dir="auto">{{ $m['body'] }}</p>
                  @if (! empty($m['attachments']))
                    <div class="mt-3 space-y-2 text-[#111111] dark:text-[#F8FAFC]">
                      @foreach ($m['attachments'] as $a)
                        @include('portal.partials.attachment-card', ['a' => $a, 'interventionId' => $item['id']])
                      @endforeach
                    </div>
                  @endif
                </div>
              </li>
            @endif
          @empty
            <li><x-empty-state :title="__('site.portal_no_messages')" /></li>
          @endforelse
        </ol>

        @if ($locked)
          <x-alert type="info" class="mt-4" id="closed-notice">{{ $closedNotice }}</x-alert>
        @else
          <form method="POST" action="{{ route('portal.interventions.message', ['locale' => $currentLocale, 'id' => $item['id']]) }}" class="mt-4 space-y-3" novalidate>
            @csrf
            <x-form-field as="textarea" name="body" :label="__('site.portal_reply')" required />
            <x-button>{{ __('site.portal_send_reply') }}</x-button>
          </form>
        @endif
      </section>
    </div>

    <aside class="space-y-5">
      <section class="rounded-2xl border border-[#E2E8F0] dark:border-[#26313C] bg-white dark:bg-[#111820] p-5" aria-labelledby="files-title">
        <h2 id="files-title" class="text-base font-extrabold">{{ __('site.portal_files') }}</h2>
        <div class="mt-3 space-y-2">
          @forelse (($item['attachments'] ?? []) as $a)
            @include('portal.partials.attachment-card', ['a' => $a, 'interventionId' => $item['id']])
          @empty
            <p class="text-sm text-[#64748B] dark:text-[#94A3B8]">{{ __('site.portal_no_files') }}</p>
          @endforelse
        </div>
        @unless ($locked)
          <form method="POST" enctype="multipart/form-data" action="{{ route('portal.interventions.attach', ['locale' => $currentLocale, 'id' => $item['id']]) }}" class="mt-4 space-y-3" novalidate>
            @csrf
            <x-form-field name="file" type="file" :label="__('site.portal_add_file')" :hint="__('site.portal_file_hint')" accept=".jpg,.jpeg,.png,.pdf,.txt,.zip" required />
            <x-button variant="secondary">{{ __('site.portal_upload') }}</x-button>
          </form>
        @endunless
      </section>

      @unless ($locked)
        @foreach ($transitions as $t)
          <form method="POST" action="{{ route('portal.interventions.transition', ['locale' => $currentLocale, 'id' => $item['id']]) }}">
            @csrf
            <input type="hidden" name="status_id" value="{{ $t['id'] }}">
            <x-button variant="secondary" class="w-full">{{ $t['label'] }}</x-button>
          </form>
        @endforeach
      @endunless
    </aside>
  </div>
  @include('portal.partials.preview-modal')
@endsection
