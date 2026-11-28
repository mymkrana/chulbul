<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/legal-page.php';

cbd_render_legal_page([
    'slug' => 'terms-of-service',
    'title' => 'Terms of Service',
    'meta_title' => 'Terms of Service | Chulbul Design',
    'meta_desc' => 'Read the Terms of Service governing use of chulbuldesign.com and web design, ecommerce, software development, and SEO engagements with Chulbul Design LLP.',
    'eyebrow' => 'Website & Engagement Terms',
    'intro' => 'These Terms of Service govern your access to chulbuldesign.com and outline the general framework for project proposals and digital services delivered by Chulbul Design LLP.',
    'updated' => 'November 28, 2026',
    'sections' => [
        [
            'id' => 'acceptance',
            'heading' => 'Acceptance of Terms',
            'paragraphs' => [
                'By accessing or using <strong>https://www.chulbuldesign.com/</strong>, you agree to be bound by these Terms of Service and our <a href="/privacy-policy">Privacy Policy</a>. If you do not agree with these terms, please refrain from using this website.',
            ],
        ],
        [
            'id' => 'scope-of-services',
            'heading' => 'Project Scope & Written Proposals',
            'paragraphs' => [
                'Information, indicative starting figures, or service descriptions published on this website are for general guidance only and do not form a binding contract. Every paid engagement with Chulbul Design LLP is governed by a separate written proposal, quotation, or service agreement that defines:',
            ],
            'bullets' => [
                'Exact deliverables, page templates, integrations, and revision rounds.',
                'Agreed pricing, quote currency, milestone schedule, and payment terms.',
                'Client responsibilities for content, brand assets, third-party accounts, and timely review approvals.',
                'Optional post-launch maintenance, support, or SEO retainer scope.',
            ],
        ],
        [
            'id' => 'ownership-ip',
            'heading' => 'Intellectual Property & Client Ownership',
            'paragraphs' => [
                'Upon receipt of full final payment under an agreed project proposal, the client receives <strong>100% ownership</strong> of the bespoke code, custom design files, and custom deliverables created specifically for their project, subject to standard third-party open-source or commercial software licences (such as WordPress, Shopify themes/apps, fonts, stock media, or frameworks).',
                'All content, text, graphics, brand marks, and code published on <strong>chulbuldesign.com</strong> remain the intellectual property of Chulbul Design LLP or its respective licensors and may not be copied or scraped for commercial redistribution without written permission.',
            ],
        ],
        [
            'id' => 'client-materials',
            'heading' => 'Client Content & Compliance Responsibility',
            'paragraphs' => [
                'Clients warrant that all text, trademarks, logos, photography, product data, and legal disclosures provided to Chulbul Design for inclusion on their website are owned by or properly licensed to the client and comply with applicable laws in their operating jurisdiction.',
            ],
        ],
        [
            'id' => 'third-party-platforms',
            'heading' => 'Third-Party Platforms, Hosting & Licences',
            'paragraphs' => [
                'Many projects integrate third-party platforms such as Shopify, WordPress plugins, domain registrars, cloud hosting, payment gateways, shipping carriers, or subscription apps. Those third-party services are billed directly by their respective providers and are subject to their own terms, uptime, and pricing policies.',
            ],
        ],
        [
            'id' => 'portfolio-credit',
            'heading' => 'Portfolio Showcase & Confidentiality',
            'paragraphs' => [
                'Unless restricted by a signed Non-Disclosure Agreement (NDA) or written request, Chulbul Design may display non-confidential screenshots and project summaries of completed public websites in its portfolio. We respect NDAs and white-label confidentiality agreements strictly whenever requested.',
            ],
        ],
        [
            'id' => 'limitation-of-liability',
            'heading' => 'Limitation of Liability',
            'paragraphs' => [
                'To the fullest extent permitted by applicable law, Chulbul Design LLP shall not be liable for indirect, incidental, consequential, or lost-profit damages arising from website usage, third-party service outages, search engine algorithm changes, or unauthorized modifications made after project handover.',
            ],
        ],
        [
            'id' => 'governing-law',
            'heading' => 'Governing Law & Jurisdiction',
            'paragraphs' => [
                'These Terms of Service are governed by the laws of India. Any dispute relating to this website or services — unless otherwise specified in a signed client agreement — shall be subject to the exclusive jurisdiction of the competent courts in Gurugram, Haryana, India.',
            ],
        ],
    ],
    'extra_cta' => [
        'title' => 'Need a Custom NDA or Service Agreement?',
        'text' => 'We provide clear written scopes and mutual NDAs before starting discovery on any custom website, ecommerce store, or web application.',
        'href' => '/contact-us',
        'button' => 'Discuss Your Project',
    ],
]);
