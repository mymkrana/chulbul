<?php
return [
    'name' => 'Workflow Automation',
    'platform' => 'workflow automation',
    'icon' => 'bi-diagram-3',
    'meta_title' => 'Workflow Automation Services & Integrations | Chulbul Design',
    'meta_desc' => 'Connect forms, CRM and everyday business tools with tested triggers, data mapping and error handling. Chulbul Design builds maintainable workflow automation.',
    'hero_badge' => 'Connect the tasks between your tools',
    'hero_h1' => 'Workflow Automation',
    'hero_highlight' => 'With Clear Rules and Visible Results.',
    'hero_desc' => 'Reduce repetitive copying between systems. We build defined workflows that move the right information, check the result and alert the right person when a step needs attention.',
    'hero_img' => '/assets/images/service-heroes/custom-software-development-hero.webp',
    'hero_img_alt' => 'Illustration of connected application components, data and workflow controls',
    'hero_cta' => 'Discuss a Repetitive Workflow',
    'browser_label' => 'workflow / trigger • validate • connect',
    'chips' => [
        [
            'bi-diagram-3',
            'Connected tools',
        ],
        [
            'bi-check2-circle',
            'Defined checks',
        ],
    ],
    'intro_kicker' => 'Make the handoffs less manual',
    'intro_heading' => 'Connect Everyday Tasks Without Hiding How They Work',
    'intro' => [
        'A website form, a CRM record and a team notification may describe the same enquiry, yet someone still copies the details between them. Workflow automation connects those steps using agreed triggers and rules.',
        'Chulbul Design builds workflow automation for businesses worldwide. We review the applications involved, their available interfaces and the data each step needs before choosing an implementation approach.',
        'The result should be understandable to the person who operates it. We define what starts the workflow, what counts as success and what happens if a service is unavailable. Not every task needs AI, and not every failed action should simply be repeated.',
    ],
    'cards_heading' => 'The Building Blocks of a Reliable Workflow',
    'cards_subtitle' => 'Select a focused workflow first, then expand when the behaviour is verified.',
    'cards' => [
        [
            'bi-play-circle',
            'Trigger Design',
            'Define the event or schedule that starts a run and the conditions that should prevent it.',
        ],
        [
            'bi-ui-checks',
            'Input Validation',
            'Check required fields and acceptable values before sending incomplete information into another system.',
        ],
        [
            'bi-arrow-left-right',
            'Field Mapping',
            'Match names, formats and identifiers between applications so the destination receives usable records.',
        ],
        [
            'bi-plug',
            'API and Webhook Connections',
            'Use authorised interfaces and verify that incoming events belong to the expected source.',
        ],
        [
            'bi-signpost-split',
            'Branching Rules',
            'Route records according to explicit business conditions with a clear default for unmatched cases.',
        ],
        [
            'bi-person-check',
            'Approval Steps',
            'Pause consequential actions for the person who is responsible for approving them.',
        ],
        [
            'bi-files',
            'Duplicate Prevention',
            'Track stable record or event identifiers so retries do not automatically create repeated work.',
        ],
        [
            'bi-arrow-repeat',
            'Failure and Retry Handling',
            'Separate temporary failures from bad inputs and define when a run should stop for review.',
        ],
        [
            'bi-bell',
            'Operational Alerts',
            'Send useful failure context to an agreed owner without exposing unnecessary sensitive information.',
        ],
        [
            'bi-journal-check',
            'Documentation and Handover',
            'Explain the workflow\'s inputs, outputs, dependencies and controls in a format the team can use.',
        ],
    ],
    'sections' => [
        [
            'h2' => 'Describe the Trigger, the Data and the Intended Result',
            'para' => 'We start with a concrete example of the task being repeated. Which event begins it? Which fields are copied? What should exist when it finishes? These questions reveal dependencies that can be missed when a workflow is described only as “connect the CRM.”

The approved specification includes ordinary and exceptional cases. We identify who owns a record, what to do with missing information and whether the destination already contains it. This makes the build testable before it becomes part of daily operations.',
            'bullets' => [
                'Explicit start and stop conditions',
                'Field-level input and output mapping',
                'Record ownership defined',
                'Exception cases agreed',
            ],
        ],
        [
            'h2' => 'Choose Connections That Match the Available Access',
            'para' => 'Some applications provide a suitable connector; others require a custom API integration or a scheduled import. We review the supported interfaces, permissions and operational limits before recommending a workflow platform or custom code.

The choice should fit the people maintaining it as well as the technical task. We explain hosting, licence and account dependencies, and keep credentials out of ordinary workflow notes. If a required interface is unavailable, we identify the limitation rather than promising a seamless connection to every system.',
            'bullets' => [
                'Available interfaces reviewed',
                'Minimum required permissions',
                'Tool and hosting dependencies listed',
                'Unsupported connections identified early',
            ],
        ],
        [
            'h2' => 'Handle Failures Without Creating Duplicate Actions',
            'para' => 'A request can reach the destination even when the sender does not receive a clear confirmation. Repeating it blindly may create another record or send another message. We design around stable identifiers and destination checks where the connected systems allow it.

Failure handling also needs an owner. We decide which problems can be retried, which require corrected data and which should stop immediately. The handover explains how to inspect and resume a run without assuming that replaying the entire workflow is always safe.',
            'bullets' => [
                'Stable identifiers where available',
                'Retries separated from corrections',
                'Partial completion considered',
                'Safe review and resume procedure',
            ],
        ],
        [
            'h2' => 'Roll Out with a Pilot and a Way to Pause',
            'para' => 'A workflow should be tested with representative sample records before it processes the full live stream. The pilot checks field mapping, permissions, duplicate events and unavailable services. Actions that send messages or modify important records use approved test destinations.

After launch, your team needs to know how to pause new runs and manage work already in progress. We document version changes and remaining dependencies so a future application update does not leave the automation without an owner or a recovery process.',
            'bullets' => [
                'Representative test records',
                'Destination and side-effect checks',
                'Pause and recovery instructions',
                'Named operational owner',
            ],
        ],
    ],
    'solutions_kicker' => 'Possible connected tasks',
    'solutions_heading' => 'Automate the Handoffs Your Team Repeats',
    'solutions_intro' => 'These examples are starting points for scoping, not claims that every integration is available on every account.',
    'solutions' => [
        [
            'bi-envelope',
            'Form to CRM',
            'Validate an enquiry, map its fields and create or update the appropriate lead record.',
        ],
        [
            'bi-people',
            'Lead Assignment',
            'Route an incoming request to an owner based on agreed service or territory rules.',
        ],
        [
            'bi-calendar-check',
            'Booking Coordination',
            'Connect confirmed booking events to the records and notifications the team needs.',
        ],
        [
            'bi-folder-plus',
            'Project Setup',
            'Create an approved project structure after the required onboarding conditions are met.',
        ],
        [
            'bi-file-earmark-text',
            'Document Preparation',
            'Assemble a draft from approved data and send it for review before external use.',
        ],
        [
            'bi-cart3',
            'Order Handoffs',
            'Pass verified order events to the relevant downstream process within the authorised scope.',
        ],
        [
            'bi-headset',
            'Support Routing',
            'Organise incoming requests and surface incomplete or unmatched cases for a person.',
        ],
        [
            'bi-bar-chart',
            'Scheduled Summaries',
            'Collect agreed operational data into a repeatable report with clear source references.',
        ],
        [
            'bi-arrow-left-right',
            'Record Synchronisation',
            'Keep selected fields aligned while defining which system is the source of truth.',
        ],
        [
            'bi-bell',
            'Internal Reminders',
            'Notify an accountable owner when a defined business step is overdue.',
        ],
    ],
    'foundation_heading' => 'Rules, Records and Recovery',
    'foundation_intro' => 'A maintainable workflow needs more than a successful first run.',
    'foundation' => [
        [
            'Rules',
            'Predictable Behaviour',
            'Specify the conditions for each branch and approval.',
            [
                'Triggers',
                'Filters',
                'Branches',
                'Approvals',
            ],
        ],
        [
            'Records',
            'Consistent Information',
            'Keep identifiers and field meanings clear across systems.',
            [
                'Mapping',
                'Validation',
                'Ownership',
                'Duplicates',
            ],
        ],
        [
            'Recovery',
            'Visible Exceptions',
            'Give failures an owner and a safe route back into the process.',
            [
                'Alerts',
                'Retries',
                'Pause',
                'Run history',
            ],
        ],
    ],
    'integration_core' => [
        'bi-diagram-3',
        'Workflow',
    ],
    'integration_nodes' => [
        [
            'bi-play-circle',
            'Trigger',
        ],
        [
            'bi-ui-checks',
            'Validate',
        ],
        [
            'bi-plug',
            'Connect',
        ],
        [
            'bi-check2-circle',
            'Verify',
        ],
    ],
    'quality_heading' => 'Test More Than the Happy Path',
    'quality_intro' => 'The agreed checks cover the events most likely to create confusing or repeated work.',
    'quality' => [
        [
            'bi-input-cursor-text',
            'Incomplete Inputs',
            'Confirm missing or invalid values take a defined path.',
        ],
        [
            'bi-files',
            'Repeated Events',
            'Check the intended behaviour when the same event arrives again.',
        ],
        [
            'bi-wifi-off',
            'Unavailable Services',
            'Verify that a failed connection becomes visible and does not silently lose the task.',
        ],
        [
            'bi-person-check',
            'Approval Boundaries',
            'Confirm restricted actions do not run before the required authorisation.',
        ],
    ],
    'process' => [
        [
            'Choose One Workflow',
            'Describe a repetitive handoff and provide representative examples.',
        ],
        [
            'Map the Rules',
            'Agree triggers, data fields, approvals and exception ownership.',
        ],
        [
            'Review the Connections',
            'Confirm application access and choose a maintainable implementation.',
        ],
        [
            'Build the Flow',
            'Implement validation, actions, duplicate handling and alerts.',
        ],
        [
            'Test a Pilot',
            'Check realistic inputs, partial failures and the destination records.',
        ],
        [
            'Launch and Document',
            'Hand over controls, operating instructions and ongoing support options.',
        ],
    ],
    'proof_text' => 'Ask for a walk-through of the trigger, data mapping and recovery plan relevant to your task. Our wider website portfolio does not prove a particular automation platform deployment.',
    'why_heading' => 'Automation Your Team Can Understand After Launch',
    'why_paragraphs' => [
        'We make the workflow\'s rules and responsibilities visible before implementation. That helps your team challenge an incorrect assumption before it becomes a repeated automated action.',
        'Our focus includes the failure path and handover, not just the connection between two apps. We discuss who monitors the result, who can change it and what the ongoing platform costs involve.',
    ],
    'reasons' => [
        [
            'bi-list-check',
            'Clear specification',
            'Agree what each step is expected to do.',
        ],
        [
            'bi-plug',
            'Practical integrations',
            'Work with authorised interfaces and account limits.',
        ],
        [
            'bi-arrow-counterclockwise',
            'Recovery planning',
            'Define how failures are investigated and resumed.',
        ],
        [
            'bi-journal-text',
            'Usable documentation',
            'Leave the rules and controls with your team.',
        ],
    ],
    'cost_intro' => 'Workflow automation cost depends on the number of systems, branching rules and reliability requirements. A single enquiry handoff is different from a bidirectional synchronisation involving several teams.',
    'cost_factors' => [
        [
            'Connected applications',
            'Each interface has its own access, fields and limitations.',
        ],
        [
            'Data mapping',
            'Inconsistent formats and identifiers add preparation work.',
        ],
        [
            'Branches and approvals',
            'More business rules require more implementation and test cases.',
        ],
        [
            'Failure recovery',
            'Retry, duplicate and partial-completion handling affect the design.',
        ],
        [
            'Run volume',
            'Usage, hosting and platform plans influence ongoing costs.',
        ],
        [
            'Ownership and support',
            'Training, changes and monitoring are separately defined.',
        ],
    ],
    'faqs' => [
        [
            'Does workflow automation always need AI?',
            'No. Defined rules and structured data often work well without a model. AI can be added for a specific language or classification task when it provides a useful, testable benefit.',
        ],
        [
            'Can you connect our existing CRM and website forms?',
            'Usually where suitable access or APIs are available. We inspect the form output and CRM requirements before confirming the mapping and implementation.',
        ],
        [
            'Which workflow platform will you use?',
            'We choose after reviewing the systems, account capabilities and maintenance needs. A workflow tool or custom integration may be appropriate; subscriptions and hosting are discussed separately.',
        ],
        [
            'What happens when an application is unavailable?',
            'The flow should report the failure and follow an agreed retry or review path. The exact behaviour depends on the operation and whether repeating it is safe.',
        ],
        [
            'Can automation create duplicate records?',
            'It can if event identity and retry behaviour are not considered. We use suitable identifiers and destination checks where possible, then test repeated events.',
        ],
        [
            'Can a manager approve a step before it runs?',
            'Yes. Approval gates can be designed for the actions that require them, with an explicit handling rule for unanswered or rejected requests.',
        ],
        [
            'Can we pause a workflow ourselves?',
            'That should be part of the operating plan. We document the pause control and what happens to queued or partly completed work.',
        ],
        [
            'Can two systems update the same information?',
            'A bidirectional connection needs clear field ownership and conflict rules. We define those before implementation so each system does not repeatedly overwrite the other.',
        ],
        [
            'Is this the same as business process automation?',
            'This service focuses on defined technical handoffs between tools. Business Process Automation examines a broader end-to-end process, including roles, policy, approvals and exception management.',
        ],
        [
            'How do we start with a small budget?',
            'Choose one repetitive task with clear inputs and a visible result. We can scope a pilot before your business commits to a wider automation programme.',
        ],
    ],
    'cta_heading' => 'Which Task Does Your Team Keep Copying by Hand?',
    'cta_text' => 'Tell us where it starts, which tools it passes through and what should happen at the end. We will help define a focused workflow.',
];
