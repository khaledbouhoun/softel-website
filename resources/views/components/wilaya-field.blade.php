@props(['name' => 'wilaya', 'label', 'wilayas' => [], 'value' => null, 'required' => false])
{{-- Searchable list of the 58 wilayas: type a code or a name (16, Alger, الجزائر) and pick; the value sent is "16 - Alger". --}}
<div data-wilaya-field>
  <x-form-field :name="$name" :label="$label" :value="$value" :required="$required" :hint="__('site.portal_wilaya_hint')"
    list="wilayas-list-{{ $name }}" autocomplete="off" autocapitalize="off" spellcheck="false" :data-invalid="__('site.portal_wilaya_invalid')" />
  <datalist id="wilayas-list-{{ $name }}">
    @foreach ($wilayas as $w)
      <option value="{{ $w['label'] }}">{{ $w['code'] }} - {{ $w['name'] }} / {{ $w['name_ar'] }}</option>
    @endforeach
  </datalist>
  <script type="application/json" data-wilayas>@json($wilayas)</script>
</div>
@once
  <script>
    (function () {
      function key(s) { return (s || '').toString().normalize('NFD').replace(/[̀-ͯ]/g, '').toLowerCase().replace(/[^a-z0-9؀-ۿ]+/g, ''); }
      document.querySelectorAll('[data-wilaya-field]').forEach(function (box) {
        var input = box.querySelector('input'), data = box.querySelector('[data-wilayas]');
        var list = []; try { list = JSON.parse(data.textContent) || []; } catch (e) {}
        if (!input || !list.length) { return; }
        input.addEventListener('change', function () {
          var raw = input.value.trim(), k = key(raw), num = raw.match(/^0*(\d{1,2})$/), hit = null;
          list.forEach(function (w) {
            if ((num && parseInt(num[1], 10) === parseInt(w.code, 10)) || key(w.label) === k || key(w.name) === k || key(w.name_ar) === k) { hit = w; }
          });
          if (hit) { input.value = hit.label; input.setCustomValidity(''); }
          else if (raw !== '') { input.setCustomValidity(input.dataset.invalid || 'Wilaya'); }
          else { input.setCustomValidity(''); }
        });
      });
    })();
  </script>
@endonce
