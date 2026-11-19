<dialog id="preview-modal" class="m-auto w-[min(56rem,94vw)] rounded-2xl border border-[#E2E8F0] dark:border-[#26313C] bg-white dark:bg-[#111820] p-0 text-[#111111] dark:text-[#F8FAFC] backdrop:bg-black/60" aria-labelledby="preview-title">
  <div class="flex items-center justify-between gap-3 border-b border-[#E2E8F0] dark:border-[#26313C] px-4 py-3">
    <h2 id="preview-title" class="truncate text-sm font-extrabold" dir="auto"></h2>
    <button type="button" data-preview-close class="rounded-lg border border-[#CBD5E1] dark:border-[#26313C] px-3 py-1.5 text-xs font-bold hover:border-[#009EE0] focus:outline-none focus:ring-2 focus:ring-[#009EE0]">{{ __('site.portal_close') }}</button>
  </div>
  <div id="preview-body" class="max-h-[78vh] overflow-auto p-3 text-center"></div>
</dialog>
<script>
  (function () {
    var modal = document.getElementById('preview-modal');
    if (!modal || typeof modal.showModal !== 'function') { return; }
    var body = document.getElementById('preview-body'), title = document.getElementById('preview-title');
    function close() { modal.close(); }
    modal.addEventListener('close', function () { body.innerHTML = ''; });
    modal.addEventListener('click', function (e) { if (e.target === modal) { close(); } });
    modal.querySelector('[data-preview-close]').addEventListener('click', close);
    document.addEventListener('click', function (e) {
      var btn = e.target.closest('[data-preview]');
      if (!btn) { return; }
      var el;
      if (btn.dataset.kind === 'pdf') {
        el = document.createElement('iframe');
        el.className = 'h-[74vh] w-full rounded-lg border-0';
        el.setAttribute('sandbox', '');
      } else {
        el = document.createElement('img');
        el.className = 'mx-auto max-h-[74vh] max-w-full rounded-lg';
        el.alt = btn.dataset.name || '';
      }
      el.src = btn.dataset.preview;
      body.innerHTML = '';
      body.appendChild(el);
      title.textContent = btn.dataset.name || '';
      modal.showModal();
    });
  })();
</script>
