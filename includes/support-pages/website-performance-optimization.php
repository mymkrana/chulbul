<?php
return [
    'name' => 'Website Performance Optimization',
    'platform' => 'website performance optimization',
    'icon' => 'bi-graph-up-arrow',
    'meta_title' => 'Website Performance Optimization Services | Chulbul Design',
    'meta_desc' => 'Find bottlenecks across browser, API, database and hosting layers. Chulbul Design improves slow website workflows with measured, scoped performance engineering.',
    'hero_badge' => 'Performance across the whole journey',
    'hero_h1' => 'Website Performance Optimization',
    'hero_highlight' => 'Beyond the First Page Load.',
    'hero_desc' => 'A website can open quickly and still feel slow when customers search, sign in or submit a request. We trace the affected journey across the application and prioritise the bottlenecks behind it.',
    'hero_img' => '/assets/images/service-heroes/custom-software-development-hero.webp',
    'hero_img_alt' => 'Illustration of application analytics linked to code, database and cloud services',
    'hero_cta' => 'Discuss Your Performance Bottleneck',
    'browser_label' => 'website-performance / browser • API • database',
    'chips' => [
        [
            'bi-diagram-3',
            'End-to-end diagnosis',
        ],
        [
            'bi-graph-up-arrow',
            'Measured improvements',
        ],
    ],
    'intro_kicker' => 'Follow the whole request',
    'intro_heading' => 'Improve the Work Visitors Are Trying to Complete',
    'intro' => [
        'Performance problems do not always appear in a homepage test. A product search can wait on the database, an account screen can make too many API requests, or a background task can compete with customer activity. The visible delay may be only the final symptom.',
        'Our website performance optimization service investigates the agreed journey across browser behaviour, application code and the accessible delivery environment. We connect the reported problem to measurements before proposing changes.',
        'Chulbul Design works with business websites and web applications worldwide. This service is broader than loading-speed work and different from a focused Core Web Vitals assessment. It is suitable when the slow behaviour spans several systems or appears only during particular workflows.',
    ],
    'cards_heading' => 'Performance Work Across the Application',
    'cards_subtitle' => 'Investigation and implementation are selected for the bottleneck, with safe test limits agreed first.',
    'cards' => [
        [
            'bi-search',
            'Journey Profiling',
            'Reproduce the slow action and identify the time spent across the browser, application and accessible external calls.',
        ],
        [
            'bi-diagram-3',
            'Request and API Review',
            'Inspect repeated requests, payloads and slow dependencies that delay a user-facing workflow.',
        ],
        [
            'bi-database',
            'Database Investigation',
            'Review accessible query behaviour and data access patterns before recommending indexes, pagination or code changes.',
        ],
        [
            'bi-code-slash',
            'Application Efficiency',
            'Investigate repeated work and expensive processing in the code paths supporting the affected journey.',
        ],
        [
            'bi-hdd-network',
            'Cache Strategy',
            'Consider safe reuse of public or computed results, with clear invalidation and private-data boundaries.',
        ],
        [
            'bi-list-task',
            'Background Work',
            'Review tasks that may not need to block a user response, including their failure and retry behaviour.',
        ],
        [
            'bi-cloud',
            'Hosting Resource Review',
            'Check available resource and runtime evidence before attributing every delay to insufficient hosting.',
        ],
        [
            'bi-puzzle',
            'External Dependency Handling',
            'Identify slow vendor calls and discuss timeouts, fallback behaviour or workflow changes within the integration\'s limits.',
        ],
        [
            'bi-activity',
            'Controlled Load Testing',
            'Define an authorised environment, traffic ceiling and stop conditions when capacity testing is part of the scope.',
        ],
        [
            'bi-file-earmark-bar-graph',
            'Performance Reporting',
            'Report comparable measurements, changes and remaining constraints without inventing a universal capacity promise.',
        ],
    ],
    'sections' => [
        [
            'h2' => 'Define the Slow Journey in Business Terms',
            'para' => '“The website is slow” is a starting point, not a test case. We establish which action is delayed, who experiences it and whether the problem changes with the account, data size or time of day. A reproducible example makes investigation more efficient.

We then define the measurement and acceptance conditions with your team. That might be search response time, report generation or the delay before an account screen becomes usable. Improvements are judged against the agreed workflow, not only against an unrelated page-speed score.',
            'bullets' => [
                'Affected action described clearly',
                'Representative test data agreed',
                'Baseline conditions recorded',
                'Success criteria tied to the workflow',
            ],
        ],
        [
            'h2' => 'Trace the Delay Across Browser, API and Database',
            'para' => 'A slow screen can result from several individually small delays. The browser may request information sequentially, the application may repeat a lookup, and an external service may keep the response waiting. We trace the accessible path instead of optimising the first suspicious line of code.

Recommendations identify the responsible layer and the evidence supporting the change. Where logs or source access are unavailable, we explain the diagnostic limit and what the relevant provider would need to investigate. We do not treat a guess about hosting as a confirmed root cause.',
            'bullets' => [
                'Browser request sequence reviewed',
                'Application work measured',
                'Data access patterns investigated',
                'External ownership and limits recorded',
            ],
        ],
        [
            'h2' => 'Improve Efficiency Without Changing Correct Results',
            'para' => 'A faster response is not useful if it returns stale account information, misses records or duplicates an action. Cache rules, query changes and background processing need verification of correctness as well as timing. We define those checks around the affected data and user roles.

Changes are staged and released with a recovery route appropriate to the application. Testing uses approved non-sensitive sample data wherever possible. Actions that could send messages, create real orders or charge a customer require separate explicit approval.',
            'bullets' => [
                'Correctness checked alongside timing',
                'Private-data cache boundaries',
                'Approved sample data',
                'Recovery route for code changes',
            ],
        ],
        [
            'h2' => 'Plan Capacity Checks Without Disrupting Operations',
            'para' => 'If performance drops during busy periods, a controlled load test may help reproduce the problem. It is not a default action against a live website. We agree the environment, permitted traffic, excluded services and stop conditions before generating test activity.

The result describes the tested workload and its limits, not a claim that the site can handle any future audience. We can also define lightweight regression checks so later releases are compared with the same baseline. Ongoing monitoring and infrastructure costs are separately agreed.',
            'bullets' => [
                'Explicit load-test authorisation',
                'Bounded traffic and stop conditions',
                'Third-party services excluded unless approved',
                'Workload and limits documented',
            ],
        ],
    ],
    'solutions_kicker' => 'Where broader performance work fits',
    'solutions_heading' => 'For Slow Journeys a Page Score Cannot Explain',
    'solutions_intro' => 'Examples of possible investigation scopes, not claims of completed work or promised capacity.',
    'solutions' => [
        [
            'bi-search',
            'Slow Website Search',
            'Trace catalogue or content searches across the request and data-access path.',
        ],
        [
            'bi-funnel',
            'Heavy Catalogue Filters',
            'Investigate how filter combinations affect API payloads, queries and browser updates.',
        ],
        [
            'bi-person-circle',
            'Member Account Areas',
            'Review delayed account screens using authorised roles and safe sample data.',
        ],
        [
            'bi-bar-chart',
            'Reporting Dashboards',
            'Identify repeated calculations, requests or rendering work behind sluggish reports.',
        ],
        [
            'bi-calendar-check',
            'Booking Workflows',
            'Trace availability checks and external dependencies that delay the next step.',
        ],
        [
            'bi-cart3',
            'Store Workflows',
            'Investigate agreed cart or account actions without creating live transactions by default.',
        ],
        [
            'bi-cloud-arrow-up',
            'Import and Export Tasks',
            'Review long-running jobs and their impact on normal website use.',
        ],
        [
            'bi-code-square',
            'Custom Web Applications',
            'Profile the specific routes and actions your team reports as slow.',
        ],
        [
            'bi-people',
            'Busy-Period Slowdowns',
            'Plan a bounded investigation of resource contention and representative workload.',
        ],
        [
            'bi-arrow-repeat',
            'Performance Regressions',
            'Compare a recent release against a known baseline to identify changed behaviour.',
        ],
    ],
    'foundation_heading' => 'Measure the Journey, the System and the Result',
    'foundation_intro' => 'A useful performance project explains both the gain and the conditions under which it was observed.',
    'foundation' => [
        [
            'Experience',
            'The User\'s Task',
            'Define the action, role and data behind the reported delay.',
            [
                'Search',
                'Accounts',
                'Reports',
                'Submissions',
            ],
        ],
        [
            'System',
            'The Work Behind It',
            'Measure the components in the accessible request path.',
            [
                'Browser',
                'API',
                'Database',
                'Runtime',
            ],
        ],
        [
            'Validation',
            'Correct and Repeatable',
            'Confirm the result remains accurate under the agreed test conditions.',
            [
                'Timing',
                'Correctness',
                'Workload',
                'Regression',
            ],
        ],
    ],
    'integration_core' => [
        'bi-diagram-3',
        'User journey',
    ],
    'integration_nodes' => [
        [
            'bi-window',
            'Browser',
        ],
        [
            'bi-code-slash',
            'API',
        ],
        [
            'bi-database',
            'Database',
        ],
        [
            'bi-cloud',
            'Hosting',
        ],
    ],
    'quality_heading' => 'Faster Must Also Mean Correct and Reliable',
    'quality_intro' => 'Verification covers the behaviour that the performance change could affect.',
    'quality' => [
        [
            'bi-check2-circle',
            'Correct Results',
            'Compare expected records and outcomes before and after the change.',
        ],
        [
            'bi-person-lock',
            'Access Boundaries',
            'Check that reuse or caching does not expose another user\'s information.',
        ],
        [
            'bi-arrow-repeat',
            'Failure Behaviour',
            'Review relevant timeouts, retries and duplicate-action risks.',
        ],
        [
            'bi-graph-up-arrow',
            'Comparable Measures',
            'Document workload, environment and variation rather than a best-case result alone.',
        ],
    ],
    'process' => [
        [
            'Describe the Bottleneck',
            'Share a specific slow action, affected users and recent changes.',
        ],
        [
            'Agree Access and Tests',
            'Define the environment, sample data and permitted diagnostic activity.',
        ],
        [
            'Profile the Journey',
            'Trace the request and identify the most significant supported findings.',
        ],
        [
            'Prioritise Improvements',
            'Agree code, query or configuration changes and their acceptance checks.',
        ],
        [
            'Implement and Verify',
            'Test timing, correctness and affected workflows before an approved release.',
        ],
        [
            'Document and Watch',
            'Hand over the findings and any agreed regression or monitoring plan.',
        ],
    ],
    'proof_text' => 'Ask for a discussion of the profiling method and acceptance criteria appropriate to your application. Portfolio work does not establish a specific throughput or response-time result for your site.',
    'why_heading' => 'Performance Work with a Clear Reason for Each Change',
    'why_paragraphs' => [
        'We treat performance as a connected engineering problem, not a list of settings to toggle. The diagnosis should explain which component creates the delay and why the proposed change is worth the effort.',
        'Our work keeps business behaviour and operational safety in view. We agree the test boundary, protect normal customer activity and explain when an issue requires the hosting provider or another integration owner.',
    ],
    'reasons' => [
        [
            'bi-diagram-3',
            'Connected diagnosis',
            'Follow the delay through the accessible request path.',
        ],
        [
            'bi-clipboard-check',
            'Agreed acceptance',
            'Define what a useful improvement looks like.',
        ],
        [
            'bi-shield-check',
            'Bounded testing',
            'Keep diagnostic traffic and side effects controlled.',
        ],
        [
            'bi-file-earmark-text',
            'Actionable handover',
            'Record the change, evidence and remaining limits.',
        ],
    ],
    'cost_intro' => 'Performance optimization is scoped around investigation depth and implementation complexity. A repeatable slow query may be a small project; an intermittent cross-system delay needs a broader diagnostic plan.',
    'cost_factors' => [
        [
            'Journey coverage',
            'Each distinct action or role can require a separate test case.',
        ],
        [
            'Source and telemetry access',
            'The available code, logs and measurements affect diagnostic effort.',
        ],
        [
            'Data complexity',
            'Large or connected datasets influence query and correctness checks.',
        ],
        [
            'Integration dependencies',
            'Third-party response times and contracts can constrain changes.',
        ],
        [
            'Capacity testing',
            'Test environments, workload preparation and safety controls require their own scope.',
        ],
        [
            'Monitoring and follow-up',
            'Ongoing observation is separate from a one-time investigation and fix.',
        ],
    ],
    'faqs' => [
        [
            'How is this different from website speed optimization?',
            'Speed optimization concentrates on page loading and delivery. This service can follow slow actions through APIs, databases, application code and hosting resources after the page has already opened.',
        ],
        [
            'Does this include Core Web Vitals?',
            'Web Vitals may provide useful context, but this scope is broader. If LCP, INP and CLS are your main concern, our dedicated Core Web Vitals page explains that focused service.',
        ],
        [
            'Can you fix a slow database?',
            'We can investigate authorised database access and query behaviour within the scope. The appropriate fix depends on the cause and may involve application code, query structure or data access changes.',
        ],
        [
            'Will you recommend a bigger server immediately?',
            'No. We first look for evidence of the bottleneck. More resources may help some workloads, but they do not automatically resolve inefficient queries or repeated application work.',
        ],
        [
            'Can you test how much traffic the site can handle?',
            'Controlled capacity testing can be separately included. We need explicit authorisation, an agreed environment, bounded traffic and stop conditions before generating load.',
        ],
        [
            'Will testing create real orders or send messages?',
            'Not by default. We use safe sample data and test environments where possible. Any action with real customer or financial side effects requires specific approval.',
        ],
        [
            'Can you guarantee a response time for every visitor?',
            'No universal response time can be promised. We agree measurable goals for defined environments and workloads, and explain factors such as networks and third-party services outside our control.',
        ],
        [
            'Do you need access to the source code?',
            'Code and relevant measurements are often important for a deeper investigation. We can start from reported symptoms, but limited access may restrict how confidently a cause can be identified or fixed.',
        ],
        [
            'Can you work with our existing developer?',
            'Yes. We can provide a scoped diagnosis, implement agreed changes or coordinate with your team. Ownership of each recommendation and release decision is confirmed first.',
        ],
        [
            'How do we stop performance getting worse later?',
            'We can define a small set of repeatable checks and monitoring responsibilities around the critical journeys. Those checks need to evolve with the application and do not replace ongoing review.',
        ],
    ],
    'cta_heading' => 'Tell Us Which Part of Your Website Feels Slow',
    'cta_text' => 'Share the action, the affected users and any recent change. We will help define a focused investigation across the systems behind that experience.',
];
