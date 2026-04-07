/* GoFly Flight Deals — Frontend JS v1.0.3 */
(function () {
  'use strict';

  /* ─────────────────────────────────────
     INQUIRY POPUP
  ───────────────────────────────────── */
  function initPopup() {
    var popup   = document.getElementById('gfd-inquiry-popup');
    var overlay = document.getElementById('gfd-popup-overlay');
    var closeBtn= document.getElementById('gfd-popup-close');
    var subtitle= document.getElementById('gfd-popup-subtitle');

    if (!popup) return;

    function openPopup(btn) {
      var origin      = btn.dataset.origin      || '';
      var destination = btn.dataset.destination || '';
      var price       = btn.dataset.price       || '';
      var title       = btn.dataset.title       || '';

      // Update the subtitle in the popup header.
      if (subtitle) {
        subtitle.textContent = origin && destination
          ? origin + '  \u2708  ' + destination
          : title;
      }

      // Populate CF7 hidden fields.
      var form = popup.querySelector('.wpcf7-form');
      if (form) {
        setField(form, 'origin-city',      origin);
        setField(form, 'destination-city', destination);
        setField(form, 'deal-price',       price);
      }

      popup.removeAttribute('hidden');
      document.body.style.overflow = 'hidden';

      // Focus the first visible input for accessibility.
      var firstInput = popup.querySelector('input:not([type="hidden"]), textarea, select');
      if (firstInput) { setTimeout(function() { firstInput.focus(); }, 50); }
    }

    function closePopup() {
      popup.setAttribute('hidden', '');
      document.body.style.overflow = '';
    }

    function setField(form, name, value) {
      var el = form.querySelector('[name="' + name + '"]');
      if (el) { el.value = value; }
    }

    // Trigger buttons.
    document.addEventListener('click', function(e) {
      var trigger = e.target.closest('.gfd-inquiry-trigger');
      if (trigger) { openPopup(trigger); }
    });

    // Close on overlay click.
    if (overlay) { overlay.addEventListener('click', closePopup); }

    // Close on X button.
    if (closeBtn) { closeBtn.addEventListener('click', closePopup); }

    // Close on Escape key.
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape' && !popup.hidden) { closePopup(); }
    });

    // Reset CF7 form after successful submission so popup is reusable.
    document.addEventListener('wpcf7mailsent', function() {
      setTimeout(closePopup, 2500);
    }, false);
  }

  /* ─────────────────────────────────────
     COUNTDOWN TIMERS
  ───────────────────────────────────── */
  function initCountdowns() {
    var countdowns = document.querySelectorAll('.gfd-deal-card__countdown');
    if (!countdowns.length) return;

    function tick() {
      countdowns.forEach(function (el) {
        var card   = el.closest('[data-expiry]');
        var expiry = card ? card.dataset.expiry : null;
        if (!expiry) { el.textContent = ''; return; }
        var diff = new Date(expiry + 'T23:59:59').getTime() - Date.now();
        if (diff <= 0) {
          el.textContent = 'Expired';
          if (card) { card.classList.add('gfd-deal-card--expired'); }
          return;
        }
        var days    = Math.floor(diff / 86400000);
        var hours   = Math.floor((diff % 86400000) / 3600000);
        var minutes = Math.floor((diff % 3600000)  / 60000);
        el.textContent = days > 0 ? days + 'd ' + hours + 'h' : hours + 'h ' + minutes + 'm';
      });
    }
    tick();
    setInterval(tick, 60000);
  }

  /* ─────────────────────────────────────
     SEARCH BAR REDIRECT
  ───────────────────────────────────── */
  function initSearchBars() {
    document.querySelectorAll('.gfd-search-bar').forEach(function (bar) {
      var btn = bar.querySelector('.gfd-search-bar__btn');
      if (!btn) return;
      btn.addEventListener('click', function () {
        var resultsUrl = bar.dataset.resultsUrl || window.location.href;
        var params = new URLSearchParams();
        ['origin', 'destination', 'date', 'airline', 'class'].forEach(function(name) {
          var el = bar.querySelector('[name="' + name + '"]');
          if (el && el.value.trim()) params.set(name, el.value.trim());
        });
        var qs = params.toString();
        window.location.href = resultsUrl + (qs ? (resultsUrl.includes('?') ? '&' : '?') + qs : '');
      });
    });
  }

  /* ─────────────────────────────────────
     SWIPER CAROUSEL INIT
  ───────────────────────────────────── */
  function initCarousels() {
    var carousels = document.querySelectorAll('.gfd-deals-carousel');
    if (!carousels.length) return;
    function tryInit() {
      if (typeof Swiper === 'undefined') return;
      carousels.forEach(function (wrap) {
        var swiperEl = wrap.querySelector('.swiper');
        if (!swiperEl || swiperEl._gfdSwiper) return;
        var opts = {};
        try { opts = JSON.parse(wrap.dataset.swiperOpts || '{}'); } catch (e) {}
        swiperEl._gfdSwiper = new Swiper(swiperEl, opts);
      });
    }
    tryInit();
    setTimeout(tryInit, 500);
  }

  /* ─────────────────────────────────────
     GA4 EVENTS
  ───────────────────────────────────── */
  function initGA4Events() {
    document.addEventListener('click', function (e) {
      var target = e.target.closest('[data-gfd-ga-event]');
      if (!target) return;
      if (typeof gtag === 'function') {
        gtag('event', target.dataset.gfdGaEvent + '_click', {
          deal_id:     target.dataset.gfdDealId     || '',
          destination: target.dataset.gfdDestination || '',
        });
      }
    });
  }

  /* ─────────────────────────────────────
     BOOT
  ───────────────────────────────────── */
  function boot() {
    initPopup();
    initCountdowns();
    initSearchBars();
    initCarousels();
    initGA4Events();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
  } else {
    boot();
  }

})();
