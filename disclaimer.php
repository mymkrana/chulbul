<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/legal-page.php';

cbd_render_legal_page([
    'slug' => 'disclaimer',
    'title' => 'Disclaimer',
    'meta_title' => 'Disclaimer | Chulbul Design',
    'meta_desc' => 'Important disclosures regarding SEO rankings, remote worldwide service areas, third-party platforms, and informational content published on chulbuldesign.com.',
    'eyebrow' => 'Transparency & Disclosures',
    'intro' => 'We believe in clear, honest expectations. This Disclaimer outlines important clarifications regarding our informational content, search/marketing results, remote service regions, and third-party trademarks.',
    'updated' => 'November 28, 2026',
    'sections' => [
        [
            'id' => 'general-information',
            'heading' => 'General Informational Purpose',
            'paragraphs' => [
                'Articles, pricing guides, technical walkthroughs, and service pages published on <strong>chulbuldesign.com</strong> are provided for general business and educational guidance. Final project scope, timelines, and pricing always depend on individual business requirements and are confirmed in a written quotation.',
            ],
        ],
        [
            'id' => 'no-guaranteed-rankings',
            'heading' => 'SEO, Marketing & Performance Results',
            'paragraphs' => [
                'While we apply modern technical SEO, structured schema, Core Web Vitals optimization, and conversion-focused UX best practices, search engine rankings, AI citations, traffic volumes, and advertising returns depend on external algorithms, market competition, budgets, and ongoing content quality. Accordingly, no agency can guarantee specific #1 rankings or fixed revenue outcomes.',
            ],
        ],
        [
            'id' => 'remote-service-areas',
            'heading' => 'Headquarters & Remote Worldwide Service Areas',
            'paragraphs' => [
                'Chulbul Design LLP is headquartered in <strong>Sector 15, Flat No. 1277, Gurugram, Haryana 122001, India</strong>. City and country pages on this website describe regions where we serve clients <strong>remotely</strong> through scheduled online collaboration — they are service areas and do not represent physical local branch offices outside India.',
            ],
        ],
        [
            'id' => 'trademarks-external-links',
            'heading' => 'Third-Party Trademarks & External Links',
            'paragraphs' => [
                'Product names, logos, and platform trademarks mentioned on this website (including WordPress, Shopify, WooCommerce, Magento, React, Next.js, Laravel, Google, Meta, and Microsoft) belong to their respective owners and are used solely to describe compatible technologies and services. Chulbul Design is an independent agency and is not endorsed by or officially affiliated with those platform owners unless explicitly stated.',
                'Links to external websites (including client websites, partner pages such as SoftLes, or verification directories) are provided for convenience; we are not responsible for the content or privacy practices of external domains.',
            ],
        ],
    ],
    'extra_cta' => [
        'title' => 'Need Clarification Before Starting?',
        'text' => 'Speak directly with our team for honest platform advice and transparent project scoping.',
        'href' => '/contact-us',
        'button' => 'Talk to Chulbul Design',
    ],
]);
