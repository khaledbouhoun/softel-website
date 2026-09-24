@extends('layouts.app')

@section('title', __('site.nav_services'))

@section('content')

    {{-- Services Header Banner --}}
    <section class="bg-gray-50 dark:bg-[#0e141b] border-b border-[#E2E8F0] dark:border-[#26313C] py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <div class="max-w-3xl space-y-4">
                <div class="inline-flex items-center gap-2 text-[#009EE0] font-bold text-xs uppercase tracking-wider">
                    <span class="w-6 h-0.5 bg-[#009EE0]"></span>
                    <span>{{ __('site.services_badge') }}</span>
                </div>
                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-[#111111] dark:text-[#F8FAFC]">
                    {{ __('site.services_title') }}
                </h1>
                <p class="text-lg text-[#475569] dark:text-[#94A3B8] leading-relaxed">
                    {{ __('site.services_subtitle') }}
                </p>
            </div>
        </div>
    </section>

    {{-- Services 2x2 Grid --}}
    <section class="py-20 bg-white dark:bg-[#0B0F14]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
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
                            <div class="flex items-center justify-between mb-6">
                                <div class="w-14 h-14 rounded-xl bg-sky-50 dark:bg-[#009EE0]/10 border border-[#009EE0]/20 flex items-center justify-center group-hover:bg-[#009EE0] group-hover:text-white transition-colors duration-300">
                                    {!! $iconSvg !!}
                                </div>
                                <span class="text-xs font-bold text-gray-500 dark:text-gray-400">0{{ $service->sort_order }}</span>
                            </div>

                            <h2 class="text-xl font-bold text-[#111111] dark:text-[#F8FAFC] group-hover:text-[#009EE0] dark:group-hover:text-[#009EE0] transition-colors mb-2">
                                {{ $service->getLocalized('title') }}
                            </h2>

                            <p class="text-sm font-semibold text-[#475569] dark:text-[#CBD5E1] mb-3 leading-relaxed">
                                {{ $service->getLocalized('summary') }}
                            </p>

                            <p class="text-sm text-[#334155] dark:text-[#94A3B8] leading-relaxed mb-6">
                                {{ $service->getLocalized('description') }}
                            </p>
                        </div>

                        <div class="pt-4 border-t border-[#E2E8F0] dark:border-[#26313C] flex items-center justify-between">
                            <a href="{{ route('services.show', [$currentLocale, $service->slug]) }}"
                               class="text-sm font-bold text-[#111111] dark:text-[#F8FAFC] group-hover:text-[#009EE0] dark:group-hover:text-[#009EE0] transition-colors flex items-center gap-1.5">
                                <span>{{ __('site.service_learn_more') }}</span>
                                <svg class="w-4 h-4 flip-rtl group-hover:translate-x-1 rtl:group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

@endsection
