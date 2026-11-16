@props(['tone' => 'neutral', 'label' => null])
@php
  $styles = [
    'success' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-200',
    'warning' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-200',
    'danger' => 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-200',
    'neutral' => 'bg-gray-100 text-gray-700 dark:bg-[#1b2530] dark:text-gray-300',
  ];
@endphp
<span {{ $attributes->merge(['class' => 'inline-flex items-center rounded-full px-2.5 py-1 text-xs font-bold whitespace-nowrap '.($styles[$tone] ?? $styles['neutral'])]) }}>{{ $label ?? $slot }}</span>
