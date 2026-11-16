@extends('portal.layout')
@section('heading', __('site.portal_nav_appointments'))
@section('portal')
  <div class="grid gap-4 md:grid-cols-2">
    @forelse ($appointments as $appt)
      @include('portal.partials.appointment-card', ['appt' => $appt])
    @empty
      <x-empty-state class="md:col-span-2" :title="__('site.portal_no_appointments')" :text="__('site.portal_no_appointments_text')" />
    @endforelse
  </div>
@endsection
