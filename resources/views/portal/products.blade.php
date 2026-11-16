@extends('portal.layout')
@section('heading', __('site.portal_nav_products'))
@section('portal')
  <div class="grid gap-5 md:grid-cols-2">
    @forelse ($products as $product)
      <article class="rounded-2xl border border-[#E2E8F0] dark:border-[#26313C] bg-white dark:bg-[#111820] p-5">
        <h2 class="text-lg font-extrabold">{{ $product['name'] }}</h2>
        @if (! empty($product['description']))
          <p class="mt-1 text-sm text-[#475569] dark:text-[#94A3B8]">{{ $product['description'] }}</p>
        @endif
        <h3 class="mt-4 text-xs font-bold uppercase tracking-wider text-[#64748B] dark:text-[#94A3B8]">{{ __('site.portal_resources') }}</h3>
        <ul class="mt-2 space-y-2">
          @forelse ($resources->get($product['id'], collect()) as $res)
            @php $href = \App\Support\PortalFormat::safeUrl($res['url'] ?? null); @endphp
            @if ($href)
              <li class="flex items-center justify-between gap-3 text-sm">
                <a class="font-semibold text-[#009EE0] hover:underline" href="{{ $href }}" target="_blank" rel="noopener noreferrer">{{ $res['title'] }}</a>
                <x-status-badge :label="$res['kind'] ?? ''" />
              </li>
            @endif
          @empty
            <li class="text-sm text-[#64748B] dark:text-[#94A3B8]">{{ __('site.portal_no_resources') }}</li>
          @endforelse
        </ul>
      </article>
    @empty
      <x-empty-state class="md:col-span-2" :title="__('site.portal_no_products')" :text="__('site.portal_no_products_text')" />
    @endforelse
  </div>
@endsection
