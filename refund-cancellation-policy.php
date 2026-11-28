<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/legal-page.php';

cbd_render_legal_page([
    'slug' => 'refund-cancellation-policy',
    'title' => 'Refund & Cancellation Policy',
    'meta_title' => 'Refund & Cancellation Policy | Chulbul Design',
    'meta_desc' => 'Transparent milestone billing, cancellation notice, and refund terms for custom web design, Shopify, development, and monthly care engagements with Chulbul Design.',
    'eyebrow' => 'Billing & Engagement Fairness',
    'intro' => 'Because custom web design, software engineering, and digital marketing involve dedicated team time and bespoke deliverables, this policy explains clearly how milestones, cancellations, and refunds are handled.',
    'updated' => 'November 28, 2026',
    'sections' => [
        [
            'id' => 'milestone-structure',
            'heading' => 'Milestone-Based Project Billing',
            'paragraphs' => [
                'Custom website, ecommerce, and web application projects are billed in clear written milestones (typically discovery/project kickoff, design approval, development staging review, and final launch handover). Specific milestone percentages and deliverables are confirmed in your signed proposal before work begins.',
            ],
        ],
        [
            'id' => 'cancellation-before-start',
            'heading' => 'Cancellation Before Work Commences',
            'paragraphs' => [
                'If you request a cancellation in writing before project kickoff and before any discovery, design, or engineering hours have been spent, we will refund the deposit minus any non-recoverable payment gateway or bank transfer fees.',
            ],
        ],
        [
            'id' => 'cancellation-mid-project',
            'heading' => 'Cancellation During an Active Project',
            'paragraphs' => [
                'Either party may cancel an ongoing custom project with written notice. In that case:',
            ],
            'bullets' => [
                'Milestones already completed, approved, or delivered are non-refundable because team capacity and custom work have been expended.',
                'Any unused advance paid for future unstarted milestones will be reconciled fairly against documented work completed up to the cancellation date.',
                'Upon settlement of completed work, all completed custom deliverables up to that stage are handed over to the client.',
            ],
        ],
        [
            'id' => 'monthly-retainers',
            'heading' => 'Monthly Care, Support & SEO Retainers',
            'paragraphs' => [
                'Ongoing monthly website maintenance, Shopify care, and SEO retainers can be cancelled prior to the next billing cycle by emailing <a href="mailto:info@chulbuldesign.com">info@chulbuldesign.com</a>. Fees for the current active billing month are non-refundable once monthly work has commenced, and all work scheduled for that cycle will be completed and handed over.',
            ],
        ],
        [
            'id' => 'third-party-costs',
            'heading' => 'Third-Party Licences, Domains & Ad Spend',
            'paragraphs' => [
                'Third-party charges paid on your behalf or directly to external vendors — including domain registrations, hosting plans, Shopify/app subscriptions, premium plugins, stock media, or Google/Meta advertising spend — are non-refundable through Chulbul Design and are governed by the respective vendor’s policies.',
            ],
        ],
        [
            'id' => 'refund-processing',
            'heading' => 'Approved Refund Processing',
            'paragraphs' => [
                'Any approved refund is issued back to the original payment method within 7 to 14 business days, depending on banking or payment-processor settlement timelines.',
            ],
        ],
    ],
    'extra_cta' => [
        'title' => 'Questions About Billing or Milestones?',
        'text' => 'We clarify every payment milestone and deliverable in writing before you commit.',
        'href' => 'mailto:info@chulbuldesign.com?subject=Billing%20%26%20Refund%20Policy%20Enquiry',
        'button' => 'Contact Billing Support',
    ],
]);
