<?php

return [
    'meta_title' => 'FinTech Websites & Software Development | Chulbul Design',
    'meta_desc' => 'Develop FinTech websites and software around onboarding, transaction status, account access and operations review, with defined integrations from Chulbul Design.',
    'badge' => 'FinTech',
    'h1' => 'FinTech Web Development for Secure Customer Account Journeys',
    'h1_highlight' => 'for Secure Customer Account Journeys',
    'desc' => 'Financial products rely on understandable states: an application may need review, a payment may be pending and an account change may require approval. Chulbul Design builds websites and software around those distinctions. We map customer journeys alongside the operational work behind them, including provider handoffs, reconciliation and support enquiries. Product owners define eligibility, disclosures and decision rules; the agreed interface explains what has happened, what remains outstanding and which action the user can take next.',
    'services' => [
        [
            'slug' => 'saas-development',
            'desc' => 'Build a financial software workspace around defined account roles and business boundaries. We map organisation setup, user invitations and provider connections before development. Subscription administration, access changes and account closure are scoped alongside the operational screens that staff use each day.',
            'features' => ['Organisation setup', 'User invitations', 'Account roles', 'Access changes'],
        ],
        [
            'slug' => 'custom-software-development',
            'desc' => 'Translate financial operations into explicit records, states and review steps. Application processing, payment tracking and reconciliation need different ownership rules. We define the source of each value, integration boundaries and exception handling so custom software reflects the process your team approves.',
            'features' => ['Application states', 'Payment tracking', 'Record ownership', 'Exception handling'],
        ],
        [
            'slug' => 'dashboard-ui-design',
            'desc' => 'Design dashboards that distinguish settled activity, pending transactions and items needing investigation. We clarify totals, date ranges, currency labels and source timestamps. Detail views connect summaries to underlying records, giving authorised staff the context needed to review discrepancies and respond to enquiries.',
            'features' => ['Status filters', 'Currency labels', 'Source timestamps', 'Record drilldowns'],
        ],
        [
            'slug' => 'ui-ux-design',
            'desc' => 'Prototype onboarding and account journeys with particular attention to consent choices, interrupted sessions and provider handoffs. We review language around pending decisions and unsuccessful actions. Forms explain required information while confirmation screens distinguish submission, acceptance and completion according to the product model.',
            'features' => ['Onboarding prototypes', 'Consent choices', 'Session recovery', 'Confirmation states'],
        ],
        [
            'slug' => 'workflow-automation',
            'desc' => 'Automate agreed administrative steps such as assigning review tasks, importing provider files and flagging unmatched records. Rules include ownership, retry limits and escalation routes. Actions requiring human approval remain visible in the workflow, with records that explain what triggered each automated step.',
            'features' => ['Review assignment', 'File imports', 'Mismatch flags', 'Approval checkpoints'],
        ],
        [
            'slug' => 'website-security',
            'desc' => 'Scope security work around the actual application, its exposed interfaces and the information it handles. We review authentication, permission checks and sensitive logging with your technical owners. Remediation priorities, verification activities and ongoing responsibilities are documented against the agreed system boundaries.',
            'features' => ['Authentication review', 'Permission checks', 'Logging review', 'Remediation planning'],
        ],
    ],
    'whyus' => [
        [
            'icon' => 'bi-signpost-split',
            'title' => 'Explicit states',
            'desc' => 'Pending, accepted and completed are different events. We model those distinctions before designing screens, helping teams agree what customers should see and which operational action follows when a provider response is delayed, incomplete or unsuccessful.',
        ],
        [
            'icon' => 'bi-journal-text',
            'title' => 'Traceable records',
            'desc' => 'A displayed total needs an understandable origin. We discuss source systems, calculation ownership and adjustment handling with product teams, then design record views that help authorised operators investigate differences without relying on an unexplained dashboard summary.',
        ],
        [
            'icon' => 'bi-person-lock',
            'title' => 'Role awareness',
            'desc' => 'Customers, reviewers and administrators need different capabilities. We document those boundaries with your team and include access changes in workflow reviews, so permissions reflect actual responsibilities rather than the convenience of a shared administrative account.',
        ],
        [
            'icon' => 'bi-plug',
            'title' => 'Provider realism',
            'desc' => 'External identity, payment and account data services shape what a product can do. We check available interfaces and test arrangements during discovery, making provider limitations, onboarding dependencies and exception ownership visible before connected functionality is agreed.',
        ],
    ],
    'features' => [
        [
            'category' => 'Onboarding',
            'icon' => 'bi-person-plus',
            'items' => [
                ['text' => 'Application drafts', 'ai' => false],
                ['text' => 'Document checklists', 'ai' => false],
                ['text' => 'Consent records', 'ai' => false],
                ['text' => 'Provider handoffs', 'ai' => false],
                ['text' => 'Review requests', 'ai' => false],
                ['text' => 'Status explanations', 'ai' => false],
            ],
        ],
        [
            'category' => 'Operations',
            'icon' => 'bi-receipt',
            'items' => [
                ['text' => 'Transaction references', 'ai' => false],
                ['text' => 'Settlement imports', 'ai' => false],
                ['text' => 'Reconciliation queues', 'ai' => false],
                ['text' => 'Adjustment notes', 'ai' => false],
                ['text' => 'Approval history', 'ai' => false],
                ['text' => 'Statement exports', 'ai' => false],
            ],
        ],
        [
            'category' => 'Account control',
            'icon' => 'bi-key',
            'items' => [
                ['text' => 'Role permissions', 'ai' => false],
                ['text' => 'Session controls', 'ai' => false],
                ['text' => 'Recovery flows', 'ai' => false],
                ['text' => 'Change approvals', 'ai' => false],
                ['text' => 'Access history', 'ai' => false],
                ['text' => 'Notification preferences', 'ai' => false],
            ],
        ],
    ],
    'industries' => ['Payment platforms', 'Lending platforms', 'Expense management', 'Personal budgeting', 'Accounting technology', 'Merchant services', 'Subscription billing', 'Financial reporting'],
    'process' => [
        [
            'step' => '01',
            'title' => 'Define responsibilities',
            'desc' => 'Review the product model with business, operations and technical owners. Identify who controls decisions, records and external accounts, then document provider dependencies and the customer actions that the first release must support within its agreed boundaries.',
        ],
        [
            'step' => '02',
            'title' => 'Model states',
            'desc' => 'Map applications, transactions and account changes from initiation to their possible outcomes. Prototype pending and exception screens alongside successful journeys, and obtain product approval for labels, disclosures and the information users need to understand their next step.',
        ],
        [
            'step' => '03',
            'title' => 'Implement connections',
            'desc' => 'Build the agreed workflows using provider test environments where available. Define reference identifiers, callback handling and permission checks, then connect operational queues so incomplete submissions and integration failures have an assigned owner and a reviewable record.',
        ],
        [
            'step' => '04',
            'title' => 'Exercise exceptions',
            'desc' => 'Test duplicate notifications, delayed responses, mismatched records and interrupted onboarding with representative data. Review access boundaries and displayed calculations with responsible teams, then record unresolved findings and provider dependencies that affect the agreed decision to release.',
        ],
        [
            'step' => '05',
            'title' => 'Hand over operations',
            'desc' => 'Prepare release controls, operational guidance and support ownership for the selected scope. Confirm how teams investigate pending activity, reconcile provider records and approve future changes, with follow-up work prioritised from observed exceptions and documented product needs.',
        ],
    ],
    'examples' => [
        [
            'title' => 'Merchant operations',
            'desc' => 'A hypothetical merchant workspace could combine payment references, provider settlement files and an unmatched transaction queue. The brief would define which records are authoritative, how adjustments are reviewed and what support staff can see when a merchant asks about a pending settlement.',
            'features' => ['Settlement views', 'Mismatch review', 'Support context'],
        ],
        [
            'title' => 'Application portal',
            'desc' => 'An illustrative lending application portal could let applicants save progress and respond to document requests. The lender would own eligibility and decisions. The project would focus on submission states, reviewer assignment and clear messages when an application needs further information.',
            'features' => ['Saved applications', 'Document requests', 'Reviewer assignment'],
        ],
        [
            'title' => 'Expense workspace',
            'desc' => 'A hypothetical expense management tool could route employee submissions to designated reviewers and prepare approved records for accounting export. Discovery would define receipt handling, correction requests and approval authority, including how staff departures affect outstanding submissions and access to previous records.',
            'features' => ['Receipt submissions', 'Correction requests', 'Accounting exports'],
        ],
    ],
    'plans' => [
        [
            'name' => 'Product presence',
            'desc' => 'An indicative scope for explaining a financial product and routing enquiries. Discovery defines approved content, review dependencies, prices and timelines before the website work is agreed.',
            'features' => ['Product pages', 'Disclosure placement', 'Enquiry routing', 'Content editing', 'Journey review'],
        ],
        [
            'name' => 'Customer workspace',
            'desc' => 'An indicative option for defined onboarding and account tasks. Provider capabilities and access requirements determine selected functions, with prices and timelines established after product discovery.',
            'features' => ['Application journeys', 'Account access', 'Provider connections', 'Status screens', 'Support handoffs'],
        ],
        [
            'name' => 'Operations platform',
            'desc' => 'An indicative scope for review queues and connected financial administration. Discovery establishes source records, approval responsibilities, prices and timelines before operational functions and integrations are commissioned.',
            'features' => ['Review queues', 'Reconciliation tools', 'Permission controls', 'Change history', 'Operational handover'],
        ],
    ],
    'faqs' => [
        [
            'q' => 'Can you connect our chosen financial providers?',
            'a' => 'We review provider interfaces, access and test facilities before confirming an integration. Required actions may differ from the data a provider makes available. Discovery records those gaps, onboarding dependencies and failure handling. Your organisation remains responsible for obtaining the provider accounts and permissions implementation requires.',
        ],
        [
            'q' => 'Who decides application eligibility?',
            'a' => 'Your product owners define eligibility rules, review responsibilities and customer explanations. We implement the agreed workflow and make its decision states visible. Where decisions come from providers or authorised reviewers, the software records that source and handles unavailable responses following the process your team approves.',
        ],
        [
            'q' => 'How are duplicate payment callbacks handled?',
            'a' => 'We agree transaction references and rules for recognising an event already processed. Callback handling should account for duplicates, ordering differences and status changes. Tests use the selected provider behaviour, and operational records help staff investigate ambiguous events before any downstream action is repeated or corrected.',
        ],
        [
            'q' => 'Can dashboards distinguish balances from settlements?',
            'a' => 'Yes, once the business defines amounts and their authoritative sources. Available balance, pending activity and settled funds should carry distinct labels and timestamps. We review currency handling, adjustments and date boundaries with your team, then provide detail views that explain the records behind displayed totals.',
        ],
        [
            'q' => 'What happens when reconciliation finds a mismatch?',
            'a' => 'The agreed workflow places unmatched records into an investigation queue with source references and an assigned owner. Staff can review missing files, timing differences or adjustments using available evidence. Correction authority and approvals are defined separately, so identifying a discrepancy does not change financial records.',
        ],
        [
            'q' => 'Can approval roles differ between organisations?',
            'a' => 'A workspace can support organisation-specific roles if included in the data and permission model. We define which actions need approval, whether a second reviewer is required and how delegated access expires. Tests cover access across organisation boundaries as well as staff changes within an account.',
        ],
        [
            'q' => 'Can existing account records be migrated?',
            'a' => 'We assess export formats, identifiers, record quality and the destination model before defining migration. Sample imports establish how history and references will be preserved. Authentication credentials and connected provider permissions may need transition steps. The cutover plan includes reconciliation checks and responsibilities for rejected records.',
        ],
        [
            'q' => 'How do you estimate a FinTech build?',
            'a' => 'We scope the product journeys, provider access, data sources and review requirements before defining prices and timelines. Integration readiness and review can affect delivery planning. The proposal identifies these dependencies, supplier charges where known and the verification work included, with later functions listed as options.',
        ],
    ],
    'related' => ['ecommerce', 'professional-services', 'startup', 'logistics-transportation'],
    'stats' => [
        ['num' => 'Plan', 'label' => 'Product states and decision ownership'],
        ['num' => 'Build', 'label' => 'Onboarding and operational workspaces'],
        ['num' => 'Test', 'label' => 'Transactions and provider exceptions'],
        ['num' => 'Care', 'label' => 'Account access and review processes'],
    ],
];
