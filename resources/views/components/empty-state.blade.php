@props(['title', 'text' => null])
<div {{ $attributes->merge(['class' => 'rounded-2xl border border-dashed border-[#CBD5E1] dark:border-[#26313C] p-8 text-center']) }}>
  <p class="font-bold text-[#111111] dark:text-[#F8FAFC]">{{ $title }}</p>
  @if ($text)
    <p class="mt-1 text-sm text-[#64748B] dark:text-[#94A3B8]">{{ $text }}</p>
  @endif
  @if (! $slot->isEmpty())
    <div class="mt-4">{{ $slot }}</div>
  @endif
</div>
