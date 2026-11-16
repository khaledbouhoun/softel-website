@extends('portal.layout')
@section('heading', __('site.portal_nav_updates'))
@section('portal')
  <div class="space-y-4 max-w-3xl">
    @forelse ($updates as $update)
      <article class="rounded-2xl border border-[#E2E8F0] dark:border-[#26313C] bg-white dark:bg-[#111820] p-5">
        <div class="flex flex-wrap items-center justify-between gap-2">
          <h2 class="font-bold">{{ $update['title'] }}</h2>
          <x-status-badge tone="neutral" :label="$update['type']['name'] ?? ''" />
        </div>
        <p class="text-xs text-[#64748B] dark:text-[#94A3B8]">{{ $update['product']['name'] ?? '' }} · {{ \App\Support\PortalFormat::date($update['published_at'] ?? null) }}</p>
        @if (! empty($update['notes']))
          <p class="mt-3 text-sm whitespace-pre-line text-[#334155] dark:text-[#CBD5E1]">{{ $update['notes'] }}</p>
        @endif
      </article>
    @empty
      <x-empty-state :title="__('site.portal_no_updates')" :text="__('site.portal_no_updates_text')" />
    @endforelse
  </div>
@endsection
