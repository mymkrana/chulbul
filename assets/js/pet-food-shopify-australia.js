/**
 * Chulbul Design — Pet Food Shopify Development Australia (Standalone JS)
 * 100% Isolated Script — Preserves existing lead-submit.php integration,
 * adds accessible mobile nav, keyboard tabs, form validation, and GA4 events.
 */
(function () {
    'use strict';

    var BASE = document.documentElement.getAttribute('data-base-path') || '';

    // Safe GA4 event helper (preserves existing G-Z45EZTKR2J property on live)
    function trackEvent(eventName, params) {
        if (typeof window.gtag === 'function') {
            window.gtag('event', eventName, params || {});
        }
    }

    // 1. Capture Campaign UTMs & Click IDs for Lead Attribution
    var params = new URLSearchParams(window.location.search);
    var utmKeys = ['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content', 'gclid', 'fbclid'];
    var utmSummary = utmKeys
        .filter(function (k) { return params.get(k); })
        .map(function (k) { return k + '=' + params.get(k); })
        .join(' | ');

    // 2. Accessible Mobile Navigation Drawer
    var menuToggle = document.getElementById('pfa-menu-toggle');
    var mobileNav = document.getElementById('pfa-mobile-nav');
    if (menuToggle && mobileNav) {
        menuToggle.addEventListener('click', function () {
            var expanded = menuToggle.getAttribute('aria-expanded') === 'true';
            menuToggle.setAttribute('aria-expanded', expanded ? 'false' : 'true');
            mobileNav.hidden = expanded;
        });
        mobileNav.querySelectorAll('a').forEach(function (link) {
            link.addEventListener('click', function () {
                menuToggle.setAttribute('aria-expanded', 'false');
                mobileNav.hidden = true;
            });
        });
    }

    // 3. CTA, Pricing & WhatsApp Event Tracking + Pricing Package Pre-selection
    document.querySelectorAll('[data-track-cta]').forEach(function (el) {
        el.addEventListener('click', function () {
            trackEvent('cta_clicked', {
                cta_location: el.getAttribute('data-track-cta') || 'unknown',
                cta_text: (el.textContent || '').trim()
            });
        });
    });

    document.querySelectorAll('[data-track-whatsapp]').forEach(function (el) {
        el.addEventListener('click', function () {
            trackEvent('whatsapp_clicked', {
                cta_location: el.getAttribute('data-track-whatsapp') || 'unknown'
            });
        });
    });

    document.querySelectorAll('[data-track-pricing]').forEach(function (el) {
        el.addEventListener('click', function () {
            var pkg = el.getAttribute('data-track-pricing') || 'unknown';
            trackEvent('pricing_cta_clicked', { package_name: pkg });

            var svcVal = el.getAttribute('data-select-service');
            var svcSelect = document.getElementById('pfa-service-select');
            if (svcVal && svcSelect) {
                svcSelect.value = svcVal;
            }
        });
    });

    // 4. Hero Quick Store URL Pre-fill -> Scrolls smoothly to Audit Form
    var quickForm = document.getElementById('pfa-quick-form');
    var quickUrlEl = document.getElementById('pfa-quick-url');
    if (quickForm && quickUrlEl) {
        quickForm.addEventListener('submit', function (e) {
            e.preventDefault();
            var url = quickUrlEl.value.trim();
            var storeUrlInput = document.getElementById('pfa-store-url-input');
            if (storeUrlInput && url) {
                storeUrlInput.value = url;
            }
            var target = document.getElementById('audit-form');
            if (target) {
                target.scrollIntoView({ behavior: 'smooth' });
            }
            setTimeout(function () {
                var nameEl = document.getElementById('pfa-name-input');
                if (nameEl) nameEl.focus();
            }, 400);
        });
    }

    // 5. Accessible Interactive Demo Tabs (Click + ArrowLeft/ArrowRight/Home/End)
    var demoTabs = Array.prototype.slice.call(document.querySelectorAll('[data-pfa-tab]'));
    var demoPanels = Array.prototype.slice.call(document.querySelectorAll('[data-pfa-panel]'));

    function activateDemoTab(tabBtn, shouldFocus) {
        var targetKey = tabBtn.getAttribute('data-pfa-tab');
        demoTabs.forEach(function (btn) {
            var active = btn === tabBtn;
            btn.classList.toggle('is-active', active);
            btn.setAttribute('aria-selected', active ? 'true' : 'false');
            btn.setAttribute('tabindex', active ? '0' : '-1');
        });
        demoPanels.forEach(function (panel) {
            var active = panel.getAttribute('data-pfa-panel') === targetKey;
            panel.classList.toggle('is-active', active);
            panel.hidden = !active;
        });
        if (shouldFocus) tabBtn.focus();
    }

    demoTabs.forEach(function (btn, idx) {
        btn.addEventListener('click', function () {
            activateDemoTab(btn, false);
        });
        btn.addEventListener('keydown', function (e) {
            var nextIdx = idx;
            if (e.key === 'ArrowRight') nextIdx = (idx + 1) % demoTabs.length;
            else if (e.key === 'ArrowLeft') nextIdx = (idx - 1 + demoTabs.length) % demoTabs.length;
            else if (e.key === 'Home') nextIdx = 0;
            else if (e.key === 'End') nextIdx = demoTabs.length - 1;
            else return;

            e.preventDefault();
            activateDemoTab(demoTabs[nextIdx], true);
        });
    });

    // 6. Sample Feeding & Subscription Estimator Demo (Illustrative Only)
    var dogWeightEl = document.getElementById('dog-weight-slider');
    var currentDiet = 'fresh';
    var dietSpecs = {
        fresh:    { factor: 28.0, boxKg: 8,  price: 132, label: '8 kg Meal Box' },
        kibble:   { factor: 15.8, boxKg: 10, price: 119, label: '10 kg Dry Bag' },
        airdried: { factor: 10.5, boxKg: 4,  price: 135, label: '4 kg Air-Dried Pack' }
    };

    function updateDogDemo() {
        if (!dogWeightEl) return;
        var kg = Number(dogWeightEl.value);
        var size = kg <= 8 ? 'Small' : (kg <= 25 ? 'Medium' : (kg <= 40 ? 'Large' : 'Giant'));
        var spec = dietSpecs[currentDiet] || dietSpecs.fresh;
        var grams = Math.round(kg * spec.factor);
        var days = Math.max(7, Math.round((spec.boxKg * 1000) / grams));
        var cadence = days <= 16 ? 'Every 2 weeks' : (days <= 32 ? 'Every 4 weeks' : 'Every 6 weeks');

        var weightVal = document.getElementById('dog-weight-val');
        var dailyEl = document.getElementById('dog-daily-grams');
        var packEl = document.getElementById('dog-pack-size');
        var oneTimeEl = document.getElementById('dog-onetime-price');
        var subEl = document.getElementById('dog-sub-price');

        if (weightVal) weightVal.textContent = kg + ' kg · ' + size;
        if (dailyEl) dailyEl.textContent = grams + ' g / day';
        if (packEl) packEl.textContent = spec.label + ' (~' + days + ' days)';
        if (oneTimeEl) oneTimeEl.textContent = 'A$' + spec.price.toFixed(2);
        if (subEl) subEl.textContent = cadence;
    }

    if (dogWeightEl) dogWeightEl.addEventListener('input', updateDogDemo);
    document.querySelectorAll('#dog-diet-chips [data-diet]').forEach(function (chip) {
        chip.addEventListener('click', function () {
            currentDiet = chip.getAttribute('data-diet');
            document.querySelectorAll('#dog-diet-chips [data-diet]').forEach(function (c) {
                var active = c === chip;
                c.classList.toggle('is-active', active);
                c.setAttribute('aria-pressed', active ? 'true' : 'false');
            });
            updateDogDemo();
        });
    });
    updateDogDemo();

    // 7. Sample Australian Postcode Delivery Demo (Illustrative Sample Data Only)
    var pcInput = document.getElementById('au-postcode-input');
    var pcResult = document.getElementById('au-postcode-result');

    function checkPostcode(raw) {
        if (!pcResult) return;
        var code = String(raw || '').replace(/\D/g, '').slice(0, 4);
        if (code.length !== 4) {
            pcResult.style.borderColor = '#e05a10';
            pcResult.innerHTML = '<strong>Enter a 4-digit Australian postcode</strong><span>Example sample postcodes: 3000 (Melbourne), 2000 (Sydney), 4000 (Brisbane), 6000 (Perth), or 4870 (Regional).</span>';
            return;
        }
        var n = Number(code);
        var zone = null;
        if (n >= 3000 && n <= 3207) zone = ['Melbourne Metro Zone (VIC ' + code + ')', 'Illustrative rule: Local metro delivery zone eligible for fresh/perishable items plus standard parcel options for dry food and treats.'];
        else if (n >= 2000 && n <= 2234) zone = ['Sydney Metro Zone (NSW ' + code + ')', 'Illustrative rule: Metro delivery zone configured for local fresh delivery windows and weight-tiered parcel shipping.'];
        else if (n >= 4000 && n <= 4179) zone = ['Brisbane Metro Zone (QLD ' + code + ')', 'Illustrative rule: Scheduled metro delivery days for chilled boxes plus standard courier tiers for dry food.'];
        else if (n >= 5000 && n <= 5159) zone = ['Adelaide Metro Zone (SA ' + code + ')', 'Illustrative rule: Metro zone configured for scheduled local delivery and standard parcel shipping.'];
        else if (n >= 6000 && n <= 6175) zone = ['Perth Metro Zone (WA ' + code + ')', 'Illustrative rule: Local WA metro delivery options paired with national parcel tiers for shelf-stable ranges.'];

        if (zone) {
            pcResult.style.borderColor = '#0e6b47';
            pcResult.innerHTML = '<strong>Sample Result: ' + zone[0] + '</strong><span>' + zone[1] + '</span>';
        } else {
            pcResult.style.borderColor = '#e05a10';
            pcResult.innerHTML = '<strong>Sample Result: Regional Postcode (' + code + ')</strong><span>Illustrative rule: Storefront highlights shelf-stable dry food, air-dried treats, and accessories for standard regional delivery while flagging metro-only chilled items clearly before checkout.</span>';
        }
    }

    var pcBtn = document.getElementById('au-postcode-btn');
    if (pcBtn && pcInput) {
        pcBtn.addEventListener('click', function () { checkPostcode(pcInput.value); });
        pcInput.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                checkPostcode(pcInput.value);
            }
        });
    }
    document.querySelectorAll('#au-postcode-chips [data-pc]').forEach(function (chip) {
        chip.addEventListener('click', function () {
            var code = chip.getAttribute('data-pc');
            if (pcInput) pcInput.value = code;
            document.querySelectorAll('#au-postcode-chips [data-pc]').forEach(function (c) {
                c.classList.toggle('is-active', c === chip);
            });
            checkPostcode(code);
        });
    });

    // 8. Illustrative AUD Shopify App & Support Cost Estimator
    var appsEl = document.getElementById('calc-apps');
    var replacePctEl = document.getElementById('calc-replace-pct');
    var retainerEl = document.getElementById('calc-retainer');
    var lastCalcSummary = '';

    function aud(n) {
        return 'A$' + Math.round(n).toLocaleString('en-AU');
    }

    function updateCalculator() {
        if (!appsEl || !replacePctEl || !retainerEl) return;
        var apps = Number(appsEl.value);
        var replacePct = Number(replacePctEl.value);
        var retainer = Number(retainerEl.value);

        var appsReducedMo = apps * (replacePct / 100);
        var retainerDiffMo = retainer > 999 ? (retainer - 999) : 0;
        var totalMo = appsReducedMo + retainerDiffMo;
        var totalYr = totalMo * 12;

        document.getElementById('calc-apps-val').textContent = aud(apps) + ' / mo';
        document.getElementById('calc-replace-val').textContent = replacePct + '%';
        document.getElementById('calc-retainer-val').textContent = aud(retainer) + ' / mo';
        document.getElementById('calc-apps-saved').textContent = aud(appsReducedMo);
        document.getElementById('calc-dev-saved').textContent = aud(retainerDiffMo);
        document.getElementById('calc-monthly-total').textContent = aud(totalMo) + ' / mo';
        document.getElementById('calc-annual-total').textContent = aud(totalYr) + ' / yr';

        lastCalcSummary = 'Illustrative Estimator Inputs: Current paid apps ' + aud(apps) + '/mo ('
            + replacePct + '% consolidation estimate), current monthly retainer ' + aud(retainer)
            + '/mo -> illustrative estimate ' + aud(totalYr) + '/yr.';
    }

    [appsEl, replacePctEl, retainerEl].forEach(function (el) {
        if (el) el.addEventListener('input', updateCalculator);
    });
    updateCalculator();

    var lockBtn = document.getElementById('pfa-lock-savings');
    if (lockBtn) {
        lockBtn.addEventListener('click', function () {
            var msg = document.getElementById('pfa-message-input');
            if (msg && lastCalcSummary) {
                msg.value = lastCalcSummary + '\nPlease review my Shopify store and share practical audit recommendations.';
            }
            var target = document.getElementById('audit-form');
            if (target) target.scrollIntoView({ behavior: 'smooth' });
        });
    }

    // 9. Accessible Audit Lead Form -> Preserves Existing /lead-submit.php Backend
    var mainForm = document.getElementById('pfa-main-form');
    var mainStatus = document.getElementById('pfa-main-status');
    var formStartedTracked = false;

    function isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(email);
    }

    function isValidStoreInput(raw) {
        if (!raw) return false;
        if (/^new(\s+store)?$/i.test(raw)) return true;
        var candidate = /^https?:\/\//i.test(raw) ? raw : 'https://' + raw;
        try {
            var u = new URL(candidate);
            return /\.[a-z]{2,}$/i.test(u.hostname);
        } catch (err) {
            return false;
        }
    }

    if (mainForm && mainStatus) {
        // Track Form Started once on first meaningful interaction
        mainForm.addEventListener('focusin', function () {
            if (!formStartedTracked) {
                formStartedTracked = true;
                trackEvent('form_started', { form_id: 'pfa-main-form' });
            }
        });

        // Clear invalid state when user edits field
        mainForm.querySelectorAll('input, select, textarea').forEach(function (field) {
            field.addEventListener('input', function () {
                field.classList.remove('is-invalid');
            });
        });

        mainForm.addEventListener('submit', function (e) {
            e.preventDefault();
            var btn = mainForm.querySelector('button[type="submit"]');
            var orig = btn.innerHTML;
            mainStatus.className = 'pfa-status';
            mainStatus.textContent = '';

            var nameInput = document.getElementById('pfa-name-input');
            var emailInput = document.getElementById('pfa-email-input');
            var storeUrlInput = document.getElementById('pfa-store-url-input');
            var phoneInput = document.getElementById('pfa-phone-input');

            [nameInput, emailInput, storeUrlInput, phoneInput].forEach(function (el) {
                if (el) el.classList.remove('is-invalid');
            });

            var fd = new FormData(mainForm);
            var name = String(fd.get('name') || '').trim();
            var email = String(fd.get('email') || '').trim();
            var storeUrl = String(fd.get('store_url') || '').trim();
            var businessName = String(fd.get('business') || '').trim();
            var phone = String(fd.get('phone') || '').trim();

            if (!name) {
                if (nameInput) { nameInput.classList.add('is-invalid'); nameInput.focus(); }
                mainStatus.className = 'pfa-status is-err';
                mainStatus.textContent = 'Please enter your name.';
                return;
            }
            if (!email || !isValidEmail(email)) {
                if (emailInput) { emailInput.classList.add('is-invalid'); emailInput.focus(); }
                mainStatus.className = 'pfa-status is-err';
                mainStatus.textContent = 'Please enter a valid business email address.';
                return;
            }
            if (!storeUrl || !isValidStoreInput(storeUrl)) {
                if (storeUrlInput) { storeUrlInput.classList.add('is-invalid'); storeUrlInput.focus(); }
                mainStatus.className = 'pfa-status is-err';
                mainStatus.textContent = 'Please enter your Shopify store URL (for example, yourpetbrand.com.au) or type "New Store".';
                return;
            }

            var combinedBusiness = businessName ? (businessName + ' (' + storeUrl + ')') : storeUrl;
            var msgParts = [];
            msgParts.push('Store URL: ' + storeUrl);
            var userMsg = String(fd.get('message') || '').trim();
            if (userMsg) msgParts.push(userMsg);
            if (utmSummary) msgParts.push('[Campaign Attribution] ' + utmSummary);

            var payload = new FormData();
            payload.append('_hp', String(fd.get('_hp') || ''));
            payload.append('form_version', 'city-service-v3');
            payload.append('name', name);
            payload.append('email', email);
            payload.append('phone', phone);
            payload.append('business', combinedBusiness);
            payload.append('service', String(fd.get('service') || 'Free Shopify Store Audit'));
            payload.append('city', 'Australia');
            payload.append('industry', 'Pet Food & Pet Products Shopify');
            payload.append('source', 'pet-food-shopify-australia');
            payload.append('page_url', window.location.href);
            payload.append('message', msgParts.join('\n'));

            trackEvent('form_submitted', {
                form_id: 'pfa-main-form',
                service_selected: String(fd.get('service') || 'Free Shopify Store Audit')
            });

            btn.disabled = true;
            btn.setAttribute('aria-busy', 'true');
            btn.textContent = 'Submitting audit request...';

            fetch(BASE + '/lead-submit.php', { method: 'POST', body: payload })
                .then(function (r) {
                    if (!r.ok) {
                        return r.json().catch(function () {
                            return { success: false, error: 'Submission failed (HTTP ' + r.status + '). Please check your details and try again.' };
                        });
                    }
                    return r.json();
                })
                .then(function (data) {
                    if (data && data.success === true) {
                        trackEvent('form_submission_successful', {
                            form_id: 'pfa-main-form',
                            service_selected: String(fd.get('service') || 'Free Shopify Store Audit')
                        });
                        mainStatus.className = 'pfa-status is-ok';
                        mainStatus.textContent = 'Thank you! Your Shopify store audit request has been received. Our team will review your details and follow up by email.';
                        mainForm.reset();
                    } else {
                        mainStatus.className = 'pfa-status is-err';
                        mainStatus.textContent = (data && data.error) ? data.error : 'We could not submit your request right now. Please try again or email info@chulbuldesign.com.';
                    }
                })
                .catch(function () {
                    mainStatus.className = 'pfa-status is-err';
                    mainStatus.textContent = 'Network error while submitting the form. Please try again or reach us directly on WhatsApp or info@chulbuldesign.com.';
                })
                .finally(function () {
                    btn.disabled = false;
                    btn.removeAttribute('aria-busy');
                    btn.innerHTML = orig;
                });
        });
    }
})();
