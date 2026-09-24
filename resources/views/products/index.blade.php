@extends('layouts.app')

@section('title', __('site.nav_products'))

@section('content')

    {{-- Products Header Banner --}}
    <section class="bg-gray-50 dark:bg-[#0e141b] border-b border-[#E2E8F0] dark:border-[#26313C] py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <div class="max-w-3xl space-y-4">
                <div class="inline-flex items-center gap-2 text-[#009EE0] font-bold text-xs uppercase tracking-wider">
                    <span class="w-6 h-0.5 bg-[#009EE0]"></span>
                    <span>{{ __('site.products_badge') }}</span>
                </div>
                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-[#111111] dark:text-[#F8FAFC]">
                    {{ __('site.products_title') }}
                </h1>
                <p class="text-lg text-[#475569] dark:text-[#94A3B8] leading-relaxed">
                    {{ __('site.products_subtitle') }}
                </p>
            </div>
        </div>
    </section>

    {{-- Products Content --}}
    <section class="py-20 bg-white dark:bg-[#0B0F14]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            @if ($products->isNotEmpty())
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach ($products as $product)
                        <div class="group rounded-2xl overflow-hidden border border-[#E2E8F0] dark:border-[#26313C] bg-white dark:bg-[#111820] hover:shadow-xl hover:border-[#009EE0] dark:hover:border-[#009EE0] transition-all duration-300 flex flex-col">
                            {{-- Product Image Container with Safe Fallback --}}
                            <div class="relative h-64 sm:h-72 p-6 bg-gradient-to-b from-sky-50/50 via-white to-gray-50/30 dark:from-[#0B0F14] dark:to-[#111820] border-b border-[#E2E8F0] dark:border-[#26313C] flex items-center justify-center overflow-hidden">
                                <div class="absolute top-4 start-4 flex items-center gap-2 z-10">
                                    <span class="font-extrabold text-sm text-[#009EE0] tracking-wider">SOFTEL</span>
                                </div>
                                <div class="absolute top-4 end-4 z-10">
                                    <span class="text-xs font-semibold px-2.5 py-1 rounded bg-sky-100 dark:bg-[#009EE0]/20 text-[#009EE0]">{{ __('site.hero_mockup_status') }}</span>
                                </div>
                                @if ($product->image)
                                    <img
                                        src="{{ asset($product->image) }}"
                                        alt="{{ $product->getLocalized('title') }}"
                                        class="max-h-full max-w-full w-auto h-auto object-contain transition-transform duration-300 group-hover:scale-105"
                                        loading="lazy"
                                    >
                                @else
                                    <div class="flex items-center justify-center w-full h-full p-8 opacity-60">
                                        <img src="{{ asset('images/softel_logo_black.svg') }}"
                                             data-light="{{ asset('images/softel_logo_black.svg') }}"
                                             data-dark="{{ asset('images/softel_logo_white.svg') }}"
                                             data-logo-light="{{ asset('images/softel_logo_black.svg') }}"
                                             data-logo-dark="{{ asset('images/softel_logo_white.svg') }}"
                                             alt="SOFTEL"
                                             class="h-10 w-auto object-contain">
                                    </div>
                                @endif
                            </div>

                            <div class="p-6 flex-grow flex flex-col justify-between space-y-4">
                                <div>
                                    <h2 class="font-bold text-xl text-[#111111] dark:text-[#F8FAFC] mb-2 leading-snug">{{ $product->getLocalized('title') }}</h2>
                                    <p class="text-sm text-[#334155] dark:text-[#94A3B8] leading-relaxed mb-4">{{ $product->getLocalized('summary') }}</p>

                                    @php $techStack = $product->getLocalizedTechStack(); @endphp
                                    @if (! empty($techStack))
                                        <div class="flex flex-wrap gap-1.5 pt-2">
                                            @foreach ($techStack as $tech)
                                                <span class="text-[11px] font-semibold px-2.5 py-1 rounded-md bg-gray-100 dark:bg-[#0B0F14] text-[#475569] dark:text-[#CBD5E1] border border-[#E2E8F0] dark:border-[#26313C]">
                                                    {{ $tech }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                                <div class="pt-4 border-t border-[#E2E8F0] dark:border-[#26313C]">
                                    <a href="{{ route('products.show', [$currentLocale, $product->slug]) }}"
                                       class="text-sm font-bold text-[#009EE0] hover:text-[#0088c2] flex items-center gap-1.5">
                                        <span>{{ __('site.products_view_details') }}</span>
                                        <svg class="w-4 h-4 flip-rtl" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                {{-- Corporate Empty State --}}
                <div class="max-w-4xl mx-auto rounded-2xl border border-[#E2E8F0] dark:border-[#26313C] bg-white dark:bg-[#111820] p-8 sm:p-12 shadow-sm space-y-8">
                    <div class="text-center space-y-3">
                        <div class="w-16 h-16 rounded-2xl bg-sky-50 dark:bg-[#009EE0]/10 border border-[#009EE0]/20 flex items-center justify-center mx-auto text-[#009EE0]">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2" />
                            </svg>
                        </div>
                        <h2 class="text-2xl sm:text-3xl font-bold text-[#111111] dark:text-[#F8FAFC]">
                            {{ __('site.products_empty_title') }}
                        </h2>
                        <p class="text-[#334155] dark:text-[#CBD5E1] text-base max-w-2xl mx-auto leading-relaxed">
                            {{ __('site.products_empty_desc') }}
                        </p>
                    </div>

                    <div class="text-center pt-4">
                        <a href="{{ route('contact.create', $currentLocale) }}"
                           class="inline-block bg-[#009EE0] hover:bg-[#0088c2] text-white font-bold px-8 py-3.5 rounded-lg shadow transition">
                            {{ __('site.products_empty_cta') }}
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </section>

@endsection
