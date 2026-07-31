/* Interações do site — Pr. Elias José da Silva */
(function () {
  'use strict';

  // --- Menu mobile (público) ---
  var navToggle = document.querySelector('[data-nav-toggle]');
  var nav = document.querySelector('[data-nav]');
  function setNav(open) {
    nav.classList.toggle('open', open);
    navToggle.classList.toggle('open', open);
    document.body.classList.toggle('nav-open', open);
    navToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
  }
  if (navToggle && nav) {
    navToggle.addEventListener('click', function (e) {
      e.stopPropagation();
      setNav(!nav.classList.contains('open'));
    });
    // Fecha ao tocar em um link do menu
    nav.querySelectorAll('a').forEach(function (a) {
      a.addEventListener('click', function () { setNav(false); });
    });
    // Fecha ao tocar fora do menu (no overlay escuro)
    document.addEventListener('click', function (e) {
      if (nav.classList.contains('open') && !nav.contains(e.target)) setNav(false);
    });
  }

  // --- Menu lateral (admin) ---
  var adminToggle = document.querySelector('[data-admin-toggle]');
  var sidebar = document.querySelector('[data-admin-sidebar]');
  if (adminToggle && sidebar) {
    adminToggle.addEventListener('click', function (e) {
      e.stopPropagation();
      sidebar.classList.toggle('open');
    });
    document.addEventListener('click', function (e) {
      if (sidebar.classList.contains('open') && !sidebar.contains(e.target) && !adminToggle.contains(e.target)) {
        sidebar.classList.remove('open');
      }
    });
  }

  // --- Banner de cookies (LGPD) ---
  var banner = document.querySelector('[data-cookie-banner]');
  var COOKIE_KEY = 'ejds_cookie_consent';
  function consentSaved() {
    try { return localStorage.getItem(COOKIE_KEY); } catch (e) { return '1'; }
  }
  function setConsent(v) {
    try { localStorage.setItem(COOKIE_KEY, v); } catch (e) {}
  }
  if (banner) {
    if (!consentSaved()) {
      banner.hidden = false;
    }
    var accept = banner.querySelector('[data-cookie-accept]');
    var reject = banner.querySelector('[data-cookie-reject]');
    if (accept) accept.addEventListener('click', function () { setConsent('all'); banner.hidden = true; });
    if (reject) reject.addEventListener('click', function () { setConsent('essential'); banner.hidden = true; });
  }
  // Reabrir preferências pelo rodapé
  var prefsLink = document.querySelector('[data-cookie-prefs]');
  if (prefsLink && banner) {
    prefsLink.addEventListener('click', function (e) {
      e.preventDefault();
      banner.hidden = false;
    });
  }

  // --- Upload de imagem no admin ---
  document.querySelectorAll('[data-upload-input]').forEach(function (input) {
    input.addEventListener('change', function () {
      var file = input.files && input.files[0];
      if (!file) return;
      var status = input.parentNode.querySelector('[data-upload-status]');
      var target = input.closest('form').querySelector('[data-upload-target]');
      if (status) status.textContent = 'Enviando…';

      var data = new FormData();
      data.append('file', file);
      data.append('_token', window.CSRF_TOKEN || '');

      fetch(baseUploadUrl(), { method: 'POST', body: data })
        .then(function (r) { return r.json(); })
        .then(function (json) {
          if (json.url && target) {
            target.value = json.url;
            target.dispatchEvent(new Event('input', { bubbles: true }));
            if (status) status.textContent = '✓ Imagem enviada';
          } else {
            if (status) status.textContent = json.error || 'Falha no envio';
          }
        })
        .catch(function () { if (status) status.textContent = 'Falha no envio'; });
    });
  });

  // --- Prévia ao vivo do banner (admin) ---
  var preview = document.querySelector('[data-bn-preview]');
  if (preview) {
    var form = preview.closest('form');
    var overlayEl = preview.querySelector('[data-bn-overlay]');
    var contentEl = preview.querySelector('[data-bn-content]');
    function val(name) { var el = form.querySelector('[data-bn="' + name + '"]'); return el ? el.value : ''; }
    function updatePreview() {
      var img = val('image');
      preview.style.backgroundImage = img ? "url('" + img.replace(/'/g, "%27") + "')" : 'none';
      preview.classList.toggle('has-image', !!img);
      var op = (parseInt(val('overlay'), 10) || 0) / 100;
      overlayEl.style.background = 'rgba(10,37,64,' + op + ')';
      var h = { small: '160px', medium: '240px', large: '340px' }[val('height')] || '240px';
      preview.style.minHeight = h;
      contentEl.style.textAlign = val('align') || 'center';
      contentEl.style.color = val('text_color') === 'dark' ? '#0A2540' : '#fff';
      [['title', 'title'], ['subtitle', 'subtitle'], ['button_text', 'button_text'], ['button2_text', 'button2_text']]
        .forEach(function (pair) {
          var view = preview.querySelector('[data-bn-view="' + pair[1] + '"]');
          if (!view) return;
          var v = val(pair[0]);
          view.textContent = v;
          view.hidden = !v;
        });
    }
    form.querySelectorAll('[data-bn]').forEach(function (el) {
      el.addEventListener('input', updatePreview);
      el.addEventListener('change', updatePreview);
    });
    updatePreview();
    // Atualiza a prévia quando uma imagem é enviada pelo upload
    var imgInput = form.querySelector('[data-upload-target]');
    if (imgInput) {
      var obs = new MutationObserver(updatePreview);
      obs.observe(imgInput, { attributes: true, attributeFilter: ['value'] });
      imgInput.addEventListener('input', updatePreview);
    }
  }

  function baseUploadUrl() {
    // Deriva a URL de /admin/upload a partir do caminho atual
    var path = window.location.pathname;
    var idx = path.indexOf('/admin');
    var prefix = idx > 0 ? path.substring(0, idx) : '';
    return prefix + '/admin/upload';
  }
})();
