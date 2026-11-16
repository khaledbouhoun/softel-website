@extends('portal.layout')
@section('heading', __('site.portal_request_title'))

@section('portal')
  @if (session('portal_duplicate_id'))
    <x-alert type="warning" class="mb-6">
      {{ __('site.portal_duplicate_request') }}
      <a class="font-bold underline" href="{{ route('portal.requests.show', ['locale' => $currentLocale, 'id' => session('portal_duplicate_id')]) }}">{{ __('site.portal_view_request') }}</a>
    </x-alert>
  @endif

  <form method="POST" action="{{ route('portal.request.store', $currentLocale) }}" class="grid gap-8 lg:grid-cols-3" novalidate id="request-form">
    @csrf
    <div class="lg:col-span-2 space-y-5 rounded-2xl border border-[#E2E8F0] dark:border-[#26313C] bg-white dark:bg-[#111820] p-5 sm:p-6">
      <div class="grid gap-5 sm:grid-cols-2">
        <x-form-field as="select" name="type" :label="__('site.portal_request_type')" required>
          @foreach (['demo', 'quotation', 'trial'] as $t)
            <option value="{{ $t }}" @selected(old('type', $type) === $t)>{{ __('site.portal_type_'.$t) }}</option>
          @endforeach
        </x-form-field>
        <x-form-field as="select" name="product_id" :label="__('site.portal_product')" required>
          <option value="">{{ __('site.portal_choose_product') }}</option>
          @foreach ($products as $p)
            <option value="{{ $p['id'] }}" @selected((string) old('product_id', $selectedProductId) === (string) $p['id'])>{{ $p['name'] }}</option>
          @endforeach
        </x-form-field>
        <x-form-field name="company" :label="__('site.portal_company')" required autocomplete="organization" />
        <x-form-field name="business_activity" :label="__('site.portal_business_activity')" />
        <x-form-field name="contact_name" :label="__('site.portal_contact_name')" :value="$account['name'] ?? ''" required autocomplete="name" />
        <x-form-field name="job_title" :label="__('site.portal_job_title')" autocomplete="organization-title" />
        <x-form-field name="phone" type="tel" :label="__('site.portal_phone')" :value="$account['phone'] ?? ''" required autocomplete="tel" dir="ltr" />
        <x-form-field name="email" type="email" :label="__('site.portal_email')" :value="$account['email'] ?? ''" required autocomplete="email" dir="ltr" />
        <x-form-field name="wilaya" :label="__('site.portal_wilaya')" required autocomplete="address-level1" />
        <x-form-field name="city" :label="__('site.portal_city')" required autocomplete="address-level2" />
      </div>
      <x-form-field as="textarea" name="notes" :label="__('site.portal_notes')" />
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
      var type = document.getElementById('f-type');
      var slots = document.getElementById('slot-section');
      if (!type || !slots) { return; }
      function sync() {
        var demo = type.value === 'demo';
        slots.hidden = !demo;
        if (!demo) { slots.querySelectorAll('input[type=radio]').forEach(function (r) { r.checked = false; }); }
      }
      type.addEventListener('change', sync);
      sync();
    })();
  </script>
@endsection
