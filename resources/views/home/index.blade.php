@extends('layouts.app')

@section('title', __('site.hero_title'))

@section('content')

    {{-- Hero Section --}}
    <section class="relative bg-white dark:bg-[#0B0F14] py-16 lg:py-24 border-b border-[#E2E8F0] dark:border-[#26313C] overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 relative">
            <div class="grid lg:grid-cols-12 gap-12 lg:gap-8 items-center">
                {{-- Left Content --}}
                <div class="lg:col-span-7 space-y-6">
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-sky-50 dark:bg-[#009EE0]/10 border border-[#009EE0]/20 text-xs font-bold text-[#009EE0]">
                        <span class="w-2 h-2 rounded-full bg-[#009EE0] animate-ping"></span>
                        <span>{{ __('site.hero_badge') }}</span>
                    </div>

                    <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-[#111111] dark:text-[#F8FAFC] tracking-tight leading-[1.15]">
                        {{ __('site.hero_title') }}
                    </h1>

                    <p class="text-lg sm:text-xl font-semibold text-[#475569] dark:text-[#CBD5E1]">
                        {{ __('site.hero_subtitle') }}
                    </p>

                    <p class="text-base sm:text-lg text-[#334155] dark:text-[#94A3B8] leading-relaxed max-w-2xl">
                        {{ __('site.hero_intro') }}
                    </p>

                    <div class="pt-2 flex flex-wrap items-center gap-4">
                        <a href="#solutions"
                           class="bg-[#009EE0] hover:bg-[#0088c2] text-white font-bold px-7 py-3.5 rounded-lg shadow-md hover:shadow-lg transition-all duration-200 flex items-center gap-2">
                            <span>{{ __('site.hero_cta') }}</span>
                            <svg class="w-5 h-5 flip-rtl" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </a>
                        <a href="{{ route('contact.create', $currentLocale) }}"
                           class="bg-white dark:bg-[#111820] border-2 border-[#009EE0] text-[#009EE0] hover:bg-sky-50 dark:hover:bg-[#009EE0]/10 font-bold px-7 py-3 rounded-lg transition-all duration-200">
                            {{ __('site.hero_cta_secondary') }}
                        </a>
                    </div>

                    {{-- Trust Indicators Bar --}}
                    <div class="pt-8 border-t border-[#E2E8F0] dark:border-[#26313C] flex flex-wrap items-center gap-6 text-xs text-[#64748B] dark:text-[#94A3B8] font-semibold">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-[#009EE0] flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            <span>{{ __('site.hero_stat_experience') }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-[#009EE0] flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            <span>{{ __('site.hero_stat_software') }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-[#009EE0] flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            <span>{{ __('site.hero_stat_education') }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-[#009EE0] flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            <span>{{ __('site.hero_stat_engineering') }}</span>
                        </div>
                    </div>
                </div>

                {{-- Right Interactive Dashboard Graphic --}}
                <div class="lg:col-span-5 relative">
                    <div class="rounded-2xl border border-[#E2E8F0] dark:border-[#26313C] bg-white dark:bg-[#111820] p-5 shadow-xl relative z-10">
                        {{-- Window Top Bar --}}
                        <div class="flex items-center justify-between pb-4 border-b border-[#E2E8F0] dark:border-[#26313C]">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-red-400 inline-block"></span>
                                <span class="w-3 h-3 rounded-full bg-yellow-400 inline-block"></span>
                                <span class="w-3 h-3 rounded-full bg-green-400 inline-block"></span>
                            </div>
                            <span class="text-xs font-semibold text-gray-700 dark:text-gray-300">{{ __('site.hero_mockup_title') }}</span>
                            <span class="text-xs font-semibold px-2 py-0.5 rounded bg-sky-50 dark:bg-[#009EE0]/10 text-[#009EE0]">{{ __('site.hero_mockup_status') }}</span>
                        </div>

                        {{-- Mock Metrics Grid --}}
                        <div class="grid grid-cols-2 gap-3 my-4">
                            <div class="p-3.5 rounded-xl bg-gray-50 dark:bg-[#0B0F14] border border-[#E2E8F0] dark:border-[#26313C]">
                                <span class="text-xs text-gray-600 dark:text-[#94A3B8] block">{{ __('site.hero_mockup_metric_stocks') }}</span>
                                <span class="text-lg font-bold text-[#111111] dark:text-[#F8FAFC]">100%</span>
                                <span class="text-[11px] text-emerald-600 dark:text-emerald-400 font-medium">{{ __('site.hero_mockup_metric_sync') }}</span>
                            </div>
                            <div class="p-3.5 rounded-xl bg-sky-50 dark:bg-[#009EE0]/10 border border-[#009EE0]/20">
                                <span class="text-xs text-[#009EE0] block">{{ __('site.hero_mockup_metric_flow') }}</span>
                                <span class="text-lg font-bold text-[#111111] dark:text-[#F8FAFC]">{{ __('site.hero_mockup_status') }}</span>
                                <span class="text-[11px] text-[#009EE0] font-medium">{{ __('site.hero_mockup_metric_grade') }}</span>
                            </div>
                        </div>

                        {{-- Flow Chart Diagram Mockup --}}
                        <div class="space-y-2.5 pt-2">
                            <div class="flex items-center justify-between text-xs font-medium text-gray-800 dark:text-gray-200 bg-gray-50 dark:bg-[#0B0F14] p-2.5 rounded-lg border border-[#E2E8F0] dark:border-[#26313C]">
                                <span class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-[#009EE0]"></span>
                                    {{ __('site.hero_mockup_module_commercial') }}
                                </span>
                                <span class="text-emerald-600 dark:text-emerald-400 font-semibold">{{ __('site.hero_mockup_module_ok') }}</span>
                            </div>
                            <div class="flex items-center justify-between text-xs font-medium text-gray-800 dark:text-gray-200 bg-gray-50 dark:bg-[#0B0F14] p-2.5 rounded-lg border border-[#E2E8F0] dark:border-[#26313C]">
                                <span class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-[#010101] dark:bg-gray-300"></span>
                                    {{ __('site.hero_mockup_module_warehouse') }}
                                </span>
                                <span class="text-emerald-600 dark:text-emerald-400 font-semibold">{{ __('site.hero_mockup_module_ok') }}</span>
                            </div>
                            <div class="flex items-center justify-between text-xs font-medium text-gray-800 dark:text-gray-200 bg-gray-50 dark:bg-[#0B0F14] p-2.5 rounded-lg border border-[#E2E8F0] dark:border-[#26313C]">
                                <span class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-[#009EE0]"></span>
                                    {{ __('site.hero_mockup_module_production') }}
                                </span>
                                <span class="text-emerald-600 dark:text-emerald-400 font-semibold">{{ __('site.hero_mockup_module_ok') }}</span>
                            </div>
                            <div class="flex items-center justify-between text-xs font-medium text-gray-800 dark:text-gray-200 bg-gray-50 dark:bg-[#0B0F14] p-2.5 rounded-lg border border-[#E2E8F0] dark:border-[#26313C]">
                                <span class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                    {{ __('site.hero_mockup_module_education') }}
                                </span>
                                <span class="text-emerald-600 dark:text-emerald-400 font-semibold">{{ __('site.hero_mockup_module_ok') }}</span>
                            </div>
                        </div>

                        {{-- Bottom Brand Strip --}}
                        <div class="mt-4 pt-3 border-t border-[#E2E8F0] dark:border-[#26313C] flex items-center justify-between text-[11px] text-gray-600 dark:text-gray-400">
                            <span>{{ __('site.hero_mockup_tagline') }}</span>
                            <span class="text-[#009EE0] font-bold">SOFTEL</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- About / Company Introduction Section --}}
    <section class="py-20 bg-white dark:bg-[#0B0F14]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <div class="grid lg:grid-cols-12 gap-12 items-center">
                {{-- Left Text & Highlights --}}
                <div class="lg:col-span-7 space-y-6">
                    <div class="inline-flex items-center gap-2 text-[#009EE0] font-bold text-xs uppercase tracking-wider">
                        <span class="w-8 h-0.5 bg-[#009EE0]"></span>
                        <span>{{ __('site.about_title') }}</span>
                    </div>

                    <h2 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-[#111111] dark:text-[#F8FAFC]">
                        {{ __('site.about_subtitle') }}
                    </h2>

                    <div class="space-y-4 text-[#334155] dark:text-[#94A3B8] text-base sm:text-lg leading-relaxed">
                        <p class="font-semibold text-[#111111] dark:text-[#F8FAFC] border-s-4 border-[#009EE0] ps-4 py-1">
                            {{ __('site.about_paragraph_1') }}
                        </p>
                        <p>
                            {{ __('site.about_paragraph_2') }}
                        </p>
                        <p>
                            {{ __('site.about_paragraph_3') }}
                        </p>
                    </div>

                    {{-- Sectors Pill Badges --}}
                    <div class="pt-4">
                        <h4 class="text-xs font-bold text-[#64748B] dark:text-gray-400 uppercase tracking-wider mb-3">{{ __('site.sectors_title') }}</h4>
                        <div class="flex flex-wrap gap-2.5">
                            <span class="inline-flex items-center px-3.5 py-1.5 rounded-full text-xs font-semibold bg-gray-100 dark:bg-[#111820] text-[#111111] dark:text-[#F8FAFC] border border-[#E2E8F0] dark:border-[#26313C]">
                                {{ __('site.sector_commercial') }}
                            </span>
                            <span class="inline-flex items-center px-3.5 py-1.5 rounded-full text-xs font-semibold bg-gray-100 dark:bg-[#111820] text-[#111111] dark:text-[#F8FAFC] border border-[#E2E8F0] dark:border-[#26313C]">
                                {{ __('site.sector_industrial') }}
                            </span>
                            <span class="inline-flex items-center px-3.5 py-1.5 rounded-full text-xs font-semibold bg-gray-100 dark:bg-[#111820] text-[#111111] dark:text-[#F8FAFC] border border-[#E2E8F0] dark:border-[#26313C]">
                                {{ __('site.sector_distribution') }}
                            </span>
                            <span class="inline-flex items-center px-3.5 py-1.5 rounded-full text-xs font-semibold bg-gray-100 dark:bg-[#111820] text-[#111111] dark:text-[#F8FAFC] border border-[#E2E8F0] dark:border-[#26313C]">
                                {{ __('site.sector_education') }}
                            </span>
                            <span class="inline-flex items-center px-3.5 py-1.5 rounded-full text-xs font-semibold bg-gray-100 dark:bg-[#111820] text-[#111111] dark:text-[#F8FAFC] border border-[#E2E8F0] dark:border-[#26313C]">
                                {{ __('site.sector_services') }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Right Statistics Grid --}}
                <div class="lg:col-span-5 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="p-6 rounded-2xl bg-gray-50 dark:bg-[#111820] border border-[#E2E8F0] dark:border-[#26313C] space-y-2">
                        <span class="text-3xl font-extrabold text-[#009EE0]">2010</span>
                        <h4 class="font-bold text-[#111111] dark:text-[#F8FAFC] text-sm">{{ __('site.hero_stat_experience') }}</h4>
                        <p class="text-xs text-[#64748B] dark:text-[#94A3B8] leading-relaxed">{{ __('site.about_stat_experience_desc') }}</p>
                    </div>

                    <div class="p-6 rounded-2xl bg-gray-50 dark:bg-[#111820] border border-[#E2E8F0] dark:border-[#26313C] space-y-2">
                        <span class="text-3xl font-extrabold text-[#111111] dark:text-[#F8FAFC]">4</span>
                        <h4 class="font-bold text-[#111111] dark:text-[#F8FAFC] text-sm">{{ __('site.about_stat_industries') }}</h4>
                        <p class="text-xs text-[#64748B] dark:text-[#94A3B8] leading-relaxed">{{ __('site.about_stat_industries_desc') }}</p>
                    </div>

                    <div class="p-6 rounded-2xl bg-gray-50 dark:bg-[#111820] border border-[#E2E8F0] dark:border-[#26313C] space-y-2">
                        <span class="text-3xl font-extrabold text-[#009EE0]">4</span>
                        <h4 class="font-bold text-[#111111] dark:text-[#F8FAFC] text-sm">{{ __('site.about_stat_services') }}</h4>
                        <p class="text-xs text-[#64748B] dark:text-[#94A3B8] leading-relaxed">{{ __('site.about_stat_services_desc') }}</p>
                    </div>

                    <div class="p-6 rounded-2xl bg-[#010101] text-white space-y-2 border border-gray-800">
                        <span class="text-3xl font-extrabold text-[#009EE0]">100%</span>
                        <h4 class="font-bold text-white text-sm">{{ __('site.about_stat_proximity') }}</h4>
                        <p class="text-xs text-gray-400 leading-relaxed">{{ __('site.about_stat_proximity_desc') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Services Section (4 Core Pillars in 2x2 Desktop Grid) --}}
    <section id="services" class="py-24 bg-gray-50 dark:bg-[#0e141b] border-y border-[#E2E8F0] dark:border-[#26313C]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            {{-- Section Heading --}}
            <div class="text-center max-w-3xl mx-auto mb-16 space-y-3">
                <div class="inline-flex items-center gap-2 text-[#009EE0] font-bold text-xs uppercase tracking-wider">
                    <span class="w-6 h-0.5 bg-[#009EE0]"></span>
                    <span>{{ __('site.services_badge') }}</span>
                    <span class="w-6 h-0.5 bg-[#009EE0]"></span>
                </div>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-[#111111] dark:text-[#F8FAFC]">
                    {{ __('site.services_title') }}
                </h2>
                <p class="text-base sm:text-lg text-[#475569] dark:text-[#94A3B8]">
                    {{ __('site.services_subtitle') }}
                </p>
            </div>

            {{-- 4 Services in 2x2 Desktop Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                @foreach ($services as $service)
                    @php
                        $iconSvg = match($service->slug) {
                            'development' => '
                                <svg class="w-7 h-7 text-[#009EE0]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                                </svg>',
                            'consulting-support' => '
                                <svg class="w-7 h-7 text-[#009EE0]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>',
                            'training' => '
                                <svg class="w-7 h-7 text-[#009EE0]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5" />
                                </svg>',
                            'after-sales-support' => '
                                <svg class="w-7 h-7 text-[#009EE0]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>',
                            default => '
                                <svg class="w-7 h-7 text-[#009EE0]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>'
                        };
                    @endphp

                    <div class="bg-white dark:bg-[#111820] rounded-2xl p-8 border border-[#E2E8F0] dark:border-[#26313C] shadow-sm hover:shadow-xl hover:border-[#009EE0] dark:hover:border-[#009EE0] hover:-translate-y-1.5 transition-all duration-300 flex flex-col justify-between group">
                        <div>
                            {{-- Icon & Number --}}
                            <div class="flex items-center justify-between mb-6">
                                <div class="w-14 h-14 rounded-xl bg-sky-50 dark:bg-[#009EE0]/10 border border-[#009EE0]/20 flex items-center justify-center group-hover:bg-[#009EE0] group-hover:text-white transition-colors duration-300">
                                    {!! $iconSvg !!}
                                </div>
                                <span class="text-xs font-bold text-gray-500 dark:text-gray-400 tracking-wider">0{{ $service->sort_order }}</span>
                            </div>

                            {{-- Title --}}
                            <h3 class="text-xl font-bold text-[#111111] dark:text-[#F8FAFC] group-hover:text-[#009EE0] dark:group-hover:text-[#009EE0] transition-colors mb-2">
                                {{ $service->getLocalized('title') }}
                            </h3>

                            {{-- Subtitle / Summary --}}
                            <p class="text-sm font-semibold text-[#475569] dark:text-[#CBD5E1] mb-4 leading-relaxed">
                                {{ $service->getLocalized('summary') }}
                            </p>

                            {{-- Description --}}
                            <p class="text-sm text-[#334155] dark:text-[#94A3B8] leading-relaxed mb-6">
                                {{ $service->getLocalized('description') }}
                            </p>
                        </div>

                        {{-- Card Bottom Link --}}
                        <div class="pt-4 border-t border-[#E2E8F0] dark:border-[#26313C] flex items-center justify-between">
                            <a href="{{ route('services.show', [$currentLocale, $service->slug]) }}"
                               class="text-sm font-bold text-[#111111] dark:text-[#CBD5E1] group-hover:text-[#009EE0] dark:group-hover:text-[#009EE0] transition-colors flex items-center gap-1.5">
                                <span>{{ __('site.service_learn_more') }}</span>
                                <svg class="w-4 h-4 flip-rtl group-hover:translate-x-1 rtl:group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                            <span class="w-2 h-2 rounded-full bg-transparent group-hover:bg-[#009EE0] transition-colors"></span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Solutions Section (Major Solution Areas) --}}
    <section id="solutions" class="py-24 bg-white dark:bg-[#0B0F14]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            {{-- Section Heading --}}
            <div class="text-center max-w-3xl mx-auto mb-16 space-y-3">
                <div class="inline-flex items-center gap-2 text-[#009EE0] font-bold text-xs uppercase tracking-wider">
                    <span class="w-6 h-0.5 bg-[#009EE0]"></span>
                    <span>{{ __('site.solutions_badge') }}</span>
                    <span class="w-6 h-0.5 bg-[#009EE0]"></span>
                </div>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-[#111111] dark:text-[#F8FAFC]">
                    {{ __('site.solutions_title') }}
                </h2>
                <p class="text-base sm:text-lg text-[#475569] dark:text-[#94A3B8]">
                    {{ __('site.solutions_subtitle') }}
                </p>
            </div>

            {{-- 3 Major Solution Pillars --}}
            <div class="space-y-12">
                {{-- Solution 1: Business & Industrial Management --}}
                <div class="rounded-3xl border border-[#E2E8F0] dark:border-[#26313C] bg-gray-50 dark:bg-[#111820] p-8 sm:p-12 transition-all">
                    <div class="grid lg:grid-cols-12 gap-8 items-start">
                        <div class="lg:col-span-7 space-y-4">
                            <div class="inline-flex items-center gap-2 px-3 py-1 rounded bg-sky-50 dark:bg-[#009EE0]/10 text-[#009EE0] text-xs font-bold border border-[#009EE0]/20">
                                <span>{{ __('site.solution_business_tag') }}</span>
                            </div>
                            <h3 class="text-2xl sm:text-3xl font-extrabold text-[#111111] dark:text-[#F8FAFC]">
                                {{ __('site.solution_business_title') }}
                            </h3>
                            <p class="text-[#334155] dark:text-[#94A3B8] text-base sm:text-lg leading-relaxed">
                                {{ __('site.solution_business_desc') }}
                            </p>
                            <div class="pt-2">
                                <a href="{{ route('products.index', $currentLocale) }}"
                                   class="inline-flex items-center gap-2 text-sm font-bold text-[#009EE0] hover:text-[#0088c2]">
                                    <span>{{ __('site.service_learn_more') }}</span>
                                    <svg class="w-4 h-4 flip-rtl" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                    </svg>
                                </a>
                            </div>
                        </div>

                        {{-- Modules Grid --}}
                        <div class="lg:col-span-5 grid sm:grid-cols-2 gap-3">
                            <div class="p-3.5 rounded-xl bg-white dark:bg-[#0B0F14] border border-[#E2E8F0] dark:border-[#26313C] flex items-center gap-2.5">
                                <span class="w-2 h-2 rounded-full bg-[#009EE0] flex-shrink-0"></span>
                                <span class="text-xs font-bold text-[#111111] dark:text-[#F8FAFC]">{{ __('site.module_commercial') }}</span>
                            </div>
                            <div class="p-3.5 rounded-xl bg-white dark:bg-[#0B0F14] border border-[#E2E8F0] dark:border-[#26313C] flex items-center gap-2.5">
                                <span class="w-2 h-2 rounded-full bg-[#009EE0] flex-shrink-0"></span>
                                <span class="text-xs font-bold text-[#111111] dark:text-[#F8FAFC]">{{ __('site.module_purchasing') }}</span>
                            </div>
                            <div class="p-3.5 rounded-xl bg-white dark:bg-[#0B0F14] border border-[#E2E8F0] dark:border-[#26313C] flex items-center gap-2.5">
                                <span class="w-2 h-2 rounded-full bg-[#009EE0] flex-shrink-0"></span>
                                <span class="text-xs font-bold text-[#111111] dark:text-[#F8FAFC]">{{ __('site.module_sales') }}</span>
                            </div>
                            <div class="p-3.5 rounded-xl bg-white dark:bg-[#0B0F14] border border-[#E2E8F0] dark:border-[#26313C] flex items-center gap-2.5">
                                <span class="w-2 h-2 rounded-full bg-[#009EE0] flex-shrink-0"></span>
                                <span class="text-xs font-bold text-[#111111] dark:text-[#F8FAFC]">{{ __('site.module_inventory') }}</span>
                            </div>
                            <div class="p-3.5 rounded-xl bg-white dark:bg-[#0B0F14] border border-[#E2E8F0] dark:border-[#26313C] flex items-center gap-2.5">
                                <span class="w-2 h-2 rounded-full bg-[#009EE0] flex-shrink-0"></span>
                                <span class="text-xs font-bold text-[#111111] dark:text-[#F8FAFC]">{{ __('site.module_treasury') }}</span>
                            </div>
                            <div class="p-3.5 rounded-xl bg-white dark:bg-[#0B0F14] border border-[#E2E8F0] dark:border-[#26313C] flex items-center gap-2.5">
                                <span class="w-2 h-2 rounded-full bg-[#009EE0] flex-shrink-0"></span>
                                <span class="text-xs font-bold text-[#111111] dark:text-[#F8FAFC]">{{ __('site.module_import') }}</span>
                            </div>
                            <div class="p-3.5 rounded-xl bg-white dark:bg-[#0B0F14] border border-[#E2E8F0] dark:border-[#26313C] flex items-center gap-2.5">
                                <span class="w-2 h-2 rounded-full bg-[#009EE0] flex-shrink-0"></span>
                                <span class="text-xs font-bold text-[#111111] dark:text-[#F8FAFC]">{{ __('site.module_production') }}</span>
                            </div>
                            <div class="p-3.5 rounded-xl bg-white dark:bg-[#0B0F14] border border-[#E2E8F0] dark:border-[#26313C] flex items-center gap-2.5">
                                <span class="w-2 h-2 rounded-full bg-[#009EE0] flex-shrink-0"></span>
                                <span class="text-xs font-bold text-[#111111] dark:text-[#F8FAFC]">{{ __('site.module_payroll') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Solution 2: Education & School Management --}}
                <div class="rounded-3xl border border-[#E2E8F0] dark:border-[#26313C] bg-gray-50 dark:bg-[#111820] p-8 sm:p-12 transition-all">
                    <div class="grid lg:grid-cols-12 gap-8 items-start">
                        <div class="lg:col-span-7 space-y-4">
                            <div class="inline-flex items-center gap-2 px-3 py-1 rounded bg-sky-50 dark:bg-[#009EE0]/10 text-[#009EE0] text-xs font-bold border border-[#009EE0]/20">
                                <span>{{ __('site.solution_education_tag') }}</span>
                            </div>
                            <h3 class="text-2xl sm:text-3xl font-extrabold text-[#111111] dark:text-[#F8FAFC]">
                                {{ __('site.solution_education_title') }}
                            </h3>
                            <p class="text-[#334155] dark:text-[#94A3B8] text-base sm:text-lg leading-relaxed">
                                {{ __('site.solution_education_desc') }}
                            </p>
                            <div class="pt-2">
                                <a href="{{ route('contact.create', $currentLocale) }}"
                                   class="inline-flex items-center gap-2 text-sm font-bold text-[#009EE0] hover:text-[#0088c2]">
                                    <span>{{ __('site.hero_cta_secondary') }}</span>
                                    <svg class="w-4 h-4 flip-rtl" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                    </svg>
                                </a>
                            </div>
                        </div>

                        {{-- Capabilities Grid --}}
                        <div class="lg:col-span-5 grid sm:grid-cols-2 gap-3">
                            <div class="p-3.5 rounded-xl bg-white dark:bg-[#0B0F14] border border-[#E2E8F0] dark:border-[#26313C] flex items-center gap-2.5">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 flex-shrink-0"></span>
                                <span class="text-xs font-bold text-[#111111] dark:text-[#F8FAFC]">{{ __('site.edu_school_mgmt') }}</span>
                            </div>
                            <div class="p-3.5 rounded-xl bg-white dark:bg-[#0B0F14] border border-[#E2E8F0] dark:border-[#26313C] flex items-center gap-2.5">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 flex-shrink-0"></span>
                                <span class="text-xs font-bold text-[#111111] dark:text-[#F8FAFC]">{{ __('site.edu_student_records') }}</span>
                            </div>
                            <div class="p-3.5 rounded-xl bg-white dark:bg-[#0B0F14] border border-[#E2E8F0] dark:border-[#26313C] flex items-center gap-2.5">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 flex-shrink-0"></span>
                                <span class="text-xs font-bold text-[#111111] dark:text-[#F8FAFC]">{{ __('site.edu_academic_followup') }}</span>
                            </div>
                            <div class="p-3.5 rounded-xl bg-white dark:bg-[#0B0F14] border border-[#E2E8F0] dark:border-[#26313C] flex items-center gap-2.5">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 flex-shrink-0"></span>
                                <span class="text-xs font-bold text-[#111111] dark:text-[#F8FAFC]">{{ __('site.edu_attendance') }}</span>
                            </div>
                            <div class="p-3.5 rounded-xl bg-white dark:bg-[#0B0F14] border border-[#E2E8F0] dark:border-[#26313C] flex items-center gap-2.5">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 flex-shrink-0"></span>
                                <span class="text-xs font-bold text-[#111111] dark:text-[#F8FAFC]">{{ __('site.edu_grades') }}</span>
                            </div>
                            <div class="p-3.5 rounded-xl bg-white dark:bg-[#0B0F14] border border-[#E2E8F0] dark:border-[#26313C] flex items-center gap-2.5">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 flex-shrink-0"></span>
                                <span class="text-xs font-bold text-[#111111] dark:text-[#F8FAFC]">{{ __('site.edu_teachers') }}</span>
                            </div>
                            <div class="p-3.5 rounded-xl bg-white dark:bg-[#0B0F14] border border-[#E2E8F0] dark:border-[#26313C] flex items-center gap-2.5">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 flex-shrink-0"></span>
                                <span class="text-xs font-bold text-[#111111] dark:text-[#F8FAFC]">{{ __('site.edu_parent_comm') }}</span>
                            </div>
                            <div class="p-3.5 rounded-xl bg-white dark:bg-[#0B0F14] border border-[#E2E8F0] dark:border-[#26313C] flex items-center gap-2.5">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 flex-shrink-0"></span>
                                <span class="text-xs font-bold text-[#111111] dark:text-[#F8FAFC]">{{ __('site.edu_parent_apps') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Solution 3: Mobile Applications --}}
                <div class="rounded-3xl border border-[#E2E8F0] dark:border-[#26313C] bg-gray-50 dark:bg-[#111820] p-8 sm:p-12 transition-all">
                    <div class="grid lg:grid-cols-12 gap-8 items-start">
                        <div class="lg:col-span-7 space-y-4">
                            <div class="inline-flex items-center gap-2 px-3 py-1 rounded bg-sky-50 dark:bg-[#009EE0]/10 text-[#009EE0] text-xs font-bold border border-[#009EE0]/20">
                                <span>{{ __('site.solution_mobile_tag') }}</span>
                            </div>
                            <h3 class="text-2xl sm:text-3xl font-extrabold text-[#111111] dark:text-[#F8FAFC]">
                                {{ __('site.solution_mobile_title') }}
                            </h3>
                            <p class="text-[#334155] dark:text-[#94A3B8] text-base sm:text-lg leading-relaxed">
                                {{ __('site.solution_mobile_desc') }}
                            </p>
                            <div class="pt-2">
                                <a href="{{ route('contact.create', $currentLocale) }}"
                                   class="inline-flex items-center gap-2 text-sm font-bold text-[#009EE0] hover:text-[#0088c2]">
                                    <span>{{ __('site.hero_cta_secondary') }}</span>
                                    <svg class="w-4 h-4 flip-rtl" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                    </svg>
                                </a>
                            </div>
                        </div>

                        {{-- Mobile Categories Grid --}}
                        <div class="lg:col-span-5 grid sm:grid-cols-2 gap-3">
                            <div class="p-3.5 rounded-xl bg-white dark:bg-[#0B0F14] border border-[#E2E8F0] dark:border-[#26313C] flex items-center gap-2.5">
                                <span class="w-2 h-2 rounded-full bg-indigo-500 flex-shrink-0"></span>
                                <span class="text-xs font-bold text-[#111111] dark:text-[#F8FAFC]">{{ __('site.mobile_cat_parent') }}</span>
                            </div>
                            <div class="p-3.5 rounded-xl bg-white dark:bg-[#0B0F14] border border-[#E2E8F0] dark:border-[#26313C] flex items-center gap-2.5">
                                <span class="w-2 h-2 rounded-full bg-indigo-500 flex-shrink-0"></span>
                                <span class="text-xs font-bold text-[#111111] dark:text-[#F8FAFC]">{{ __('site.mobile_cat_business') }}</span>
                            </div>
                            <div class="p-3.5 rounded-xl bg-white dark:bg-[#0B0F14] border border-[#E2E8F0] dark:border-[#26313C] flex items-center gap-2.5">
                                <span class="w-2 h-2 rounded-full bg-indigo-500 flex-shrink-0"></span>
                                <span class="text-xs font-bold text-[#111111] dark:text-[#F8FAFC]">{{ __('site.mobile_cat_management') }}</span>
                            </div>
                            <div class="p-3.5 rounded-xl bg-white dark:bg-[#0B0F14] border border-[#E2E8F0] dark:border-[#26313C] flex items-center gap-2.5">
                                <span class="w-2 h-2 rounded-full bg-indigo-500 flex-shrink-0"></span>
                                <span class="text-xs font-bold text-[#111111] dark:text-[#F8FAFC]">{{ __('site.mobile_cat_customer') }}</span>
                            </div>
                            <div class="p-3.5 rounded-xl bg-white dark:bg-[#0B0F14] border border-[#E2E8F0] dark:border-[#26313C] flex items-center gap-2.5 sm:col-span-2">
                                <span class="w-2 h-2 rounded-full bg-indigo-500 flex-shrink-0"></span>
                                <span class="text-xs font-bold text-[#111111] dark:text-[#F8FAFC]">{{ __('site.mobile_cat_internal') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Products Section (Database-Driven) --}}
    <section id="products" class="py-20 bg-gray-50 dark:bg-[#0e141b] border-t border-[#E2E8F0] dark:border-[#26313C]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <div class="text-center max-w-3xl mx-auto mb-14 space-y-3">
                <div class="inline-flex items-center gap-2 text-[#009EE0] font-bold text-xs uppercase tracking-wider">
                    <span class="w-6 h-0.5 bg-[#009EE0]"></span>
                    <span>{{ __('site.products_badge') }}</span>
                    <span class="w-6 h-0.5 bg-[#009EE0]"></span>
                </div>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-[#111111] dark:text-[#F8FAFC]">
                    {{ __('site.products_title') }}
                </h2>
                <p class="text-base text-[#475569] dark:text-[#94A3B8]">
                    {{ __('site.products_subtitle') }}
                </p>
            </div>

            @if ($products->isNotEmpty())
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach ($products as $product)
                        <div class="group rounded-2xl overflow-hidden border border-[#E2E8F0] dark:border-[#26313C] bg-white dark:bg-[#111820] hover:shadow-xl hover:border-[#009EE0] dark:hover:border-[#009EE0] transition-all duration-300 flex flex-col">
                            {{-- Product Image Container with Safe Fallback --}}
                            <div class="relative h-64 p-6 bg-gradient-to-b from-sky-50/50 via-white to-gray-50/30 dark:from-[#0B0F14] dark:to-[#111820] border-b border-[#E2E8F0] dark:border-[#26313C] flex items-center justify-center overflow-hidden">
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
                                    <h3 class="font-bold text-lg text-[#111111] dark:text-[#F8FAFC] mb-2 leading-snug">{{ $product->getLocalized('title') }}</h3>
                                    <p class="text-sm text-[#334155] dark:text-[#94A3B8] leading-relaxed mb-4">{{ $product->getLocalized('summary') }}</p>

                                    @php $techStack = $product->getLocalizedTechStack(); @endphp
                                    @if (! empty($techStack))
                                        <div class="flex flex-wrap gap-1.5 pt-2">
                                            @foreach (array_slice($techStack, 0, 4) as $tag)
                                                <span class="text-[11px] font-semibold px-2 py-0.5 rounded bg-gray-100 dark:bg-[#0B0F14] text-[#475569] dark:text-[#CBD5E1] border border-[#E2E8F0] dark:border-[#26313C]">
                                                    {{ $tag }}
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
                {{-- Corporate Custom Software Introductory Showcase --}}
                <div class="rounded-2xl border border-[#E2E8F0] dark:border-[#26313C] bg-white dark:bg-[#111820] p-8 sm:p-12 shadow-sm">
                    <div class="grid lg:grid-cols-12 gap-8 items-center">
                        <div class="lg:col-span-8 space-y-4">
                            <h3 class="text-2xl sm:text-3xl font-bold text-[#111111] dark:text-[#F8FAFC]">
                                {{ __('site.products_empty_title') }}
                            </h3>
                            <p class="text-[#334155] dark:text-[#94A3B8] text-base leading-relaxed max-w-2xl">
                                {{ __('site.products_empty_desc') }}
                            </p>
                        </div>
                        <div class="lg:col-span-4 flex justify-start lg:justify-end">
                            <a href="{{ route('contact.create', $currentLocale) }}"
                               class="bg-[#009EE0] hover:bg-[#0088c2] text-white font-bold px-6 py-3.5 rounded-lg shadow transition-all duration-200 text-center">
                                {{ __('site.products_empty_cta') }}
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </section>

    {{-- Testimonials Section (Dynamically shown only when records exist) --}}
    @if ($testimonials->isNotEmpty())
        <section class="py-20 bg-white dark:bg-[#0B0F14] border-t border-[#E2E8F0] dark:border-[#26313C]">
            <div class="max-w-7xl mx-auto px-4 sm:px-6">
                <div class="text-center max-w-2xl mx-auto mb-12">
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-[#111111] dark:text-[#F8FAFC]">{{ __('site.testimonials_title') }}</h2>
                    <p class="text-[#475569] dark:text-[#94A3B8] text-sm mt-2">{{ __('site.testimonials_subtitle') }}</p>
                </div>
                <div class="grid md:grid-cols-2 gap-6">
                    @foreach ($testimonials as $t)
                        <blockquote class="p-6 rounded-2xl border border-[#E2E8F0] dark:border-[#26313C] bg-gray-50 dark:bg-[#111820] shadow-sm">
                            <p class="text-[#334155] dark:text-[#CBD5E1] text-sm leading-relaxed italic">&ldquo;{{ $t->getLocalized('quote') }}&rdquo;</p>
                            <footer class="mt-4 pt-4 border-t border-[#E2E8F0] dark:border-[#26313C] text-sm font-bold text-[#111111] dark:text-[#F8FAFC] flex items-center justify-between">
                                <span>{{ $t->author_name }}</span>
                                @if ($t->author_role)
                                    <span class="text-xs text-[#009EE0] font-semibold">{{ $t->author_role }}</span>
                                @endif
                            </footer>
                        </blockquote>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Contact / Final CTA Section --}}
    <section class="py-20 bg-[#010101] text-white relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 relative z-10">
            <div class="grid lg:grid-cols-12 gap-10 items-center">
                {{-- Left Text --}}
                <div class="lg:col-span-7 space-y-4">
                    <div class="inline-flex items-center gap-2 text-[#009EE0] font-bold text-xs uppercase tracking-wider">
                        <span class="w-6 h-0.5 bg-[#009EE0]"></span>
                        <span>{{ __('site.contact_badge') }}</span>
                    </div>
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight">
                        {{ __('site.contact_title') }}
                    </h2>
                    <p class="text-base sm:text-lg text-gray-300 leading-relaxed max-w-xl">
                        {{ __('site.contact_subtitle') }}
                    </p>

                    <div class="pt-4 flex flex-wrap gap-4">
                        <a href="{{ route('contact.create', $currentLocale) }}"
                           class="bg-[#009EE0] hover:bg-[#0088c2] text-white font-bold px-7 py-3 rounded-lg shadow transition duration-200">
                            {{ __('site.hero_cta_secondary') }}
                        </a>
                        <a href="mailto:info@softel.dz"
                           class="border border-gray-700 hover:border-[#009EE0] text-gray-200 hover:text-[#009EE0] font-bold px-6 py-3 rounded-lg transition duration-200" dir="ltr">
                            info@softel.dz
                        </a>
                    </div>
                </div>

                {{-- Right Direct Contact Info Cards --}}
                <div class="lg:col-span-5 grid sm:grid-cols-2 gap-4">
                    <div class="p-5 rounded-xl bg-gray-900 border border-gray-800">
                        <div class="w-9 h-9 rounded-lg bg-[#009EE0]/10 flex items-center justify-center text-[#009EE0] mb-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                        </div>
                        <h4 class="text-xs font-bold text-gray-400 uppercase mb-1">{{ __('site.contact_phone_label') }}</h4>
                        <div class="space-y-1 text-sm font-semibold text-white" dir="ltr">
                            <div><a href="tel:+213798757380" class="hover:text-[#009EE0] transition-colors">0798 75 73 80</a></div>
                            <div><a href="tel:+213558925355" class="hover:text-[#009EE0] transition-colors">0558 92 53 55</a></div>
                            <div><a href="tel:+21320242020" class="hover:text-[#009EE0] transition-colors">020 24 20 20</a></div>
                        </div>
                    </div>

                    <div class="p-5 rounded-xl bg-gray-900 border border-gray-800">
                        <div class="w-9 h-9 rounded-lg bg-[#009EE0]/10 flex items-center justify-center text-[#009EE0] mb-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <h4 class="text-xs font-bold text-gray-400 uppercase mb-1">{{ __('site.contact_address_label') }}</h4>
                        <p class="text-xs text-gray-300 leading-relaxed">
                            {{ __('site.contact_address_value') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
