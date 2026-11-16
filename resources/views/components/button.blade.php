@props(['variant' => 'primary', 'href' => null, 'type' => 'submit'])
@php
  $base = 'inline-flex items-center justify-center gap-2 rounded-lg px-4 py-2.5 text-sm font-bold transition focus:outline-none focus:ring-2 focus:ring-[#009EE0] focus:ring-offset-2 dark:focus:ring-offset-[#0B0F14] disabled:opacity-60 disabled:cursor-not-allowed';
  $variants = [
    'primary' => 'bg-[#009EE0] text-white hover:bg-[#0088c2]',
    'secondary' => 'border border-[#CBD5E1] dark:border-[#26313C] text-[#111111] dark:text-[#F8FAFC] hover:border-[#009EE0] hover:text-[#009EE0]',
    'danger' => 'border border-red-300 text-red-700 hover:bg-red-50 dark:border-red-800 dark:text-red-300 dark:hover:bg-red-950/40',
  ];
  $classes = $base.' '.($variants[$variant] ?? $variants['primary']);
@endphp
@if ($href)
  <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</a>
@else
  <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</button>
@endif
