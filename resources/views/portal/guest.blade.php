@extends('layouts.app')

@section('title', $__env->yieldContent('heading'))

@section('content')
  <section class="py-12 lg:py-16">
    <div class="max-w-md mx-auto px-4 sm:px-6">
      <h1 class="text-2xl sm:text-3xl font-extrabold text-[#111111] dark:text-[#F8FAFC]">@yield('heading')</h1>
      @hasSection('intro')
        <p class="mt-2 text-sm text-[#475569] dark:text-[#94A3B8]">@yield('intro')</p>
      @endif
      <div class="mt-6">@include('portal.partials.flash')</div>
      <div class="mt-6 rounded-2xl border border-[#E2E8F0] dark:border-[#26313C] bg-white dark:bg-[#111820] p-5 sm:p-6 shadow-sm">
        @yield('card')
      </div>
      <div class="mt-5 text-sm text-center text-[#475569] dark:text-[#94A3B8]">@yield('footer')</div>
    </div>
  </section>
  @include('portal.partials.once-script')
@endsection
