<?php
/**
 * Chulbul Design — Lightweight Cookie Consent Banner
 * - Non-blocking floating card (bottom-left; never collides with WhatsApp float on bottom-right)
 * - Persists choice in localStorage ('cbd_cookie_consent_v1')
 * - Updates Google Consent Mode (gtag) & Microsoft Clarity consent signals when available
 * - Exposes window.cbdOpenCookieSettings() so visitors can revisit preferences anytime
 */
$__cbd_cc_base = function_exists('cbd_base_path') ? cbd_base_path() : '';
?>
<style>
.cbd-cookie-banner{position:fixed;left:20px;bottom:20px;z-index:9990;width:min(410px,calc(100vw - 32px));background:#0f1233;color:#fff;border:1px solid rgba(255,255,255,.16);border-radius:20px;padding:18px 20px;box-shadow:0 22px 54px rgba(10,13,41,.38);font-family:'Plus Jakarta Sans',Inter,sans-serif;display:none;animation:cbdCookieSlide .28s ease-out}
.cbd-cookie-banner.is-visible{display:block}
@keyframes cbdCookieSlide{from{opacity:0;transform:translateY(14px)}to{opacity:1;transform:translateY(0)}}
.cbd-cookie-head{display:flex;align-items:center;gap:9px;margin-bottom:8px;font-size:14px;font-weight:800;color:#fff}
.cbd-cookie-head i{color:#ff8a65;font-size:16px}
.cbd-cookie-text{margin:0 0 14px;font-size:12.5px;line-height:1.6;color:rgba(255,255,255,.78)}
.cbd-cookie-text a{color:#ff9f94;text-decoration:underline;font-weight:700}
.cbd-cookie-actions{display:flex;flex-wrap:wrap;gap:8px;align-items:center}
.cbd-cookie-btn{appearance:none;border-radius:999px;padding:8px 15px;font-size:12.5px;font-weight:700;cursor:pointer;font-family:inherit;transition:all .18s ease}
.cbd-cookie-accept{background:#c8231a;color:#fff;border:1px solid #c8231a}
.cbd-cookie-accept:hover{background:#a91d15}
.cbd-cookie-essential{background:rgba(255,255,255,.08);color:#fff;border:1px solid rgba(255,255,255,.24)}
.cbd-cookie-essential:hover{background:rgba(255,255,255,.16)}
@media(max-width:600px){.cbd-cookie-banner{left:12px;right:12px;bottom:84px;width:auto}}
</style>

<aside id="cbdCookieBanner" class="cbd-cookie-banner" role="dialog" aria-label="Cookie preferences" aria-live="polite">
    <div class="cbd-cookie-head">
        <i class="bi bi-cookie" aria-hidden="true"></i>
        <span>Cookies &amp; Privacy</span>
    </div>
    <p class="cbd-cookie-text">
        We use essential cookies to keep this website working reliably, and optional analytics cookies (Google Analytics &amp; Microsoft Clarity) to understand traffic and improve user experience. Read our
        <a href="<?= htmlspecialchars($__cbd_cc_base, ENT_QUOTES, 'UTF-8') ?>/cookie-policy">Cookie Policy</a> and
        <a href="<?= htmlspecialchars($__cbd_cc_base, ENT_QUOTES, 'UTF-8') ?>/privacy-policy">Privacy Policy</a>.
    </p>
    <div class="cbd-cookie-actions">
        <button type="button" class="cbd-cookie-btn cbd-cookie-accept" data-cbd-cookie="all">Accept All</button>
        <button type="button" class="cbd-cookie-btn cbd-cookie-essential" data-cbd-cookie="essential">Essential Only</button>
    </div>
</aside>

<script>
(function () {
    var KEY = 'cbd_cookie_consent_v1';
    var banner = document.getElementById('cbdCookieBanner');
    if (!banner) return;

    function applyConsent(choice) {
        var granted = choice === 'all';
        try {
            if (typeof window.gtag === 'function') {
                window.gtag('consent', 'update', {
                    analytics_storage: granted ? 'granted' : 'denied',
                    ad_storage: granted ? 'granted' : 'denied',
                    ad_user_data: granted ? 'granted' : 'denied',
                    ad_personalization: granted ? 'granted' : 'denied'
                });
            }
            if (typeof window.clarity === 'function') {
                window.clarity('consent', granted);
            }
        } catch (e) {}
    }

    function saveChoice(choice) {
        try {
            localStorage.setItem(KEY, JSON.stringify({ choice: choice, updatedAt: new Date().toISOString() }));
        } catch (e) {}
        applyConsent(choice);
        banner.classList.remove('is-visible');
    }

    window.cbdOpenCookieSettings = function () {
        banner.classList.add('is-visible');
    };

    banner.querySelectorAll('[data-cbd-cookie]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            saveChoice(btn.getAttribute('data-cbd-cookie') || 'essential');
        });
    });

    var saved = null;
    try {
        var raw = localStorage.getItem(KEY);
        if (raw) saved = JSON.parse(raw);
    } catch (e) {}

    if (saved && saved.choice) {
        applyConsent(saved.choice);
    } else {
        setTimeout(function () { banner.classList.add('is-visible'); }, 650);
    }
})();
</script>
