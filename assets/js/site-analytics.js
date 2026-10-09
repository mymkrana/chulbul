(function () {
    'use strict';
    var script = document.currentScript, robots = document.querySelector('meta[name="robots"]');
    if (!script || /\/admin(?:\/|$)/.test(location.pathname) || (robots && /noindex/i.test(robots.content))) return;
    if (navigator.webdriver || navigator.globalPrivacyControl || navigator.doNotTrack === '1') return;
    function storage(kind, key, value) {
        try { return value === undefined ? window[kind].getItem(key) : window[kind].setItem(key, value); } catch (ignore) { return null; }
    }
    var prefix = 'cbd_analytics_';
    if (storage('localStorage', prefix + 'optout') === '1' || !window.crypto || !crypto.getRandomValues || !window.fetch) return;
    function id() {
        var bytes = new Uint8Array(16); crypto.getRandomValues(bytes);
        return Array.from(bytes, function (b) { return b.toString(16).padStart(2, '0'); }).join('');
    }
    function savedId(kind, key, lifetime) {
        var state;
        try { state = JSON.parse(storage(kind, prefix + key) || 'null'); } catch (ignore) {}
        if (!state || !/^[a-f0-9]{32}$/.test(state.id) || Date.now() - (state.last || 0) > lifetime) state = {id: id(), last: Date.now()};
        storage(kind, prefix + key, JSON.stringify(state)); return state.id;
    }
    var endpoint = script.getAttribute('data-endpoint') || '/analytics-collect.php';
    var view = id(), token = id(), session = savedId('sessionStorage', 'session', 1800000);
    var visitor = savedId('localStorage', 'visitor', 90 * 86400000);
    var queue = [], ready = false, stopped = false, paused = false, busy = false, attempts = 0;
    var visible = document.visibilityState === 'visible', focused = document.hasFocus();
    var lastTick = performance.now(), lastActivity = lastTick, elapsed = 0, maxScroll = 0;
    var milestones = {}, formsStarted = new WeakSet(), timer = 0;
    function text(value, max) {
        return String(value || '').replace(/[\w.+-]+@[\w.-]+\.[a-z]{2,}/gi, '[email]')
            .replace(/\+?\d[\d ().-]{6,}\d/g, '[number]').replace(/\s+/g, ' ').trim().slice(0, max || 180);
    }
    function safeUrl(value) {
        try {
            var u = new URL(value, location.href);
            if (u.protocol === 'mailto:') return 'email';
            if (u.protocol === 'tel:') return 'telephone';
            if (/(^|\.)whatsapp\.com$/.test(u.hostname) || u.hostname === 'wa.me') return 'whatsapp';
            if (!/^https?:$/.test(u.protocol)) return '';
            return u.origin === location.origin ? u.pathname : u.origin;
        } catch (ignore) { return ''; }
    }
    function payload(action, extra) {
        return Object.assign({action: action, session_id: session, visitor_id: visitor, view_id: view,
            view_token: token, page_path: location.pathname, page_title: text(document.title, 255)}, extra || {});
    }
    function transport(data, beacon) {
        if (stopped) return Promise.resolve(null);
        var body = JSON.stringify(data);
        if (beacon && navigator.sendBeacon) {
            try { if (navigator.sendBeacon(endpoint, new Blob([body], {type: 'application/json'}))) return Promise.resolve({ok: true}); } catch (ignore) {}
        }
        return fetch(endpoint, {method: 'POST', credentials: 'same-origin', keepalive: true,
            headers: {'Content-Type': 'application/json'}, body: body})
            .then(function (r) { return r.ok ? r.json() : null; }).catch(function () { return null; });
    }
    function tick() {
        var now = performance.now();
        // Only focused, visible time; no more than 60 seconds since the last interaction.
        if (visible && focused && !paused) elapsed += Math.max(0, Math.min(now, lastActivity + 60000) - lastTick) / 1000;
        lastTick = now;
    }
    function activity() {
        if (stopped || paused) return;
        tick(); lastActivity = performance.now();
        storage('sessionStorage', prefix + 'session', JSON.stringify({id: session, last: Date.now()}));
    }
    function add(event, urgent) {
        if (stopped || paused || queue.length >= 50) return;
        event.event_key = id(); queue.push(event);
        if (urgent) { flush(true); return; }
        if (!timer) timer = setTimeout(function () { timer = 0; flush(false); }, 1200);
    }
    function flush(beacon) {
        if (stopped || !ready || !queue.length || busy) return;
        var batch = queue.splice(0, 20); busy = true;
        transport(payload('events', {events: batch}), beacon).then(function (r) {
            busy = false;
            if (!r && !stopped) queue = batch.concat(queue).slice(0, 50);
            if (r && r.ignored) stop(false);
            if (queue.length && !stopped && !paused && !timer) timer = setTimeout(function () { timer = 0; flush(false); }, 1500);
        });
    }
    function scroll() {
        var range = document.documentElement.scrollHeight - innerHeight;
        maxScroll = Math.max(maxScroll, range <= 0 ? 100 : Math.min(100, Math.round(scrollY / range * 100)));
        [25, 50, 75, 90, 100].forEach(function (n) {
            if (maxScroll >= n && !milestones[n]) { milestones[n] = true; add({event_type: 'scroll', event_name: n + '% scroll'}); }
        });
    }
    function state() { tick(); return {engaged_seconds: Math.floor(elapsed), max_scroll: maxScroll}; }
    function utm(name) { try { return text(new URLSearchParams(location.search).get(name), 120); } catch (ignore) { return ''; } }
    function start(beacon) {
        if (stopped) return;
        // One start envelope on early exit prevents events arriving before their page view.
        var batch = beacon ? queue.splice(0, 20) : [];
        transport(payload('start', Object.assign(state(), {referrer: document.referrer ? safeUrl(document.referrer) : '',
            utm_source: utm('utm_source'), utm_medium: utm('utm_medium'), utm_campaign: utm('utm_campaign'),
            viewport_width: innerWidth, viewport_height: innerHeight, events: batch})), beacon).then(function (r) {
            if (r && r.ignored) { stop(false); return; }
            if (r && r.ok) { ready = true; attachForms(); flush(false); }
            else if (!beacon && !stopped && ++attempts < 3) setTimeout(function () { start(false); }, 2500 * attempts);
        });
    }
    function formAllowed(form) {
        if (!form || form.closest('[data-analytics-private]')) return false;
        return /\/(lead-submit\.php|contact-us(?:\.php)?)$/.test(safeUrl(form.action || location.href));
    }
    function attach(form) {
        if (!ready || stopped || !formAllowed(form)) return;
        [['_cbd_view', view], ['_cbd_token', token]].forEach(function (pair) {
            var input = form.querySelector('input[name="' + pair[0] + '"]');
            if (!input) { input = document.createElement('input'); input.type = 'hidden'; input.name = pair[0]; form.appendChild(input); }
            input.value = pair[1];
        });
    }
    function attachForms() { Array.from(document.forms).forEach(attach); }
    function formLabel(form) { return text(form.getAttribute('data-analytics-label') || form.id || 'Enquiry form'); }
    document.addEventListener('focusin', function (e) {
        activity(); var form = e.target.form;
        if (formAllowed(form) && !formsStarted.has(form)) {
            attach(form); formsStarted.add(form);
            add({event_type: 'form_start', event_name: 'Form started', element_text: formLabel(form), target_path: safeUrl(form.action)});
        }
    }, true);
    document.addEventListener('submit', function (e) {
        if (!formAllowed(e.target)) return;
        attach(e.target);
        add({event_type: 'form_submit', event_name: 'Submit attempt', element_tag: 'form', element_text: formLabel(e.target), target_path: safeUrl(e.target.action)}, true);
    }, true);
    document.addEventListener('formdata', function (e) {
        // Includes AJAX and dynamically inserted enquiry forms.
        if (ready && !stopped && formAllowed(e.target)) { e.formData.set('_cbd_view', view); e.formData.set('_cbd_token', token); }
    }, true);
    document.addEventListener('click', function (e) {
        activity(); var source = e.target && e.target.closest ? e.target : null;
        if (!source || source.closest('input:not([type="submit"]):not([type="button"]),textarea,select,[contenteditable],[data-analytics-private]')) return;
        var el = source.closest('a,button,[role="button"],summary,input[type="submit"],input[type="button"],[data-analytics-label]');
        if (!el || el.querySelector('input:not([type="submit"]):not([type="button"]),textarea,select,[contenteditable]')) return;
        var link = el.closest('a[href]'), target = link ? safeUrl(link.href) : '';
        var names = {whatsapp: 'WhatsApp', telephone: 'Phone call', email: 'Email'};
        var kind = names[target] ? 'conversion' : (link ? (target.charAt(0) === '/' ? 'link_click' : 'outbound_click') : 'button_click');
        add({event_type: kind, event_name: names[target] || 'Click', element_tag: el.tagName.toLowerCase(),
            element_text: text(el.getAttribute('data-analytics-label') || el.getAttribute('aria-label') || el.getAttribute('title') || el.textContent || el.tagName),
            target_path: target, click_x: e.detail ? Math.round(e.clientX / Math.max(1, innerWidth) * 10000) / 100 : null,
            click_y: e.detail ? Math.round((scrollY + e.clientY) / Math.max(1, document.documentElement.scrollHeight) * 10000) / 100 : null}, !!link);
    }, true);
    function leave() {
        if (stopped || paused) return;
        if (!ready) start(true); else { transport(payload('update', state()), true); flush(true); }
    }
    function stop(persist) {
        stopped = true; queue = []; clearTimeout(timer); clearInterval(heartbeat);
        document.querySelectorAll('input[name="_cbd_view"],input[name="_cbd_token"]').forEach(function (el) { el.remove(); });
        if (persist) storage('localStorage', prefix + 'optout', '1');
    }
    window.CBDAnalytics = {track: function (name, options) {
        options = options || {};
        add({event_type: 'custom', event_name: text(name, 100), element_text: text(options.label), target_path: options.target ? safeUrl(options.target) : ''}, !!options.immediate);
    }, optOut: function () { stop(true); }};
    document.addEventListener('visibilitychange', function () {
        if (document.visibilityState === 'hidden') leave(); else { lastTick = performance.now(); lastActivity = lastTick; }
        visible = document.visibilityState === 'visible'; focused = document.hasFocus();
    });
    window.addEventListener('blur', function () { leave(); focused = false; });
    window.addEventListener('focus', function () { focused = true; lastTick = performance.now(); lastActivity = lastTick; });
    window.addEventListener('pagehide', function () { leave(); paused = true; });
    window.addEventListener('pageshow', function (e) { if (e.persisted) { paused = false; visible = document.visibilityState === 'visible'; focused = document.hasFocus(); lastTick = performance.now(); lastActivity = lastTick; } });
    ['pointerdown', 'keydown', 'touchstart'].forEach(function (name) { document.addEventListener(name, activity, {passive: true}); });
    window.addEventListener('scroll', function () { activity(); scroll(); }, {passive: true});
    var heartbeat = setInterval(function () {
        if (stopped || paused) return;
        tick();
        if (ready && visible && focused && performance.now() - lastActivity < 60000) { transport(payload('update', state()), false); flush(false); }
    }, 15000);
    scroll(); start(false);
})();
