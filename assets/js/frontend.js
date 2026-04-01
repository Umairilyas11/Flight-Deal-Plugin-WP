/* GoFly Flight Deals — Frontend JS */
(function () {
  'use strict';

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

        var expiryMs = new Date(expiry + 'T23:59:59').getTime();
        var nowMs    = Date.now();
        var diff     = expiryMs - nowMs;

        if (diff <= 0) {
          el.textContent = 'Expired';
          if (card) { card.classList.add('gfd-deal-card--expired'); }
          return;
        }

        var days    = Math.floor(diff / 86400000);
        var hours   = Math.floor((diff % 86400000) / 3600000);
        var minutes = Math.floor((diff % 3600000)  / 60000);

        if (days > 0) {
          el.textContent = days + 'd ' + hours + 'h';
        } else {
          el.textContent = hours + 'h ' + minutes + 'm';
        }
      });
    }

    tick();
    setInterval(tick, 60000);
  }

  /* ─────────────────────────────────────
     SEARCH BAR REDIRECT
  ───────────────────────────────────── */
  function initSearchBars() {
    var bars = document.querySelectorAll('.gfd-search-bar');
    bars.forEach(function (bar) {
      var btn = bar.querySelector('.gfd-search-bar__btn');
      if (!btn) return;

      btn.addEventListener('click', function () {
        var resultsUrl = bar.dataset.resultsUrl || window.location.href;
        var params     = new URLSearchParams();

        function addParam(name) {
          var el = bar.querySelector('[name="' + name + '"]');
          if (el && el.value.trim()) params.set(name, el.value.trim());
        }

        ['origin', 'destination', 'date', 'airline', 'class'].forEach(addParam);

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

    // Try immediately; Elementor/theme may load Swiper later.
    tryInit();
    setTimeout(tryInit, 500);
    document.addEventListener('DOMContentLoaded', tryInit);
  }

  /* ─────────────────────────────────────
     GA4 BOOK NOW EVENTS
  ───────────────────────────────────── */
  function initGA4Events() {
    document.addEventListener('click', function (e) {
      var target = e.target.closest('[data-gfd-ga-event="book_now"]');
      if (!target) return;
      if (typeof gtag === 'function') {
        gtag('event', 'book_now_click', {
          deal_id:     target.dataset.gfdDealId     || '',
          destination: target.dataset.gfdDestination || '',
        });
      }
    });
  }

  /* ─────────────────────────────────────
     BOOT
  ───────────────────────────────────── */
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
  } else {
    boot();
  }

  function boot() {
    initCountdowns();
    initSearchBars();
    initCarousels();
    initGA4Events();
  }

})();
