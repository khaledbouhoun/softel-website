<script>
    // Prevent double submits: disable submit buttons once a form is sent.
    document.addEventListener('submit', function (event) {
      var form = event.target;
      if (form.dataset.submitted === '1') { event.preventDefault(); return; }
      form.dataset.submitted = '1';
      form.querySelectorAll('button[type="submit"], button:not([type])').forEach(function (b) { b.disabled = true; b.setAttribute('aria-busy', 'true'); });
    });
    window.addEventListener('pageshow', function (e) { if (e.persisted) { document.querySelectorAll('form').forEach(function (f) { f.dataset.submitted = ''; f.querySelectorAll('button').forEach(function (b) { b.disabled = false; }); }); } });
  </script>
