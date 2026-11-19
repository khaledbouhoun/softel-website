@props(['name' => '', 'url' => null, 'size' => 'h-10 w-10'])
@php
  $src = \App\Support\PortalFormat::avatarUrl($url);
@endphp
@if ($src)
  {{-- Google pictures refuse some referrers: none is sent. --}}
  <img src="{{ $src }}" alt="" referrerpolicy="no-referrer" loading="lazy" {{ $attributes->merge(['class' => $size.' shrink-0 rounded-full object-cover bg-gray-100 dark:bg-[#1b2530]']) }}>
@else
  <span aria-hidden="true" {{ $attributes->merge(['class' => $size.' shrink-0 inline-flex items-center justify-center rounded-full bg-[#009EE0]/15 text-sm font-extrabold text-[#009EE0]']) }}>{{ \App\Support\PortalFormat::initials($name) }}</span>
@endif
