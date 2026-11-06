(function () {
  'use strict';
  var $ = function (s, c) { return (c || document).querySelector(s); };
  var $$ = function (s, c) { return Array.prototype.slice.call((c || document).querySelectorAll(s)); };

  // Drawer sidebar (dashboard) & menu publik
  $$('[data-toggle="drawer"]').forEach(function (b) { b.addEventListener('click', function () { var a = $('.app'); if (a) a.classList.toggle('open'); }); });
  var scrim = $('.scrim'); if (scrim) scrim.addEventListener('click', function () { $('.app').classList.remove('open'); });
  $$('[data-toggle="nav"]').forEach(function (b) { b.addEventListener('click', function () { var t = $('.topbar'); var o = t.classList.toggle('open'); b.setAttribute('aria-expanded', o); }); });
  document.addEventListener('keydown', function (e) { if (e.key === 'Escape') { var a = $('.app'); if (a) a.classList.remove('open'); } });

  // Tabel -> kartu di ponsel (label diambil dari header kolom)
  $$('table').forEach(function (t) {
    var heads = $$('thead th', t).map(function (h) { return h.textContent.trim(); });
    if (!heads.length) return;
    t.classList.add('stack');
    $$('tbody tr', t).forEach(function (tr) {
      if ($$('td[colspan]', tr).length) return;
      $$('td', tr).forEach(function (td, i) { td.setAttribute('data-label', heads[i] || ''); });
    });
  });

  // Tampilkan/sembunyikan kata sandi
  $$('[data-reveal]').forEach(function (b) {
    b.addEventListener('click', function () {
      var i = b.parentNode.querySelector('input'); var show = i.type === 'password';
      i.type = show ? 'text' : 'password'; b.setAttribute('aria-label', show ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
    });
  });

  // Pratinjau foto di bingkai 4:3 / 8:3 yang sama dengan hasil akhir
  $$('input[type=file][data-preview]').forEach(function (inp) {
    inp.addEventListener('change', function () {
      var f = inp.files[0], box = document.getElementById(inp.dataset.preview); if (!box) return;
      if (!f) return;
      if (f.size > 5 * 1024 * 1024) { alert('Ukuran foto maksimal 5 MB.'); inp.value = ''; return; }
      var img = box.querySelector('img') || box.appendChild(document.createElement('img'));
      img.src = URL.createObjectURL(f); img.alt = 'Pratinjau foto'; var ph = box.querySelector('span'); if (ph) ph.hidden = true;
    });
  });

  // Search suggest (merek mobil, mobil, suku cadang)
  $$('[data-suggest]').forEach(function (inp) {
    var wrap = inp.parentNode; wrap.classList.add('sg');
    var box = document.createElement('div'); box.className = 'sg-box'; box.hidden = true; box.setAttribute('role', 'listbox');
    box.id = 'sg-' + Math.random().toString(36).slice(2, 8); wrap.appendChild(box);
    inp.setAttribute('role', 'combobox'); inp.setAttribute('aria-autocomplete', 'list'); inp.setAttribute('aria-controls', box.id); inp.setAttribute('aria-expanded', 'false'); inp.autocomplete = 'off';
    var target = inp.dataset.target ? $(inp.dataset.target) : null, items = [], idx = -1, timer, ctl;

    function close() { box.hidden = true; inp.setAttribute('aria-expanded', 'false'); idx = -1; }
    function mark() { $$('.sg-i', box).forEach(function (el, i) { el.setAttribute('aria-selected', i === idx); if (i === idx) el.scrollIntoView({ block: 'nearest' }); }); }
    function pick(it) {
      if (it.url) { location.href = it.url; return; }
      inp.value = it.value || it.label; if (target) { target.value = it.id || ''; inp.setCustomValidity(''); }
      close(); if (inp.dataset.submit !== undefined && inp.form) inp.form.submit();
    }
    function render() {
      box.innerHTML = '';
      if (!items.length) { var e = document.createElement('div'); e.className = 'sg-empty'; e.textContent = 'Tidak ada saran'; box.appendChild(e); }
      var last = '';
      items.forEach(function (it, i) {
        if (it.group && it.group !== last) { var g = document.createElement('div'); g.className = 'sg-g'; g.textContent = it.group; box.appendChild(g); last = it.group; }
        var b = document.createElement('button'); b.type = 'button'; b.className = 'sg-i'; b.setAttribute('role', 'option');
        b.appendChild(document.createTextNode(it.label)); if (it.sub) { var s = document.createElement('small'); s.textContent = it.sub; b.appendChild(s); }
        b.addEventListener('mousedown', function (ev) { ev.preventDefault(); pick(it); });
        box.appendChild(b);
      });
      box.hidden = false; inp.setAttribute('aria-expanded', 'true'); idx = -1;
    }
    function fetchIt() {
      var q = inp.value.trim(); if (!q) { close(); return; }
      var u = new URL(inp.dataset.suggest, location.origin); u.searchParams.set('q', q);
      if (ctl) ctl.abort(); ctl = window.AbortController ? new AbortController() : null;
      fetch(u, { headers: { 'Accept': 'application/json' }, signal: ctl ? ctl.signal : undefined })
        .then(function (r) { return r.ok ? r.json() : []; }).then(function (d) { items = d; render(); }).catch(function () {});
    }
    inp.addEventListener('input', function () {
      if (target) { target.value = ''; inp.setCustomValidity('Pilih dari daftar saran.'); }
      clearTimeout(timer); timer = setTimeout(fetchIt, 180);
    });
    inp.addEventListener('focus', function () { if (items.length && inp.value) box.hidden = false; });
    inp.addEventListener('blur', function () { setTimeout(close, 120); });
    inp.addEventListener('keydown', function (e) {
      var n = $$('.sg-i', box).length; if (box.hidden || !n) return;
      if (e.key === 'ArrowDown') { e.preventDefault(); idx = (idx + 1) % n; mark(); }
      else if (e.key === 'ArrowUp') { e.preventDefault(); idx = (idx - 1 + n) % n; mark(); }
      else if (e.key === 'Enter' && idx >= 0) { e.preventDefault(); pick(items[$$('.sg-i', box).indexOf($$('.sg-i', box)[idx])]); }
      else if (e.key === 'Escape') close();
    });
  });
})();
