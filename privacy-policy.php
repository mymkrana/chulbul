<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/legal-page.php';

cbd_render_legal_page([
    'slug' => 'privacy-policy',
    'title' => 'Privacy Policy',
    'meta_title' => 'Privacy Policy | Chulbul Design',
    'meta_desc' => 'Learn how Chulbul Design collects, uses, stores, and protects personal information submitted through chulbuldesign.com, contact forms, and analytics tools.',
    'eyebrow' => 'Privacy & Data Protection',
    'intro' => 'Chulbul Design LLP respects your privacy. This policy explains what information we collect when you visit chulbuldesign.com or request a project quote, how we use it, and the choices available to you worldwide.',
    'updated' => 'November 28, 2026',
    'sections' => [
        [
            'id' => 'who-we-are',
            'heading' => 'Who We Are',
            'paragraphs' => [
                'This website (<strong>https://www.chulbuldesign.com/</strong>) is operated by <strong>Chulbul Design LLP</strong>, headquartered at Sector 15, Flat No. 1277, Gurugram, Haryana 122001, India. We provide web design, ecommerce development, custom software engineering, and digital marketing services remotely to businesses worldwide.',
                'For any privacy questions or data requests, email us at <a href="mailto:info@chulbuldesign.com">info@chulbuldesign.com</a> or call +91 9990 548 795.',
            ],
        ],
        [
            'id' => 'information-we-collect',
            'heading' => 'Information We Collect',
            'paragraphs' => [
                'We only collect information needed to respond to your enquiries, deliver contracted projects, and improve website reliability:',
            ],
            'bullets' => [
                '<strong>Enquiry &amp; Contact Information:</strong> Your name, email address, phone/WhatsApp number, business name, website URL, and project requirements submitted via our contact forms, quote forms, email, or WhatsApp.',
                '<strong>Project &amp; Billing Details:</strong> Invoicing details, tax/GST details where applicable, and project assets you voluntarily share when engaging us for a scoped project.',
                '<strong>Usage &amp; Technical Data:</strong> Pages visited, device type, browser, approximate region, referring URL, and interaction insights collected via Google Analytics 4, Microsoft Clarity, and our lightweight first-party analytics.',
            ],
        ],
        [
            'id' => 'how-we-use-data',
            'heading' => 'How We Use Your Information',
            'bullets' => [
                'To respond to project enquiries, prepare written proposals, and schedule consultation calls.',
                'To design, develop, test, launch, and support websites, ecommerce stores, and software projects you commission.',
                'To measure website performance, diagnose usability issues, and improve page speed and user journeys.',
                'To protect our forms and infrastructure against spam, abuse, and security threats.',
                'To comply with applicable legal, accounting, and tax obligations.',
            ],
            'paragraphs' => [
                'We <strong>never sell or rent</strong> your personal information to third-party data brokers.',
            ],
        ],
        [
            'id' => 'third-party-tools',
            'heading' => 'Third-Party Tools & Service Providers',
            'paragraphs' => [
                'To operate our website and analytics responsibly, limited technical data may be processed by trusted third-party infrastructure providers:',
            ],
            'bullets' => [
                '<strong>Google Analytics (GA4):</strong> Aggregated traffic measurement and page performance insights.',
                '<strong>Microsoft Clarity:</strong> Privacy-masked heatmaps and session diagnostics to detect UX friction.',
                '<strong>Meta Pixel (select campaign landing pages only):</strong> Campaign conversion measurement where enabled.',
                '<strong>Hosting, Email &amp; Communication Tools:</strong> Web hosting infrastructure, business email, and WhatsApp when you choose to message us directly.',
            ],
        ],
        [
            'id' => 'cookies-consent',
            'heading' => 'Cookies & Tracking Preferences',
            'paragraphs' => [
                'You can review our full breakdown of essential and optional analytics cookies — and update your preference at any time — on our <a href="/cookie-policy">Cookie Policy</a> page.',
            ],
        ],
        [
            'id' => 'data-retention-security',
            'heading' => 'Data Retention & Security',
            'paragraphs' => [
                'We retain enquiry records only as long as reasonably necessary to follow up on your request, maintain client project documentation, or meet statutory record-keeping requirements. We use HTTPS/TLS encryption, server access controls, and sensible security practices to protect submitted data, though no internet transmission can be guaranteed 100% immune from risk.',
            ],
        ],
        [
            'id' => 'your-rights',
            'heading' => 'Your Privacy Rights (India DPDP, GDPR, UK, Australia & CCPA)',
            'paragraphs' => [
                'Depending on your location, you may have the right to:',
            ],
            'bullets' => [
                'Request access to the personal information we hold about you.',
                'Request correction of inaccurate or incomplete details.',
                'Request deletion of your enquiry data where no legal retention obligation applies.',
                'Withdraw consent for optional analytics cookies at any time.',
            ],
        ],
        [
            'id' => 'policy-updates',
            'heading' => 'Updates to This Policy',
            'paragraphs' => [
                'We may update this Privacy Policy periodically to reflect changes in our website tools, services, or legal requirements. The latest revision date is always displayed at the top of this page.',
            ],
        ],
    ],
    'extra_cta' => [
        'title' => 'Questions or Data Requests?',
        'text' => 'Email info@chulbuldesign.com with "Privacy Request" in the subject line and our team will respond promptly.',
        'href' => 'mailto:info@chulbuldesign.com?subject=Privacy%20Request%20-%20Chulbul%20Design',
        'button' => 'Email Privacy Team',
    ],
]);
