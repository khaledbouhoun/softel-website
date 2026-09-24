<!DOCTYPE html>
<html lang="{{ $currentLocale }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}" class="scroll-smooth">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', config('app.name', 'SOFTEL')) | SOFTEL</title>
  <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

  {{-- Fonts --}}
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Tajawal:wght@400;500;700;800&display=swap"
    rel="stylesheet">

  {{-- Anti-FOUC Theme Initialization & Functions --}}
  <script>
    (function () {
      try {
        const stored = localStorage.getItem('theme');
        if (stored === 'dark' || (!stored && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
          document.documentElement.classList.add('dark');
        } else {
          document.documentElement.classList.remove('dark');
        }
      } catch (e) { }
    })();

    function updateLogo(theme) {
      const isDark = theme === 'dark' || theme === true;
      document.querySelectorAll('img[data-light][data-dark], img[data-logo-light][data-logo-dark]').forEach(function (img) {
        const lightSrc = img.getAttribute('data-light') || img.getAttribute('data-logo-light');
        const darkSrc = img.getAttribute('data-dark') || img.getAttribute('data-logo-dark');
        const targetSrc = isDark ? darkSrc : lightSrc;
        if (targetSrc && img.getAttribute('src') !== targetSrc) {
          img.setAttribute('src', targetSrc);
        }
      });
    }

    function applyThemeUI(isDark) {
      updateLogo(isDark ? 'dark' : 'light');

      document.querySelectorAll('.theme-toggle-btn').forEach(function (btn) {
        btn.setAttribute('aria-label', isDark ? "{{ __('site.ui_theme_light') }}" : "{{ __('site.ui_theme_dark') }}");
        btn.setAttribute('title', isDark ? "{{ __('site.ui_theme_light') }}" : "{{ __('site.ui_theme_dark') }}");
      });
    }

    window.toggleTheme = function () {
      const isDark = document.documentElement.classList.toggle('dark');
      try {
        localStorage.setItem('theme', isDark ? 'dark' : 'light');
      } catch (e) { }
      applyThemeUI(isDark);
    };

    document.addEventListener('DOMContentLoaded', function () {
      const isDark = document.documentElement.classList.contains('dark');
      applyThemeUI(isDark);
    });
  </script>

  {{-- Tailwind Play CDN --}}
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      darkMode: 'class',
      theme: {
        extend: {
          colors: {
            softel: {
              black: '#010101',
              blue: '#009EE0',
              'blue-hover': '#0088c2',
              'blue-light': '#f0f9ff',
              'blue-subtle': '#e0f2fe',
              'gray-dark': '#111111',
              'gray-card': '#181818',
              'gray-border': '#262626',
              dark: '#0B0F14',
              'dark-alt': '#0e141b',
              'dark-card': '#111820',
              'dark-border': '#26313C',
              'dark-muted': '#94A3B8',
              'dark-foreground': '#F8FAFC',
            },
          },
          fontFamily: {
            sans: {!! $isRtl ? "['Tajawal', 'system-ui', 'sans-serif']" : "['Plus Jakarta Sans', 'system-ui', 'sans-serif']" !!},
          },
        },
      },
    };
  </script>
  <style>
    :root {
      --background: #FFFFFF;
      --foreground: #111111;
      --muted-foreground: #64748B;
      --card: #FFFFFF;
      --border: #E2E8F0;
      --primary: #009EE0;
      --primary-foreground: #FFFFFF;
      --input: #FFFFFF;
      --input-border: #CBD5E1;
    }

    html.dark {
      --background: #0B0F14;
      --foreground: #F8FAFC;
      --muted-foreground: #94A3B8;
      --card: #111820;
      --border: #26313C;
      --primary: #009EE0;
      --primary-foreground: #FFFFFF;
      --input: #111820;
      --input-border: #26313C;
    }

    body {
      font-family:
        {!! $isRtl ? "'Tajawal', system-ui, sans-serif" : "'Plus Jakarta Sans', system-ui, sans-serif" !!}
      ;
      background-color: var(--background);
      color: var(--foreground);
    }

    [dir="rtl"] .flip-rtl {
      transform: scaleX(-1);
    }

    /* Global Text Selection with high contrast in both themes */
    ::selection {
      background-color: #009EE0 !important;
      color: #FFFFFF !important;
    }

    ::-moz-selection {
      background-color: #009EE0 !important;
      color: #FFFFFF !important;
    }

    /* Keep selected text inside blue elements fully visible */
    .bg-softel-blue::selection,
    .bg-softel-blue *::selection,
    [class*="bg-[#009EE0]"]::selection,
    [class*="bg-[#009EE0]"] *::selection {
      background-color: #010101 !important;
      color: #FFFFFF !important;
    }

    /* Bulletproof Theme Toggle Icons */
    .theme-sun-icon {
      display: none !important;
    }

    .theme-moon-icon {
      display: block !important;
    }

    html.dark .theme-sun-icon {
      display: block !important;
    }

    html.dark .theme-moon-icon {
      display: none !important;
    }
  </style>
</head>

<body
  class="antialiased text-[#111111] dark:text-[#F8FAFC] bg-white dark:bg-[#0B0F14] min-h-screen flex flex-col overflow-x-hidden transition-colors duration-200">

  {{-- Corporate Top Bar --}}
  <div class="bg-[#010101] text-gray-300 text-xs border-b border-gray-800 py-2 px-4 sm:px-6">
    <div class="max-w-7xl mx-auto flex flex-wrap justify-between items-center gap-2">
      <div class="flex items-center gap-4">
        <a href="tel:+213798757380" class="hover:text-[#009EE0] transition-colors flex items-center gap-1.5" dir="ltr">
          <svg class="w-3.5 h-3.5 text-[#009EE0]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
          </svg>
          <span>0798 75 73 80</span>
        </a>
        <span class="hidden sm:inline text-gray-600">|</span>
        <a href="mailto:info@softel.dz"
          class="hidden sm:flex hover:text-[#009EE0] transition-colors items-center gap-1.5" dir="ltr">
          <svg class="w-3.5 h-3.5 text-[#009EE0]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
          </svg>
          <span>info@softel.dz</span>
        </a>
      </div>
      <div class="flex items-center gap-3">
        <span class="hidden md:inline text-gray-400">{{ __('site.topbar_badge') }}</span>
      </div>
    </div>
  </div>

  {{-- Main Navbar --}}
  <header
    class="border-b border-[#E2E8F0] dark:border-[#26313C] sticky top-0 bg-white dark:bg-[#0B0F14] z-50 shadow-sm transition-colors">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-3.5 flex items-center justify-between">
      {{-- Official SOFTEL Theme-Aware Logo --}}
      <a href="{{ route('home', $currentLocale) }}" class="flex items-center gap-3 focus:outline-none group"
        aria-label="SOFTEL">
        <div class="flex items-center">
          <img id="navbar-logo" src="{{ asset('images/softel_logo_black.svg') }}"
            data-light="{{ asset('images/softel_logo_black.svg') }}"
            data-dark="{{ asset('images/softel_logo_white.svg') }}"
            data-logo-light="{{ asset('images/softel_logo_black.svg') }}"
            data-logo-dark="{{ asset('images/softel_logo_white.svg') }}" alt="SOFTEL"
            class="h-9 sm:h-10 md:h-11 w-auto object-contain transition-transform group-hover:scale-105 duration-200">
          <script>
            if (document.documentElement.classList.contains('dark')) {
              document.getElementById('navbar-logo').src = "{{ asset('images/softel_logo_white.svg') }}";
            }
          </script>
        </div>
      </a>

      {{-- Desktop Navigation --}}
      <nav class="hidden md:flex items-center gap-7 text-sm font-semibold text-[#111111] dark:text-[#F8FAFC]">
        <a href="{{ route('home', $currentLocale) }}"
          class="transition-colors hover:text-[#009EE0] dark:hover:text-[#009EE0] {{ request()->routeIs('home') ? 'text-[#009EE0] dark:text-[#009EE0]' : 'text-[#111111] dark:text-[#F8FAFC]' }}">
          {{ __('site.nav_home') }}
        </a>
        <a href="{{ route('about', $currentLocale) }}"
          class="transition-colors hover:text-[#009EE0] dark:hover:text-[#009EE0] {{ request()->routeIs('about') ? 'text-[#009EE0] dark:text-[#009EE0]' : 'text-[#111111] dark:text-[#F8FAFC]' }}">
          {{ __('site.nav_about') }}
        </a>
        <a href="{{ route('services.index', $currentLocale) }}"
          class="transition-colors hover:text-[#009EE0] dark:hover:text-[#009EE0] {{ request()->routeIs('services.*') ? 'text-[#009EE0] dark:text-[#009EE0]' : 'text-[#111111] dark:text-[#F8FAFC]' }}">
          {{ __('site.nav_services') }}
        </a>
        <a href="{{ route('home', $currentLocale) }}#solutions"
          class="transition-colors hover:text-[#009EE0] dark:hover:text-[#009EE0] text-[#111111] dark:text-[#F8FAFC]">
          {{ __('site.nav_solutions') }}
        </a>
        <a href="{{ route('products.index', $currentLocale) }}"
          class="transition-colors hover:text-[#009EE0] dark:hover:text-[#009EE0] {{ request()->routeIs('products.*') ? 'text-[#009EE0] dark:text-[#009EE0]' : 'text-[#111111] dark:text-[#F8FAFC]' }}">
          {{ __('site.nav_products') }}
        </a>
        <a href="{{ route('contact.create', $currentLocale) }}"
          class="transition-colors hover:text-[#009EE0] dark:hover:text-[#009EE0] {{ request()->routeIs('contact.*') ? 'text-[#009EE0] dark:text-[#009EE0]' : 'text-[#111111] dark:text-[#F8FAFC]' }}">
          {{ __('site.nav_contact') }}
        </a>
      </nav>

      {{-- Right Actions: Language Selector, Theme Switcher & CTA --}}
      <div class="hidden sm:flex items-center gap-3">
        {{-- Language Selector --}}
        <div
          class="flex items-center bg-gray-100 dark:bg-[#111820] p-1 rounded-lg text-xs font-bold border border-[#E2E8F0] dark:border-[#26313C]">
          @foreach (['en' => 'EN', 'fr' => 'FR', 'ar' => 'عربي'] as $code => $label)
            @php
              $targetUrl = route(
                Route::currentRouteName() ?: 'home',
                array_merge(Route::current() ? Route::current()->parameters() : [], ['locale' => $code])
              );
            @endphp
            <a href="{{ $targetUrl }}"
              class="px-2.5 py-1 rounded transition-all {{ $currentLocale === $code ? 'bg-[#009EE0] text-white shadow-sm' : 'text-[#111111] dark:text-[#F8FAFC] hover:text-[#009EE0] dark:hover:text-[#009EE0]' }}">
              {{ $label }}
            </a>
          @endforeach
        </div>

        {{-- Theme Switch Button (Desktop) --}}
        <button type="button" onclick="toggleTheme()"
          class="theme-toggle-btn p-2 rounded-lg text-[#111111] hover:text-[#009EE0] hover:bg-gray-100 dark:text-[#F8FAFC] dark:hover:text-[#009EE0] dark:hover:bg-[#111820] border border-[#E2E8F0] dark:border-[#26313C] focus:outline-none focus:ring-2 focus:ring-[#009EE0] transition-colors flex items-center justify-center cursor-pointer"
          aria-label="{{ __('site.ui_toggle_theme') }}" title="{{ __('site.ui_toggle_theme') }}">
          <svg class="theme-sun-icon w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
          </svg>
          <svg class="theme-moon-icon w-4 h-4 text-[#111111] dark:text-[#F8FAFC]" fill="none" stroke="currentColor"
            viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
          </svg>
        </button>

        {{-- Contact CTA Button --}}
        <a href="{{ route('contact.create', $currentLocale) }}"
          class="bg-[#009EE0] hover:bg-[#0088c2] text-white text-sm font-bold px-5 py-2.5 rounded-lg shadow-sm hover:shadow transition-all duration-200 flex items-center gap-2">
          <span>{{ __('site.hero_cta_secondary') }}</span>
          <svg class="w-4 h-4 flip-rtl" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
          </svg>
        </a>
      </div>

      {{-- Mobile Actions --}}
      <div class="flex items-center gap-2 md:hidden">
        {{-- Language Switcher Compact on Mobile --}}
        <div
          class="flex bg-gray-100 dark:bg-[#111820] p-0.5 rounded text-xs font-semibold border border-[#E2E8F0] dark:border-[#26313C]">
          @foreach (['en' => 'EN', 'fr' => 'FR', 'ar' => 'عر'] as $code => $label)
            @php
              $targetUrl = route(
                Route::currentRouteName() ?: 'home',
                array_merge(Route::current() ? Route::current()->parameters() : [], ['locale' => $code])
              );
            @endphp
            <a href="{{ $targetUrl }}"
              class="px-1.5 py-0.5 rounded {{ $currentLocale === $code ? 'bg-[#009EE0] text-white font-bold' : 'text-[#111111] dark:text-[#F8FAFC]' }}">
              {{ $label }}
            </a>
          @endforeach
        </div>

        {{-- Theme Switch Button (Mobile) --}}
        <button type="button" onclick="toggleTheme()"
          class="theme-toggle-btn p-1.5 rounded-lg text-[#111111] hover:text-[#009EE0] hover:bg-gray-100 dark:text-[#F8FAFC] dark:hover:text-[#009EE0] dark:hover:bg-[#111820] border border-[#E2E8F0] dark:border-[#26313C] focus:outline-none transition-colors flex items-center justify-center cursor-pointer"
          aria-label="{{ __('site.ui_toggle_theme') }}" title="{{ __('site.ui_toggle_theme') }}">
          <svg class="theme-sun-icon w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
          </svg>
          <svg class="theme-moon-icon w-4 h-4 text-[#111111] dark:text-[#F8FAFC]" fill="none" stroke="currentColor"
            viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
          </svg>
        </button>

        {{-- Mobile Hamburger Button --}}
        <button type="button" id="mobile-menu-button"
          class="p-2 text-[#111111] dark:text-[#F8FAFC] hover:text-[#009EE0] hover:bg-gray-100 dark:hover:bg-[#111820] rounded-lg focus:outline-none"
          aria-label="{{ __('site.ui_menu_toggle') }}">
          <svg id="menu-icon" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
          </svg>
          <svg id="close-icon" class="w-6 h-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
    </div>

    {{-- Mobile Collapsible Drawer --}}
    <div id="mobile-menu"
      class="hidden md:hidden border-t border-[#E2E8F0] dark:border-[#26313C] bg-white dark:bg-[#0B0F14] px-6 py-5 shadow-lg animate-fadeIn">
      <nav class="flex flex-col space-y-4 text-base font-semibold text-[#111111] dark:text-[#F8FAFC]">
        <a href="{{ route('home', $currentLocale) }}"
          class="px-2 py-1.5 rounded-md hover:bg-sky-50 dark:hover:bg-[#111820] hover:text-[#009EE0] dark:hover:text-[#009EE0] {{ request()->routeIs('home') ? 'text-[#009EE0] dark:text-[#009EE0] bg-sky-50 dark:bg-[#009EE0]/10' : '' }}">
          {{ __('site.nav_home') }}
        </a>
        <a href="{{ route('about', $currentLocale) }}"
          class="px-2 py-1.5 rounded-md hover:bg-sky-50 dark:hover:bg-[#111820] hover:text-[#009EE0] dark:hover:text-[#009EE0] {{ request()->routeIs('about') ? 'text-[#009EE0] dark:text-[#009EE0] bg-sky-50 dark:bg-[#009EE0]/10' : '' }}">
          {{ __('site.nav_about') }}
        </a>
        <a href="{{ route('services.index', $currentLocale) }}"
          class="px-2 py-1.5 rounded-md hover:bg-sky-50 dark:hover:bg-[#111820] hover:text-[#009EE0] dark:hover:text-[#009EE0] {{ request()->routeIs('services.*') ? 'text-[#009EE0] dark:text-[#009EE0] bg-sky-50 dark:bg-[#009EE0]/10' : '' }}">
          {{ __('site.nav_services') }}
        </a>
        <a href="{{ route('home', $currentLocale) }}#solutions"
          class="px-2 py-1.5 rounded-md hover:bg-sky-50 dark:hover:bg-[#111820] hover:text-[#009EE0] dark:hover:text-[#009EE0]">
          {{ __('site.nav_solutions') }}
        </a>
        <a href="{{ route('products.index', $currentLocale) }}"
          class="px-2 py-1.5 rounded-md hover:bg-sky-50 dark:hover:bg-[#111820] hover:text-[#009EE0] dark:hover:text-[#009EE0] {{ request()->routeIs('products.*') ? 'text-[#009EE0] dark:text-[#009EE0] bg-sky-50 dark:bg-[#009EE0]/10' : '' }}">
          {{ __('site.nav_products') }}
        </a>
        <a href="{{ route('contact.create', $currentLocale) }}"
          class="px-2 py-1.5 rounded-md hover:bg-sky-50 dark:hover:bg-[#111820] hover:text-[#009EE0] dark:hover:text-[#009EE0] {{ request()->routeIs('contact.*') ? 'text-[#009EE0] dark:text-[#009EE0] bg-sky-50 dark:bg-[#009EE0]/10' : '' }}">
          {{ __('site.nav_contact') }}
        </a>
        <div class="pt-2 border-t border-[#E2E8F0] dark:border-[#26313C]">
          <a href="{{ route('contact.create', $currentLocale) }}"
            class="w-full text-center block bg-[#009EE0] text-white font-bold py-2.5 rounded-lg hover:bg-[#0088c2] shadow transition">
            {{ __('site.hero_cta_secondary') }}
          </a>
        </div>
      </nav>
    </div>
  </header>

  {{-- Main Page Content --}}
  <main class="flex-grow">
    @yield('content')
  </main>

  {{-- Footer --}}
  <footer class="bg-[#010101] text-gray-300 mt-24 border-t border-[#26313C]">
    <div class="max-w-7xl mx-auto px-6 py-14 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10">
      {{-- Brand Column --}}
      <div class="space-y-4">
        <a href="{{ route('home', $currentLocale) }}" class="inline-block">
          <img src="{{ asset('images/softel_logo_white.svg') }}" alt="SOFTEL" class="h-9 w-auto object-contain">
        </a>
        <p class="text-sm text-gray-400 leading-relaxed">
          {{ __('site.footer_company_desc') }}
        </p>
        <div
          class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-[#111820] border border-[#26313C] text-xs text-[#009EE0] font-medium">
          <span class="w-2 h-2 rounded-full bg-[#009EE0] animate-pulse"></span>
          <span>{{ __('site.about_stat_experience') }}</span>
        </div>
      </div>

      {{-- Quick Links --}}
      <div>
        <h3 class="text-white font-bold text-base mb-4 tracking-wide">{{ __('site.footer_quick_links') }}</h3>
        <ul class="space-y-2.5 text-sm text-gray-400">
          <li>
            <a href="{{ route('home', $currentLocale) }}" class="hover:text-[#009EE0] transition-colors">
              {{ __('site.nav_home') }}
            </a>
          </li>
          <li>
            <a href="{{ route('about', $currentLocale) }}" class="hover:text-[#009EE0] transition-colors">
              {{ __('site.nav_about') }}
            </a>
          </li>
          <li>
            <a href="{{ route('services.index', $currentLocale) }}" class="hover:text-[#009EE0] transition-colors">
              {{ __('site.nav_services') }}
            </a>
          </li>
          <li>
            <a href="{{ route('home', $currentLocale) }}#solutions" class="hover:text-[#009EE0] transition-colors">
              {{ __('site.nav_solutions') }}
            </a>
          </li>
          <li>
            <a href="{{ route('products.index', $currentLocale) }}" class="hover:text-[#009EE0] transition-colors">
              {{ __('site.nav_products') }}
            </a>
          </li>
          <li>
            <a href="{{ route('contact.create', $currentLocale) }}" class="hover:text-[#009EE0] transition-colors">
              {{ __('site.nav_contact') }}
            </a>
          </li>
        </ul>
      </div>

      {{-- Core Services / Solutions Links --}}
      <div>
        <h3 class="text-white font-bold text-base mb-4 tracking-wide">{{ __('site.footer_services') }}</h3>
        <ul class="space-y-2.5 text-sm text-gray-400">
          <li>
            <a href="{{ route('services.show', [$currentLocale, 'development']) }}"
              class="hover:text-[#009EE0] transition-colors">
              {{ __('site.about_pillar1_title') }}
            </a>
          </li>
          <li>
            <a href="{{ route('services.show', [$currentLocale, 'consulting-support']) }}"
              class="hover:text-[#009EE0] transition-colors">
              {{ __('site.about_pillar2_title') }}
            </a>
          </li>
          <li>
            <a href="{{ route('services.show', [$currentLocale, 'training']) }}"
              class="hover:text-[#009EE0] transition-colors">
              {{ __('site.about_pillar3_title') }}
            </a>
          </li>
          <li>
            <a href="{{ route('services.show', [$currentLocale, 'after-sales-support']) }}"
              class="hover:text-[#009EE0] transition-colors">
              {{ __('site.about_pillar4_title') }}
            </a>
          </li>
        </ul>
      </div>

      {{-- Contact Information --}}
      <div>
        <h3 class="text-white font-bold text-base mb-4 tracking-wide">{{ __('site.footer_contact_us') }}</h3>
        <ul class="space-y-3 text-sm text-gray-400">
          <li class="flex items-start gap-3">
            <svg class="w-5 h-5 text-[#009EE0] flex-shrink-0 mt-0.5" fill="none" stroke="currentColor"
              viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <span class="leading-relaxed">{{ __('site.contact_address_value') }}</span>
          </li>
          <li class="flex items-start gap-3">
            <svg class="w-5 h-5 text-[#009EE0] flex-shrink-0 mt-0.5" fill="none" stroke="currentColor"
              viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
            </svg>
            <div class="flex flex-col space-y-1" dir="ltr">
              <a href="tel:+213798757380" class="hover:text-[#009EE0] transition-colors">0798 75 73 80</a>
              <a href="tel:+213558925355" class="hover:text-[#009EE0] transition-colors">0558 92 53 55</a>
              <a href="tel:+21320242020" class="hover:text-[#009EE0] transition-colors">020 24 20 20</a>
            </div>
          </li>
          <li class="flex items-center gap-3">
            <svg class="w-5 h-5 text-[#009EE0] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
            <a href="mailto:info@softel.dz" class="hover:text-[#009EE0] transition-colors" dir="ltr">
              info@softel.dz
            </a>
          </li>
        </ul>
      </div>
    </div>

    {{-- Bottom Copyright Strip --}}
    <div class="border-t border-[#26313C] py-6 px-6">
      <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between text-xs text-gray-500 gap-4">
        <div>
          &copy; {{ date('Y') }} SOFTEL. {{ __('site.footer_rights') }}
        </div>
        <div class="flex items-center gap-4 text-gray-500">
          <span>{{ __('site.footer_subtitle') }}</span>
          <span>•</span>
          <a href="{{ route('contact.create', $currentLocale) }}"
            class="hover:text-[#009EE0] transition-colors">{{ __('site.footer_location') }}</a>
        </div>
      </div>
    </div>
  </footer>

  {{-- Mobile Menu Toggle Script --}}
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const menuBtn = document.getElementById('mobile-menu-button');
      const mobileMenu = document.getElementById('mobile-menu');
      const menuIcon = document.getElementById('menu-icon');
      const closeIcon = document.getElementById('close-icon');

      if (menuBtn && mobileMenu) {
        menuBtn.addEventListener('click', function () {
          const isHidden = mobileMenu.classList.contains('hidden');
          if (isHidden) {
            mobileMenu.classList.remove('hidden');
            menuIcon.classList.add('hidden');
            closeIcon.classList.remove('hidden');
          } else {
            mobileMenu.classList.add('hidden');
            menuIcon.classList.remove('hidden');
            closeIcon.classList.add('hidden');
          }
        });
      }
    });
  </script>
</body>

</html>
