<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/legal-page.php';

cbd_render_legal_page([
    'slug' => 'cookie-policy',
    'title' => 'Cookie Policy',
    'meta_title' => 'Cookie Policy | Chulbul Design',
    'meta_desc' => 'Understand how Chulbul Design uses essential and analytics cookies (Google Analytics 4 and Microsoft Clarity) and manage your cookie preferences.',
    'eyebrow' => 'Cookies & Consent',
    'intro' => 'This Cookie Policy explains what cookies and similar browser storage technologies are used on chulbuldesign.com, why we use them, and how you can control or withdraw your consent at any time.',
    'updated' => 'November 28, 2026',
    'sections' => [
        [
            'id' => 'what-are-cookies',
            'heading' => 'What Are Cookies?',
            'paragraphs' => [
                'Cookies are small text files stored on your device when you visit a website. Along with browser local storage, they help websites remember user preferences, keep forms secure, and understand how visitors navigate pages.',
            ],
        ],
        [
            'id' => 'types-of-cookies',
            'heading' => 'Categories of Cookies We Use',
            'bullets' => [
                '<strong>Strictly Necessary / Essential Cookies:</strong> Required for core site functionality, security checks, form submissions, and remembering your cookie consent choice (<code>cbd_cookie_consent_v1</code>). These cannot be disabled because the website relies on them to function reliably.',
                '<strong>Analytics &amp; UX Diagnostics Cookies (Optional):</strong> Used to understand aggregated visitor behaviour, page speed, and navigation friction so we can improve our website:',
            ],
            'paragraphs' => [
                'Specifically, our analytics stack includes <strong>Google Analytics 4 (GA4)</strong> (e.g., <code>_ga</code>, <code>_ga_*</code>), <strong>Microsoft Clarity</strong> (e.g., <code>_clck</code>, <code>_clsk</code>), and lightweight first-party pageview metrics.',
            ],
        ],
        [
            'id' => 'marketing-cookies',
            'heading' => 'Advertising & Campaign Measurement Cookies',
            'paragraphs' => [
                'On dedicated advertising landing pages where enabled, Meta Pixel or Google Ads conversion tags may measure whether a campaign click resulted in an enquiry submission. Choosing <strong>"Essential Only"</strong> in our Cookie Settings updates Google Consent Mode and Microsoft Clarity consent signals accordingly.',
            ],
        ],
        [
            'id' => 'manage-cookies',
            'heading' => 'How to Manage or Disable Cookies',
            'paragraphs' => [
                'You have full control over cookies:',
            ],
            'bullets' => [
                'Click the <strong>"Open Cookie Settings"</strong> button below (or the Cookie Settings link in our website footer) at any time to switch between <em>Accept All</em> and <em>Essential Only</em>.',
                'Adjust cookie controls directly inside your browser settings (Chrome, Safari, Firefox, Edge) to block or clear stored cookies.',
            ],
        ],
    ],
    'extra_cta' => [
        'title' => 'Update Your Cookie Preferences',
        'text' => 'You can change or withdraw your optional analytics cookie consent at any time with one click.',
        'onclick' => 'window.cbdOpenCookieSettings && window.cbdOpenCookieSettings()',
        'button' => 'Open Cookie Settings',
    ],
]);
