@extends('portal.layout')
@section('heading', __('site.portal_nav_downloads'))
@section('portal')
  <div class="space-y-8 max-w-4xl">
    @forelse ($byProduct as $product => $items)
      <section aria-labelledby="dl-{{ $loop->index }}">
        <h2 id="dl-{{ $loop->index }}" class="text-lg font-extrabold">{{ $product }}</h2>
        <div class="mt-3 space-y-3">
          @foreach ($items as $r)
            @php
              $external = \App\Support\PortalFormat::safeUrl($r['download_url'] ?? null);
              $installer = $r['installer'] ?? null;
            @endphp
            <article class="rounded-2xl border border-[#E2E8F0] dark:border-[#26313C] bg-white dark:bg-[#111820] p-5">
              <div class="flex flex-wrap items-center justify-between gap-2">
                <h3 class="font-bold"><span dir="ltr">{{ __('site.portal_version') }} {{ $r['version_number'] }}</span></h3>
                <span class="text-xs text-[#64748B] dark:text-[#94A3B8]">{{ __('site.portal_release_date') }}: {{ \App\Support\PortalFormat::date($r['release_date'] ?? null) }}</span>
              </div>
              @if (! empty($r['changelog']))
                <div class="mt-3">
                  <p class="text-xs font-bold uppercase tracking-wider text-[#64748B] dark:text-[#94A3B8]">{{ __('site.portal_changelog') }}</p>
                  <p class="mt-1 text-sm whitespace-pre-line text-[#334155] dark:text-[#CBD5E1]">{{ \Illuminate\Support\Str::limit($r['changelog'], 400) }}</p>
                </div>
              @endif
              <div class="mt-4 flex flex-wrap items-center gap-3">
                @if ($installer)
                  <x-button :href="route('portal.downloads.file', ['locale' => $currentLocale, 'id' => $r['id']])">{{ __('site.portal_download_installer') }}
                    <span class="text-xs font-normal opacity-80" dir="ltr">{{ $installer['name'] ?? '' }} · {{ \App\Support\PortalFormat::fileSize($installer['size'] ?? 0) }}</span></x-button>
                @endif
                @if ($external)
                  <x-button variant="secondary" :href="$external" target="_blank" rel="noopener noreferrer">{{ __('site.portal_download_link') }}</x-button>
                @endif
                @if (! $installer && ! $external)
                  <span class="text-sm text-[#64748B] dark:text-[#94A3B8]">{{ __('site.portal_no_download_yet') }}</span>
                @endif
              </div>
            </article>
          @endforeach
        </div>
      </section>
    @empty
      <x-empty-state :title="__('site.portal_no_releases')" :text="__('site.portal_no_releases_text')" />
    @endforelse
  </div>
@endsection
