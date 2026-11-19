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
        <p id="product-hint" class="text-xs text-[#64748B] dark:text-[#94A3B8]"><span data-hint="multi">{{ __('site.portal_products_hint') }}</span><span data-hint="single" hidden>{{ __('site.portal_presentation_single') }}</span></p>

        <div class="mt-4 space-y-5">
          @foreach ($groups as $group)
            @php $isPack = ($group['type'] ?? '') === 'pack'; @endphp
            @if (! empty($group['products']))
              <div @if ($isPack) role="group" aria-label="{{ $group['pack']['name'] ?? '' }}" @endif class="{{ $isPack ? 'rounded-xl border border-dashed border-[#CBD5E1] dark:border-[#26313C] p-3' : '' }}">
                @if ($isPack)
                  <p class="mb-2 text-sm font-bold text-[#111111] dark:text-[#F8FAFC]">{{ $group['pack']['name'] ?? '' }} <span class="font-normal text-xs text-[#64748B] dark:text-[#94A3B8]">· {{ __('site.portal_pack_one_of') }}</span></p>
                @endif
                <div class="grid gap-2 sm:grid-cols-2">
                  @foreach ($group['products'] as $p)
                    <label class="flex cursor-pointer items-start gap-3 rounded-lg border border-[#CBD5E1] dark:border-[#26313C] p-3 hover:border-[#009EE0] has-[:checked]:border-[#009EE0] has-[:checked]:bg-sky-50 dark:has-[:checked]:bg-sky-950/30">
                      <input type="checkbox" name="product_ids[]" value="{{ $p['id'] }}" data-exclusive="{{ implode(',', (array) ($p['exclusive_with'] ?? [])) }}" @checked(in_array((string) $p['id'], $checked, true))
                        class="mt-0.5 h-4 w-4 rounded border-[#CBD5E1] text-[#009EE0] focus:ring-[#009EE0]">
                      <span class="min-w-0">
                        <span class="block text-sm font-bold text-[#111111] dark:text-[#F8FAFC]">{{ $p['name'] }}</span>
                        @if (! empty($p['description']))<span class="block text-xs text-[#64748B] dark:text-[#94A3B8]">{{ \Illuminate\Support\Str::limit($p['description'], 80) }}</span>@endif
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

      {{-- 3. About you (saved details are filled in; change them if needed). --}}
      <div class="space-y-5 rounded-2xl border border-[#E2E8F0] dark:border-[#26313C] bg-white dark:bg-[#111820] p-5 sm:p-6">
        @if (! empty($defaults['company']))
          <p class="text-xs text-[#64748B] dark:text-[#94A3B8]">{{ __('site.portal_prefilled_notice') }}</p>
        @endif
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
      var hintMulti = document.querySelector('[data-hint="multi"]'), hintSingle = document.querySelector('[data-hint="single"]');

      function type() { var t = form.querySelector('input[name="type"]:checked'); return t ? t.value : 'demo'; }
      function single() { return type() === 'demo'; }
      function checked() { return boxes.filter(function (b) { return b.checked; }); }

      // Products of one pack exclude each other; a presentation holds one slot, so it takes one product only.
      boxes.forEach(function (box) {
        box.addEventListener('change', function () {
          if (box.checked) {
            var exclusive = (box.dataset.exclusive || '').split(',').filter(Boolean);
            boxes.forEach(function (other) {
              if (other !== box && (single() || exclusive.indexOf(other.value) > -1)) { other.checked = false; }
            });
          }
          err.hidden = checked().length > 0;
        });
      });

      function sync() {
        var demo = single();
        if (slots) {
          slots.hidden = type() !== 'demo';
          if (slots.hidden) { slots.querySelectorAll('input[type=radio]').forEach(function (r) { r.checked = false; }); }
        }
        if (hintMulti) { hintMulti.hidden = demo; }
        if (hintSingle) { hintSingle.hidden = !demo; }
        if (demo) { checked().slice(1).forEach(function (b) { b.checked = false; }); }
      }
      types.forEach(function (t) { t.addEventListener('change', sync); });
      sync();

      // Client-side validation (the CRM validates again): stop the submit and show what is missing.
      form.addEventListener('submit', function (event) {
        var missingProduct = checked().length === 0;
        err.hidden = !missingProduct;
        var invalid = !form.checkValidity();
        if (missingProduct || invalid) {
          event.preventDefault();
          event.stopPropagation();
          if (invalid) { form.reportValidity(); } else { err.scrollIntoView({ block: 'center' }); }
        }
      }, true);
    })();
  </script>
@endsection
