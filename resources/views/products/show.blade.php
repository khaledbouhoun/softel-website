@extends('layouts.app')

@section('title', $product->getLocalized('title'))

@section('content')

  {{-- Breadcrumb / Header --}}

  <section class="bg-gray-50 dark:bg-[#0e141b] border-b border-[#E2E8F0] dark:border-[#26313C] py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

      ```
      <a href="{{ route('products.index', $currentLocale) }}"
        class="inline-flex items-center gap-2 text-sm font-semibold text-[#009EE0] hover:text-[#0088c2] transition-colors">
        <svg class="w-4 h-4 flip-rtl rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>

        <span>{{ __('site.products_back') }}</span>
      </a>

    </div>
    ```

  </section>

  {{-- Product Details --}}

  <section class="py-10 lg:py-14 bg-white dark:bg-[#0B0F14]">

    ```
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

      {{-- Main Horizontal Product Layout --}}
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 xl:gap-16 items-start">

        {{-- LEFT : Product Image --}}
        <div class="relative rounded-2xl overflow-hidden border border-[#E2E8F0] dark:border-[#26313C]
          bg-gradient-to-br from-sky-50/50 via-white to-gray-50/30
          dark:from-[#0B0F14] dark:via-[#111820] dark:to-[#0B0F14]
          shadow-sm min-h-[360px] lg:min-h-[520px]
          flex items-center justify-center p-8 lg:p-12">

          @if ($product->image)

            <img src="{{ asset($product->image) }}" alt="{{ $product->getLocalized('title') }}"
              class="max-h-[360px] lg:max-h-[460px] max-w-full w-auto h-auto object-contain" loading="lazy">

          @else

            <div class="flex items-center justify-center opacity-60">
              <img src="{{ asset('images/softel_logo_black.svg') }}"
                data-light="{{ asset('images/softel_logo_black.svg') }}"
                data-dark="{{ asset('images/softel_logo_white.svg') }}"
                data-logo-light="{{ asset('images/softel_logo_black.svg') }}"
                data-logo-dark="{{ asset('images/softel_logo_white.svg') }}" alt="SOFTEL"
                class="h-16 lg:h-20 w-auto object-contain">
            </div>

          @endif

        </div>


        {{-- RIGHT : Product Information --}}
        <div class="flex flex-col justify-center">

          {{-- Title --}}
          <div class="mb-5">

            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight
              text-[#111111] dark:text-[#F8FAFC] leading-tight">

              {{ $product->getLocalized('title') }}

            </h1>

            <p class="text-base sm:text-lg lg:text-xl
              text-[#475569] dark:text-[#94A3B8]
              mt-4 leading-relaxed">

              {{ $product->getLocalized('summary') }}

            </p>

          </div>


          {{-- Description --}}
          <div class="border-s-4 border-[#009EE0] ps-5 py-1 mb-7
            text-[#334155] dark:text-[#CBD5E1]
            leading-relaxed text-base lg:text-lg">

            {{ $product->getLocalized('description') }}

          </div>


          {{-- Technologies --}}
          @php
            $techStack = $product->getLocalizedTechStack();
          @endphp

          @if (!empty($techStack))

            <div class="mb-8">

              <h3 class="text-xs font-bold text-[#64748B] dark:text-[#94A3B8]
                    uppercase tracking-wider mb-3">

                {{ __('site.products_tech_title') }}

              </h3>

              <div class="flex flex-wrap gap-2">

                @foreach ($techStack as $tech)

                  <span class="text-xs sm:text-sm font-semibold
                            bg-gray-100 dark:bg-[#111820]
                            border border-[#E2E8F0] dark:border-[#26313C]
                            text-[#111111] dark:text-[#F8FAFC]
                            px-3.5 py-2 rounded-lg">

                    {{ $tech }}

                  </span>

                @endforeach

              </div>

            </div>

          @endif


          {{-- Contact Card --}}
          <div class="mt-auto p-5 sm:p-6
            rounded-2xl
            bg-gray-50 dark:bg-[#111820]
            border border-[#E2E8F0] dark:border-[#26313C]">

            <div class="flex flex-col xl:flex-row
              xl:items-center
              justify-between
              gap-5">

              {{-- Contact Text --}}
              <div>

                <h2 class="font-bold text-[#111111] dark:text-[#F8FAFC]
                  text-lg sm:text-xl">

                  {{ __('site.products_interest_title') }}

                </h2>

                <p class="text-sm text-[#475569] dark:text-[#94A3B8]
                  mt-1 leading-relaxed">

                  {{ __('site.products_interest_desc') }}

                </p>

              </div>


              {{-- Contact Actions --}}
              <div class="flex flex-col sm:flex-row items-stretch gap-3 shrink-0">

                {{-- WhatsApp --}}
                <a href="{{ $whatsappUrl ?? $product->getWhatsAppUrl($currentLocale) }}" target="_blank"
                  rel="noopener noreferrer" aria-label="{{ __('site.products_contact_cta') }} - WhatsApp" class="inline-flex items-center justify-center gap-2.5
                  bg-[#25D366] hover:bg-[#20ba59]
                  active:bg-[#1da850]
                  text-white font-bold
                  px-5 py-3
                  rounded-xl
                  shadow-sm hover:shadow
                  transition-all duration-200
                  text-sm whitespace-nowrap
                  focus:outline-none focus:ring-2
                  focus:ring-[#25D366]
                  focus:ring-offset-2
                  dark:focus:ring-offset-[#111820]">

                  <svg class="w-5 h-5 flex-shrink-0 fill-current" viewBox="0 0 24 24" aria-hidden="true">

                    <path
                      d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.82 9.82 0 0012.04 2zm.01 1.67c2.2 0 4.26.86 5.82 2.42a8.23 8.23 0 012.41 5.83c0 4.54-3.7 8.24-8.24 8.24-1.48 0-2.93-.4-4.2-1.15l-.3-.18-3.12.82.83-3.04-.2-.31a8.19 8.19 0 01-1.26-4.38c0-4.54 3.7-8.24 8.24-8.24zm4.52 11.63c-.25-.13-1.47-.72-1.7-.81-.23-.08-.39-.13-.56.13-.17.25-.64.81-.79.97-.14.17-.29.19-.54.06-.25-.13-1.06-.39-2.03-1.25-.75-.67-1.26-1.5-1.41-1.75-.15-.25-.02-.39.11-.51.11-.11.25-.29.37-.44.13-.15.17-.25.25-.42.08-.17.04-.31-.02-.44-.06-.13-.56-1.34-.76-1.84-.2-.49-.4-.42-.56-.43h-.47c-.17 0-.44.06-.67.31-.23.25-.88.86-.88 2.1s.9 2.44 1.03 2.61c.13.17 1.77 2.7 4.29 3.79.6.26 1.07.41 1.44.53.6.19 1.15.16 1.58.1.48-.07 1.47-.6 1.68-1.18.21-.58.21-1.07.15-1.18-.06-.12-.22-.19-.47-.31z" />

                  </svg>

                  <span>
                    {{ __('site.products_contact_cta') }}
                  </span>

                </a>


                {{-- Phone --}}
                <a href="tel:+213558925355" dir="ltr" class="inline-flex items-center justify-center gap-2
                  font-bold
                  text-[#111111] dark:text-[#F8FAFC]
                  hover:text-[#009EE0]
                  dark:hover:text-[#009EE0]
                  transition-colors
                  py-3 px-4
                  rounded-xl
                  bg-white dark:bg-[#0B0F14]
                  border border-[#E2E8F0]
                  dark:border-[#26313C]
                  hover:border-[#009EE0]
                  shadow-sm
                  text-sm whitespace-nowrap">

                  <svg class="w-4 h-4 text-[#009EE0] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3 5a2 2 0 012-2h3.28a2 2 0 011.897 1.368l1.498 4.493a2 2 0 01-1.003 2.419l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a2 2 0 012.419-1.003l4.493 1.498A2 2 0 0121 19v1a2 2 0 01-2 2h-1C9.716 22 3 15.284 3 7V5z" />

                  </svg>

                  <span>0558 92 53 55</span>

                </a>

              </div>

            </div>

          </div>

        </div>

      </div>


      {{-- Secondary Contact Link --}}
      <div class="mt-8 pt-6
        border-t border-[#E2E8F0] dark:border-[#26313C]
        text-center sm:text-start
        text-xs sm:text-sm
        text-[#64748B] dark:text-[#94A3B8]">

        <span>
          {{ __('site.products_empty_desc') }}
        </span>

        <a href="{{ route('contact.create', $currentLocale) }}" class="text-[#009EE0] hover:text-[#0088c2]
          font-semibold hover:underline
          ms-1 inline-flex items-center gap-1">

          <span>
            {{ __('site.hero_cta_secondary') }}
          </span>

          <svg class="w-3 h-3 flip-rtl" fill="none" stroke="currentColor" viewBox="0 0 24 24">

            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />

          </svg>

        </a>

      </div>

    </div>
    ```

  </section>

@endsection
