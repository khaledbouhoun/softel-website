<div class="space-y-3">
  @if (session('success'))
    <x-alert type="success">{{ session('success') }}</x-alert>
  @endif
  @if (session('portal_error'))
    <x-alert type="error">{{ session('portal_error') }}</x-alert>
  @endif
</div>
