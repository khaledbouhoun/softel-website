@extends('portal.layout')
@section('heading', __('site.portal_request_title'))

@php
  $checked = array_map('strval', (array) old('product_ids', old('product_id') ? [old('product_id')] : $selectedProductIds));
  $currentType = old('type', $type);
  $typeChoices = [
    'demo' => ['portal_choice_presentation', 'portal_choice_presentation_text'],
    'trial' => ['portal_choice_demo_version', 'portal_choice_demo_version_text'],
    'quotation' => ['portal_type_quotation', 'portal_choice_quotation_text'],
  ];
  $productError = $errors->first('product_ids') ?: $errors->first('product_id') ?: $errors->first('product_ids.0');
@endphp

@section('portal')
  @if (session('portal_duplicate_id'))
    <x-alert type="warning" class="mb-6">
      {{ __('site.portal_duplicate_request') }}
      <a class="font-bold underline" href="{{ route('portal.requests.show', ['locale' => $currentLocale, 'id' => session('portal_duplicate_id')]) }}">{{ __('site.portal_view_request') }}</a>
    </x-alert>
  @endif

  @if (($onboarding['redirect_to'] ?? null) === 'new_request')
    <x-alert type="info" class="mb-6">
      <strong>{{ __('site.portal_onboarding_title') }}</strong> {{ __('site.portal_onboarding_text') }}
    </x-alert>
  @endif

  <form method="POST" action="{{ route('portal.request.store', $currentLocale) }}" class="grid gap-8 lg:grid-cols-3" novalidate id="request-form">
    @csrf
    <div class="lg:col-span-2 space-y-6">
      {{-- 1. What do you want? --}}
      <fieldset class="rounded-2xl border border-[#E2E8F0] dark:border-[#26313C] bg-white dark:bg-[#111820] p-5 sm:p-6">
        <legend class="px-1 text-base font-extrabold text-[#111111] dark:text-[#F8FAFC]">{{ __('site.portal_request_type') }}</legend>
        <div class="mt-3 grid gap-3 sm:grid-cols-3" id="type-choices">
          @foreach ($typeChoices as $value => [$titleKey, $textKey])
            <label class="cursor-pointer">
              <input type="radio" name="type" value="{{ $value }}" class="peer sr-only" @checked($currentType === $value)>
              <span class="block h-full rounded-xl border-2 border-[#CBD5E1] dark:border-[#26313C] p-4 transition peer-checked:border-[#009EE0] peer-checked:bg-sky-50 dark:peer-checked:bg-sky-950/30 peer-focus-visible:ring-2 peer-focus-visible:ring-[#009EE0] hover:border-[#009EE0]">
                <span class="block font-extrabold text-[#111111] dark:text-[#F8FAFC]">{{ __('site.'.$titleKey) }}</span>
                <span class="mt-1 block text-xs text-[#64748B] dark:text-[#94A3B8]">{{ __('site.'.$textKey) }}</span>
              </span>
            </label>
          @endforeach
        </div>
        @error('type')<p class="mt-2 text-xs font-semibold text-red-600 dark:text-red-400" role="alert">{{ $message }}</p>@enderror
      </fieldset>

      {{-- 2. Which product(s)? Products of one pack are alternatives; the others combine freely. --}}
      <fieldset class="rounded-2xl border border-[#E2E8F0] dark:border-[#26313C] bg-white dark:bg-[#111820] p-5 sm:p-6" id="product-section" aria-describedby="product-hint">
        <legend class="px-1 text-base font-extrabold text-[#111111] dark:text-[#F8FAFC]">{{ __('site.portal_products_legend') }} <span class="text-red-600" aria-hidden="true">*</span></legend>
        <p id="product-hint" class="text-xs text-[#64748B] dark:text-[#94A3B8]">{{ __('site.portal_products_hint') }}</p>

        <div class="mt-4 space-y-5">
          @foreach ($groups as $group)
            @php $isPack = ($group['type'] ?? '') === 'pack'; @endphp
            @if (! empty($group['products']))
              <div @if ($isPack) role="group" aria-label="{{ $group['pack']['name'] ?? '' }}" @endif class="{{ $isPack ? 'rounded-xl border border-dashed border-[#CBD5E1] dark:border-[#26313C] has-[:checked]:border-[#009EE0] has-[:checked]:border-solid has-[:checked]:ring-1 has-[:checked]:ring-[#009EE0] p-4 transition-all duration-200' : '' }}">
                @if ($isPack)
                  <div class="mb-3 flex items-center justify-between">
                    <p class="text-sm font-bold text-[#111111] dark:text-[#F8FAFC] flex items-center gap-2">
                      <span class="inline-block w-2 h-2 rounded-full bg-[#CBD5E1] dark:bg-[#475569] in-[.has-\[\:checked\]]:bg-[#009EE0]"></span>
                      {{ $group['pack']['name'] ?? '' }}
                    </p>
                    <span class="rounded-full bg-slate-100 dark:bg-slate-800 px-2.5 py-0.5 text-xs font-medium text-[#64748B] dark:text-[#94A3B8]">
                      {{ __('site.portal_pack_one_of') }}
                    </span>
                  </div>
                @endif
                <div class="grid gap-2 sm:grid-cols-2">
                  @foreach ($group['products'] as $p)
                    @php $hasDemo = ! empty($p['has_demo']); @endphp
                    <label data-product-card data-has-demo="{{ $hasDemo ? '1' : '0' }}" class="flex cursor-pointer items-start gap-3 rounded-lg border border-[#CBD5E1] dark:border-[#26313C] p-3 hover:border-[#009EE0] has-[:checked]:border-[#009EE0] has-[:checked]:bg-sky-50 dark:has-[:checked]:bg-sky-950/30 transition-all duration-200">
                      <input type="checkbox" name="product_ids[]" value="{{ $p['id'] }}" data-exclusive="{{ implode(',', (array) ($p['exclusive_with'] ?? [])) }}" data-has-demo="{{ $hasDemo ? '1' : '0' }}" @checked(in_array((string) $p['id'], $checked, true))
                        class="mt-0.5 h-4 w-4 rounded border-[#CBD5E1] text-[#009EE0] focus:ring-[#009EE0] disabled:cursor-not-allowed">
                      <span class="min-w-0 flex-1">
                        <span class="block text-sm font-bold text-[#111111] dark:text-[#F8FAFC] product-title">{{ $p['name'] }}</span>
                        @if (! empty($p['description']))<span class="block text-xs text-[#64748B] dark:text-[#94A3B8] product-desc">{{ \Illuminate\Support\Str::limit($p['description'], 80) }}</span>@endif
                        <span class="no-demo-badge hidden mt-1 text-[11px] font-medium text-amber-700 dark:text-amber-400">{{ __('site.portal_no_demo_available') }}</span>
                      </span>
                    </label>
                  @endforeach
                </div>
              </div>
            @endif
          @endforeach
        </div>
        <p id="product-error" class="mt-3 text-xs font-semibold text-red-600 dark:text-red-400" role="alert" @if (! $productError) hidden @endif>{{ $productError ?: __('site.portal_select_one_product') }}</p>
      </fieldset>

      {{-- 3. About you: if the customer already has saved details, show summary card only and prevent re-editing --}}
      @php
        $hasExistingDetails = ! empty($defaults['company'])
                           && ! empty($defaults['phone'])
                           && ! empty($defaults['contact_name'])
                           && ! empty($defaults['wilaya'])
                           && ! empty($defaults['city'])
                           && ! $errors->hasAny(['company', 'contact_name', 'phone', 'email', 'wilaya', 'city']);
      @endphp

      @if ($hasExistingDetails)
        <input type="hidden" name="company" value="{{ $defaults['company'] }}">
        <input type="hidden" name="business_activity" value="{{ $defaults['business_activity'] ?? '' }}">
        <input type="hidden" name="contact_name" value="{{ $defaults['contact_name'] ?? '' }}">
        <input type="hidden" name="job_title" value="{{ $defaults['job_title'] ?? '' }}">
        <input type="hidden" name="phone" value="{{ $defaults['phone'] }}">
        <input type="hidden" name="email" value="{{ $defaults['email'] }}">
        <input type="hidden" name="wilaya" value="{{ $defaults['wilaya'] ?? '' }}">
        <input type="hidden" name="city" value="{{ $defaults['city'] ?? '' }}">

        <div class="space-y-4 rounded-2xl border border-[#E2E8F0] dark:border-[#26313C] bg-white dark:bg-[#111820] p-5 sm:p-6">
          <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 pb-3 border-b border-[#E2E8F0] dark:border-[#26313C]">
            <h3 class="text-sm font-extrabold text-[#111111] dark:text-[#F8FAFC] flex items-center gap-2">
              <svg class="w-4 h-4 text-[#009EE0]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
              {{ __('site.portal_saved_info_title') }}
            </h3>
            <a href="{{ route('portal.profile', $currentLocale) }}" class="text-xs font-semibold text-[#009EE0] hover:underline flex items-center gap-1">
              {{ __('site.portal_edit_profile_link') }} &rarr;
            </a>
          </div>

          <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
            <div class="rounded-lg bg-slate-50 dark:bg-slate-900/50 p-3">
              <span class="text-[#64748B] dark:text-[#94A3B8] block mb-0.5">{{ __('site.portal_company') }}</span>
              <span class="font-bold text-[#111111] dark:text-[#F8FAFC]">{{ $defaults['company'] }}</span>
            </div>
            <div class="rounded-lg bg-slate-50 dark:bg-slate-900/50 p-3">
              <span class="text-[#64748B] dark:text-[#94A3B8] block mb-0.5">{{ __('site.portal_contact_name') }}</span>
              <span class="font-bold text-[#111111] dark:text-[#F8FAFC]">{{ $defaults['contact_name'] }}</span>
            </div>
            <div class="rounded-lg bg-slate-50 dark:bg-slate-900/50 p-3">
              <span class="text-[#64748B] dark:text-[#94A3B8] block mb-0.5">{{ __('site.portal_phone') }}</span>
              <span class="font-bold text-[#111111] dark:text-[#F8FAFC]" dir="ltr">{{ $defaults['phone'] }}</span>
            </div>
            <div class="rounded-lg bg-slate-50 dark:bg-slate-900/50 p-3">
              <span class="text-[#64748B] dark:text-[#94A3B8] block mb-0.5">{{ __('site.portal_wilaya') }}</span>
              <span class="font-bold text-[#111111] dark:text-[#F8FAFC]">{{ $defaults['wilaya'] ?? '' }} - {{ $defaults['city'] ?? '' }}</span>
            </div>
          </div>

          <div class="pt-2">
            <x-form-field as="textarea" name="notes" :label="__('site.portal_notes')" />
          </div>
        </div>
      @else
        <div class="space-y-5 rounded-2xl border border-[#E2E8F0] dark:border-[#26313C] bg-white dark:bg-[#111820] p-5 sm:p-6">
          <div class="grid gap-5 sm:grid-cols-2">
            <x-form-field name="company" :label="__('site.portal_company')" :value="$defaults['company'] ?? ''" required autocomplete="organization" />
            <x-form-field name="business_activity" :label="__('site.portal_business_activity')" :value="$defaults['business_activity'] ?? ''" />
            <x-form-field name="contact_name" :label="__('site.portal_contact_name')" :value="$defaults['contact_name'] ?? ''" required autocomplete="name" />
            <x-form-field name="job_title" :label="__('site.portal_job_title')" :value="$defaults['job_title'] ?? ''" autocomplete="organization-title" />
            <x-form-field name="phone" type="tel" :label="__('site.portal_phone')" :value="$defaults['phone'] ?? ''" required autocomplete="tel" dir="ltr" />
            <x-form-field name="email" type="email" :label="__('site.portal_email')" :value="$defaults['email'] ?? ''" required autocomplete="email" dir="ltr" />
            <x-wilaya-field name="wilaya" :label="__('site.portal_wilaya')" :wilayas="$wilayas" :value="$defaults['wilaya'] ?? ''" required />
            <x-form-field name="city" :label="__('site.portal_city')" :value="$defaults['city'] ?? ''" required autocomplete="address-level2" />
          </div>
          <x-form-field as="textarea" name="notes" :label="__('site.portal_notes')" />
        </div>
      @endif
    </div>

    <div class="space-y-5">
      <fieldset id="slot-section" class="rounded-2xl border border-[#E2E8F0] dark:border-[#26313C] bg-white dark:bg-[#111820] p-5">
        <legend class="px-1 text-base font-extrabold text-[#111111] dark:text-[#F8FAFC]">{{ __('site.portal_pick_slot') }}</legend>
        <p class="text-xs text-[#64748B] dark:text-[#94A3B8]">
          {{ __('site.portal_slot_tz', ['tz' => $timezone]) }}
          @if ($slotMinutes) {{ __('site.portal_slot_length', ['min' => $slotMinutes]) }} @endif
          @if ($holdMinutes) {{ __('site.portal_hold_notice', ['min' => $holdMinutes]) }} @endif
        </p>

        @error('slot_start')<p class="mt-2 text-xs font-semibold text-red-600 dark:text-red-400" role="alert">{{ $message }}</p>@enderror
        <p id="slot-client-error" class="mt-2 text-xs font-semibold text-red-600 dark:text-red-400" role="alert" hidden>{{ __('site.portal_slot_required') }}</p>

        @if ($slotsFailed)
          <x-alert type="warning" class="mt-3">{{ __('site.portal_slots_failed') }}</x-alert>
        @elseif (empty($days))
          <x-empty-state class="mt-3" :title="__('site.portal_no_slots')" :text="__('site.portal_no_slots_text')" />
        @else
          <div class="mt-4 space-y-4 max-h-[28rem] overflow-y-auto pe-1">
            @foreach ($days as $date => $day)
              <div>
                <p class="text-sm font-bold text-[#111111] dark:text-[#F8FAFC]">{{ $day['label'] }}</p>
                <div class="mt-2 flex flex-wrap gap-2">
                  @foreach ($day['slots'] as $slot)
                    <label class="cursor-pointer">
                      <input type="radio" name="slot_start" value="{{ $slot['value'] }}" class="peer sr-only" @checked(old('slot_start') === $slot['value'])>
                      <span class="block rounded-lg border border-[#CBD5E1] dark:border-[#26313C] px-3 py-1.5 text-sm font-semibold peer-checked:bg-[#009EE0] peer-checked:text-white peer-checked:border-[#009EE0] peer-focus-visible:ring-2 peer-focus-visible:ring-[#009EE0] hover:border-[#009EE0]" dir="ltr">{{ $slot['label'] }}</span>
                    </label>
                  @endforeach
                </div>
              </div>
            @endforeach
          </div>
        @endif

        <div class="mt-4 flex justify-between text-sm font-semibold">
          @if ($prevFrom)
            <a class="text-[#009EE0] hover:underline" href="{{ route('portal.request.create', array_filter(['locale' => $currentLocale, 'type' => request('type'), 'product' => request('product'), 'product_id' => request('product_id'), 'from' => $prevFrom])) }}">{{ __('site.portal_slots_earlier') }}</a>
          @else <span></span> @endif
          <a class="text-[#009EE0] hover:underline" href="{{ route('portal.request.create', array_filter(['locale' => $currentLocale, 'type' => request('type'), 'product' => request('product'), 'product_id' => request('product_id'), 'from' => $nextFrom])) }}">{{ __('site.portal_slots_later') }}</a>
        </div>
      </fieldset>

      <x-button class="w-full">{{ __('site.portal_submit_request') }}</x-button>
      <p class="text-xs text-[#64748B] dark:text-[#94A3B8]">{{ __('site.portal_request_help') }}</p>
    </div>
  </form>

  <script>
    (function () {
      var form = document.getElementById('request-form');
      if (!form) { return; }
      var types = form.querySelectorAll('input[name="type"]');
      var slots = document.getElementById('slot-section');
      var boxes = [].slice.call(form.querySelectorAll('input[name="product_ids[]"]'));
      var err = document.getElementById('product-error');
      function type() { var t = form.querySelector('input[name="type"]:checked'); return t ? t.value : 'demo'; }
      function checked() { return boxes.filter(function (b) { return b.checked; }); }

      // Products of one pack exclude each other; otherwise products combine freely.
      boxes.forEach(function (box) {
        box.addEventListener('change', function () {
          if (box.checked) {
            var exclusive = (box.dataset.exclusive || '').split(',').filter(Boolean);
            boxes.forEach(function (other) {
              if (other !== box && exclusive.indexOf(other.value) > -1) { other.checked = false; }
            });
          }
          err.hidden = checked().length > 0;
        });
      });

      function sync() {
        var currentType = type();
        var isTrial = currentType === 'trial';

        // When requesting a demo version (trial), disable and gray out products without a demo URL
        boxes.forEach(function (box) {
          var hasDemo = box.dataset.hasDemo === '1';
          var card = box.closest('[data-product-card]');
          var badge = card ? card.querySelector('.no-demo-badge') : null;

          if (isTrial && !hasDemo) {
            box.disabled = true;
            if (box.checked) {
              box.checked = false;
            }
            if (card) {
              card.classList.add('opacity-50', 'bg-slate-100', 'dark:bg-slate-900/60', 'cursor-not-allowed', 'border-slate-200', 'dark:border-slate-800');
              card.classList.remove('hover:border-[#009EE0]', 'cursor-pointer');
            }
            if (badge) {
              badge.classList.remove('hidden');
            }
          } else {
            box.disabled = false;
            if (card) {
              card.classList.remove('opacity-50', 'bg-slate-100', 'dark:bg-slate-900/60', 'cursor-not-allowed', 'border-slate-200', 'dark:border-slate-800');
              card.classList.add('hover:border-[#009EE0]', 'cursor-pointer');
            }
            if (badge) {
              badge.classList.add('hidden');
            }
          }
        });

        err.hidden = checked().length > 0;

        if (slots) {
          slots.hidden = currentType !== 'demo';
          if (slots.hidden) {
            slots.querySelectorAll('input[type=radio]').forEach(function (r) { r.checked = false; });
            var slotErr = document.getElementById('slot-client-error');
            if (slotErr) { slotErr.hidden = true; }
          }
        }
      }
      types.forEach(function (t) { t.addEventListener('change', sync); });
      sync();

      if (slots) {
        slots.querySelectorAll('input[type=radio]').forEach(function (r) {
          r.addEventListener('change', function () {
            var slotErr = document.getElementById('slot-client-error');
            if (slotErr) { slotErr.hidden = true; }
          });
        });
      }

      // Client-side validation (the CRM validates again): stop the submit and show what is missing.
      form.addEventListener('submit', function (event) {
        var missingProduct = checked().length === 0;
        err.hidden = !missingProduct;
        var missingSlot = type() === 'demo' && !form.querySelector('input[name="slot_start"]:checked');
        var slotErr = document.getElementById('slot-client-error');
        if (slotErr) { slotErr.hidden = !missingSlot; }
        var invalid = !form.checkValidity();
        if (missingProduct || missingSlot || invalid) {
          event.preventDefault();
          event.stopPropagation();
          if (missingProduct) {
            err.scrollIntoView({ block: 'center' });
          } else if (missingSlot) {
            slots.scrollIntoView({ block: 'center' });
          } else if (invalid) {
            form.reportValidity();
          }
        }
      }, true);
    })();
  </script>
@endsection
