<?php
return [
    'name' => 'AI Integration',
    'platform' => 'AI integration',
    'icon' => 'bi-cpu',
    'meta_title' => 'AI Integration Services for Websites & Apps | Chulbul Design',
    'meta_desc' => 'Add scoped AI features to your website, CRM or application with data controls, output checks and fallback behaviour. AI integration services for businesses worldwide.',
    'hero_badge' => 'Add useful intelligence to the tools you already use',
    'hero_h1' => 'AI Integration Services',
    'hero_highlight' => 'Built into Your Existing Workflow.',
    'hero_desc' => 'Bring document assistance, search, summaries or classification into your website or application. We connect the AI capability to a defined task, with clear data boundaries and a way to handle uncertain results.',
    'hero_img' => '/assets/images/service-heroes/web-development-hero.webp',
    'hero_img_alt' => 'Illustration of website code connected to databases, cloud services and application controls',
    'hero_cta' => 'Discuss an AI Feature',
    'browser_label' => 'ai-integration / application • model • validation',
    'chips' => [
        [
            'bi-plug',
            'Connected AI features',
        ],
        [
            'bi-shield-check',
            'Defined data boundaries',
        ],
    ],
    'intro_kicker' => 'A feature, not another disconnected tool',
    'intro_heading' => 'Put AI Where Your Team Already Works',
    'intro' => [
        'Useful AI integration starts with a specific task. An employee may need a short summary of a long request, a customer may need help finding a relevant document, or an editor may need a first draft to review. These are different product features, not one generic AI button.',
        'Chulbul Design provides AI integration services for websites, CRMs and custom applications. We review the current software and design a capability that fits its users, permissions and existing work.',
        'The integration includes more than a model call. We define the input, the expected output, validation, failure behaviour and operating cost. If a conventional search, rule or template would meet the need more simply, that remains an option.',
    ],
    'cards_heading' => 'What an AI Integration Project Can Cover',
    'cards_subtitle' => 'Choose a specific capability and evaluate it before extending its role in the application.',
    'cards' => [
        [
            'bi-bullseye',
            'Use-Case Definition',
            'Identify the user task, the expected benefit and the situations where an AI feature should not be used.',
        ],
        [
            'bi-plug',
            'Model API Integration',
            'Connect an approved provider through the application backend with appropriate access and error handling.',
        ],
        [
            'bi-search',
            'Knowledge Search',
            'Retrieve authorised source material to support answers or navigation within a defined information collection.',
        ],
        [
            'bi-file-earmark-text',
            'Document Assistance',
            'Prepare summaries or extract agreed fields from supported documents, with validation and review for uncertain results.',
        ],
        [
            'bi-tags',
            'Classification and Routing',
            'Suggest categories for incoming text and provide a review path for ambiguous or unmatched cases.',
        ],
        [
            'bi-pencil-square',
            'Drafting Features',
            'Generate a draft in the user\'s workflow while keeping publication or external sending under the agreed approval process.',
        ],
        [
            'bi-ui-checks',
            'Structured Output Checks',
            'Validate expected fields and formats before another system relies on the model\'s response.',
        ],
        [
            'bi-person-lock',
            'Permission-Aware Retrieval',
            'Limit accessible records according to the user\'s role and the source system\'s authorisation rules.',
        ],
        [
            'bi-speedometer2',
            'Latency and Cost Controls',
            'Set practical input, usage and timeout limits around the feature rather than leaving every request unbounded.',
        ],
        [
            'bi-clipboard-check',
            'Evaluation and Fallback',
            'Test representative examples and define what the interface does when the provider fails or the output is unsuitable.',
        ],
    ],
    'sections' => [
        [
            'h2' => 'Define the Feature Before Selecting the Model',
            'para' => 'We begin with the user\'s job and the output they can actually use. A customer-facing answer needs different checks from an internal draft, and a fixed set of form fields needs different validation from a free-form summary. Those requirements guide the technical choice.

A small evaluation set helps compare approaches on representative inputs. We review usefulness, response time and likely operating cost rather than selecting a model only because it is new. The proposed feature includes the cases where a person should remain responsible for the result.',
            'bullets' => [
                'User task and output defined',
                'Representative examples collected',
                'Success and failure criteria agreed',
                'Model choice tied to the task',
            ],
        ],
        [
            'h2' => 'Connect the Right Information with the Right Permissions',
            'para' => 'Before sending data to a provider, we identify what the feature needs and what it should not receive. The scope covers access rules, sensitive fields, retention expectations and the provider settings that must be reviewed for the intended use.

For knowledge-based features, retrieval should respect the source permissions. A document\'s availability somewhere in the business does not make it appropriate for every user. We design the access checks in the application rather than asking the model alone to decide who may see a record.',
            'bullets' => [
                'Minimum necessary input data',
                'Source permissions enforced',
                'Provider settings reviewed',
                'Retention responsibilities documented',
            ],
        ],
        [
            'h2' => 'Validate the Output Before It Changes Another System',
            'para' => 'A model may return a plausible answer that does not match the expected format or meaning. We define checks suitable for the feature, such as required fields, permitted categories or source references. Unusable output should reach a clear fallback rather than silently becoming a trusted record.

For consequential actions, the application needs its own approval and permission checks. External documents and user text are treated as input data, not as authority to change the feature\'s instructions. The model\'s response does not bypass normal business controls.',
            'bullets' => [
                'Expected output checked',
                'Uncertain cases sent for review',
                'Application-side permission controls',
                'External content treated as untrusted input',
            ],
        ],
        [
            'h2' => 'Keep the Feature Usable When the Provider Is Slow or Unavailable',
            'para' => 'The interface should not leave a user guessing while a request waits indefinitely. We agree loading states, timeouts and fallback behaviour that fit the task. A draft assistant may allow manual work to continue; a search feature may fall back to ordinary results.

The handover covers usage reporting, configuration ownership and evaluation after significant changes. Provider models, limits or commercial terms can change, so the system needs a maintenance plan. Replacing the provider is a design consideration, not a guarantee that every model can be exchanged without retesting.',
            'bullets' => [
                'Useful loading and error states',
                'Bounded requests and timeouts',
                'Manual or conventional fallback',
                'Retesting after material changes',
            ],
        ],
    ],
    'solutions_kicker' => 'Possible AI features',
    'solutions_heading' => 'Focused Capabilities for Existing Websites and Apps',
    'solutions_intro' => 'These examples describe potential features, subject to data access and evaluation.',
    'solutions' => [
        [
            'bi-search',
            'Knowledge Search',
            'Help authorised users locate relevant material within a defined document collection.',
        ],
        [
            'bi-file-text',
            'Request Summaries',
            'Create a reviewable summary of a long customer or internal request.',
        ],
        [
            'bi-tags',
            'Support Classification',
            'Suggest categories for tickets while allowing a person to correct the routing.',
        ],
        [
            'bi-pencil-square',
            'Draft Replies',
            'Prepare a response for team review without automatically sending it to a customer.',
        ],
        [
            'bi-ui-checks',
            'Document Field Extraction',
            'Propose structured fields from agreed document types and flag missing information.',
        ],
        [
            'bi-journal-text',
            'Editorial Assistance',
            'Support content preparation with drafts that remain subject to editorial checks.',
        ],
        [
            'bi-diagram-3',
            'CRM Assistance',
            'Surface an authorised record summary inside the workflow used by the team.',
        ],
        [
            'bi-translate',
            'Language Assistance',
            'Prepare translations for the agreed audience with suitable review of important wording.',
        ],
        [
            'bi-chat-square-text',
            'In-App Help',
            'Add contextual assistance using the documentation relevant to the user\'s current task.',
        ],
        [
            'bi-bar-chart',
            'Report Narratives',
            'Draft explanatory text from verified business data without inventing missing figures.',
        ],
    ],
    'foundation_heading' => 'Useful Input, Checked Output and a Responsible Owner',
    'foundation_intro' => 'The feature should remain a controlled part of your software.',
    'foundation' => [
        [
            'Input',
            'Authorised Context',
            'Define what data reaches the model and who can access the result.',
            [
                'Permissions',
                'Sources',
                'Redaction',
                'Retention',
            ],
        ],
        [
            'Output',
            'A Testable Contract',
            'Specify the format, acceptable behaviour and review path.',
            [
                'Fields',
                'Categories',
                'Evidence',
                'Fallback',
            ],
        ],
        [
            'Operations',
            'A Maintainable Feature',
            'Assign ownership of provider settings and ongoing quality checks.',
            [
                'Usage',
                'Latency',
                'Versions',
                'Evaluation',
            ],
        ],
    ],
    'integration_core' => [
        'bi-cpu',
        'AI feature',
    ],
    'integration_nodes' => [
        [
            'bi-window',
            'Application',
        ],
        [
            'bi-journal-text',
            'Context',
        ],
        [
            'bi-ui-checks',
            'Validation',
        ],
        [
            'bi-person-check',
            'Review',
        ],
    ],
    'quality_heading' => 'Evaluate the Feature as Part of the Product',
    'quality_intro' => 'A successful API response alone is not an acceptance test.',
    'quality' => [
        [
            'bi-check2-circle',
            'Useful Results',
            'Review representative outputs against the task\'s agreed criteria.',
        ],
        [
            'bi-person-lock',
            'Access Boundaries',
            'Test that one user\'s context cannot expose another user\'s records.',
        ],
        [
            'bi-exclamation-circle',
            'Invalid Responses',
            'Check missing fields, unsuitable content and provider failures.',
        ],
        [
            'bi-speedometer2',
            'Operating Limits',
            'Verify the intended input limits, timeouts and usage controls.',
        ],
    ],
    'process' => [
        [
            'Choose the Capability',
            'Define the task, users and expected output in the existing software.',
        ],
        [
            'Review Data and Access',
            'Map source permissions, privacy requirements and provider dependencies.',
        ],
        [
            'Evaluate an Approach',
            'Compare a small set of representative examples before committing to the build.',
        ],
        [
            'Integrate the Feature',
            'Implement the interface, backend connection and output checks.',
        ],
        [
            'Test the Boundaries',
            'Review normal, ambiguous, unauthorised and failure cases with your team.',
        ],
        [
            'Launch and Maintain',
            'Document settings, ownership and the plan for quality and usage review.',
        ],
    ],
    'proof_text' => 'Ask about the data boundary, evaluation examples and fallback design relevant to your application. Our broader website work should not be read as proof of a particular AI model implementation.',
    'why_heading' => 'AI Connected to Real Product Requirements',
    'why_paragraphs' => [
        'We look at the feature from the user\'s task through to the system receiving the output. That keeps interface behaviour, permissions and integration details in the same discussion.',
        'We distinguish a demonstration from a maintainable feature. Evaluation, error handling, usage costs and ongoing ownership are part of the scope rather than assumptions left for your team to discover later.',
    ],
    'reasons' => [
        [
            'bi-bullseye',
            'Task-first design',
            'Start with a defined capability and measurable acceptance.',
        ],
        [
            'bi-person-lock',
            'Data-aware implementation',
            'Keep access checks in the application.',
        ],
        [
            'bi-ui-checks',
            'Validated outputs',
            'Define what downstream systems may accept.',
        ],
        [
            'bi-arrow-repeat',
            'Ongoing evaluation',
            'Plan for changes to sources, models and usage.',
        ],
    ],
    'cost_intro' => 'AI integration pricing depends on the feature, source data, application architecture and evaluation requirements. The implementation quote is separate from ongoing model, storage and hosting usage.',
    'cost_factors' => [
        [
            'Existing application',
            'The codebase and available APIs affect how the feature is embedded.',
        ],
        [
            'Data preparation',
            'Document quality, permissions and source structure influence the work.',
        ],
        [
            'Output requirements',
            'Structured extraction and consequential actions need different checks from drafts.',
        ],
        [
            'User interface',
            'Review, correction and failure states require product work.',
        ],
        [
            'Request volume',
            'Input size and usage patterns influence provider and infrastructure costs.',
        ],
        [
            'Maintenance scope',
            'Source updates, provider changes and evaluation need an agreed owner.',
        ],
    ],
    'faqs' => [
        [
            'Can you add AI to our current website or CRM?',
            'Yes, where the application and permissions support an appropriate integration. We review the code or available API before recommending the implementation.',
        ],
        [
            'Do we need to build an AI model from scratch?',
            'Usually a project starts by evaluating an existing model for the task. Training or fine-tuning is considered only when the requirement and evidence justify a separate scope.',
        ],
        [
            'What is the difference between AI integration and a chatbot?',
            'AI integration can add search, extraction, drafting or classification inside software without a chat interface. A chatbot is one particular conversational experience.',
        ],
        [
            'Can the feature use our private documents?',
            'Only within an agreed data and access design. We review source permissions, provider handling and retention settings before connecting private material.',
        ],
        [
            'Will the provider use our information for training?',
            'That depends on the chosen provider, product and account terms. We review the applicable arrangement; this page does not promise a universal data-retention or training policy.',
        ],
        [
            'How do you reduce incorrect AI output?',
            'We define representative tests, suitable source context, output validation and human review where needed. These controls can reduce errors but cannot guarantee perfect results.',
        ],
        [
            'Can AI update records automatically?',
            'Only for explicitly approved actions with application-side checks. Sensitive or consequential changes should use the agreed review and permission process.',
        ],
        [
            'What happens if the AI service is unavailable?',
            'We design a fallback suitable for the task, such as manual completion, conventional search or a clear retry option. The application should not silently treat an incomplete request as success.',
        ],
        [
            'Can we change the model provider later?',
            'We can consider provider separation in the architecture. A change still needs compatibility, quality and cost testing; it is not necessarily a drop-in replacement.',
        ],
        [
            'What should we prepare for a first discussion?',
            'Describe the feature, the people using it, the software involved and a few safe sample inputs and desired outputs. Do not share credentials or sensitive customer records in an ordinary message.',
        ],
    ],
    'cta_heading' => 'Which AI Feature Would Make Your Existing Software More Useful?',
    'cta_text' => 'Show us the task and the result your team needs. We will help define a focused integration, its checks and its operating costs.',
];
