@extends('portal.layout')
@section('heading', __('site.portal_nav_requests'))
@section('portal')
  <div class="mb-5"><x-button :href="route('portal.request.create', $currentLocale)">{{ __('site.portal_nav_new_request') }}</x-button></div>
  <div class="grid gap-4 md:grid-cols-2">
    @forelse ($requests as $req)
      @include('portal.partials.request-card', ['req' => $req])
    @empty
      <x-empty-state class="md:col-span-2" :title="__('site.portal_no_requests')" :text="__('site.portal_no_requests_text')" />
    @endforelse
  </div>
@endsection
