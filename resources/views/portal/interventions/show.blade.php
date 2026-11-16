@extends('portal.layout')
@section('heading', $item['title'])
@section('portal')
  <div class="grid gap-8 lg:grid-cols-3">
    <div class="lg:col-span-2 space-y-5">
      <div class="flex flex-wrap items-center gap-3">
        <x-status-badge :tone="! empty($item['status']['is_closed']) ? 'success' : (! empty($item['status']['awaits_customer']) ? 'warning' : 'neutral')" :label="$item['status']['label'] ?? ''" />
        <span class="text-sm text-[#64748B] dark:text-[#94A3B8]">{{ $item['reference'] ?? '' }} · {{ $item['type']['name'] ?? '' }}@if (! empty($item['product']['name'])) · {{ $item['product']['name'] }}@endif</span>
      </div>

      @if (! empty($item['status']['awaits_customer']))
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
        <ol class="mt-3 space-y-3">
          @forelse (($item['messages'] ?? []) as $m)
            <li class="rounded-2xl p-4 text-sm {{ ($m['author'] ?? '') === 'you' ? 'bg-sky-50 dark:bg-sky-950/30 border border-sky-200 dark:border-sky-900' : 'bg-gray-50 dark:bg-[#111820] border border-[#E2E8F0] dark:border-[#26313C]' }}">
              <div class="flex justify-between gap-2 text-xs text-[#64748B] dark:text-[#94A3B8]">
                <span class="font-bold">{{ ($m['author'] ?? '') === 'you' ? __('site.portal_you') : __('site.portal_support_team') }}</span>
                <span>{{ \App\Support\PortalFormat::dateTime($m['created_at'] ?? null) }}</span>
              </div>
              <p class="mt-1 whitespace-pre-line">{{ $m['body'] }}</p>
              @if (! empty($m['status_label']))
                <p class="mt-2 text-xs font-semibold text-[#009EE0]">{{ $m['status_label'] }}</p>
              @endif
            </li>
          @empty
            <li><x-empty-state :title="__('site.portal_no_messages')" /></li>
          @endforelse
        </ol>

        <form method="POST" action="{{ route('portal.interventions.message', ['locale' => $currentLocale, 'id' => $item['id']]) }}" class="mt-4 space-y-3" novalidate>
          @csrf
          <x-form-field as="textarea" name="body" :label="__('site.portal_reply')" required />
          <x-button>{{ __('site.portal_send_reply') }}</x-button>
        </form>
      </section>
    </div>

    <aside class="space-y-5">
      <section class="rounded-2xl border border-[#E2E8F0] dark:border-[#26313C] bg-white dark:bg-[#111820] p-5" aria-labelledby="files-title">
        <h2 id="files-title" class="text-base font-extrabold">{{ __('site.portal_files') }}</h2>
        <ul class="mt-3 space-y-2 text-sm">
          @forelse (($item['attachments'] ?? []) as $a)
            <li class="flex items-center justify-between gap-2">
              <a class="font-semibold text-[#009EE0] hover:underline break-all" href="{{ route('portal.interventions.download', ['locale' => $currentLocale, 'id' => $item['id'], 'attachmentId' => $a['id']]) }}">{{ $a['name'] }}</a>
              <span class="text-xs text-[#64748B] whitespace-nowrap" dir="ltr">{{ number_format(((int) ($a['size'] ?? 0)) / 1024, 0) }} KB</span>
            </li>
          @empty
            <li class="text-[#64748B] dark:text-[#94A3B8]">{{ __('site.portal_no_files') }}</li>
          @endforelse
        </ul>
        <form method="POST" enctype="multipart/form-data" action="{{ route('portal.interventions.attach', ['locale' => $currentLocale, 'id' => $item['id']]) }}" class="mt-4 space-y-3" novalidate>
          @csrf
          <x-form-field name="file" type="file" :label="__('site.portal_add_file')" :hint="__('site.portal_file_hint')" accept=".jpg,.jpeg,.png,.pdf,.txt,.zip" required />
          <x-button variant="secondary">{{ __('site.portal_upload') }}</x-button>
        </form>
      </section>

      @foreach ($transitions as $t)
        <form method="POST" action="{{ route('portal.interventions.transition', ['locale' => $currentLocale, 'id' => $item['id']]) }}">
          @csrf
          <input type="hidden" name="status_id" value="{{ $t['id'] }}">
          <x-button variant="secondary" class="w-full">{{ $t['label'] }}</x-button>
        </form>
      @endforeach
    </aside>
  </div>
@endsection
