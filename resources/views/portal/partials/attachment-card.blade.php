@php
  $params = ['locale' => $currentLocale, 'id' => $interventionId, 'attachmentId' => $a['id']];
  $download = route('portal.interventions.download', $params);
  $previewUrl = ! empty($a['is_previewable']) ? route('portal.interventions.preview', $params) : null;
  $isPdf = ($a['mime'] ?? '') === 'application/pdf';
  $ext = strtoupper(pathinfo((string) ($a['name'] ?? ''), PATHINFO_EXTENSION));
@endphp
<div class="flex items-center gap-3 rounded-xl border border-[#E2E8F0] dark:border-[#26313C] bg-white dark:bg-[#111820] p-3">
  <span aria-hidden="true" class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-gray-100 dark:bg-[#1b2530] text-[10px] font-extrabold text-[#475569] dark:text-[#CBD5E1]">{{ $ext ?: 'FILE' }}</span>
  <div class="min-w-0 flex-1">
    <p class="truncate text-sm font-semibold text-[#111111] dark:text-[#F8FAFC]" dir="auto" title="{{ $a['name'] ?? '' }}">{{ $a['name'] ?? '' }}</p>
    <p class="text-xs text-[#64748B] dark:text-[#94A3B8]" dir="ltr">{{ \App\Support\PortalFormat::fileSize($a['size'] ?? 0) }}</p>
  </div>
  @if ($previewUrl)
    <button type="button" data-preview="{{ $previewUrl }}" data-kind="{{ $isPdf ? 'pdf' : 'image' }}" data-name="{{ $a['name'] ?? '' }}"
      class="rounded-lg border border-[#CBD5E1] dark:border-[#26313C] px-3 py-1.5 text-xs font-bold hover:border-[#009EE0] hover:text-[#009EE0] focus:outline-none focus:ring-2 focus:ring-[#009EE0]">{{ __('site.portal_preview') }}</button>
  @endif
  <a href="{{ $download }}" class="rounded-lg bg-[#009EE0] px-3 py-1.5 text-xs font-bold text-white hover:bg-[#0088c2] focus:outline-none focus:ring-2 focus:ring-[#009EE0] focus:ring-offset-2">{{ __('site.portal_download') }}</a>
</div>
