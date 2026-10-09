<?php
return [
    'name' => 'Dashboard UI Design',
    'platform' => 'dashboard interface design',
    'icon' => 'bi-speedometer2',
    'meta_title' => 'Dashboard UI Design for SaaS & Admin Panels | Chulbul Design',
    'meta_desc' => 'Dashboard UI design for SaaS products, admin panels and internal tools. Make tables, metrics, filters and operational workflows clearer with Chulbul Design.',
    'hero_badge' => 'SaaS, Admin & Data-Heavy Interface Design',
    'hero_h1' => 'Dashboard UI Design',
    'hero_highlight' => 'For Decisions, Not Just Data',
    'hero_desc' => 'Help teams see what matters and take the right action. We design dashboards and admin interfaces around user roles, clear data definitions and real operational workflows—not a screen filled with decorative charts.',
    'hero_img' => '/assets/images/service-heroes/crm-development-hero.webp',
    'hero_img_alt' => 'Illustration of a customer management dashboard connected to records, workflow stages and analytics',
    'hero_cta' => 'Discuss Your Dashboard',
    'browser_label' => 'roles / data / useful actions',
    'chips' => [
        [
            'bi-bar-chart',
            'Clear information',
        ],
        [
            'bi-list-check',
            'Task-focused flows',
        ],
    ],
    'intro_kicker' => 'Start with the decision behind the metric',
    'intro_heading' => 'What Should Someone Do After Opening the Dashboard?',
    'intro' => [
        'A dashboard can display a lot of information without helping anyone decide what to do. Teams need to understand which records need attention, what a metric means and where to act. The design starts with those tasks rather than choosing a chart style for every available number.',
        'We work through roles, data definitions and the workflows that sit behind the screen. A manager\'s overview and an operator\'s daily queue may need different levels of detail. Tables, filters, charts and actions are organized around the questions each person needs to answer.',
        'Chulbul Design supports dashboard interface work for SaaS products, internal tools and customer portals worldwide. The scope can cover new screens or improvements to an existing system. UI design is separated from backend development, analytics engineering and live data integrations unless those are explicitly included.',
    ],
    'cards_heading' => 'Dashboard and Admin UI Design Services',
    'cards_subtitle' => 'Design the overview and the operational detail as parts of one coherent experience.',
    'cards' => [
        [
            'bi-people',
            'Role-Based Information Design',
            'Identify the information and actions relevant to different user groups without treating visibility as backend security.',
        ],
        [
            'bi-speedometer2',
            'Overview Dashboards',
            'Arrange key signals around useful questions, priorities and routes to deeper detail.',
        ],
        [
            'bi-table',
            'Data Table Design',
            'Specify columns, sorting, density, selection and row actions for real records.',
        ],
        [
            'bi-funnel',
            'Filters and Search',
            'Make active filters, date ranges and result conditions understandable and recoverable.',
        ],
        [
            'bi-bar-chart',
            'Data Visualization',
            'Choose clear representations with meaningful labels, units and comparison context.',
        ],
        [
            'bi-list-check',
            'Operational Workflows',
            'Connect queues, record details, forms and actions into practical task journeys.',
        ],
        [
            'bi-bell',
            'Alerts and Status Views',
            'Explain what needs attention and what action is available without relying only on colour.',
        ],
        [
            'bi-exclamation-circle',
            'Empty and Failure States',
            'Design no-data, loading, partial-access and error situations alongside the main view.',
        ],
        [
            'bi-grid',
            'Reusable Dashboard Patterns',
            'Create coherent controls and components for related screens and future expansion.',
            '/figma-design',
        ],
        [
            'bi-journal-code',
            'Implementation Handoff',
            'Document data assumptions, responsive decisions and interaction states for developers.',
        ],
    ],
    'sections' => [
        [
            'h2' => 'Define the Users, Questions and Data Before the Layout',
            'para' => 'A dashboard brief should identify who uses the screen and what they need to decide. We review the tasks, frequency of use and the consequences of missing important information. That helps distinguish a strategic overview from a work queue or a detailed record view.

Data definitions matter as much as visual hierarchy. A total needs a time period and a unit; a status needs an agreed meaning. We work with your domain or data team to clarify those points rather than invent metrics because they look useful in a mockup.',
            'bullets' => [
                'User roles and frequent tasks identified',
                'Overview and operational needs separated',
                'Metric definitions and date context agreed',
                'Data availability and limitations recorded',
            ],
        ],
        [
            'h2' => 'Connect Summary Information With the Next Useful Action',
            'para' => 'People often need to move from a signal to the records behind it. A dashboard should make that path understandable, preserving relevant filters and explaining what the next view contains. Actions that change business data need clear feedback and appropriate safeguards.

We map those interactions with the underlying product team. The interface can show a role-specific experience, but actual access restrictions must be enforced by the application. Design notes explain intended behavior without claiming that a hidden button is a security control.',
            'bullets' => [
                'Clear routes from summaries to relevant records',
                'Filter and date context preserved where appropriate',
                'Confirmations and recovery for important actions',
                'Role-specific UI distinguished from server authorization',
            ],
        ],
        [
            'h2' => 'Make Dense Information Readable and Comparable',
            'para' => 'Tables and charts should reduce the work of interpreting data. We select the information needed for the task, then review labels, units, formatting and meaningful comparisons. A chart is useful when it explains a pattern more clearly than a small list or table.

Realistic examples expose problems that tidy demo data hides: long names, missing values, large counts and mixed statuses. We also consider keyboard use and alternatives to colour-only signals. The approved design specifies relevant responsive behavior instead of assuming a wide desktop table will simply shrink onto a phone.',
            'bullets' => [
                'Labels, units and comparisons made explicit',
                'Long, missing and unusual values included in reviews',
                'Charts used for a clear information purpose',
                'Responsive and accessibility needs documented',
            ],
        ],
        [
            'h2' => 'Plan for Data That Is Late, Empty or Unavailable',
            'para' => 'A dashboard is not always in its ideal state. A new account may have no records, a filter can return no matches and an external source may stop updating. People need to know whether there is nothing to show or whether the system could not retrieve the information.

We design the important conditions and explain data freshness where the product supports it. Handoff notes distinguish simulated examples from live behavior. Developers and domain owners review the assumptions before implementation, with design QA available to check the resulting screens.',
            'bullets' => [
                'First-use and no-results states distinguished',
                'Loading and failure behavior specified',
                'Freshness and missing-data messages considered',
                'Assumptions reviewed with implementation owners',
            ],
        ],
    ],
    'solutions_kicker' => 'Dashboards for different types of work',
    'solutions_heading' => 'Interface Design Around Real Operational Questions',
    'solutions_intro' => 'Examples of possible dashboard scopes, subject to the data and workflows your system can support.',
    'solutions' => [
        [
            'bi-cloud',
            'SaaS Workspaces',
            'Organize recurring product tasks and account-level information.',
        ],
        [
            'bi-person-lines-fill',
            'CRM Interfaces',
            'Connect contact records, queues and next actions.',
        ],
        [
            'bi-cart3',
            'Commerce Operations',
            'Clarify orders, fulfilment states and operational exceptions.',
        ],
        [
            'bi-cash-stack',
            'Business Reporting',
            'Present defined financial or commercial metrics with appropriate context.',
        ],
        [
            'bi-truck',
            'Logistics Views',
            'Show relevant status, exceptions and record-level follow-up.',
        ],
        [
            'bi-calendar-check',
            'Booking Administration',
            'Support availability, reservations and changes through clear workflows.',
        ],
        [
            'bi-people',
            'Team Management',
            'Organize tasks and staff information around relevant permissions.',
        ],
        [
            'bi-mortarboard',
            'Learning Administration',
            'Present participation and course information for the intended roles.',
        ],
        [
            'bi-chat-dots',
            'Support Workspaces',
            'Help teams prioritize requests and understand their current state.',
        ],
        [
            'bi-diagram-3',
            'Multi-Account Portals',
            'Separate account context and avoid confusing shared and local information.',
        ],
    ],
    'foundation_heading' => 'Meaningful Data, Useful Controls and Complete States',
    'foundation_intro' => 'A dashboard\'s usefulness depends on how these decisions fit together.',
    'foundation' => [
        [
            '01 / Meaning',
            'Information with a defined purpose',
            'Metrics and records answer specific questions for the intended role.',
            [
                'Roles',
                'Definitions',
                'Units',
                'Time periods',
            ],
        ],
        [
            '02 / Interaction',
            'Controls that support the task',
            'Navigation and actions connect an overview with the work behind it.',
            [
                'Filters',
                'Tables',
                'Drill-down',
                'Actions',
            ],
        ],
        [
            '03 / Reliability',
            'A clear explanation of every state',
            'People can understand whether data is loading, missing or unavailable.',
            [
                'Empty states',
                'Errors',
                'Freshness',
                'Feedback',
            ],
        ],
    ],
    'integration_core' => [
        'bi-speedometer2',
        'Dashboard UI',
    ],
    'integration_nodes' => [
        [
            'bi-people',
            'Roles',
        ],
        [
            'bi-database',
            'Data',
        ],
        [
            'bi-funnel',
            'Filters',
        ],
        [
            'bi-check2-circle',
            'Actions',
        ],
    ],
    'quality_heading' => 'Review the Dashboard With Realistic Scenarios',
    'quality_intro' => 'Design checks should cover interpretation and task completion, not just visual balance.',
    'quality' => [
        [
            'bi-bar-chart',
            'Clear interpretation',
            'Labels, units and comparison context explain what the numbers mean.',
        ],
        [
            'bi-table',
            'Useful density',
            'Tables prioritize relevant detail without hiding necessary information.',
        ],
        [
            'bi-universal-access',
            'Accessible intentions',
            'Controls and status signals are designed with more than pointer and colour use in mind.',
        ],
        [
            'bi-exclamation-circle',
            'Complete states',
            'Missing data, errors and restricted views have understandable behavior.',
        ],
    ],
    'process' => [
        [
            'Understand roles and tasks',
            'Agree who uses the interface and the decisions or actions it should support.',
        ],
        [
            'Review data and workflows',
            'Clarify metric definitions, sources, permissions and operational dependencies.',
        ],
        [
            'Map hierarchy and navigation',
            'Separate summary views from detailed work and define routes between them.',
        ],
        [
            'Design patterns and screens',
            'Develop tables, controls, visualizations and the relevant states.',
        ],
        [
            'Prototype and review',
            'Test representative tasks with realistic records and domain feedback.',
        ],
        [
            'Prepare implementation guidance',
            'Deliver approved designs, data assumptions and interaction notes, with QA if scoped.',
        ],
    ],
    'proof_text' => 'Ask to review a dashboard with realistic records and an explanation of the roles, data definitions and states it covers. A UI concept or wider website portfolio is not proof that a live analytics pipeline or backend workflow has been implemented.',
    'why_heading' => 'We Design the Work Around the Data',
    'why_paragraphs' => [
        'We ask what people need to understand and do before deciding how many charts belong on the screen. That keeps the scope grounded in operational use rather than visual density alone.',
        'Our handoff connects the interface with the data and development questions behind it. We make unsupported assumptions visible and distinguish design decisions from the engineering required to deliver reliable live behavior.',
    ],
    'reasons' => [
        [
            'bi-bullseye',
            'Task-led hierarchy',
            'Important information has a clear role.',
        ],
        [
            'bi-table',
            'Realistic records',
            'Reviews include difficult values and dense content.',
        ],
        [
            'bi-exclamation-circle',
            'State coverage',
            'The design accounts for non-ideal conditions.',
        ],
        [
            'bi-journal-code',
            'Clear assumptions',
            'Developers receive context about data and behavior.',
        ],
    ],
    'cost_intro' => 'Dashboard UI design cost depends on roles, workflows, data complexity and the range of states. A simple overview differs substantially from an operational product with record editing and multi-account permissions.',
    'cost_factors' => [
        [
            'Roles and contexts',
            'Different views, account scopes and permission-related states.',
        ],
        [
            'Workflow depth',
            'Queues, details, forms and record-changing actions.',
        ],
        [
            'Data complexity',
            'Metrics, tables, visualizations and comparison needs.',
        ],
        [
            'Component coverage',
            'Reusable controls and existing design-system integration.',
        ],
        [
            'Responsive and state scope',
            'Device requirements, empty views, errors and loading behavior.',
        ],
        [
            'Validation and handoff',
            'Domain reviews, prototypes and developer coordination.',
        ],
    ],
    'faqs' => [
        [
            'Is a dashboard just a page of charts?',
            'No. Some dashboards primarily support operational tables and actions. We choose the information and visual form based on what the user needs to decide or do.',
        ],
        [
            'Can you redesign our existing admin panel?',
            'Yes. We can review specific tasks and data-heavy screens, then agree targeted improvements or a wider interface scope.',
        ],
        [
            'Will you build the backend or connect live data?',
            'Not as part of UI design unless implementation is explicitly included. Data pipelines, APIs, calculations and application permissions are separate engineering responsibilities.',
        ],
        [
            'How do you choose which metrics to show?',
            'We work with your business and data owners to identify meaningful questions and agreed definitions. We do not invent metrics simply to fill the layout.',
        ],
        [
            'Can you design different views for different roles?',
            'Yes. We can specify role-appropriate screens and controls. The application still needs to enforce actual authorization on the server.',
        ],
        [
            'Will the dashboard work on mobile?',
            'The agreed scope defines mobile tasks and responsive behavior. Some dense workflows need a different presentation on small screens rather than a scaled-down desktop layout.',
        ],
        [
            'Do you include loading and error states?',
            'Yes, the relevant states are included in the defined screen scope. We distinguish no records, no filter matches, loading, partial information and failures where the product requires them.',
        ],
        [
            'Can you use our existing design system?',
            'Yes. We review its components and conventions first, then identify whether any new patterns or states need to be designed.',
        ],
        [
            'What should our team provide?',
            'User roles, key tasks, metric definitions, sample records and available system behavior. Sensitive production data should be replaced with suitable anonymized or representative examples.',
        ],
        [
            'How do you validate the design?',
            'We review representative tasks and realistic data with stakeholders or agreed participants. Implementation QA can be added to check whether the built interface follows the important design decisions.',
        ],
    ],
    'cta_heading' => 'What Should Your Dashboard Help People Decide?',
    'cta_text' => 'Share the user roles, important tasks and the data your system provides. We will help turn those requirements into a clear dashboard design scope.',
];

