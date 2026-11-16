@extends('portal.layout')
@section('heading', __('site.portal_nav_trials'))
@section('portal')
  <div class="grid gap-4 md:grid-cols-2">
    @forelse ($trials as $trial)
      @include('portal.partials.trial-card', ['trial' => $trial])
    @empty
      <x-empty-state class="md:col-span-2" :title="__('site.portal_no_trials')" :text="__('site.portal_no_trials_text')" />
    @endforelse
  </div>
@endsection
