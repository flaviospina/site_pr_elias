/* Interações do site — Pr. Elias José da Silva */
(function () {
  'use strict';

  // --- Menu mobile (público) ---
  var navToggle = document.querySelector('[data-nav-toggle]');
  var nav = document.querySelector('[data-nav]');
  if (navToggle && nav) {
    navToggle.addEventListener('click', function () {
      var open = nav.classList.toggle('open');
      navToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
  }

  // --- Menu lateral (admin) ---
  var adminToggle = document.querySelector('[data-admin-toggle]');
  var sidebar = document.querySelector('[data-admin-sidebar]');
  if (adminToggle && sidebar) {
    adminToggle.addEventListener('click', function () {
      sidebar.classList.toggle('open');
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
            if (status) status.textContent = '✓ Imagem enviada';
          } else {
            if (status) status.textContent = json.error || 'Falha no envio';
          }
        })
        .catch(function () { if (status) status.textContent = 'Falha no envio'; });
    });
  });

  function baseUploadUrl() {
    // Deriva a URL de /admin/upload a partir do caminho atual
    var path = window.location.pathname;
    var idx = path.indexOf('/admin');
    var prefix = idx > 0 ? path.substring(0, idx) : '';
    return prefix + '/admin/upload';
  }
})();
