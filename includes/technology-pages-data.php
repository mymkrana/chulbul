<?php
/**
 * Technology service copy. All six pages reuse the existing 17-section design.
 * Display names do not change established canonical slugs, including /joomla.
 * Technical references and editorial QA are recorded in the release notes.
 */
$pages = [
    'nodejs-development' => [
        'name' => 'Node.js Development',
        'platform' => 'Node.js',
        'icon' => 'bi-code-slash',
        'meta_title' => 'Node.js Development Company | APIs & Backends | Chulbul Design',
        'meta_desc' => 'Node.js development for business APIs, real-time applications, integrations and background jobs. Plan a maintainable backend with Chulbul Design.',
        'hero_h1' => 'Node.js Development',
        'hero_highlight' => 'for Connected Business Applications',
        'hero_desc' => 'Build the backend that keeps your website, mobile app and business tools working together. We develop Node.js APIs, real-time features and integrations with clear data rules, practical testing and a release plan your team can operate.',
        'hero_img' => '/assets/images/service-heroes/web-development-hero.webp',
        'hero_img_alt' => 'Illustration of an application connected to databases, cloud services, integrations and responsive devices',
        'hero_cta' => 'Discuss Your Node.js Project',
        'browser_label' => 'your-backend.app',
        'chips' => [
            [
                'bi-plug',
                'Connected APIs',
            ],
            [
                'bi-activity',
                'Live updates',
            ],
        ],
        'intro_kicker' => 'Backend development with an operational purpose',
        'intro_heading' => 'A Reliable API Makes Every Connected Experience More Useful',
        'intro' => [
            'A booking website, customer portal or mobile application may look simple while coordinating several systems behind the scenes. Accounts, availability, payments, messages and reporting need to agree on what happened. Node.js development brings these responsibilities into a backend with defined interfaces instead of leaving each screen to solve the same problem differently.',
            'Node.js runs JavaScript on the server and is often useful for applications that spend time communicating with databases and external services. That does not make every workload a good fit. We review demanding calculations, long-running tasks and traffic patterns before choosing an architecture, so the technology supports the requirement rather than becoming the requirement.',
            'Our engagement can cover a new API, an existing backend that needs stabilizing, or one integration that is slowing down your team. The starting point is a map of users, data ownership and failure scenarios, followed by a scope that can be demonstrated and tested.',
        ],
        'cards_heading' => 'Node.js Development Services',
        'cards_subtitle' => 'Choose the backend capabilities your product needs, from a focused integration to a connected application platform.',
        'cards' => [
            [
                'bi-braces',
                'Custom REST APIs',
                'Documented endpoints for websites, mobile apps and partners, with consistent validation, responses and versioning.',
            ],
            [
                'bi-diagram-3',
                'Backend Architecture',
                'Application modules and service boundaries organized around business responsibilities rather than a collection of routes.',
            ],
            [
                'bi-person-lock',
                'Authentication & Permissions',
                'Account access, sessions and role checks designed around the actions each user is allowed to perform.',
            ],
            [
                'bi-broadcast',
                'Real-Time Features',
                'Live status updates, notifications and collaboration features with reconnection and authorization considered.',
            ],
            [
                'bi-arrow-repeat',
                'Background Jobs',
                'Import, export, notification and processing jobs moved out of interactive requests where appropriate.',
            ],
            [
                'bi-database',
                'Database Integration',
                'Data models, queries, transactions and indexes reviewed against the actual read and write patterns.',
            ],
            [
                'bi-credit-card',
                'Payment & Webhook Integration',
                'Provider callbacks checked for authenticity, duplicates and order changes before business records are updated.',
            ],
            [
                'bi-plug',
                'Third-Party Connections',
                'CRM, booking, messaging and operational APIs connected with limits, timeouts and recovery paths.',
            ],
            [
                'bi-speedometer2',
                'Backend Performance Review',
                'Slow requests investigated through measurements, query review and realistic load scenarios.',
            ],
            [
                'bi-tools',
                'Node.js Maintenance',
                'Dependency review, defect fixes, logging improvements and planned updates for an existing backend.',
            ],
        ],
        'sections' => [
            [
                'h2' => 'Design the API Around the Work It Must Support',
                'para' => 'A useful endpoint does more than return data. It represents a business action with an owner, permission rules and a clear result. We map how records are created, changed and approved before deciding which endpoints a frontend or integration needs.

This helps avoid APIs that expose internal database structure without protecting the workflow. Request formats, validation errors, pagination and version changes are documented so another developer can connect a client without reverse-engineering the server.',
                'bullets' => [
                    'Business actions mapped to clear API contracts',
                    'Input validation and predictable error responses',
                    'Access checks for records as well as endpoints',
                    'Versioning and integration documentation',
                ],
            ],
            [
                'h2' => 'Keep Connected Systems Consistent When Requests Fail',
                'para' => 'A payment provider may retry a webhook, a CRM may be unavailable, and a customer may press submit twice. The backend needs to recognize those situations rather than creating duplicate orders or silently losing a lead.

We define which system owns each record, how repeated events are handled and which operations can be retried safely. Background queues are useful when work should continue after the initial request, but they also need visibility, retry limits and a way for operators to resolve failed jobs.',
                'bullets' => [
                    'Timeouts and retry rules for external services',
                    'Duplicate-event and idempotency planning',
                    'Queue monitoring and failed-job recovery',
                    'Traceable records across connected systems',
                ],
            ],
            [
                'h2' => 'Make Real-Time Features Useful on Unreliable Connections',
                'para' => 'Live tracking, support conversations and shared dashboards depend on more than opening a persistent connection. People switch networks, leave a tab idle and return after the underlying records have changed. The interface and server must agree on how to recover.

We plan authenticated subscriptions, reconnection behavior and a reliable source of current state. Where frequent polling is sufficient, we discuss that simpler option. Real-time infrastructure belongs in the scope only when it improves a specific user journey.',
                'bullets' => [
                    'Live events restricted to authorized users',
                    'Reconnection and state-refresh behavior',
                    'Clear distinction between events and saved records',
                    'Connection limits tested against expected usage',
                ],
            ],
            [
                'h2' => 'Prepare the Backend for Release and Ongoing Operation',
                'para' => 'A backend is not ready because one demonstration succeeds. Important routes need tests for invalid data, missing permissions, provider outages and changes to stored records. We also check what happens when the process restarts during active work.

Before handover, the release plan identifies environment settings, secrets, database changes, logs and rollback responsibilities. Hosting is selected for the runtime and background processes in scope. A simple deployment is preferable when it meets the requirement; distributed services are not an automatic upgrade.',
                'bullets' => [
                    'Functional, integration and authorization tests',
                    'Environment and secret-management documentation',
                    'Measured request and database performance',
                    'Deployment checklist and rollback responsibilities',
                ],
            ],
        ],
        'solutions_kicker' => 'Where a Node.js backend can help',
        'solutions_heading' => 'Connected Workflows for Customers, Staff and Partners',
        'solutions_intro' => 'These are application scenarios we can scope with you, not claims that every example is a completed client project.',
        'solutions' => [
            [
                'bi-phone',
                'Mobile App Backends',
                'Accounts, content and actions served to Android and iOS clients through one defined API.',
            ],
            [
                'bi-calendar-check',
                'Booking Systems',
                'Availability requests, reservation states and provider updates coordinated behind the interface.',
            ],
            [
                'bi-person-workspace',
                'Customer Portals',
                'Secure requests, documents and account history connected to operational records.',
            ],
            [
                'bi-broadcast',
                'Live Dashboards',
                'Current status and activity delivered to teams without repeated manual refreshes.',
            ],
            [
                'bi-chat-dots',
                'Messaging Workflows',
                'Notifications and conversation events connected to the relevant customer or task.',
            ],
            [
                'bi-cart3',
                'Commerce Integrations',
                'Orders, payment events and inventory updates passed between supported systems.',
            ],
            [
                'bi-cloud',
                'SaaS Backends',
                'Tenant-aware APIs, subscriptions and administration for browser-based products.',
            ],
            [
                'bi-diagram-3',
                'Partner APIs',
                'Controlled data exchange for suppliers, distributors or external business partners.',
            ],
            [
                'bi-file-earmark-arrow-up',
                'Data Processing Pipelines',
                'Imports and exports managed as observable jobs with validation and error reports.',
            ],
            [
                'bi-clipboard-check',
                'Internal Approval Tools',
                'Requests move through defined review steps with access rules and an audit trail.',
            ],
        ],
        'foundation_heading' => 'API Contracts, Data and Operations Working Together',
        'foundation_intro' => 'The interface, stored records and runtime each need an explicit responsibility.',
        'foundation' => [
            [
                '01 / Contracts',
                'Predictable application boundaries',
                'Requests, responses, errors and permissions define how each client interacts with the backend.',
                [
                    'Endpoints',
                    'Validation',
                    'Versioning',
                    'Access',
                ],
            ],
            [
                '02 / Data',
                'Reliable business records',
                'Queries and transactions protect the records that orders, bookings and reports depend on.',
                [
                    'Models',
                    'Transactions',
                    'Indexes',
                    'Ownership',
                ],
            ],
            [
                '03 / Runtime',
                'An observable service',
                'Logs, jobs and deployment settings make failures understandable and releases repeatable.',
                [
                    'Queues',
                    'Logs',
                    'Monitoring',
                    'Releases',
                ],
            ],
        ],
        'integration_core' => [
            'bi-braces',
            'Node.js',
        ],
        'integration_nodes' => [
            [
                'bi-phone',
                'Clients',
            ],
            [
                'bi-database',
                'Data',
            ],
            [
                'bi-credit-card',
                'Payments',
            ],
            [
                'bi-chat-dots',
                'Messages',
            ],
        ],
        'quality_heading' => 'Backend Quality You Can Test',
        'quality_intro' => 'Acceptance checks are tied to the routes and operating conditions that matter to your product.',
        'quality' => [
            [
                'bi-shield-lock',
                'Permission boundaries',
                'Test whether users can access another account\'s records, not only whether login works.',
            ],
            [
                'bi-arrow-repeat',
                'Failure recovery',
                'Review retries, duplicate events and interrupted processing on important business actions.',
            ],
            [
                'bi-speedometer2',
                'Measured response time',
                'Profile representative requests before choosing query, caching or infrastructure changes.',
            ],
            [
                'bi-journal-code',
                'Maintainable contracts',
                'Document interfaces and dependencies so future clients and releases remain compatible.',
            ],
        ],
        'process' => [
            [
                'Map the workflow',
                'Identify clients, business actions, existing services and the source of truth for each record.',
            ],
            [
                'Define API contracts',
                'Agree endpoint behavior, data models, permissions and acceptance criteria with the consuming team.',
            ],
            [
                'Plan integrations',
                'Check provider documentation, test access, callbacks, rate limits and failure consequences.',
            ],
            [
                'Build reviewable modules',
                'Implement connected journeys in manageable increments with demonstrations and automated checks.',
            ],
            [
                'Test and deploy',
                'Exercise critical failure cases, confirm environment configuration and release with a rollback plan.',
            ],
            [
                'Observe and improve',
                'Use request logs, job failures and support evidence to prioritize maintenance and improvements.',
            ],
        ],
        'why_heading' => 'Backend Decisions Explained Beyond the Framework',
        'why_paragraphs' => [
            'We help you compare a focused Node.js service with the alternatives already available in your stack. If an existing application can solve the problem with a small, well-tested change, a complete rebuild may not be the right investment.',
            'Chulbul Design works from Gurugram with businesses worldwide. Written API decisions, shared test examples and scheduled remote reviews make coordination practical across frontend teams, providers and time zones.',
        ],
        'reasons' => [
            [
                'bi-diagram-3',
                'Clear interfaces',
                'Clients and integrations work from documented contracts.',
            ],
            [
                'bi-search',
                'Evidence-led optimization',
                'Measured bottlenecks guide performance work.',
            ],
            [
                'bi-eye',
                'Visible failure handling',
                'Operators can identify and resolve failed actions.',
            ],
            [
                'bi-journal-code',
                'Practical handover',
                'Runtime and maintenance responsibilities are recorded.',
            ],
        ],
        'proof_text' => 'For a Node.js enquiry, ask us to walk through a comparable API or integration decision and clarify our contribution. We can discuss the technical scope with you before proposing delivery responsibilities, including any collaboration with our technology partner SoftLes.',
        'cost_intro' => 'Node.js development cost depends on the number of business actions, connected clients, external services and operational requirements. A small integration and a multi-role product backend should not be priced as the same job.',
        'cost_factors' => [
            [
                'API surface',
                'Endpoints, validation rules, user roles and versioning obligations.',
            ],
            [
                'Data complexity',
                'Relationships, transactions, query patterns and existing data quality.',
            ],
            [
                'External services',
                'Provider access, webhooks, rate limits and recovery behavior.',
            ],
            [
                'Real-time and jobs',
                'Connection patterns, queue processing and operational visibility.',
            ],
            [
                'Testing depth',
                'Security boundaries, realistic load and provider failure scenarios.',
            ],
            [
                'Deployment and support',
                'Runtime hosting, monitoring, documentation and maintenance arrangements.',
            ],
        ],
        'faqs' => [
            [
                'What can you build with Node.js?',
                'We can scope APIs, application backends, integrations, real-time updates and background processing. The right choice depends on the workload, existing systems and team ownership, not just the language used by the frontend.',
            ],
            [
                'How is Node.js different from React?',
                'Node.js runs JavaScript outside the browser and can serve backend requests. React is used to build interfaces. They can work together, but a React frontend does not require every backend service to use Node.js.',
            ],
            [
                'Is Node.js suitable for heavy calculations?',
                'CPU-intensive tasks need separate consideration because blocking work can delay other requests. We assess worker processes, queues or another service for those tasks instead of assuming one Node.js process should handle everything.',
            ],
            [
                'Can you connect our existing mobile app?',
                'Yes, after reviewing its current API usage, authentication, data formats and release constraints. We agree compatibility rules so backend changes do not unexpectedly break installed versions of the app.',
            ],
            [
                'Can you improve an existing Node.js backend?',
                'We start with its code, dependencies, logs, tests and deployment process. The findings determine whether targeted fixes, module refactoring or phased replacement is appropriate.',
            ],
            [
                'How much does Node.js development cost?',
                'An estimate follows the API scope, data model, roles, integrations and operating requirements. Hosting, paid providers and ongoing support are identified separately so the total ownership cost is visible.',
            ],
            [
                'Can a Node.js application handle growing traffic?',
                'Capacity depends on application behavior, database work, infrastructure and third-party limits. We test representative traffic and address measured bottlenecks; choosing Node.js alone does not guarantee a particular request volume.',
            ],
            [
                'What hosting will the backend need?',
                'It needs an environment that supports the chosen Node.js runtime and any required workers or persistent connections. We confirm process management, secrets, logs, backups and deployment support before recommending a hosting setup.',
            ],
            [
                'Will we receive the API documentation and source code?',
                'Repository access, documentation, credentials and handover obligations are defined in the proposal. The aim is an operable backend with clear ownership rather than an undocumented dependency on one developer.',
            ],
            [
                'Can we start with one integration instead of a full rebuild?',
                'Yes. A bounded integration or API module can be a sensible first phase. We agree its interfaces and success criteria, then use the result to decide whether further backend work is worthwhile.',
            ],
        ],
        'cta_heading' => 'Planning an API, Integration or Node.js Backend?',
        'cta_text' => 'Share the application, systems it must connect to and the workflow that needs to improve. We will help turn that requirement into a clear backend scope.',
    ],
    'reactjs-development' => [
        'name' => 'React JS Development',
        'platform' => 'React',
        'icon' => 'bi-braces',
        'meta_title' => 'React JS Development Company | UI & Web Apps | Chulbul Design',
        'meta_desc' => 'React JS development for responsive web apps, dashboards, customer portals and reusable interfaces. Get a clear frontend plan from Chulbul Design.',
        'hero_h1' => 'React JS Development',
        'hero_highlight' => 'for Interfaces People Can Use',
        'hero_desc' => 'Turn complex product requirements into clear, responsive web interfaces. We develop React applications, dashboards and reusable components that connect to your backend and handle real content, user actions and error states.',
        'hero_img' => '/assets/images/service-heroes/web-design-hero.webp',
        'hero_img_alt' => 'Illustration of website interface design, reusable layout components and responsive screens',
        'hero_cta' => 'Discuss Your React Application',
        'browser_label' => 'your-interface.app',
        'chips' => [
            [
                'bi-grid',
                'Reusable UI',
            ],
            [
                'bi-phone',
                'Responsive journeys',
            ],
        ],
        'intro_kicker' => 'Frontend development beyond the first screen',
        'intro_heading' => 'An Interface Has to Work Before, During and After Every Action',
        'intro' => [
            'A dashboard can look finished while leaving its hardest questions unanswered. What does a new account see with no data? How does a long table work on a phone? What happens when a save request fails? React development turns those situations into deliberate interface behavior rather than leaving them to be discovered by customers.',
            'We build the frontend around your users\' tasks, approved design and API capabilities. Reusable components keep repeated controls consistent, while page-level flows explain what people can do next. The aim is a product that remains understandable as real data, permissions and new features are introduced.',
            'React is an interface library, not a complete hosting or backend plan. We agree routing, data loading, rendering and deployment choices alongside the UI. A private operational dashboard and a public content website may need different foundations even when both use React.',
        ],
        'cards_heading' => 'React JS Development Services',
        'cards_subtitle' => 'Frontend capabilities for new products, existing applications and design systems that need a dependable implementation.',
        'cards' => [
            [
                'bi-window',
                'React Web Applications',
                'Browser-based products built around purposeful routes, account journeys and clear interaction states.',
            ],
            [
                'bi-clipboard-data',
                'Dashboard Interfaces',
                'Tables, filters, charts and role-based views organized around decisions rather than visual decoration.',
            ],
            [
                'bi-grid',
                'Reusable Component Systems',
                'Shared controls and layout patterns that reduce inconsistent behavior as the application grows.',
            ],
            [
                'bi-bezier2',
                'Design-to-React Development',
                'Approved designs translated into responsive components with realistic content and interaction details.',
            ],
            [
                'bi-plug',
                'API-Connected Frontends',
                'Loading, validation, errors and data updates coordinated with the backend\'s actual contracts.',
            ],
            [
                'bi-ui-checks',
                'Forms & Multi-Step Flows',
                'Accessible inputs, progress, validation and recovery for important application tasks.',
            ],
            [
                'bi-phone',
                'Responsive Application UX',
                'Navigation and complex content adapted to smaller screens without hiding essential actions.',
            ],
            [
                'bi-universal-access',
                'Accessibility Improvements',
                'Keyboard paths, focus, labels and meaningful structure reviewed across key interfaces.',
            ],
            [
                'bi-speedometer2',
                'React Performance Review',
                'Rendering, bundle weight and data requests measured before optimization decisions are made.',
            ],
            [
                'bi-tools',
                'Existing React Modernization',
                'Targeted refactoring, dependency updates and component improvements with regression checks.',
            ],
        ],
        'sections' => [
            [
                'h2' => 'Define the Component System Without Losing the User Journey',
                'para' => 'Repeated buttons and cards are only the beginning of a component system. The harder work is deciding which behaviors belong together and which rules should remain specific to a page. A generic form that supports every imaginable exception can become harder to maintain than a small set of purposeful components.

We start with representative screens and realistic states. Components are named, documented and reviewed around their responsibilities so another developer can use them consistently. Visual reuse supports the experience; it should not force different workflows into the same interaction.',
                'bullets' => [
                    'Reusable controls with defined states and behavior',
                    'Page flows reviewed with realistic content',
                    'Consistent form, navigation and feedback patterns',
                    'Component boundaries that leave room for change',
                ],
            ],
            [
                'h2' => 'Connect the Interface to Data Without Hiding Uncertainty',
                'para' => 'Users need to know whether information is current, whether an action is still processing and whether their change was saved. We agree how the React application fetches, displays and updates data instead of treating every API response as an immediate success.

Local interface state, shared application state and server-owned records have different responsibilities. Keeping those boundaries clear helps prevent stale displays, duplicated requests and confusing back-button behavior. Sensitive permissions remain enforced by the backend, even when the UI hides unavailable actions.',
                'bullets' => [
                    'Loading, empty, error and success states',
                    'Server data separated from local UI state',
                    'Permission-aware screens with server-side enforcement',
                    'Predictable refresh and navigation behavior',
                ],
            ],
            [
                'h2' => 'Design Complex Screens for Real Devices and Abilities',
                'para' => 'A desktop table cannot always be made useful on a phone by shrinking it. We prioritize the fields and actions that matter, then consider summaries, progressive disclosure or an alternative mobile arrangement. The same attention applies to filters, menus and multi-step forms.

Keyboard focus, control labels and validation feedback are considered during implementation. These details help more people complete tasks and reduce ambiguity for everyone. Accessibility is reviewed on the agreed journeys rather than claimed from the presence of a component library.',
                'bullets' => [
                    'Responsive tables, forms and navigation',
                    'Keyboard operation and visible focus',
                    'Descriptive labels and actionable error feedback',
                    'Content tested at practical lengths and screen sizes',
                ],
            ],
            [
                'h2' => 'Choose Rendering and Release Practices for the Type of Product',
                'para' => 'A signed-in tool mainly serves returning users, while a public service website also needs discoverable content and useful first-load behavior. We compare a client-rendered application with a React framework such as Next.js when routing, server rendering or publishing requirements call for it.

Before release, critical journeys are tested against the real API and deployment environment. We inspect bundle cost, avoid unnecessary work in the browser and document how builds are produced. Rendering technology can support technical SEO, but it cannot replace useful content or guarantee rankings.',
                'bullets' => [
                    'Rendering approach matched to public or private content',
                    'Route, form and API integration tests',
                    'Bundle and request behavior measured',
                    'Build, deployment and rollback instructions',
                ],
            ],
        ],
        'solutions_kicker' => 'Interfaces shaped around different users',
        'solutions_heading' => 'React Applications for Everyday Product Work',
        'solutions_intro' => 'The interface structure follows the task, whether the user is comparing information, completing a request or managing operations.',
        'solutions' => [
            [
                'bi-person-workspace',
                'Customer Portals',
                'Account details, requests and documents presented through clear self-service journeys.',
            ],
            [
                'bi-clipboard-data',
                'Operations Dashboards',
                'Filters and status views that help staff identify the next action.',
            ],
            [
                'bi-cloud',
                'SaaS Interfaces',
                'Onboarding, settings and core product features designed as one connected experience.',
            ],
            [
                'bi-cart3',
                'Commerce Frontends',
                'Product selection and basket interactions connected to a supported commerce backend.',
            ],
            [
                'bi-calendar-check',
                'Booking Experiences',
                'Availability, selections and confirmation states made understandable across screens.',
            ],
            [
                'bi-people',
                'CRM Interfaces',
                'Contacts, activities and sales records arranged around daily follow-up tasks.',
            ],
            [
                'bi-file-earmark-text',
                'Content Administration',
                'Editing, preview and publishing interfaces with relevant permissions and feedback.',
            ],
            [
                'bi-bar-chart',
                'Reporting Tools',
                'Charts paired with useful context, filters and access to the underlying detail.',
            ],
            [
                'bi-diagram-3',
                'Partner Workspaces',
                'Restricted workflows for distributors, suppliers or project collaborators.',
            ],
            [
                'bi-grid',
                'Shared Product UI',
                'Reusable components serving multiple screens or related business applications.',
            ],
        ],
        'foundation_heading' => 'Components, State and Data With Clear Responsibilities',
        'foundation_intro' => 'A maintainable React application separates reusable interface behavior from the records and rules owned by the server.',
        'foundation' => [
            [
                '01 / Components',
                'A consistent interaction language',
                'Controls, layout patterns and feedback behave predictably across screens.',
                [
                    'Controls',
                    'Variants',
                    'Forms',
                    'Navigation',
                ],
            ],
            [
                '02 / State',
                'Understandable user actions',
                'Selections, drafts and workflow progress have an explicit home and lifecycle.',
                [
                    'Local state',
                    'Drafts',
                    'Routes',
                    'Feedback',
                ],
            ],
            [
                '03 / Data',
                'An honest view of the backend',
                'Requests and updates communicate loading, failure and changes to saved records.',
                [
                    'APIs',
                    'Caching',
                    'Errors',
                    'Permissions',
                ],
            ],
        ],
        'integration_core' => [
            'bi-braces',
            'React',
        ],
        'integration_nodes' => [
            [
                'bi-palette',
                'Design',
            ],
            [
                'bi-plug',
                'APIs',
            ],
            [
                'bi-person-lock',
                'Accounts',
            ],
            [
                'bi-bar-chart',
                'Events',
            ],
        ],
        'quality_heading' => 'Frontend Quality Beyond a Matching Screenshot',
        'quality_intro' => 'We review what the user can understand and complete, not only whether the interface resembles a mockup.',
        'quality' => [
            [
                'bi-universal-access',
                'Usable controls',
                'Keyboard access, labels and feedback checked on important tasks.',
            ],
            [
                'bi-phone',
                'Responsive behavior',
                'Real records and long content tested across agreed device widths.',
            ],
            [
                'bi-arrow-repeat',
                'Reliable states',
                'Loading, failed saves and interrupted journeys have a clear recovery path.',
            ],
            [
                'bi-lightning-charge',
                'Measured browser work',
                'Bundle size and rendering bottlenecks reviewed before adding optimizations.',
            ],
        ],
        'process' => [
            [
                'Understand the users',
                'Map the main tasks, roles, current pain points and available backend capabilities.',
            ],
            [
                'Plan screens and states',
                'Agree routes, responsive behavior and the loading, empty and failure states.',
            ],
            [
                'Define reusable components',
                'Build a small set of reviewable controls and patterns using approved design rules.',
            ],
            [
                'Implement connected journeys',
                'Connect screens to real API contracts and demonstrate complete user actions.',
            ],
            [
                'Review access and usability',
                'Test keyboard paths, device layouts, permissions, forms and regression scenarios.',
            ],
            [
                'Release and document',
                'Provide build instructions, component guidance and a prioritized improvement backlog.',
            ],
        ],
        'why_heading' => 'Design and Frontend Decisions in the Same Conversation',
        'why_paragraphs' => [
            'Chulbul Design connects visual hierarchy with the engineering work needed to make an interface dependable. We discuss missing states and unclear API behavior early, so a polished mockup does not conceal an incomplete product requirement.',
            'Our India-based team supports clients worldwide through remote design reviews, working previews and written decisions. We agree how feedback is consolidated and who approves each journey before implementation expands.',
        ],
        'reasons' => [
            [
                'bi-eye',
                'Reviewable interfaces',
                'Working screens expose unclear decisions before launch.',
            ],
            [
                'bi-grid',
                'Purposeful reuse',
                'Shared components make common tasks consistent.',
            ],
            [
                'bi-plug',
                'Backend alignment',
                'API limitations are discussed rather than hidden in the UI.',
            ],
            [
                'bi-journal-code',
                'Developer-ready handover',
                'Build instructions and component responsibilities remain visible.',
            ],
        ],
        'proof_text' => 'For a React project, we can review relevant interface and component decisions with you and identify what our portfolio demonstrates. Any proposed specialist implementation or partnership role is explained before it is included in the scope.',
        'cost_intro' => 'React development cost is driven by unique screen behavior rather than page count alone. A simple content view and a permission-sensitive dashboard with forms, filters and live data involve different levels of design, implementation and testing.',
        'cost_factors' => [
            [
                'Screen and state coverage',
                'Unique layouts plus loading, error, empty and success behavior.',
            ],
            [
                'Design readiness',
                'Approved assets, responsive rules and the amount of UX work still needed.',
            ],
            [
                'Data interaction',
                'API quality, updates, filters, caching and real-time requirements.',
            ],
            [
                'Component scope',
                'Reusable controls, documentation and compatibility with existing UI.',
            ],
            [
                'Accessibility and QA',
                'Keyboard journeys, device support, browser testing and regression coverage.',
            ],
            [
                'Product integration',
                'Authentication, deployment setup, analytics and future maintenance needs.',
            ],
        ],
        'faqs' => [
            [
                'What is included in React JS development?',
                'The scope can include application structure, reusable components, responsive screens, API integration, forms, testing and deployment preparation. UX design, backend work and support are agreed separately where they are not already provided.',
            ],
            [
                'Should we use React or Next.js?',
                'React provides the interface layer. Next.js adds a framework for routing, rendering and other application concerns. Public content, data needs, hosting and the team\'s maintenance plan determine whether those additional capabilities are useful.',
            ],
            [
                'Can you build from our existing design files?',
                'Yes. We first check responsive layouts, assets, component behavior and missing states. Any decisions that the design does not cover are resolved before they create inconsistent implementation choices.',
            ],
            [
                'Can React connect to a PHP or Laravel backend?',
                'Yes. A React interface can consume a suitably designed API regardless of the backend language. Authentication, permissions, response formats and deployment configuration must be agreed between both sides.',
            ],
            [
                'Will a React website automatically rank on Google?',
                'No. Public content needs an appropriate rendering approach, crawlable routes, metadata and a useful information structure. Content quality and other search signals still matter; React itself is not a ranking advantage.',
            ],
            [
                'Can you update only part of an existing website?',
                'Often yes. A self-contained feature or a set of screens can be improved incrementally. We check how it shares styles, authentication, routing and data before recommending that approach.',
            ],
            [
                'How do you handle complex dashboards on mobile?',
                'We prioritize the actions and information users need at smaller widths. Tables, filters and navigation may use different arrangements rather than simply shrinking the desktop layout.',
            ],
            [
                'How much does a React application cost?',
                'Cost depends on the screens, interaction states, design readiness, backend integration and quality requirements. We estimate after reviewing representative journeys, not by multiplying a flat price by the number of URLs.',
            ],
            [
                'Do you provide automated tests?',
                'Testing can cover reusable components and critical user journeys, with API integration and browser checks where appropriate. Coverage is agreed around risk and maintainability rather than a percentage promised without context.',
            ],
            [
                'Can you maintain a React application built by another team?',
                'Yes, following a code and dependency review. We assess build reproducibility, architecture, tests and known defects, then propose a manageable stabilization or improvement plan.',
            ],
        ],
        'cta_heading' => 'Need a Clearer, More Capable React Interface?',
        'cta_text' => 'Send your product idea, current application or approved designs. We will review the main journeys and explain what is needed to turn them into a working frontend.',
    ],
    'laravel-development' => [
        'name' => 'Laravel Development',
        'platform' => 'Laravel',
        'icon' => 'bi-code-square',
        'meta_title' => 'Laravel Development Company | Custom Apps | Chulbul Design',
        'meta_desc' => 'Laravel development for business applications, customer portals, APIs and workflow tools. Plan secure access, integrations and a maintainable release.',
        'hero_h1' => 'Laravel Development',
        'hero_highlight' => 'Built Around Your Business Rules',
        'hero_desc' => 'Move beyond disconnected forms and manual hand-offs. We develop Laravel applications, portals and APIs around your users, approval rules and data, with a practical plan for testing, deployment and ongoing ownership.',
        'hero_img' => '/assets/images/service-heroes/custom-software-development-hero.webp',
        'hero_img_alt' => 'Illustration of business software connecting application screens, workflows, data and integrations',
        'hero_cta' => 'Discuss Your Laravel Project',
        'browser_label' => 'your-business-platform.app',
        'chips' => [
            [
                'bi-diagram-3',
                'Defined workflows',
            ],
            [
                'bi-shield-lock',
                'Role-based access',
            ],
        ],
        'intro_kicker' => 'A framework for work that needs structure',
        'intro_heading' => 'Your Application Should Reflect How the Business Actually Operates',
        'intro' => [
            'When a quotation needs approval, an order changes status or a partner uploads a document, the application has to do more than display a form. It must apply rules, update related records and make the next responsibility clear. Laravel development gives these behaviors a structured place to live.',
            'We use Laravel where a PHP framework is a practical fit for the application and the team maintaining it. Routing, validation, data access and authorization provide useful foundations, but the business rules still need to be understood and implemented. A framework cannot resolve conflicting definitions of a customer, an order or an approval.',
            'Chulbul Design can help with a new business application, a focused module or the modernization of an existing Laravel system. We begin with the workflow and current constraints, then agree a release that can be reviewed by the people who will use and operate it.',
        ],
        'cards_heading' => 'Laravel Development Services',
        'cards_subtitle' => 'Build a focused operational tool or connect several capabilities within a maintainable business application.',
        'cards' => [
            [
                'bi-window-stack',
                'Custom Business Applications',
                'Purpose-built modules for the records, decisions and exceptions that standard tools cannot comfortably support.',
            ],
            [
                'bi-person-workspace',
                'Customer & Partner Portals',
                'Account-based access to requests, documents, orders and communication with clear permissions.',
            ],
            [
                'bi-braces',
                'Laravel API Development',
                'Documented endpoints for mobile apps, React interfaces and connected business systems.',
            ],
            [
                'bi-person-lock',
                'Roles & Authorization',
                'Access rules applied to actions and records so responsibilities remain separated.',
            ],
            [
                'bi-diagram-3',
                'Approval Workflows',
                'Defined stages, decisions and audit history for requests that move between teams.',
            ],
            [
                'bi-database',
                'Database & Reporting',
                'Structured relationships, migrations and reports built around agreed definitions of business data.',
            ],
            [
                'bi-arrow-repeat',
                'Queues & Scheduled Work',
                'Notifications, exports and repetitive tasks processed with visibility into failures and retries.',
            ],
            [
                'bi-plug',
                'Business Integrations',
                'Supported payment, CRM, messaging and operational APIs connected to the application workflow.',
            ],
            [
                'bi-arrow-up-circle',
                'Laravel Upgrades',
                'Compatibility review and staged updates covering dependencies, application behavior and deployment.',
            ],
            [
                'bi-tools',
                'Application Support',
                'Defect resolution, test improvements and incremental changes for a system already in use.',
            ],
        ],
        'sections' => [
            [
                'h2' => 'Make Business Rules Explicit Before Building Modules',
                'para' => 'A feature list rarely explains what should happen when a request is incomplete, an approval is rejected or an order changes after an invoice is created. These exceptions often determine whether staff trust a new application.

We map users, states, transitions and ownership before translating the process into Laravel modules. Acceptance criteria describe the expected result in business language. This keeps the build reviewable and reduces the risk of important rules being scattered across controllers, views and individual developers\' assumptions.',
                'bullets' => [
                    'Roles, states and exceptions mapped with stakeholders',
                    'Approval and rejection behavior documented',
                    'Data ownership agreed between departments',
                    'Acceptance criteria for each important workflow',
                ],
            ],
            [
                'h2' => 'Connect Laravel to the Tools Your Team Already Uses',
                'para' => 'Replacing every existing system is rarely necessary. A Laravel application can coordinate a specific workflow while finance, CRM or another platform continues to own its records. The integration plan must explain which data moves and what happens when the receiving service cannot accept it.

We review APIs, webhooks and import formats before committing to an integration. Queued work can handle tasks that do not need to complete inside a user\'s request, but failed jobs still need an owner, a retry policy and enough context to be investigated.',
                'bullets' => [
                    'Documented APIs and data exchange responsibilities',
                    'Webhook validation and repeated-event handling',
                    'Queues with visible failures and controlled retries',
                    'Imports checked before records are changed',
                ],
            ],
            [
                'h2' => 'Build Administration Around Tasks, Not Just Database Tables',
                'para' => 'A useful administration screen helps someone complete work accurately. It should explain which records need attention, which fields can be changed and what an action will affect. A generated list of every database column rarely provides that clarity.

We design forms, filters and permission-aware actions around the team using them. Laravel can serve a conventional web interface or support a separate React frontend where the interaction requirements justify it. The choice is made alongside accessibility, performance and future maintenance responsibilities.',
                'bullets' => [
                    'Task-oriented lists, forms and filters',
                    'Server-side validation with useful feedback',
                    'Action and record-level authorization',
                    'Interface approach matched to workflow complexity',
                ],
            ],
            [
                'h2' => 'Plan Database Changes and Releases Together',
                'para' => 'An application update may change the structure of data that staff are using every day. We review migrations, backups and compatibility so a release does not depend on an undocumented manual database edit.

Testing covers the important rules, permissions and integrations rather than only the successful path through a form. Deployment planning also includes configuration, workers, scheduled tasks and logs. The team receives a clear runbook for the agreed environment and a practical approach to recovery if a release has to be reversed.',
                'bullets' => [
                    'Versioned database changes and recovery planning',
                    'Tests for rules, permissions and integrations',
                    'Worker, scheduler and environment configuration',
                    'Release notes and maintainable handover',
                ],
            ],
        ],
        'solutions_kicker' => 'Applications organized around accountable work',
        'solutions_heading' => 'Laravel Solutions for Teams, Customers and Partners',
        'solutions_intro' => 'Each example starts with a defined operational need. Scope depends on the people, systems and business rules involved.',
        'solutions' => [
            [
                'bi-people',
                'Sales Operations',
                'Lead qualification, quotations and hand-offs with clear ownership.',
            ],
            [
                'bi-person-workspace',
                'Customer Self-Service',
                'Requests, documents and account history available through a secure portal.',
            ],
            [
                'bi-box-seam',
                'Inventory Workflows',
                'Stock-related records and approvals connected to existing operational systems.',
            ],
            [
                'bi-clipboard-check',
                'Approval Platforms',
                'Requests routed through review stages with an explainable decision history.',
            ],
            [
                'bi-calendar-check',
                'Booking Administration',
                'Availability, requests and staff actions managed around real scheduling rules.',
            ],
            [
                'bi-building',
                'Partner Portals',
                'Restricted product, document or order access for business partners.',
            ],
            [
                'bi-file-earmark-text',
                'Document Workflows',
                'Submission, review and version history around documents that need controlled handling.',
            ],
            [
                'bi-cloud',
                'Subscription Applications',
                'Account access and product workflows connected to a scoped billing model.',
            ],
            [
                'bi-bar-chart',
                'Management Reporting',
                'Reports based on agreed data definitions rather than conflicting spreadsheets.',
            ],
            [
                'bi-phone',
                'Mobile Application APIs',
                'Backend actions and business data exposed to supported mobile clients.',
            ],
        ],
        'foundation_heading' => 'Business Rules, Data and Access as One Foundation',
        'foundation_intro' => 'Laravel\'s framework features support the application; explicit business decisions make it useful.',
        'foundation' => [
            [
                '01 / Domain',
                'Rules the team can explain',
                'Modules and state changes represent how work progresses and who is responsible.',
                [
                    'Modules',
                    'States',
                    'Rules',
                    'Approvals',
                ],
            ],
            [
                '02 / Records',
                'Data that stays coherent',
                'Models, transactions and migrations reflect relationships the business has agreed.',
                [
                    'Models',
                    'Relations',
                    'Migrations',
                    'Reports',
                ],
            ],
            [
                '03 / Access',
                'Controlled actions and visibility',
                'Authorization is checked on the server for the records and operations within scope.',
                [
                    'Roles',
                    'Policies',
                    'Validation',
                    'Audit',
                ],
            ],
        ],
        'integration_core' => [
            'bi-code-square',
            'Laravel',
        ],
        'integration_nodes' => [
            [
                'bi-database',
                'Records',
            ],
            [
                'bi-people',
                'CRM',
            ],
            [
                'bi-credit-card',
                'Payments',
            ],
            [
                'bi-arrow-repeat',
                'Jobs',
            ],
        ],
        'quality_heading' => 'Test the Rules That Keep Operations Dependable',
        'quality_intro' => 'A working form is one check; correct permissions, record updates and recovery behavior are equally important.',
        'quality' => [
            [
                'bi-shield-lock',
                'Authorization',
                'Confirm both allowed and disallowed actions for the relevant roles.',
            ],
            [
                'bi-database-check',
                'Data integrity',
                'Review validation, related updates and migration behavior against real records.',
            ],
            [
                'bi-arrow-repeat',
                'Job reliability',
                'Observe failed tasks and test safe retry behavior where jobs affect business records.',
            ],
            [
                'bi-journal-check',
                'Release discipline',
                'Keep configuration, migrations and deployment steps reproducible.',
            ],
        ],
        'process' => [
            [
                'Map operations',
                'Interview the people doing the work and document decisions, exceptions and data ownership.',
            ],
            [
                'Define modules',
                'Agree user roles, record relationships, workflows and the boundary of the first release.',
            ],
            [
                'Prototype key tasks',
                'Review forms, lists and approvals with representative data before detailed implementation.',
            ],
            [
                'Build and integrate',
                'Develop modules and supported connections in increments with visible acceptance criteria.',
            ],
            [
                'Test and rehearse',
                'Check permissions, imports, jobs and database changes in a staging environment.',
            ],
            [
                'Release and hand over',
                'Deploy with a runbook, user guidance and an agreed support or improvement plan.',
            ],
        ],
        'why_heading' => 'A Laravel Build Your Team Can Understand and Maintain',
        'why_paragraphs' => [
            'We keep conversations about workflow and technology connected. Business stakeholders review what a module does; developers receive the rules, data definitions and interfaces needed to implement it without guessing.',
            'Our Gurugram-based team collaborates with clients worldwide through scheduled reviews and documented milestones. We clarify repository access, environment ownership and any specialist partner involvement before delivery begins.',
        ],
        'reasons' => [
            [
                'bi-clipboard-check',
                'Defined acceptance',
                'Important workflows have an agreed definition of done.',
            ],
            [
                'bi-diagram-3',
                'Manageable modules',
                'Application responsibilities are separated without unnecessary complexity.',
            ],
            [
                'bi-shield-lock',
                'Access considered early',
                'Permissions are part of the workflow design.',
            ],
            [
                'bi-journal-code',
                'Documented operation',
                'Workers, migrations and releases have clear instructions.',
            ],
        ],
        'proof_text' => 'For Laravel work, a useful discussion includes the business rules, access model and deployment decisions behind a comparable application. We explain the evidence we can share and our role; broader engineering work may involve SoftLes with responsibilities agreed in advance.',
        'cost_intro' => 'Laravel project cost follows workflow depth, not the number of framework features listed in a proposal. Roles, approval paths, data migration and external systems usually matter more than how many menu items the application has.',
        'cost_factors' => [
            [
                'Workflow complexity',
                'States, approvals, exceptions and cross-team responsibilities.',
            ],
            [
                'Data architecture',
                'Relationships, imports, audit requirements and reporting definitions.',
            ],
            [
                'User access',
                'Role combinations, restricted actions and account lifecycle rules.',
            ],
            [
                'Integrations and jobs',
                'External APIs, scheduled work, webhooks and failed-job handling.',
            ],
            [
                'Existing code condition',
                'Dependency compatibility, test coverage and modernization risk.',
            ],
            [
                'Release responsibility',
                'Hosting setup, database changes, training and post-launch support.',
            ],
        ],
        'faqs' => [
            [
                'When is Laravel a good choice for a business application?',
                'It can be a practical choice for PHP-based applications with structured data, access rules and custom workflows. We compare its fit with existing software, hosting and team skills before recommending a build.',
            ],
            [
                'Can you build a portal rather than a complete ERP?',
                'Yes. A focused customer, staff or partner portal may solve the immediate need without replacing all operational systems. We define its data boundaries and integrations before deciding what belongs inside it.',
            ],
            [
                'Can Laravel work with React or Next.js?',
                'Yes. Laravel can provide backend APIs to a separate frontend. Authentication, permissions and data responsibilities must be planned across both applications; a split architecture is not necessary for every project.',
            ],
            [
                'How do you manage different user roles?',
                'We map which actions and records each role can access, then enforce those rules on the server. Tests include attempts to perform restricted actions, not only successful sign-in.',
            ],
            [
                'Can you upgrade an old Laravel application?',
                'We first review the current version, dependencies, custom code, tests and hosting requirements. A staged plan addresses compatibility issues and validates critical workflows before the upgraded application is released.',
            ],
            [
                'Do you build custom reports and dashboards?',
                'Yes, where the underlying data and definitions are agreed. We confirm how figures are calculated, who can access them and whether the records are complete enough to support the report.',
            ],
            [
                'What affects Laravel development cost?',
                'The main factors are workflows, roles, data relationships, integrations, existing code quality and release obligations. A written scope separates initial development from hosting, paid services and ongoing maintenance.',
            ],
            [
                'Will database changes be documented?',
                'The scope includes versioned migrations and deployment notes for the changes we make. Backup, recovery and access responsibilities are agreed for the target environment before release.',
            ],
            [
                'Can background tasks run without keeping a user waiting?',
                'Suitable work can be queued or scheduled. We also plan worker operation, retries and failure visibility, so a task does not simply disappear after the interface reports that it was accepted.',
            ],
            [
                'Do you support international businesses?',
                'Yes. We work remotely with businesses worldwide and agree review windows, milestones and communication channels. Any regional payment, language or compliance requirements are clarified as part of discovery.',
            ],
        ],
        'cta_heading' => 'Does Your Business Need a Better-Fitting Application?',
        'cta_text' => 'Tell us where work gets delayed, which people need access and which systems must stay connected. We will help define a Laravel project with a realistic first release.',
    ],
    'php-development' => [
        'name' => 'PHP Development',
        'platform' => 'PHP',
        'icon' => 'bi-filetype-php',
        'meta_title' => 'PHP Development Company | Custom Web Solutions | Chulbul Design',
        'meta_desc' => 'Custom PHP development, legacy application upgrades, API integrations and website maintenance. Improve your existing system or plan a new PHP build.',
        'hero_h1' => 'PHP Development',
        'hero_highlight' => 'for Websites and Systems You Can Maintain',
        'hero_desc' => 'Build new functionality or improve the PHP application your business already depends on. We develop websites, portals and integrations with attention to existing data, supported dependencies and the people responsible for the next update.',
        'hero_img' => '/assets/images/service-heroes/web-development-hero.webp',
        'hero_img_alt' => 'Illustration of a web application connected to data storage, cloud infrastructure and business integrations',
        'hero_cta' => 'Discuss Your PHP Requirement',
        'browser_label' => 'your-web-system.com',
        'chips' => [
            [
                'bi-tools',
                'Maintainable code',
            ],
            [
                'bi-arrow-up-circle',
                'Planned upgrades',
            ],
        ],
        'intro_kicker' => 'Improve the system without losing its useful history',
        'intro_heading' => 'Existing PHP Software Deserves a Clear Plan, Not an Automatic Rewrite',
        'intro' => [
            'Many businesses rely on PHP websites and applications that have grown through years of small changes. The system may still do valuable work even when one feature is slow, a dependency is outdated or the original developer is no longer available. The first task is to understand what must be preserved.',
            'We review the request flow, database structure, dependencies and deployment arrangement before recommending a change. A targeted fix may be enough. A larger application may benefit from a framework such as Laravel, while a small, well-structured PHP implementation can remain a reasonable choice for a narrower requirement.',
            'Our PHP development services cover custom functionality, API connections, version upgrades and ongoing improvement. We aim to leave the code and operating instructions clearer than we found them, with a testable explanation of what changed and why.',
        ],
        'cards_heading' => 'Custom PHP Development Services',
        'cards_subtitle' => 'Practical development and modernization work for PHP websites, applications and the systems they connect to.',
        'cards' => [
            [
                'bi-window',
                'Custom PHP Websites',
                'Business websites with server-rendered pages, structured content and purpose-built functionality.',
            ],
            [
                'bi-person-workspace',
                'Business Portals',
                'Account-based tools for requests, records and customer or staff workflows.',
            ],
            [
                'bi-search',
                'Legacy Code Review',
                'Map request handling, dependencies, data access and fragile areas before changing an established application.',
            ],
            [
                'bi-arrow-up-circle',
                'PHP Version Upgrades',
                'Compatibility checks and staged updates toward a supported runtime appropriate for the application.',
            ],
            [
                'bi-plug',
                'API Integration',
                'Connect supported external services with validation, authentication and useful error handling.',
            ],
            [
                'bi-ui-checks',
                'Forms & Enquiry Workflows',
                'Server-side validation and clear submission handling for forms that feed business processes.',
            ],
            [
                'bi-database',
                'Database Improvements',
                'Query review, prepared statements and data-structure changes based on actual application behavior.',
            ],
            [
                'bi-shield-check',
                'Application Hardening',
                'Review access checks, output handling, uploads and configuration within the agreed security scope.',
            ],
            [
                'bi-arrow-left-right',
                'Application Migration',
                'Move hosting or application components with inventory, backups and a defined cutover plan.',
            ],
            [
                'bi-tools',
                'PHP Maintenance',
                'Targeted fixes, dependency care and incremental improvements for a system in daily use.',
            ],
        ],
        'sections' => [
            [
                'h2' => 'Understand the Existing Code Before Changing the Architecture',
                'para' => 'An old file structure does not tell you which parts of an application are reliable or which workflows are commercially important. We trace representative requests, identify data dependencies and review where the system stores configuration and handles failures.

The findings separate urgent defects from future improvements. Where practical, we protect important behavior with repeatable checks before refactoring it. This creates a safer path to change than replacing working code simply because a different framework is available.',
                'bullets' => [
                    'Inventory of routes, dependencies and integrations',
                    'Critical user journeys and data relationships mapped',
                    'Targeted fixes separated from modernization work',
                    'Regression checks before structural changes',
                ],
            ],
            [
                'h2' => 'Build Integrations With Clear Validation and Recovery',
                'para' => 'A PHP application may need to send enquiries to a CRM, receive payment events or import supplier data. Those connections introduce external failure conditions even when the website itself is working normally.

We validate incoming data, protect credentials and define how unsuccessful requests are recorded and retried. The design also distinguishes a user submitting a form from the downstream system accepting it. That makes it easier for your team to investigate a missing record without assuming that a success message proves every delivery step completed.',
                'bullets' => [
                    'Validated requests and authenticated provider callbacks',
                    'Configuration kept outside public-facing code',
                    'Explicit submission and delivery states',
                    'Logs that support investigation without exposing secrets',
                ],
            ],
            [
                'h2' => 'Make Content and Administration Easier to Operate',
                'para' => 'Custom functionality should not leave routine changes dependent on a developer. We identify which content and records staff need to manage, how fields should be validated and which actions require additional access.

For a content-heavy website, a CMS may be more useful than an expanding set of custom administration screens. For an operational application, focused forms and lists can be the better fit. The recommendation considers usability, update responsibility and the cost of maintaining that choice over time.',
                'bullets' => [
                    'Editing responsibilities agreed with the business',
                    'Useful validation and readable feedback',
                    'Appropriate CMS or custom administration choice',
                    'Restricted actions protected on the server',
                ],
            ],
            [
                'h2' => 'Upgrade PHP Through Compatibility Testing and a Controlled Release',
                'para' => 'Changing the server\'s PHP version without reviewing the application can expose removed functions, dependency conflicts or different error behavior. We assess the current code and libraries against the target runtime, using official support information when planning the upgrade.

Testing takes place away from production before the agreed cutover. Database changes, environment settings and rollback requirements are documented. We do not promise that every legacy application can be upgraded without code changes, downtime or a careful review of third-party components.',
                'bullets' => [
                    'Runtime and dependency compatibility review',
                    'Staging checks for important pages and actions',
                    'Backup and rollback responsibilities confirmed',
                    'Post-release logs and workflow verification',
                ],
            ],
        ],
        'solutions_kicker' => 'Practical PHP work for existing and new systems',
        'solutions_heading' => 'Web Functionality That Supports Everyday Business',
        'solutions_intro' => 'The implementation can remain focused or become a larger application when the requirements justify it.',
        'solutions' => [
            [
                'bi-building',
                'Business Websites',
                'Service information, landing pages and enquiry workflows delivered from a maintainable codebase.',
            ],
            [
                'bi-person-workspace',
                'Customer Accounts',
                'Requests and records available through appropriately restricted account access.',
            ],
            [
                'bi-calendar-check',
                'Booking Administration',
                'Forms and operational records for booking-related workflows and staff follow-up.',
            ],
            [
                'bi-file-earmark-text',
                'Content Publishing',
                'Structured content and manageable editing for a growing business website.',
            ],
            [
                'bi-credit-card',
                'Payment Connections',
                'Supported payment provider interactions and clearly tracked transaction states.',
            ],
            [
                'bi-people',
                'Lead Routing',
                'Website enquiries passed to the correct business process or supported CRM.',
            ],
            [
                'bi-box-seam',
                'Supplier Data Imports',
                'Validated catalogue or operational data imported with useful error reports.',
            ],
            [
                'bi-bar-chart',
                'Internal Reporting',
                'Purpose-built reports based on defined queries and access requirements.',
            ],
            [
                'bi-arrow-up-circle',
                'Legacy Modernization',
                'Useful functionality retained while fragile code and dependencies are improved.',
            ],
            [
                'bi-plug',
                'System Extensions',
                'A bounded feature or integration added without replacing the entire application.',
            ],
        ],
        'foundation_heading' => 'Request Handling, Data Access and Deployment Made Clear',
        'foundation_intro' => 'Good PHP maintenance becomes easier when responsibilities are visible rather than spread across unrelated files.',
        'foundation' => [
            [
                '01 / Requests',
                'Explicit input and response behavior',
                'Routes, form handling and output have defined validation and error behavior.',
                [
                    'Routes',
                    'Validation',
                    'Responses',
                    'Errors',
                ],
            ],
            [
                '02 / Data',
                'Controlled access to records',
                'Queries, transactions and permissions protect how application data is read and changed.',
                [
                    'Queries',
                    'Records',
                    'Access',
                    'Transactions',
                ],
            ],
            [
                '03 / Delivery',
                'A repeatable operating setup',
                'Runtime versions, configuration and release steps are documented for the hosting environment.',
                [
                    'Runtime',
                    'Dependencies',
                    'Config',
                    'Releases',
                ],
            ],
        ],
        'integration_core' => [
            'bi-filetype-php',
            'PHP',
        ],
        'integration_nodes' => [
            [
                'bi-window',
                'Website',
            ],
            [
                'bi-database',
                'Database',
            ],
            [
                'bi-plug',
                'APIs',
            ],
            [
                'bi-envelope',
                'Enquiries',
            ],
        ],
        'quality_heading' => 'Protect the Behavior the Business Relies On',
        'quality_intro' => 'The checks depend on the application, with special attention to existing workflows and changes to the runtime.',
        'quality' => [
            [
                'bi-clipboard-check',
                'Regression coverage',
                'Compare important pages and actions before and after the change.',
            ],
            [
                'bi-shield-lock',
                'Safer data handling',
                'Review validation, prepared queries, output escaping and sensitive actions in scope.',
            ],
            [
                'bi-speedometer2',
                'Measured bottlenecks',
                'Investigate request and database costs before prescribing a performance fix.',
            ],
            [
                'bi-journal-code',
                'Operational clarity',
                'Document dependencies, configuration and the steps required to reproduce a release.',
            ],
        ],
        'process' => [
            [
                'Inventory the application',
                'Review code, runtime, integrations, hosting and the workflows that must keep working.',
            ],
            [
                'Set priorities',
                'Separate immediate defects, security concerns and business features from optional refactoring.',
            ],
            [
                'Protect current behavior',
                'Create repeatable checks and an appropriate staging copy before important changes.',
            ],
            [
                'Implement focused changes',
                'Build or update the required modules with clear validation and maintainable boundaries.',
            ],
            [
                'Verify compatibility',
                'Test data handling, existing journeys and target-runtime behavior before release.',
            ],
            [
                'Release and support',
                'Follow the agreed cutover plan, inspect outcomes and document the changed system.',
            ],
        ],
        'why_heading' => 'Practical PHP Advice Before a Large Rebuild',
        'why_paragraphs' => [
            'We do not assume that every PHP project needs to become a new application. The recommendation starts with what the business needs, what the current code can safely support and which operating risks require attention.',
            'Chulbul Design supports worldwide clients from India. Remote code reviews, written change notes and shared staging checks let your team see what is being retained, improved or replaced before it reaches production.',
        ],
        'reasons' => [
            [
                'bi-search',
                'Review before replacement',
                'Existing behavior and dependencies are investigated first.',
            ],
            [
                'bi-tools',
                'Focused improvements',
                'The scope targets a clear problem rather than an unnecessary rewrite.',
            ],
            [
                'bi-database',
                'Respect for existing data',
                'Changes account for records and processes already in use.',
            ],
            [
                'bi-journal-check',
                'Clear release notes',
                'The next maintainer can understand the work completed.',
            ],
        ],
        'proof_text' => 'For PHP maintenance or development, the most relevant discussion is often a specific code, data or deployment decision. We clarify the work we can demonstrate and the proposed contribution of our team, including any additional engineering support agreed with SoftLes.',
        'cost_intro' => 'PHP development estimates depend heavily on the condition of the current application. A well-documented integration can be straightforward to scope; unfamiliar legacy code may need a paid or separately scoped discovery stage before a reliable implementation estimate is possible.',
        'cost_factors' => [
            [
                'Existing code quality',
                'Documentation, structure, dependencies and reproducible behavior.',
            ],
            [
                'Feature requirements',
                'New routes, forms, user roles and business rules.',
            ],
            [
                'Runtime compatibility',
                'Removed functionality, dependency changes and hosting constraints.',
            ],
            [
                'Data and integrations',
                'Database relationships, provider access and import quality.',
            ],
            [
                'Regression risk',
                'Workflows that require comparison, staging and additional testing.',
            ],
            [
                'Operating responsibilities',
                'Deployment, backups, monitoring and future support expectations.',
            ],
        ],
        'faqs' => [
            [
                'Do you work on core PHP projects as well as frameworks?',
                'Yes. We can review a custom PHP application or a framework-based system. The approach depends on its structure, dependencies and requirement; we do not replace the architecture without a reason.',
            ],
            [
                'Can you take over a PHP website built by someone else?',
                'Yes, after reviewing the code, hosting access, database and existing behavior. This review helps identify immediate risks and produce a realistic scope for changes and maintenance.',
            ],
            [
                'Should our PHP application be moved to Laravel?',
                'Not automatically. Laravel may help when a larger application needs more structure, but migration also introduces cost and risk. We compare incremental improvements with a framework move before recommending either.',
            ],
            [
                'Can you upgrade an old PHP version?',
                'We can assess compatibility and plan the required code and dependency changes. The target runtime is selected using current support information and the needs of the application, followed by staging tests before release.',
            ],
            [
                'Will an upgrade preserve our existing data?',
                'Preserving required records is part of the migration plan. Backups, validation and any database changes are agreed before implementation. We do not modify production data as an unreviewed side effect of a runtime upgrade.',
            ],
            [
                'Can you connect our website form to a CRM?',
                'Yes, if the CRM provides a suitable supported interface. We confirm required fields, authentication, duplicate handling and how failed delivery is reported to the team.',
            ],
            [
                'How do you approach PHP security?',
                'We review the application\'s input validation, database access, output handling, permissions, uploads and configuration within scope. No software can be promised attack-proof; ongoing updates and operating controls also matter.',
            ],
            [
                'What determines PHP development cost?',
                'The existing code, required features, integration complexity and testing risk determine the estimate. A short discovery phase may be needed when the application is undocumented or cannot be reproduced reliably.',
            ],
            [
                'Can the work be released without downtime?',
                'Some changes can be deployed with little interruption, but this depends on hosting, data changes and application behavior. We agree the release window and recovery plan rather than guaranteeing zero downtime for every system.',
            ],
            [
                'Do you offer ongoing PHP maintenance?',
                'Yes. Maintenance can include dependency reviews, defect fixes, monitoring coordination and small improvements. Response arrangements and the boundary between support and new development are defined in the agreement.',
            ],
        ],
        'cta_heading' => 'Need Help With a New or Existing PHP System?',
        'cta_text' => 'Share the website, current problem and any known hosting or version details. We will identify what needs investigation before recommending development, repair or modernization.',
    ],
    'joomla' => [
        'name' => 'Joomla Development',
        'platform' => 'Joomla',
        'icon' => 'bi-grid-1x2',
        'meta_title' => 'Joomla Development Company | Websites & CMS | Chulbul Design',
        'meta_desc' => 'Joomla development for business websites, custom templates, extensions and multilingual content. Plan a manageable CMS, migration or website upgrade.',
        'hero_h1' => 'Joomla Development',
        'hero_highlight' => 'for Content Your Team Can Manage',
        'hero_desc' => 'Create a Joomla website that makes sense to visitors and the people publishing it. We develop templates, structured content, extensions and integrations with clear editing roles, a practical upgrade plan and support for everyday administration.',
        'hero_img' => '/assets/images/service-heroes/cms-development-hero.webp',
        'hero_img_alt' => 'Illustration of a content management system with publishing tools, user roles, media and multilingual content',
        'hero_cta' => 'Discuss Your Joomla Website',
        'browser_label' => 'your-content-website.com',
        'chips' => [
            [
                'bi-pencil-square',
                'Editor control',
            ],
            [
                'bi-translate',
                'Language planning',
            ],
        ],
        'intro_kicker' => 'Content management with a clear publishing structure',
        'intro_heading' => 'A Useful CMS Connects Pages, People and Publishing Responsibilities',
        'intro' => [
            'A Joomla website can become difficult to manage when content categories, menus and access rules grow without a shared plan. Editors may not know where a new article belongs, while visitors encounter inconsistent navigation or several routes to the same information. The solution begins with structure, not another extension.',
            'We plan the content model, templates and publishing workflow around the organization using the website. Joomla\'s menu, access-control and multilingual capabilities can support detailed publishing needs, but they require careful configuration and content ownership. We discuss whether those capabilities match your requirement before committing to the platform.',
            'The engagement may be a new business website, a template refresh, a custom extension or an upgrade of an existing installation. In each case, the goal is an understandable site that your editors can operate and your technical team can maintain.',
        ],
        'cards_heading' => 'Joomla Website Development Services',
        'cards_subtitle' => 'Combine the publishing and technical capabilities your organization needs without turning routine updates into a developer-only task.',
        'cards' => [
            [
                'bi-window',
                'Custom Joomla Websites',
                'Business and information websites with a planned content structure, navigation and enquiry journey.',
            ],
            [
                'bi-palette',
                'Joomla Template Development',
                'Responsive templates and supported overrides aligned with your brand and content needs.',
            ],
            [
                'bi-pencil-square',
                'Content Architecture',
                'Categories, fields and menu relationships organized around visitors and publishing responsibilities.',
            ],
            [
                'bi-people',
                'User Roles & Access',
                'Editing and viewing permissions planned for contributors, reviewers, administrators and members.',
            ],
            [
                'bi-translate',
                'Multilingual Configuration',
                'Language associations, navigation and publishing workflows configured around the content you can maintain.',
            ],
            [
                'bi-puzzle',
                'Extension Integration',
                'Suitable extensions assessed for required features, maintenance, compatibility and ongoing cost.',
            ],
            [
                'bi-code-square',
                'Custom Joomla Functionality',
                'Scoped components, modules or plugins where an existing supported extension is not the right fit.',
            ],
            [
                'bi-plug',
                'Website Integrations',
                'Forms, supported CRM connections and other services connected to clear operational workflows.',
            ],
            [
                'bi-arrow-up-circle',
                'Joomla Migration & Upgrades',
                'Version, template, extension and content checks before a staged migration or update.',
            ],
            [
                'bi-tools',
                'Joomla Maintenance',
                'Planned updates, backup checks, troubleshooting and incremental improvements.',
            ],
        ],
        'sections' => [
            [
                'h2' => 'Plan Articles, Menus and Fields as One Content System',
                'para' => 'Editors and visitors encounter the same information through different paths. A publishing category helps the team organize records, while a menu helps visitors find them. Treating those structures as interchangeable can create awkward navigation and duplicated routes.

We review the actual content types, relationships and publishing frequency before defining Joomla categories, fields and menus. Representative content is used during implementation so long titles, missing images and changing editorial needs do not break an otherwise attractive template.',
                'bullets' => [
                    'Content types and ownership documented',
                    'Menus organized around visitor questions',
                    'Custom fields used for repeatable structured information',
                    'Representative content tested across templates',
                ],
            ],
            [
                'h2' => 'Connect Extensions Without Losing Control of the Website',
                'para' => 'An extension can save development effort, but it also introduces maintenance, licensing and compatibility obligations. We compare the requirement with Joomla\'s core capabilities and the support history of suitable extensions before adding another dependency.

Where custom functionality is needed, we define its boundaries and integration points rather than editing core files to make a feature work. Forms and provider connections also need error handling, access control and a clear explanation of which system receives the submitted information.',
                'bullets' => [
                    'Core features checked before installing extensions',
                    'Compatibility and recurring licences reviewed',
                    'Custom code separated from core updates',
                    'Integration ownership and failure behavior documented',
                ],
            ],
            [
                'h2' => 'Make Multilingual Publishing and Permissions Practical',
                'para' => 'A language switcher does not create a multilingual website by itself. Each language needs maintained content, useful navigation and a publishing process that keeps related pages aligned. We map language associations and decide what happens when a translated page is not yet ready.

Permissions require the same care. Contributors should understand what they can edit and who approves a change. We configure access around actual responsibilities, then test the relevant roles with representative content. Translation production is scoped separately; it is not implied by enabling Joomla\'s language features.',
                'bullets' => [
                    'Language and navigation relationships planned',
                    'Translation ownership and review process agreed',
                    'Contributor, reviewer and administrator access tested',
                    'Missing or outdated translations handled deliberately',
                ],
            ],
            [
                'h2' => 'Upgrade Joomla Without Treating the Website as Disposable',
                'para' => 'A successful upgrade includes the content, template, extensions and integrations that make the installation useful. We inventory those dependencies and compare them with the intended Joomla and PHP versions before scheduling a release.

The staging review checks menus, forms, media, permissions and important URLs. Migration planning includes backups, redirect mapping where required and a rollback decision. Search visibility can fluctuate after changes, so post-launch checks and monitoring are part of the plan rather than a promise that rankings can never move.',
                'bullets' => [
                    'Template, extension and runtime compatibility inventory',
                    'Staging checks for content and user journeys',
                    'Important URL mapping and redirect verification',
                    'Backups, cutover and post-launch review',
                ],
            ],
        ],
        'solutions_kicker' => 'Publishing scenarios suited to structured management',
        'solutions_heading' => 'Joomla Websites for Content-Rich Organizations',
        'solutions_intro' => 'The right CMS depends on the content, editorial team and administration responsibilities—not simply the number of pages.',
        'solutions' => [
            [
                'bi-building',
                'Company Websites',
                'Services, departments and supporting information organized through consistent templates.',
            ],
            [
                'bi-journal-text',
                'Resource Libraries',
                'Articles, downloads and reference material made easier to maintain and discover.',
            ],
            [
                'bi-people',
                'Membership Content',
                'Restricted information and user groups planned around real access requirements.',
            ],
            [
                'bi-translate',
                'Multilingual Sites',
                'Connected language versions supported by a defined translation workflow.',
            ],
            [
                'bi-calendar-event',
                'Event Information',
                'Event content and enquiries managed through an appropriate feature set.',
            ],
            [
                'bi-mortarboard',
                'Education Information',
                'Programmes, departments and resources organized for different audiences.',
            ],
            [
                'bi-globe',
                'Association Websites',
                'Member resources, organization information and publishing responsibilities kept clear.',
            ],
            [
                'bi-file-earmark-text',
                'News & Editorial Sites',
                'Categories, contributors and review steps mapped to the editorial operation.',
            ],
            [
                'bi-box-seam',
                'Product Information',
                'Structured product content presented without forcing a full online-store model.',
            ],
            [
                'bi-person-workspace',
                'Internal Publishing',
                'Selected information and documents shared through restricted user access.',
            ],
        ],
        'foundation_heading' => 'Content, Templates and Publishing Roles in Balance',
        'foundation_intro' => 'A maintainable Joomla website keeps editorial decisions separate from presentation and technical maintenance.',
        'foundation' => [
            [
                '01 / Content',
                'A structure editors understand',
                'Articles, fields and relationships give information a consistent home.',
                [
                    'Articles',
                    'Categories',
                    'Fields',
                    'Media',
                ],
            ],
            [
                '02 / Presentation',
                'Templates that fit the content',
                'Layouts and supported overrides present the same information coherently across devices.',
                [
                    'Templates',
                    'Overrides',
                    'Navigation',
                    'Responsive',
                ],
            ],
            [
                '03 / Governance',
                'Clear responsibility for changes',
                'Permissions, review steps and update ownership keep publishing manageable.',
                [
                    'Roles',
                    'Review',
                    'Languages',
                    'Updates',
                ],
            ],
        ],
        'integration_core' => [
            'bi-grid-1x2',
            'Joomla',
        ],
        'integration_nodes' => [
            [
                'bi-pencil',
                'Content',
            ],
            [
                'bi-people',
                'Editors',
            ],
            [
                'bi-puzzle',
                'Extensions',
            ],
            [
                'bi-translate',
                'Languages',
            ],
        ],
        'quality_heading' => 'Check the Website From Both Sides of the CMS',
        'quality_intro' => 'Visitors need a usable website; editors need a publishing process they can understand and repeat.',
        'quality' => [
            [
                'bi-signpost',
                'Content findability',
                'Menus, article routes and language links reviewed with actual content.',
            ],
            [
                'bi-person-lock',
                'Publishing access',
                'Contributor and administrator actions checked against agreed permissions.',
            ],
            [
                'bi-phone',
                'Template behavior',
                'Layouts tested with long titles, media and practical mobile widths.',
            ],
            [
                'bi-arrow-up-circle',
                'Update readiness',
                'Extension and template dependencies documented before upgrades.',
            ],
        ],
        'process' => [
            [
                'Audit content and installation',
                'Review the current Joomla setup, extensions, templates, URLs and editorial needs.',
            ],
            [
                'Plan the content model',
                'Agree categories, custom fields, navigation and language relationships.',
            ],
            [
                'Develop templates and features',
                'Implement approved layouts and scoped functionality using maintainable extension points.',
            ],
            [
                'Configure roles and connections',
                'Set publishing access, supported integrations and any multilingual workflows.',
            ],
            [
                'Test and migrate',
                'Validate content, forms, permissions and important URLs in staging before cutover.',
            ],
            [
                'Train and maintain',
                'Provide editor guidance, maintenance responsibilities and a plan for future updates.',
            ],
        ],
        'why_heading' => 'Joomla Development That Includes the People Publishing',
        'why_paragraphs' => [
            'We review how your team will create and update information, not only how the homepage should look. That helps keep editorial structure, design and technical choices aligned as the website grows.',
            'From our Gurugram base, we support organizations worldwide through remote planning and review. Language configuration, translation delivery, time-zone coordination and ongoing update responsibilities are agreed explicitly rather than assumed.',
        ],
        'reasons' => [
            [
                'bi-pencil-square',
                'Editor-aware planning',
                'Content and permissions match actual responsibilities.',
            ],
            [
                'bi-puzzle',
                'Considered extensions',
                'Dependencies are assessed before being added.',
            ],
            [
                'bi-arrow-left-right',
                'Careful migration',
                'Useful content and established routes are accounted for.',
            ],
            [
                'bi-journal-text',
                'Usable guidance',
                'The handover explains routine publishing and maintenance.',
            ],
        ],
        'proof_text' => 'For a Joomla requirement, we can discuss relevant content architecture and publishing decisions and clarify the evidence available for the requested implementation. Any specialist extension or migration work is scoped on its merits, with delivery roles made clear before engagement.',
        'cost_intro' => 'Joomla development cost depends on the content model, templates, editorial access and the condition of any existing installation. Multilingual setup, extension compatibility and migration work can change the effort significantly even when the visible page count stays similar.',
        'cost_factors' => [
            [
                'Content structure',
                'Article types, custom fields, menus and imported material.',
            ],
            [
                'Template scope',
                'Unique layouts, responsive behavior and supported overrides.',
            ],
            [
                'Publishing responsibilities',
                'User groups, review needs and restricted content.',
            ],
            [
                'Languages and translation',
                'Language configuration, associations and separately scoped translated content.',
            ],
            [
                'Extensions and compatibility',
                'Licences, custom functionality and upgrade requirements.',
            ],
            [
                'Migration and support',
                'URL mapping, data checks, training and ongoing maintenance.',
            ],
        ],
        'faqs' => [
            [
                'What types of websites can you build with Joomla?',
                'Joomla can be considered for business, editorial, association and other content-rich websites. We assess content structure, publishing roles and extension requirements before deciding whether it is the right CMS for your project.',
            ],
            [
                'Should we choose Joomla or WordPress?',
                'The decision depends on your publishing workflow, required features, current installation and the team maintaining it. We compare practical fit and ownership cost instead of assuming either platform is always better.',
            ],
            [
                'Can you improve our Joomla site without replacing all content?',
                'Yes. A template or functionality update can often preserve useful content. We inspect how articles, fields, menus and extensions are connected before defining the changes and migration needs.',
            ],
            [
                'Can you build a multilingual Joomla website?',
                'We can configure languages, associations and publishing workflows. Accurate translations and their ongoing maintenance need an agreed owner and separate scope where translation production is required.',
            ],
            [
                'Do you develop custom Joomla extensions?',
                'Custom components, modules or plugins can be scoped when the requirement is not well served by core functionality or a suitable maintained extension. Compatibility, testing and future update responsibilities are part of that decision.',
            ],
            [
                'Can you upgrade an older Joomla installation?',
                'We review its version, PHP runtime, template, extensions and content before proposing an upgrade path. Unsupported components may need replacement or custom work, so testing in staging is essential.',
            ],
            [
                'Will editors be able to update the website?',
                'The content structure and permissions are planned around the editing tasks you need. We provide guidance for those tasks and identify changes that still require technical support.',
            ],
            [
                'Is Joomla automatically SEO optimized?',
                'It provides useful controls, but content structure, metadata, crawlable URLs, internal links and page performance still need attention. No CMS can guarantee search rankings by itself.',
            ],
            [
                'How much does Joomla development cost?',
                'Cost depends on templates, content complexity, user roles, languages, extensions and migration requirements. Licences, hosting, translation and ongoing maintenance are identified separately where applicable.',
            ],
            [
                'Will our existing Joomla URL change?',
                'An established URL should be retained when it remains appropriate. If a project requires route changes, we map important old URLs to relevant destinations and test redirects rather than discarding their history.',
            ],
        ],
        'cta_heading' => 'Planning a Joomla Build, Refresh or Upgrade?',
        'cta_text' => 'Share your current site, publishing needs and any extension or language requirements. We will help you identify the right content structure and a practical next step.',
    ],
    'nextjs-development' => [
        'name' => 'Next.js Development',
        'platform' => 'Next.js',
        'icon' => 'bi-window-stack',
        'meta_title' => 'Next.js Development Company | Websites & Apps | Chulbul Design',
        'meta_desc' => 'Next.js development for content websites, headless CMS platforms and web applications. Plan rendering, data freshness, integrations and a reliable launch.',
        'hero_h1' => 'Next.js Development',
        'hero_highlight' => 'for Content-Rich Websites and Web Apps',
        'hero_desc' => 'Bring public content and interactive features into a considered web application. We develop Next.js websites with purposeful routing, server and client boundaries, CMS connections and a clear plan for data freshness, performance and deployment.',
        'hero_img' => '/assets/images/service-heroes/web-design-hero.webp',
        'hero_img_alt' => 'Illustration of a responsive web experience with reusable interface components and connected screens',
        'hero_cta' => 'Discuss Your Next.js Project',
        'browser_label' => 'your-next-website.com',
        'chips' => [
            [
                'bi-file-earmark-text',
                'Content-first routes',
            ],
            [
                'bi-arrow-repeat',
                'Planned data freshness',
            ],
        ],
        'intro_kicker' => 'A React framework with decisions beyond the interface',
        'intro_heading' => 'A Public Website and a Signed-In Feature Need Different Treatment',
        'intro' => [
            'A business website may combine service pages, an editorial library, customer accounts and interactive tools. Those experiences do not all have the same content, freshness or access requirements. Treating every route identically can send unnecessary work to the browser or serve information with the wrong caching behavior.',
            'Next.js provides a React-based framework for building those routes and deciding where application work happens. We plan which content can be prepared ahead of time, which requests need current server data and which interactions belong in the browser. The appropriate approach depends on the installed version, hosting and product requirements.',
            'Chulbul Design develops new Next.js websites and improves existing implementations. Our focus is a usable publishing and operating system: editors can update content, visitors receive the intended page, and developers understand how data, releases and integrations fit together.',
        ],
        'cards_heading' => 'Next.js Development Services',
        'cards_subtitle' => 'Framework-level planning and implementation for public websites, content platforms and connected web products.',
        'cards' => [
            [
                'bi-window',
                'Custom Next.js Websites',
                'Business and product websites with a planned route structure and reusable React interfaces.',
            ],
            [
                'bi-diagram-3',
                'App Router Architecture',
                'Layouts, route groups and application boundaries organized around content and user journeys.',
            ],
            [
                'bi-server',
                'Server & Client Planning',
                'Rendering and interactive behavior divided according to data access, browser needs and security boundaries.',
            ],
            [
                'bi-pencil-square',
                'Headless CMS Integration',
                'Structured content, preview and publishing flows connected to a suitable CMS.',
            ],
            [
                'bi-search',
                'Technical SEO Foundations',
                'Route metadata, canonical URLs, sitemap behavior and crawlable content implemented intentionally.',
            ],
            [
                'bi-arrow-repeat',
                'Caching & Data Freshness',
                'Public and personalized data handled according to how current and restricted it needs to be.',
            ],
            [
                'bi-plug',
                'API & Backend Integration',
                'Existing services connected through documented interfaces with clear authorization and error handling.',
            ],
            [
                'bi-cart3',
                'Content-Led Commerce',
                'Product and editorial journeys connected to a supported commerce backend.',
            ],
            [
                'bi-arrow-left-right',
                'Migration to Next.js',
                'Routes, content and integrations mapped before a phased or complete website migration.',
            ],
            [
                'bi-speedometer2',
                'Performance & Maintenance',
                'Measured rendering, asset and dependency improvements followed by a reproducible release process.',
            ],
        ],
        'sections' => [
            [
                'h2' => 'Plan Routes Around Content, Access and the User Journey',
                'para' => 'A route is more than a URL. It has a purpose, an audience and rules about where its information comes from. A service page, search result and account area should not inherit the same assumptions simply because they share a header.

We map layouts, navigation and content relationships before building the application structure. Existing URLs are retained where appropriate. Missing pages, redirects and error states are also planned so visitors and search engines receive a deliberate response instead of an accidental route fallback.',
                'bullets' => [
                    'Public, account and utility routes distinguished',
                    'Layouts and navigation mapped to user journeys',
                    'Established URLs considered before migration',
                    'Not-found, redirect and error behavior defined',
                ],
            ],
            [
                'h2' => 'Connect the CMS and Backend With the Right Data Boundaries',
                'para' => 'Editors need reliable preview and publishing behavior, while customers need accurate account or transaction information. We identify which system owns each type of data and how a change reaches the website.

Next.js allows server and client components to take different responsibilities. We keep credentials and sensitive access checks on the server, while browser interactions receive only the data they need. An existing Laravel, Node.js or other backend can remain authoritative rather than being replaced solely because the frontend framework changes.',
                'bullets' => [
                    'CMS preview and publication flow agreed',
                    'Backend ownership retained where it makes sense',
                    'Server-side authorization for sensitive operations',
                    'Client interfaces limited to appropriate data',
                ],
            ],
            [
                'h2' => 'Treat Caching and Freshness as Product Decisions',
                'para' => 'A page that loads quickly but shows the wrong availability or another user\'s information is not a successful optimization. We classify data by its sensitivity and update frequency, then choose caching and invalidation behavior appropriate to the application\'s version and deployment.

Editors should understand when a published change becomes visible. Account-specific requests need explicit handling so shared caching does not expose personalized information. These behaviors are tested with content changes and separate users rather than assumed from a default configuration.',
                'bullets' => [
                    'Freshness requirements recorded for important routes',
                    'Personalized data separated from shared content',
                    'Publishing changes checked through to the website',
                    'Cache invalidation and failure cases tested',
                ],
            ],
            [
                'h2' => 'Launch With Measurable SEO and Deployment Checks',
                'para' => 'Next.js provides tools for metadata and social images, but those tools still need accurate page content and consistent URLs. We implement route-specific titles, descriptions, canonical links and relevant structured data, then inspect what the server and browser actually deliver.

Performance review considers images, fonts, scripts and the interaction work sent to the client. Deployment planning confirms runtime features, environment variables and CMS or backend access. The release includes route checks and recovery instructions; choosing Next.js does not guarantee rankings or a particular performance score.',
                'bullets' => [
                    'Rendered content and page metadata inspected',
                    'Sitemap, redirects and canonical URLs checked',
                    'Assets and client-side work measured',
                    'Hosting compatibility and release rollback confirmed',
                ],
            ],
        ],
        'solutions_kicker' => 'When content and application behavior share a website',
        'solutions_heading' => 'Next.js Experiences for Publishing, Products and Customers',
        'solutions_intro' => 'Framework choices follow the balance of public content, interaction, access and operating responsibility.',
        'solutions' => [
            [
                'bi-building',
                'Business Service Websites',
                'Discoverable service routes with reusable layouts and manageable publishing.',
            ],
            [
                'bi-journal-text',
                'Editorial Platforms',
                'Article collections and related content connected to an appropriate CMS.',
            ],
            [
                'bi-cloud',
                'SaaS Marketing Sites',
                'Product information and conversion journeys linked to the application experience.',
            ],
            [
                'bi-cart3',
                'Headless Storefronts',
                'Content and product discovery backed by an existing commerce platform.',
            ],
            [
                'bi-person-workspace',
                'Customer Portals',
                'Account features separated from public content and shared caching.',
            ],
            [
                'bi-search',
                'Search & Discovery',
                'Filtered experiences with deliberate URL, data and indexing behavior.',
            ],
            [
                'bi-globe',
                'Multi-Market Websites',
                'Regional content and navigation organized around maintainable publishing responsibilities.',
            ],
            [
                'bi-file-earmark-text',
                'Resource Hubs',
                'Guides, documents and landing pages built from structured content.',
            ],
            [
                'bi-calculator',
                'Interactive Tools',
                'Useful calculators or product tools placed alongside relevant explanatory content.',
            ],
            [
                'bi-arrow-left-right',
                'Website Modernization',
                'Existing content and URLs migrated into a maintainable route and rendering model.',
            ],
        ],
        'foundation_heading' => 'Routing, Rendering and Publishing Working Together',
        'foundation_intro' => 'The application structure should make it clear where content lives, where work runs and when visitors see an update.',
        'foundation' => [
            [
                '01 / Routes',
                'A purposeful information structure',
                'Pages, layouts and navigation follow the needs of public visitors and signed-in users.',
                [
                    'Layouts',
                    'Routes',
                    'Navigation',
                    'Metadata',
                ],
            ],
            [
                '02 / Rendering',
                'The right work in the right place',
                'Server and client boundaries reflect data access, interactivity and operating constraints.',
                [
                    'Server',
                    'Client',
                    'Data',
                    'Access',
                ],
            ],
            [
                '03 / Publishing',
                'Predictable content delivery',
                'CMS changes, previews and freshness rules have a tested path into the website.',
                [
                    'CMS',
                    'Preview',
                    'Caching',
                    'Revalidation',
                ],
            ],
        ],
        'integration_core' => [
            'bi-window-stack',
            'Next.js',
        ],
        'integration_nodes' => [
            [
                'bi-pencil',
                'CMS',
            ],
            [
                'bi-plug',
                'Backend',
            ],
            [
                'bi-person-lock',
                'Accounts',
            ],
            [
                'bi-cloud',
                'Hosting',
            ],
        ],
        'quality_heading' => 'Validate Content Delivery, Not Just the Build Command',
        'quality_intro' => 'Checks cover the website visitors receive and the update process your team depends on.',
        'quality' => [
            [
                'bi-file-earmark-check',
                'Rendered content',
                'Confirm public routes expose the intended headings, links and metadata.',
            ],
            [
                'bi-person-lock',
                'Data isolation',
                'Review authorization and personalized data across separate sessions.',
            ],
            [
                'bi-arrow-repeat',
                'Freshness behavior',
                'Test that publishing and record changes reach the correct views.',
            ],
            [
                'bi-lightning-charge',
                'Page experience',
                'Measure assets and interaction costs on representative routes and devices.',
            ],
        ],
        'process' => [
            [
                'Map content and routes',
                'Identify public pages, account features, current URLs and publishing responsibilities.',
            ],
            [
                'Choose rendering boundaries',
                'Define server/client behavior, backend ownership and hosting requirements.',
            ],
            [
                'Model CMS and data flows',
                'Agree structured content, previews, data access and freshness rules.',
            ],
            [
                'Build connected routes',
                'Implement layouts, interactions and integrations with working content and reviewable milestones.',
            ],
            [
                'Verify delivery behavior',
                'Test metadata, redirects, publishing updates, permissions and representative performance.',
            ],
            [
                'Deploy and document',
                'Release with environment notes, a rollback plan and guidance for editors and maintainers.',
            ],
        ],
        'why_heading' => 'Next.js Decisions Connected to Publishing and Operations',
        'why_paragraphs' => [
            'We treat rendering, content structure and interface design as related decisions. That helps avoid a website that looks polished but leaves editors uncertain about publishing or developers guessing about data freshness.',
            'Chulbul Design works with businesses worldwide from Gurugram. Shared previews and written route, content and deployment decisions make remote reviews practical for both marketing teams and technical stakeholders.',
        ],
        'reasons' => [
            [
                'bi-signpost',
                'Intentional routes',
                'Public content and account features have clear boundaries.',
            ],
            [
                'bi-pencil-square',
                'Editor-aware integration',
                'Preview and publishing behavior are part of the scope.',
            ],
            [
                'bi-arrow-repeat',
                'Explicit freshness',
                'Caching is discussed as a product requirement.',
            ],
            [
                'bi-journal-code',
                'Operable delivery',
                'The handover includes environment and release decisions.',
            ],
        ],
        'proof_text' => 'For a Next.js project, we can discuss the route, publishing and rendering decisions relevant to your requirement. Our wider portfolio is context, not proof that every site uses Next.js; we identify the implementation evidence and any technology-partner role we can substantiate.',
        'cost_intro' => 'Next.js development cost depends on the route types, rendering requirements, CMS workflow and existing backend. A content website with predictable publishing differs from an account-based product with personalized data and complex integrations.',
        'cost_factors' => [
            [
                'Route and layout types',
                'Distinct page structures, account areas and error states.',
            ],
            [
                'CMS requirements',
                'Content models, editorial previews, migrations and publishing access.',
            ],
            [
                'Rendering and freshness',
                'Server/client boundaries, personalization and cache behavior.',
            ],
            [
                'Integrations',
                'Backend APIs, authentication, commerce and third-party services.',
            ],
            [
                'Migration and SEO checks',
                'Existing URL preservation, redirects, metadata and rendered-content validation.',
            ],
            [
                'Deployment and maintenance',
                'Runtime needs, hosting, monitoring and planned framework updates.',
            ],
        ],
        'faqs' => [
            [
                'What is the difference between React and Next.js development?',
                'React supplies the interface building model. Next.js is a framework built on React that also addresses routing, rendering and other application concerns. We choose those capabilities according to the website and its operating needs.',
            ],
            [
                'Is Next.js suitable for a company website?',
                'It can be, especially when the site needs structured content, custom interfaces or connections to a headless CMS. A simpler CMS or server-rendered approach may be more economical for other requirements.',
            ],
            [
                'Can Next.js connect to our existing backend?',
                'Yes. A Laravel, Node.js or other backend can continue to own business data and rules. We agree API contracts, authentication and which responsibilities belong in Next.js before implementation.',
            ],
            [
                'Can editors update content without changing code?',
                'Yes, when the project includes a suitable CMS and publishing workflow. We define content fields, preview access and how publication updates the site instead of assuming every page must be maintained in source files.',
            ],
            [
                'Will Next.js improve our Google rankings?',
                'It can support technical requirements such as rendered content and route metadata, but rankings are not guaranteed. Useful content, internal links, search intent and other signals remain important.',
            ],
            [
                'How do you prevent customers seeing stale or private data?',
                'We classify data by access and freshness requirements, then test the relevant fetching and caching behavior. Sensitive operations require server-side authorization, and personalized data must not be treated like shared public content.',
            ],
            [
                'Does Next.js require one particular hosting provider?',
                'No single provider is required for every project. The deployment must support the features used by the application. We assess runtime, caching, environment and operational needs before recommending hosting.',
            ],
            [
                'Can you migrate our current site without changing its URLs?',
                'Existing routes can often be retained. We inventory important pages and map any required changes before migration, then test redirects, metadata and content delivery after the release.',
            ],
            [
                'What determines Next.js development cost?',
                'The main factors are route complexity, CMS and preview needs, personalized behavior, integrations and migration requirements. Hosting, paid services and ongoing maintenance are separated from the initial implementation scope.',
            ],
            [
                'Do you maintain existing Next.js applications?',
                'Yes, after reviewing the version, dependencies, routing, data behavior and deployment process. Updates are planned around compatibility and regression checks rather than applied directly to production without review.',
            ],
        ],
        'cta_heading' => 'Planning a Next.js Website or a Better Content Workflow?',
        'cta_text' => 'Share your current website, CMS and the features you need. We will help define the routes, rendering approach and publishing setup that fit the project.',
    ],
];

foreach ($pages as &$page) {
    $page['hero_badge'] = $page['name'] . ' Services';
    $page['process'] = array_map(static fn(array $step, int $index): array => [
        'num' => str_pad((string)($index + 1), 2, '0', STR_PAD_LEFT),
        'icon' => 'bi-check2-circle',
        'title' => $step[0],
        'desc' => $step[1],
    ], $page['process'], array_keys($page['process']));
    $page['faqs'] = array_map(static fn(array $faq): array => ['q' => $faq[0], 'a' => $faq[1]], $page['faqs']);
}
unset($page);

return $pages;

