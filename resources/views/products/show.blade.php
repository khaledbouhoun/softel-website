@extends('layouts.app')

@section('title', $product->getLocalized('title'))

@section('content')

    {{-- Breadcrumb Header --}}
    <section class="bg-gray-50 dark:bg-[#0e141b] border-b border-[#E2E8F0] dark:border-[#26313C] py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6">
            <a href="{{ route('products.index', $currentLocale) }}"
               class="inline-flex items-center gap-2 text-sm font-semibold text-[#009EE0] hover:text-[#0088c2] mb-4">
                <svg class="w-4 h-4 flip-rtl rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <span>{{ __('site.products_back') }}</span>
            </a>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-[#111111] dark:text-[#F8FAFC]">
                {{ $product->getLocalized('title') }}
            </h1>
            <p class="text-lg text-[#475569] dark:text-[#94A3B8] mt-2 leading-relaxed">
                {{ $product->getLocalized('summary') }}
            </p>
        </div>
    </section>

    {{-- Product Body --}}
    <section class="py-16 bg-white dark:bg-[#0B0F14]">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 space-y-8">
            {{-- Product Showcase Image with Safe Fallback --}}
            <div class="rounded-2xl overflow-hidden border border-[#E2E8F0] dark:border-[#26313C] bg-gradient-to-b from-sky-50/40 via-white to-gray-50/20 dark:from-[#0B0F14] dark:to-[#111820] shadow-md flex items-center justify-center p-8 sm:p-12 min-h-[320px] sm:min-h-[420px]">
                @if ($product->image)
                    <img
                        src="{{ asset($product->image) }}"
                        alt="{{ $product->getLocalized('title') }}"
                        class="max-h-80 sm:max-h-[420px] w-auto h-auto object-contain mx-auto"
                        loading="lazy"
                    >
                @else
                    <div class="flex items-center justify-center p-8 opacity-60">
                        <img src="{{ asset('images/softel_logo_black.svg') }}"
                             data-light="{{ asset('images/softel_logo_black.svg') }}"
                             data-dark="{{ asset('images/softel_logo_white.svg') }}"
                             data-logo-light="{{ asset('images/softel_logo_black.svg') }}"
                             data-logo-dark="{{ asset('images/softel_logo_white.svg') }}"
                             alt="SOFTEL"
                             class="h-14 w-auto object-contain">
                    </div>
                @endif
            </div>

            @php $techStack = $product->getLocalizedTechStack(); @endphp
            @if (! empty($techStack))
                <div>
                    <h3 class="text-xs font-bold text-[#64748B] dark:text-[#94A3B8] uppercase tracking-wider mb-3">{{ __('site.products_tech_title') }}</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($techStack as $tech)
                            <span class="text-xs font-semibold bg-gray-100 dark:bg-[#111820] border border-[#E2E8F0] dark:border-[#26313C] text-[#111111] dark:text-[#F8FAFC] px-3.5 py-1.5 rounded-lg">
                                {{ $tech }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="text-[#334155] dark:text-[#CBD5E1] leading-relaxed text-base sm:text-lg space-y-4">
                <p class="border-s-4 border-[#009EE0] ps-4 py-1 font-medium text-[#111111] dark:text-[#F8FAFC]">
                    {{ $product->getLocalized('description') }}
                </p>
            </div>

            <div class="pt-8 border-t border-[#E2E8F0] dark:border-[#26313C]">
                <div class="p-6 sm:p-8 rounded-2xl bg-gray-50 dark:bg-[#111820] border border-[#E2E8F0] dark:border-[#26313C] flex flex-col sm:flex-row items-center justify-between gap-6">
                    <div>
                        <h4 class="font-bold text-[#111111] dark:text-[#F8FAFC] text-lg mb-1">{{ __('site.products_interest_title') }}</h4>
                        <p class="text-sm text-[#475569] dark:text-[#94A3B8]">{{ __('site.products_interest_desc') }}</p>
                    </div>
                    <a href="{{ route('contact.create', $currentLocale) }}"
                       class="bg-[#009EE0] hover:bg-[#0088c2] text-white font-bold px-7 py-3 rounded-lg shadow transition whitespace-nowrap">
                        {{ __('site.hero_cta_secondary') }}
                    </a>
                </div>
            </div>
        </div>
    </section>

@endsection
