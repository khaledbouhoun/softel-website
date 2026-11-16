@props(['name', 'label', 'as' => 'input', 'type' => 'text', 'value' => null, 'required' => false, 'hint' => null, 'dir' => null])
@php
  $id = 'f-'.str_replace(['[', ']', '.'], '-', $name);
  $current = old($name, $value);
  $hasError = $errors->has($name);
  $control = 'block w-full rounded-lg border bg-white dark:bg-[#111820] px-3 py-2.5 text-sm text-[#111111] dark:text-[#F8FAFC] focus:outline-none focus:ring-2 focus:ring-[#009EE0] '
    .($hasError ? 'border-red-500' : 'border-[#CBD5E1] dark:border-[#26313C]');
  $describedBy = trim(($hasError ? $id.'-error ' : '').($hint ? $id.'-hint' : ''));
@endphp
<div>
  <label for="{{ $id }}" class="mb-1.5 block text-sm font-semibold text-[#111111] dark:text-[#F8FAFC]">
    {{ $label }}@if ($required)<span class="text-red-600" aria-hidden="true"> *</span>@endif
  </label>
  @if ($as === 'textarea')
    <textarea id="{{ $id }}" name="{{ $name }}" rows="4" @required($required) @if ($describedBy) aria-describedby="{{ $describedBy }}" @endif @if ($hasError) aria-invalid="true" @endif {{ $attributes->merge(['class' => $control]) }}>{{ $current }}</textarea>
  @elseif ($as === 'select')
    <select id="{{ $id }}" name="{{ $name }}" @required($required) @if ($describedBy) aria-describedby="{{ $describedBy }}" @endif @if ($hasError) aria-invalid="true" @endif {{ $attributes->merge(['class' => $control]) }}>{{ $slot }}</select>
  @else
    <input id="{{ $id }}" name="{{ $name }}" type="{{ $type }}" @if ($type !== 'password' && $type !== 'file') value="{{ $current }}" @endif @required($required) @if ($dir) dir="{{ $dir }}" @endif @if ($describedBy) aria-describedby="{{ $describedBy }}" @endif @if ($hasError) aria-invalid="true" @endif {{ $attributes->merge(['class' => $control]) }}>
  @endif
  @if ($hint)
    <p id="{{ $id }}-hint" class="mt-1 text-xs text-[#64748B] dark:text-[#94A3B8]">{{ $hint }}</p>
  @endif
  @error($name)
    <p id="{{ $id }}-error" class="mt-1 text-xs font-semibold text-red-600 dark:text-red-400">{{ $message }}</p>
  @enderror
</div>
