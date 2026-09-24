@extends('layouts.app')

@section('title', __('site.about_title'))

@section('content')

    {{-- Header Banner --}}
    <section class="bg-gray-50 dark:bg-[#0e141b] border-b border-[#E2E8F0] dark:border-[#26313C] py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <div class="max-w-3xl space-y-4">
                <div class="inline-flex items-center gap-2 text-[#009EE0] font-bold text-xs uppercase tracking-wider">
                    <span class="w-6 h-0.5 bg-[#009EE0]"></span>
                    <span>{{ __('site.about_title') }}</span>
                </div>
                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-[#111111] dark:text-[#F8FAFC]">
                    {{ __('site.about_subtitle') }}
                </h1>
                <p class="text-lg text-[#475569] dark:text-[#94A3B8] leading-relaxed">
                    {{ __('site.about_paragraph_1') }}
                </p>
            </div>
        </div>
    </section>

    {{-- Main About Content --}}
    <section class="py-20 bg-white dark:bg-[#0B0F14]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <div class="grid lg:grid-cols-12 gap-12 items-start">
                <div class="lg:col-span-7 space-y-8 text-[#334155] dark:text-[#CBD5E1] leading-relaxed text-base sm:text-lg">
                    <div>
                        <h2 class="text-2xl font-bold text-[#111111] dark:text-[#F8FAFC] mb-4">{{ __('site.about_mission_title') }}</h2>
                        <p class="mb-4">
                            {{ __('site.about_paragraph_2') }}
                        </p>
                        <p>
                            {{ __('site.about_paragraph_3') }}
                        </p>
                    </div>

                    {{-- 4 Pillars Grid --}}
                    <div class="grid sm:grid-cols-2 gap-6 pt-4">
                        <div class="p-6 rounded-xl border border-[#E2E8F0] dark:border-[#26313C] bg-gray-50 dark:bg-[#111820] space-y-2">
                            <div class="w-10 h-10 rounded-lg bg-sky-50 dark:bg-[#009EE0]/10 flex items-center justify-center text-[#009EE0] font-bold text-base">01</div>
                            <h3 class="font-bold text-[#111111] dark:text-[#F8FAFC] text-base">{{ __('site.about_pillar1_title') }}</h3>
                            <p class="text-xs text-[#64748B] dark:text-[#94A3B8] leading-relaxed">{{ __('site.about_pillar1_desc') }}</p>
                        </div>
                        <div class="p-6 rounded-xl border border-[#E2E8F0] dark:border-[#26313C] bg-gray-50 dark:bg-[#111820] space-y-2">
                            <div class="w-10 h-10 rounded-lg bg-sky-50 dark:bg-[#009EE0]/10 flex items-center justify-center text-[#009EE0] font-bold text-base">02</div>
                            <h3 class="font-bold text-[#111111] dark:text-[#F8FAFC] text-base">{{ __('site.about_pillar2_title') }}</h3>
                            <p class="text-xs text-[#64748B] dark:text-[#94A3B8] leading-relaxed">{{ __('site.about_pillar2_desc') }}</p>
                        </div>
                        <div class="p-6 rounded-xl border border-[#E2E8F0] dark:border-[#26313C] bg-gray-50 dark:bg-[#111820] space-y-2">
                            <div class="w-10 h-10 rounded-lg bg-sky-50 dark:bg-[#009EE0]/10 flex items-center justify-center text-[#009EE0] font-bold text-base">03</div>
                            <h3 class="font-bold text-[#111111] dark:text-[#F8FAFC] text-base">{{ __('site.about_pillar3_title') }}</h3>
                            <p class="text-xs text-[#64748B] dark:text-[#94A3B8] leading-relaxed">{{ __('site.about_pillar3_desc') }}</p>
                        </div>
                        <div class="p-6 rounded-xl border border-[#E2E8F0] dark:border-[#26313C] bg-gray-50 dark:bg-[#111820] space-y-2">
                            <div class="w-10 h-10 rounded-lg bg-sky-50 dark:bg-[#009EE0]/10 flex items-center justify-center text-[#009EE0] font-bold text-base">04</div>
                            <h3 class="font-bold text-[#111111] dark:text-[#F8FAFC] text-base">{{ __('site.about_pillar4_title') }}</h3>
                            <p class="text-xs text-[#64748B] dark:text-[#94A3B8] leading-relaxed">{{ __('site.about_pillar4_desc') }}</p>
                        </div>
                    </div>

                    {{-- Industries Served Section --}}
                    <div class="pt-6 border-t border-[#E2E8F0] dark:border-[#26313C]">
                        <h3 class="text-xl font-bold text-[#111111] dark:text-[#F8FAFC] mb-4">{{ __('site.sectors_title') }}</h3>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                            <div class="p-4 rounded-lg bg-gray-50 dark:bg-[#111820] border border-[#E2E8F0] dark:border-[#26313C] text-center">
                                <span class="font-bold text-sm text-[#111111] dark:text-[#F8FAFC] block">{{ __('site.sector_commercial') }}</span>
                            </div>
                            <div class="p-4 rounded-lg bg-gray-50 dark:bg-[#111820] border border-[#E2E8F0] dark:border-[#26313C] text-center">
                                <span class="font-bold text-sm text-[#111111] dark:text-[#F8FAFC] block">{{ __('site.sector_industrial') }}</span>
                            </div>
                            <div class="p-4 rounded-lg bg-gray-50 dark:bg-[#111820] border border-[#E2E8F0] dark:border-[#26313C] text-center">
                                <span class="font-bold text-sm text-[#111111] dark:text-[#F8FAFC] block">{{ __('site.sector_distribution') }}</span>
                            </div>
                            <div class="p-4 rounded-lg bg-gray-50 dark:bg-[#111820] border border-[#E2E8F0] dark:border-[#26313C] text-center">
                                <span class="font-bold text-sm text-[#111111] dark:text-[#F8FAFC] block">{{ __('site.sector_education') }}</span>
                            </div>
                            <div class="p-4 rounded-lg bg-gray-50 dark:bg-[#111820] border border-[#E2E8F0] dark:border-[#26313C] text-center sm:col-span-2">
                                <span class="font-bold text-sm text-[#111111] dark:text-[#F8FAFC] block">{{ __('site.sector_services') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right Corporate Overview Card --}}
                <div class="lg:col-span-5 space-y-6">
                    <div class="p-8 rounded-2xl bg-[#010101] text-white space-y-6 shadow-xl border border-gray-800">
                        <div>
                            <img src="{{ asset('images/softel_logo_white.svg') }}" alt="SOFTEL" class="h-8 w-auto">
                        </div>
                        <div>
                            <span class="text-[#009EE0] font-bold text-xs uppercase tracking-wider block mb-1">{{ __('site.about_card_profile') }}</span>
                            <h3 class="text-xl font-bold text-white">{{ __('site.about_card_legal') }}</h3>
                            <p class="text-xs text-gray-400 mt-1">{{ __('site.about_card_tagline') }}</p>
                        </div>
                        <ul class="space-y-3 text-sm text-gray-300 border-t border-gray-800 pt-4">
                            <li class="flex justify-between items-center">
                                <span class="text-gray-500">{{ __('site.about_card_established') }}</span>
                                <span class="font-semibold text-white">2010</span>
                            </li>
                            <li class="flex justify-between items-center">
                                <span class="text-gray-500">{{ __('site.about_card_location') }}</span>
                                <span class="font-semibold text-white">{{ __('site.footer_location') }}</span>
                            </li>
                            <li class="flex justify-between items-center">
                                <span class="text-gray-500">{{ __('site.about_card_focus') }}</span>
                                <span class="font-semibold text-white text-end max-w-[200px] text-xs">{{ __('site.about_card_focus_val') }}</span>
                            </li>
                            <li class="flex justify-between items-center">
                                <span class="text-gray-500">{{ __('site.about_card_contact') }}</span>
                                <span class="font-semibold text-[#009EE0]" dir="ltr">info@softel.dz</span>
                            </li>
                        </ul>
                        <div class="pt-2">
                            <a href="{{ route('contact.create', $currentLocale) }}"
                               class="w-full text-center block bg-[#009EE0] hover:bg-[#0088c2] text-white font-bold py-3 rounded-lg shadow transition">
                                {{ __('site.hero_cta_secondary') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
