@extends('portal.layout')
@section('heading', __('site.portal_new_intervention'))
@section('portal')
  @if (empty($products))
    <x-empty-state :title="__('site.portal_no_products')" :text="__('site.portal_support_needs_product')" />
  @else
    <form method="POST" action="{{ route('portal.interventions.store', $currentLocale) }}" class="max-w-2xl space-y-5 rounded-2xl border border-[#E2E8F0] dark:border-[#26313C] bg-white dark:bg-[#111820] p-5 sm:p-6" novalidate>
      @csrf
      <x-form-field as="select" name="intervention_type_id" :label="__('site.portal_intervention_type')" required>
        <option value="">{{ __('site.portal_choose') }}</option>
        @foreach ($types as $type)
          <option value="{{ $type['id'] }}" @selected((string) old('intervention_type_id') === (string) $type['id'])>{{ $type['name'] }}</option>
        @endforeach
      </x-form-field>
      <x-form-field as="select" name="product_id" :label="__('site.portal_product')" required>
        <option value="">{{ __('site.portal_choose_product') }}</option>
        @foreach ($products as $p)
          <option value="{{ $p['id'] }}" @selected((string) old('product_id') === (string) $p['id'])>{{ $p['name'] }}</option>
        @endforeach
      </x-form-field>
      <x-form-field name="title" :label="__('site.portal_subject')" required />
      <x-form-field as="textarea" name="description" :label="__('site.portal_description')" required rows="6" />
      <p class="text-xs text-[#64748B] dark:text-[#94A3B8]">{{ __('site.portal_attach_later') }}</p>
      <x-button>{{ __('site.portal_send') }}</x-button>
    </form>
  @endif
@endsection
