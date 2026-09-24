@extends('layouts.app')

@section('title', $service->getLocalized('title'))

@section('content')

    @php
        $iconSvg = match($service->slug) {
            'development' => '
                <svg class="w-10 h-10 text-[#009EE0]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                </svg>',
            'consulting-support' => '
                <svg class="w-10 h-10 text-[#009EE0]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>',
            'training' => '
                <svg class="w-10 h-10 text-[#009EE0]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5" />
                </svg>',
            'after-sales-support' => '
                <svg class="w-10 h-10 text-[#009EE0]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>',
            default => '
                <svg class="w-10 h-10 text-[#009EE0]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>'
        };
    @endphp

    {{-- Breadcrumb & Top Bar --}}
    <section class="bg-gray-50 dark:bg-[#0e141b] border-b border-[#E2E8F0] dark:border-[#26313C] py-12">
        <div class="max-w-5xl mx-auto px-4 sm:px-6">
            <a href="{{ route('services.index', $currentLocale) }}"
               class="inline-flex items-center gap-2 text-sm font-semibold text-[#009EE0] hover:text-[#0088c2] mb-6">
                <svg class="w-4 h-4 flip-rtl rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <span>{{ __('site.service_back') }}</span>
            </a>

            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-6">
                <div class="w-16 h-16 rounded-2xl bg-sky-50 dark:bg-[#009EE0]/10 border border-[#009EE0]/20 flex items-center justify-center flex-shrink-0">
                    {!! $iconSvg !!}
                </div>
                <div>
                    <h1 class="text-3xl sm:text-4xl font-extrabold text-[#111111] dark:text-[#F8FAFC]">
                        {{ $service->getLocalized('title') }}
                    </h1>
                    <p class="text-lg text-[#475569] dark:text-[#CBD5E1] mt-1">
                        {{ $service->getLocalized('summary') }}
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- Detailed Content --}}
    <section class="py-16 bg-white dark:bg-[#0B0F14]">
        <div class="max-w-5xl mx-auto px-4 sm:px-6">
            <div class="grid lg:grid-cols-12 gap-12">
                <div class="lg:col-span-8 space-y-6">
                    <div class="prose max-w-none text-[#334155] dark:text-[#CBD5E1] leading-relaxed text-base sm:text-lg">
                        <p class="text-[#111111] dark:text-[#F8FAFC] font-medium text-lg sm:text-xl border-s-4 border-[#009EE0] ps-4 py-1">
                            {{ $service->getLocalized('description') }}
                        </p>
                    </div>

                    <div class="pt-6 border-t border-[#E2E8F0] dark:border-[#26313C]">
                        <h3 class="text-xl font-bold text-[#111111] dark:text-[#F8FAFC] mb-4">{{ __('site.service_partner_title') }}</h3>
                        <ul class="space-y-3 text-sm text-[#334155] dark:text-[#CBD5E1]">
                            <li class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-[#009EE0] flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>{{ __('site.service_partner_1') }}</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-[#009EE0] flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>{{ __('site.service_partner_2') }}</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-[#009EE0] flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>{{ __('site.service_partner_3') }}</span>
                            </li>
                        </ul>
                    </div>
                </div>

                {{-- Side Inquiry Card --}}
                <div class="lg:col-span-4">
                    <div class="p-6 rounded-2xl bg-gray-50 dark:bg-[#111820] border border-[#E2E8F0] dark:border-[#26313C] space-y-4">
                        <h4 class="font-bold text-[#111111] dark:text-[#F8FAFC] text-base">{{ __('site.service_request_title') }}</h4>
                        <p class="text-xs text-[#334155] dark:text-[#CBD5E1] leading-relaxed">
                            {{ __('site.service_request_desc') }}
                        </p>
                        <a href="{{ route('contact.create', $currentLocale) }}"
                           class="w-full text-center block bg-[#009EE0] hover:bg-[#0088c2] text-white font-bold py-3 rounded-lg shadow transition">
                            {{ __('site.hero_cta_secondary') }}
                        </a>
                        <div class="pt-3 border-t border-[#E2E8F0] dark:border-[#26313C] text-xs text-[#475569] dark:text-[#CBD5E1] space-y-1">
                            <div>{{ __('site.service_direct_phone') }} <a href="tel:+213798757380" class="font-semibold text-[#111111] dark:text-[#F8FAFC] hover:text-[#009EE0] dark:hover:text-[#009EE0]" dir="ltr">0798 75 73 80</a></div>
                            <div>{{ __('site.service_direct_email') }} <a href="mailto:info@softel.dz" class="font-semibold text-[#111111] dark:text-[#F8FAFC] hover:text-[#009EE0] dark:hover:text-[#009EE0]" dir="ltr">info@softel.dz</a></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
