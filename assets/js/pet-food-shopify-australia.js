/**
 * Chulbul Design — Australia Pet Food & Dog Goods Shopify (Standalone JS)
 * 100% Isolated Script — Zero Dependency on Sitewide JS
 */
(function () {
    'use strict';

    var BASE = document.documentElement.getAttribute('data-base-path') || '';

    // 1. Capture Google Ads / Meta Ads UTMs & Click IDs
    var params = new URLSearchParams(window.location.search);
    var utmKeys = ['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content', 'gclid', 'fbclid'];
    var utmSummary = utmKeys
        .filter(function (k) { return params.get(k); })
        .map(function (k) { return k + '=' + params.get(k); })
        .join(' | ');

    // 2. Hero Dual-Audience Switcher (Pet Food Brands vs Existing AU Agencies)
    var audienceCopy = {
        brand: {
            h1: 'Complete <span>Pet Food &amp; Pet Store</span> Shopify Solutions for Australia.',
            desc: 'Specialist Shopify Australia partner for pet store ecommerce: we design high-converting pet food & pet shop websites, handle ongoing monthly Shopify store management, and slash recurring app & agency costs by 40–60%.',
            placeholder: 'Enter yourpetstore.com.au for a free 5-min Shopify cost audit'
        },
        agency: {
            h1: 'White-Label <span>Shopify Design, Management &amp; Cost-Saving</span> Pod for AU Agencies.',
            desc: 'Running a Shopify agency Australia or performance marketing team? Plug in our NDA-protected Shopify developer pod to cut client app bills, handle monthly store management, and keep 65%+ agency margin.',
            placeholder: 'Enter youragency.com.au for our white-label AU rate card'
        }
    };

    var h1El = document.getElementById('pfa-hero-h1');
    var descEl = document.getElementById('pfa-hero-desc');
    var quickUrlEl = document.getElementById('pfa-quick-url');

    document.querySelectorAll('[data-pfa-audience]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var mode = btn.getAttribute('data-pfa-audience');
            var copy = audienceCopy[mode];
            if (!copy) return;

            document.querySelectorAll('[data-pfa-audience]').forEach(function (b) {
                var active = b === btn;
                b.classList.toggle('is-active', active);
                b.setAttribute('aria-selected', active ? 'true' : 'false');
            });

            if (h1El) h1El.innerHTML = copy.h1;
            if (descEl) descEl.textContent = copy.desc;
            if (quickUrlEl) quickUrlEl.placeholder = copy.placeholder;

            if (mode === 'agency') {
                var storesSlider = document.getElementById('calc-stores');
                if (storesSlider && Number(storesSlider.value) === 1) {
                    storesSlider.value = '4';
                    updateCalculator();
                }
                var svc = document.getElementById('pfa-service-select');
                if (svc) svc.value = 'AU Agency White-Label Shopify Partner';
            }
        });
    });

    // 3. Hero Quick URL Form -> Scrolls & Pre-fills Audit Form
    var quickForm = document.getElementById('pfa-quick-form');
    if (quickForm && quickUrlEl) {
        quickForm.addEventListener('submit', function (e) {
            e.preventDefault();
            var url = quickUrlEl.value.trim();
            var biz = document.getElementById('pfa-business-input');
            if (biz && url) biz.value = url;
            var target = document.getElementById('audit-form');
            if (target) target.scrollIntoView({ behavior: 'smooth' });
            setTimeout(function () {
                var nameEl = document.getElementById('pfa-name-input');
                if (nameEl) nameEl.focus();
            }, 450);
        });
    }

    // 4. Live Pet Storefront Browser Demo Tabs
    document.querySelectorAll('[data-pfa-tab]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var tab = btn.getAttribute('data-pfa-tab');
            document.querySelectorAll('[data-pfa-tab]').forEach(function (b) {
                var active = b === btn;
                b.classList.toggle('is-active', active);
                b.setAttribute('aria-selected', active ? 'true' : 'false');
            });
            document.querySelectorAll('[data-pfa-panel]').forEach(function (p) {
                p.classList.toggle('is-active', p.getAttribute('data-pfa-panel') === tab);
            });
        });
    });

    // 5. Interactive Dog Weight Feeding & Auto-Ship Demo
    var dogWeightEl = document.getElementById('dog-weight-slider');
    var currentDiet = 'fresh';
    var dietSpecs = {
        fresh:    { factor: 28.0, boxKg: 8,  price: 132, label: '16 × 500g Fresh Meal Box' },
        kibble:   { factor: 15.8, boxKg: 10, price: 119, label: '10 kg Grain-Free Bag' },
        airdried: { factor: 10.5, boxKg: 4,  price: 135, label: '4 kg Air-Dried Roo & Beef' }
    };

    function updateDogDemo() {
        if (!dogWeightEl) return;
        var kg = Number(dogWeightEl.value);
        var size = kg <= 8 ? 'Small Dog' : (kg <= 25 ? 'Medium Dog' : (kg <= 40 ? 'Large Dog' : 'Giant Breed'));
        var spec = dietSpecs[currentDiet];
        var grams = Math.round(kg * spec.factor);
        var days = Math.max(7, Math.round((spec.boxKg * 1000) / grams));
        var subPrice = (spec.price * 0.85).toFixed(2);
        var cadence = days <= 16 ? 'every 2 wks' : (days <= 32 ? 'every 4 wks' : 'every 6 wks');

        document.getElementById('dog-weight-val').textContent = kg + ' kg · ' + size;
        document.getElementById('dog-daily-grams').textContent = grams + ' g / day';
        document.getElementById('dog-pack-size').textContent = spec.label + ' (~' + days + 'd)';
        document.getElementById('dog-onetime-price').textContent = 'A$' + spec.price.toFixed(2);
        document.getElementById('dog-sub-price').textContent = 'A$' + subPrice + ' ' + cadence;
    }

    if (dogWeightEl) dogWeightEl.addEventListener('input', updateDogDemo);
    document.querySelectorAll('#dog-diet-chips [data-diet]').forEach(function (chip) {
        chip.addEventListener('click', function () {
            currentDiet = chip.getAttribute('data-diet');
            document.querySelectorAll('#dog-diet-chips [data-diet]').forEach(function (c) {
                c.classList.toggle('is-active', c === chip);
            });
            updateDogDemo();
        });
    });
    updateDogDemo();

    // 6. Interactive AU Postcode Cold-Chain & Heavy-Bag Shipping Demo
    var pcInput = document.getElementById('au-postcode-input');
    var pcResult = document.getElementById('au-postcode-result');

    function checkPostcode(raw) {
        if (!pcResult) return;
        var code = String(raw || '').replace(/\D/g, '').slice(0, 4);
        if (code.length !== 4) {
            pcResult.style.borderColor = '#f26b1d';
            pcResult.innerHTML = '<strong style="color:#d9550c;display:block;">Enter a valid 4-digit Australian postcode (e.g. 3000, 2000, 4000, 6000).</strong>';
            return;
        }
        var n = Number(code);
        var zone = null;
        if (n >= 3000 && n <= 3207) zone = ['Melbourne Metro (VIC ' + code + ')', 'Chilled fresh dog food van delivery (Next-day · Free over A$120) + AusPost Metro for air-dried treats & kibble.'];
        else if (n >= 2000 && n <= 2234) zone = ['Sydney Metro (NSW ' + code + ')', 'Insulated cold-chain fresh meal delivery (A$12.95) · 15kg Kibble via AusPost/Sendle Metro (A$9.95).'];
        else if (n >= 4000 && n <= 4179) zone = ['Brisbane Metro (QLD ' + code + ')', 'Chilled metro delivery Tue/Thu/Sat (A$14.95) · Heavy dry food via Shippit/AusPost (A$10.95).'];
        else if (n >= 5000 && n <= 5159) zone = ['Adelaide Metro (SA ' + code + ')', 'Chilled local courier (A$14.95) · Dry food & treats via AusPost eParcel (A$10.95).'];
        else if (n >= 6000 && n <= 6175) zone = ['Perth Metro (WA ' + code + ')', 'Local Perth chilled delivery (A$15.95) · Heavy bags via Sendle/AusPost WA (A$11.95).'];

        if (zone) {
            pcResult.style.borderColor = '#0e6b47';
            pcResult.innerHTML = '<strong style="color:#094f34;display:block;margin-bottom:4px;">✓ ' + zone[0] + ' — Chilled Fresh Delivery Ready</strong><span style="color:#52625a;">' + zone[1] + '</span>';
        } else {
            pcResult.style.borderColor = '#f26b1d';
            pcResult.innerHTML = '<strong style="color:#d9550c;display:block;margin-bottom:4px;">Regional Australia (' + code + ') — Air-Dried Treats &amp; Dry Food Ready</strong><span style="color:#52625a;">Chilled fresh boxes safely guarded against warm transit; shoppers automatically offered shelf-stable Air-Dried Roo/Beef Treats &amp; Kibble via AusPost Regional.</span>';
        }
    }

    var pcBtn = document.getElementById('au-postcode-btn');
    if (pcBtn && pcInput) pcBtn.addEventListener('click', function () { checkPostcode(pcInput.value); });
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

    // 7. Interactive AUD Shopify Cost-Saving Calculator
    var retainerEl = document.getElementById('calc-retainer');
    var appsEl = document.getElementById('calc-apps');
    var storesEl = document.getElementById('calc-stores');
    var lastCalcSummary = '';

    function aud(n) {
        return 'A$' + Math.round(n).toLocaleString('en-AU');
    }

    function updateCalculator() {
        if (!retainerEl || !appsEl || !storesEl) return;
        var retainer = Number(retainerEl.value);
        var apps = Number(appsEl.value);
        var stores = Number(storesEl.value);

        var devSavedMo = retainer * 0.55 * stores;
        var appsSavedMo = apps * 0.45 * stores;
        var totalMo = devSavedMo + appsSavedMo;
        var totalYr = totalMo * 12;

        document.getElementById('calc-retainer-val').textContent = aud(retainer);
        document.getElementById('calc-apps-val').textContent = aud(apps);
        document.getElementById('calc-stores-val').textContent = stores + (stores === 1 ? ' Store' : ' Stores');
        document.getElementById('calc-dev-saved').textContent = aud(devSavedMo);
        document.getElementById('calc-apps-saved').textContent = aud(appsSavedMo);
        document.getElementById('calc-monthly-total').textContent = aud(totalMo) + ' / mo';
        document.getElementById('calc-annual-total').textContent = aud(totalYr) + ' / yr';

        lastCalcSummary = 'AUD Shopify Savings Estimate: ' + stores + ' store(s), current retainer '
            + aud(retainer) + '/mo, paid apps ' + aud(apps)
            + '/mo -> projected savings ' + aud(totalYr) + '/yr.';
    }

    [retainerEl, appsEl, storesEl].forEach(function (el) {
        if (el) el.addEventListener('input', updateCalculator);
    });
    updateCalculator();

    var lockBtn = document.getElementById('pfa-lock-savings');
    if (lockBtn) {
        lockBtn.addEventListener('click', function () {
            var msg = document.getElementById('pfa-message-input');
            if (msg && lastCalcSummary) {
                msg.value = lastCalcSummary + '\nPlease send my free 5-minute Shopify cost-saving Loom audit.';
            }
            var target = document.getElementById('audit-form');
            if (target) target.scrollIntoView({ behavior: 'smooth' });
        });
    }

    // 8. Main Free Audit Form -> /lead-submit.php with Paid Ads UTM Attribution
    var mainForm = document.getElementById('pfa-main-form');
    var mainStatus = document.getElementById('pfa-main-status');
    if (mainForm && mainStatus) {
        mainForm.addEventListener('submit', function (e) {
            e.preventDefault();
            var btn = mainForm.querySelector('button[type="submit"]');
            var orig = btn.innerHTML;
            mainStatus.className = 'pfa-status';
            mainStatus.textContent = '';

            var fd = new FormData(mainForm);
            var name = String(fd.get('name') || '').trim();
            var email = String(fd.get('email') || '').trim();
            var business = String(fd.get('business') || '').trim();

            if (!name || !email || !business) {
                mainStatus.className = 'pfa-status is-err';
                mainStatus.textContent = 'Please enter your name, work email, and Shopify store URL or AU agency name.';
                return;
            }

            var payload = new FormData();
            payload.append('_hp', String(fd.get('_hp') || ''));
            payload.append('form_version', 'city-service-v3');
            payload.append('name', name);
            payload.append('email', email);
            payload.append('phone', String(fd.get('phone') || '').trim());
            payload.append('business', business);
            payload.append('service', String(fd.get('service') || 'AU Pet Food Shopify Audit'));
            payload.append('city', 'Australia');
            payload.append('industry', 'Pet Food & Dog Goods Shopify');
            payload.append('budget', String(fd.get('budget') || ''));
            payload.append('source', 'au-pet-shopify-petstore-ads');
            payload.append('page_url', window.location.href);

            var msg = String(fd.get('message') || 'Requested a free 5-min AU Pet Food Shopify design, management & cost-saving audit.').trim();
            if (utmSummary) {
                msg += '\n[Ad Attribution] ' + utmSummary;
            }
            payload.append('message', msg);

            btn.disabled = true;
            btn.textContent = 'Submitting...';

            fetch(BASE + '/lead-submit.php', { method: 'POST', body: payload })
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    if (data && data.success) {
                        mainStatus.className = 'pfa-status is-ok';
                        mainStatus.textContent = 'Received! Our Shopify engineers will send your personalised 5-minute AUD cost-saving Loom audit within 24 hours.';
                        mainForm.reset();
                    } else {
                        mainStatus.className = 'pfa-status is-err';
                        mainStatus.textContent = (data && data.error) ? data.error : 'Could not submit right now. Please message us on WhatsApp.';
                    }
                })
                .catch(function () {
                    mainStatus.className = 'pfa-status is-err';
                    mainStatus.textContent = 'Network error. Please retry or chat directly on WhatsApp.';
                })
                .finally(function () {
                    btn.disabled = false;
                    btn.innerHTML = orig;
                });
        });
    }
})();
