@extends('layouts.app')

@section('title', __('site.contact_title'))

@section('content')

    {{-- Contact Header Banner --}}
    <section class="bg-gray-50 dark:bg-[#0e141b] border-b border-[#E2E8F0] dark:border-[#26313C] py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <div class="max-w-3xl space-y-4">
                <div class="inline-flex items-center gap-2 text-[#009EE0] font-bold text-xs uppercase tracking-wider">
                    <span class="w-6 h-0.5 bg-[#009EE0]"></span>
                    <span>{{ __('site.contact_badge') }}</span>
                </div>
                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-[#111111] dark:text-[#F8FAFC]">
                    {{ __('site.contact_title') }}
                </h1>
                <p class="text-lg text-[#475569] dark:text-[#94A3B8] leading-relaxed">
                    {{ __('site.contact_subtitle') }}
                </p>
            </div>
        </div>
    </section>

    {{-- Contact Information & Form --}}
    <section class="py-20 bg-white dark:bg-[#0B0F14]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <div class="grid lg:grid-cols-12 gap-12 items-start">
                {{-- Left: Contact Information Cards --}}
                <div class="lg:col-span-5 space-y-6">
                    <div class="p-8 rounded-2xl bg-[#010101] text-white space-y-8 shadow-xl border border-gray-800">
                        <div>
                            <h2 class="text-2xl font-bold text-white mb-2">{{ __('site.contact_info_title') }}</h2>
                            <p class="text-sm text-gray-400">
                                {{ __('site.contact_info_desc') }}
                            </p>
                        </div>

                        {{-- Address --}}
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-xl bg-[#009EE0]/10 flex items-center justify-center text-[#009EE0] flex-shrink-0 mt-1">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-xs font-bold text-gray-400 uppercase mb-1">{{ __('site.contact_address_label') }}</h3>
                                <p class="text-sm text-gray-200 leading-relaxed">
                                    {{ __('site.contact_address_value') }}
                                </p>
                            </div>
                        </div>

                        {{-- Phones --}}
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-xl bg-[#009EE0]/10 flex items-center justify-center text-[#009EE0] flex-shrink-0 mt-1">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-xs font-bold text-gray-400 uppercase mb-1">{{ __('site.contact_phone_label') }}</h3>
                                <div class="space-y-1 text-sm font-semibold text-white" dir="ltr">
                                    <div><a href="tel:+213798757380" class="hover:text-[#009EE0] transition-colors">0798 75 73 80</a></div>
                                    <div><a href="tel:+213558925355" class="hover:text-[#009EE0] transition-colors">0558 92 53 55</a></div>
                                    <div><a href="tel:+21320242020" class="hover:text-[#009EE0] transition-colors">020 24 20 20</a></div>
                                </div>
                            </div>
                        </div>

                        {{-- Email --}}
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-xl bg-[#009EE0]/10 flex items-center justify-center text-[#009EE0] flex-shrink-0 mt-1">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-xs font-bold text-gray-400 uppercase mb-1">{{ __('site.contact_email_label') }}</h3>
                                <a href="mailto:info@softel.dz" class="text-sm font-semibold text-[#009EE0] hover:underline" dir="ltr">
                                    info@softel.dz
                                </a>
                            </div>
                        </div>

                        {{-- Working Hours --}}
                        <div class="flex items-start gap-4 pt-4 border-t border-gray-800">
                            <div class="w-10 h-10 rounded-xl bg-[#009EE0]/10 flex items-center justify-center text-[#009EE0] flex-shrink-0 mt-1">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-xs font-bold text-gray-400 uppercase mb-1">{{ __('site.contact_hours_label') }}</h3>
                                <p class="text-sm text-gray-300">
                                    {{ __('site.contact_hours_value') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right: Contact Lead Form --}}
                <div class="lg:col-span-7 bg-white dark:bg-[#111820] rounded-2xl border border-[#E2E8F0] dark:border-[#26313C] p-8 sm:p-10 shadow-sm">
                    <h2 class="text-2xl font-bold text-[#111111] dark:text-[#F8FAFC] mb-6">{{ __('site.contact_form_title') }}</h2>

                    @if (session('success'))
                        <div class="mb-6 p-4 rounded-xl bg-green-50 dark:bg-green-950/40 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-300 text-sm flex items-center gap-3">
                            <svg class="w-5 h-5 text-green-600 dark:text-green-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            <span>{{ session('success') }}</span>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('contact.store', $currentLocale) }}" class="space-y-6">
                        @csrf

                        <div class="grid sm:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-xs font-bold text-[#111111] dark:text-[#CBD5E1] uppercase tracking-wider mb-2">
                                    {{ __('site.contact_name') }} <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="name" value="{{ old('name') }}" required
                                       class="w-full bg-white dark:bg-[#0B0F14] text-[#111111] dark:text-[#F8FAFC] placeholder-gray-400 dark:placeholder-gray-500 border border-gray-300 dark:border-[#26313C] rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#009EE0] focus:border-[#009EE0] transition"
                                       placeholder="{{ __('site.contact_name_placeholder') }}">
                                @error('name') <p class="text-red-600 dark:text-red-400 text-xs mt-1.5">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-[#111111] dark:text-[#CBD5E1] uppercase tracking-wider mb-2">
                                    {{ __('site.contact_email') }} <span class="text-red-500">*</span>
                                </label>
                                <input type="email" name="email" value="{{ old('email') }}" required
                                       class="w-full bg-white dark:bg-[#0B0F14] text-[#111111] dark:text-[#F8FAFC] placeholder-gray-400 dark:placeholder-gray-500 border border-gray-300 dark:border-[#26313C] rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#009EE0] focus:border-[#009EE0] transition"
                                       placeholder="{{ __('site.contact_email_placeholder') }}">
                                @error('email') <p class="text-red-600 dark:text-red-400 text-xs mt-1.5">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="grid sm:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-xs font-bold text-[#111111] dark:text-[#CBD5E1] uppercase tracking-wider mb-2">
                                    {{ __('site.contact_phone') }}
                                </label>
                                <input type="text" name="phone" value="{{ old('phone') }}"
                                       class="w-full bg-white dark:bg-[#0B0F14] text-[#111111] dark:text-[#F8FAFC] placeholder-gray-400 dark:placeholder-gray-500 border border-gray-300 dark:border-[#26313C] rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#009EE0] focus:border-[#009EE0] transition"
                                       placeholder="{{ __('site.contact_phone_placeholder') }}">
                                @error('phone') <p class="text-red-600 dark:text-red-400 text-xs mt-1.5">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-[#111111] dark:text-[#CBD5E1] uppercase tracking-wider mb-2">
                                    {{ __('site.contact_company') }}
                                </label>
                                <input type="text" name="company" value="{{ old('company') }}"
                                       class="w-full bg-white dark:bg-[#0B0F14] text-[#111111] dark:text-[#F8FAFC] placeholder-gray-400 dark:placeholder-gray-500 border border-gray-300 dark:border-[#26313C] rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#009EE0] focus:border-[#009EE0] transition"
                                       placeholder="{{ __('site.contact_company_placeholder') }}">
                                @error('company') <p class="text-red-600 dark:text-red-400 text-xs mt-1.5">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-[#111111] dark:text-[#CBD5E1] uppercase tracking-wider mb-2">
                                {{ __('site.contact_message') }} <span class="text-red-500">*</span>
                            </label>
                            <textarea name="message" rows="5" required
                                      class="w-full bg-white dark:bg-[#0B0F14] text-[#111111] dark:text-[#F8FAFC] placeholder-gray-400 dark:placeholder-gray-500 border border-gray-300 dark:border-[#26313C] rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#009EE0] focus:border-[#009EE0] transition"
                                      placeholder="{{ __('site.contact_message_placeholder') }}">{{ old('message') }}</textarea>
                            @error('message') <p class="text-red-600 dark:text-red-400 text-xs mt-1.5">{{ $message }}</p> @enderror
                        </div>

                        <button type="submit"
                                class="w-full sm:w-auto bg-[#009EE0] hover:bg-[#0088c2] text-white font-bold px-8 py-3.5 rounded-lg shadow-md hover:shadow-lg transition-all duration-200">
                            {{ __('site.contact_submit') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

@endsection
