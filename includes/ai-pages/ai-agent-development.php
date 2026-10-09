<?php
return [
    'name' => 'AI Agent Development',
    'platform' => 'AI agent development',
    'icon' => 'bi-robot',
    'meta_title' => 'AI Agent Development Services for Business | Chulbul Design',
    'meta_desc' => 'Build task-focused AI agents with scoped tools, approval gates, evaluation and usage limits. Chulbul Design develops controlled business agents for teams worldwide.',
    'hero_badge' => 'Useful initiative. Defined authority.',
    'hero_h1' => 'AI Agent Development',
    'hero_highlight' => 'With Boundaries Your Business Controls.',
    'hero_desc' => 'Build an assistant that can work through a defined task using approved tools. We design the objective, permissions, review points and stop conditions before giving an agent access to business systems.',
    'hero_img' => '/assets/images/service-heroes/custom-software-development-hero.webp',
    'hero_img_alt' => 'Illustration of application tools, data connections and role-based access',
    'hero_cta' => 'Discuss a Task-Focused Agent',
    'browser_label' => 'ai-agent / objective • tools • approval',
    'chips' => [
        [
            'bi-tools',
            'Scoped tools',
        ],
        [
            'bi-person-check',
            'Approval checkpoints',
        ],
    ],
    'intro_kicker' => 'More than a conversational answer',
    'intro_heading' => 'Give an Agent a Useful Task, Not Unlimited Access',
    'intro' => [
        'Some tasks need more than a fixed sequence of steps. An assistant might need to locate relevant information, compare records and prepare a recommended next action based on what it finds. An agent can help with that flexible path, provided its authority is clearly limited.',
        'Chulbul Design offers AI agent development for business-specific use cases. We start with the outcome, the available tools and the actions the agent must not take, then evaluate whether an agent is the right approach at all.',
        'Agent behaviour can be unpredictable, and mistakes can compound across steps. The implementation therefore needs application-side permission checks, evaluation and a clear way to stop or escalate. We do not promise a fully autonomous replacement for your team.',
    ],
    'cards_heading' => 'What a Business Agent Project Can Include',
    'cards_subtitle' => 'The initial scope should be narrow enough to test, review and operate responsibly.',
    'cards' => [
        [
            'bi-bullseye',
            'Task and Success Definition',
            'Specify the requested outcome, required evidence and conditions that mean the agent should stop without completing it.',
        ],
        [
            'bi-tools',
            'Tool Interface Design',
            'Expose only the approved operations with clear inputs, outputs and permission boundaries.',
        ],
        [
            'bi-person-lock',
            'Scoped Access',
            'Separate read access from write actions and preserve the permissions of the user or process requesting the work.',
        ],
        [
            'bi-journal-text',
            'Knowledge Retrieval',
            'Connect authorised sources and keep retrieved content separate from the rules governing the agent.',
        ],
        [
            'bi-person-check',
            'Approval Checkpoints',
            'Require review before agreed consequential actions such as sending messages or changing important records.',
        ],
        [
            'bi-list-task',
            'Task State Tracking',
            'Record what has happened and what remains pending so a paused run can be reviewed accurately.',
        ],
        [
            'bi-shield-check',
            'Untrusted-Input Handling',
            'Treat documents, web content and tool output as data that may contain misleading or malicious instructions.',
        ],
        [
            'bi-speedometer2',
            'Usage and Execution Limits',
            'Bound the time, number of steps and spending available to a task.',
        ],
        [
            'bi-clipboard-check',
            'Scenario Evaluation',
            'Test representative success, failure and misuse cases before expanding access.',
        ],
        [
            'bi-journal-check',
            'Operational Handover',
            'Document permissions, logs, review responsibilities and the controls used to pause or disable the agent.',
        ],
    ],
    'sections' => [
        [
            'h2' => 'Decide Whether the Task Needs an Agent',
            'para' => 'A fixed approval flow or a single model response may be easier to test and maintain than an agent. We examine the task\'s variability before adding a system that chooses its next step. Flexibility is useful only when it helps achieve a defined result.

We collect representative examples and agree how the result will be assessed. The scope identifies when the assistant can continue, when it should ask for information and when it should return an incomplete outcome. Completing a task is not permission to invent missing facts or authority.',
            'bullets' => [
                'Simpler approaches considered',
                'Representative tasks reviewed',
                'Success evidence defined',
                'Incomplete outcomes handled honestly',
            ],
        ],
        [
            'h2' => 'Give Tools Clear Permissions and Safe Defaults',
            'para' => 'The agent should see only the operations needed for the approved task. A tool that retrieves a record is different from one that edits it, sends an email or initiates a payment. We separate those capabilities and enforce the relevant checks in application code.

Where an action needs review, the approval should show the proposed target and change before execution. A prompt asking the model to be careful is not a substitute for a permission boundary. Access can be expanded after evaluation rather than granted broadly at the start.',
            'bullets' => [
                'Read and write tools separated',
                'Explicit action targets',
                'Application-side permission checks',
                'Review before consequential changes',
            ],
        ],
        [
            'h2' => 'Keep External Information from Becoming New Instructions',
            'para' => 'An agent may encounter a document, message or tool response that contains instructions unrelated to the user\'s task. We treat that material as untrusted content and design the integration so it cannot automatically redefine the objective or grant access to other systems.

No single guardrail eliminates every failure. We combine constrained tool interfaces, source-aware data handling and tests using misleading or hostile inputs. If the agent cannot proceed within the boundary, it should pause and explain the issue instead of reaching for wider access.',
            'bullets' => [
                'External content treated as data',
                'No authority from retrieved instructions',
                'Bounded tool arguments',
                'Suspicious cases stopped or escalated',
            ],
        ],
        [
            'h2' => 'Make Multi-Step Work Reviewable and Recoverable',
            'para' => 'A run that stops halfway needs more than an error message. We record the relevant actions and tool outcomes so an operator can tell what happened, what was not completed and whether any action is safe to retry. Sensitive data in those records is limited according to the agreed retention policy.

The operating plan includes execution limits, a pause control and responsibility for reviewing failures. Changes to models, tools or source material should trigger suitable retesting. Ongoing support is scoped separately from the initial pilot and build.',
            'bullets' => [
                'Action and outcome records',
                'Partial completion visible',
                'Bounded runtime and usage',
                'Pause and review controls',
            ],
        ],
    ],
    'solutions_kicker' => 'Possible bounded agent tasks',
    'solutions_heading' => 'Start with Work Your Team Can Review',
    'solutions_intro' => 'These examples are candidate scopes, not promises of autonomous completion or existing deployments.',
    'solutions' => [
        [
            'bi-journal-text',
            'Knowledge Research',
            'Locate authorised internal material and assemble an evidence-linked briefing for review.',
        ],
        [
            'bi-headset',
            'Support Assistance',
            'Investigate an issue using approved records and propose a next step to a human agent.',
        ],
        [
            'bi-diagram-3',
            'CRM Preparation',
            'Review accessible lead context and draft a proposed record update without applying it automatically.',
        ],
        [
            'bi-files',
            'Document Comparison',
            'Compare supported documents and flag differences for a person to evaluate.',
        ],
        [
            'bi-list-check',
            'Checklist Review',
            'Gather evidence against a defined operational checklist and report missing items.',
        ],
        [
            'bi-bar-chart',
            'Report Preparation',
            'Collect verified inputs and draft a summary while distinguishing unavailable data.',
        ],
        [
            'bi-folder2-open',
            'Project Context Assembly',
            'Find relevant authorised records and organise a briefing for the project owner.',
        ],
        [
            'bi-envelope-paper',
            'Draft Follow-Up',
            'Prepare a contextual message and wait for approval before any external send.',
        ],
        [
            'bi-search',
            'Record Investigation',
            'Follow a bounded inquiry across approved systems and return the evidence found.',
        ],
        [
            'bi-tools',
            'Operations Assistance',
            'Recommend an action from observed task state while preserving human authority over consequential changes.',
        ],
    ],
    'foundation_heading' => 'Objective, Authority and Oversight',
    'foundation_intro' => 'An agent should remain inside a system your business can understand and control.',
    'foundation' => [
        [
            'Objective',
            'A Bounded Outcome',
            'Define the result and evidence needed before a run can be considered complete.',
            [
                'Task',
                'Evidence',
                'Questions',
                'Stop rules',
            ],
        ],
        [
            'Authority',
            'Only the Necessary Tools',
            'Restrict available operations and enforce access outside the model.',
            [
                'Read scope',
                'Write scope',
                'Approvals',
                'Validation',
            ],
        ],
        [
            'Oversight',
            'Visible Operation',
            'Make progress, limits and incomplete work reviewable.',
            [
                'Logs',
                'Budgets',
                'Pause',
                'Evaluation',
            ],
        ],
    ],
    'integration_core' => [
        'bi-robot',
        'Task agent',
    ],
    'integration_nodes' => [
        [
            'bi-bullseye',
            'Objective',
        ],
        [
            'bi-tools',
            'Tools',
        ],
        [
            'bi-person-check',
            'Approval',
        ],
        [
            'bi-check2-circle',
            'Evidence',
        ],
    ],
    'quality_heading' => 'Evaluate Behaviour Before Expanding Access',
    'quality_intro' => 'The pilot tests not only what the agent achieves, but what it refuses or escalates.',
    'quality' => [
        [
            'bi-bullseye',
            'Task Completion',
            'Compare outcomes with the agreed evidence and acceptance criteria.',
        ],
        [
            'bi-person-lock',
            'Permission Limits',
            'Test attempts to read or change records outside the approved boundary.',
        ],
        [
            'bi-shield-check',
            'Misleading Inputs',
            'Check responses to untrusted instructions embedded in source content.',
        ],
        [
            'bi-stop-circle',
            'Controlled Failure',
            'Verify step limits, unavailable tools, rejected approvals and partial outcomes.',
        ],
    ],
    'process' => [
        [
            'Define the Task',
            'Agree the user, outcome, evidence and actions that must remain prohibited.',
        ],
        [
            'Design the Tools',
            'Map authorised sources, operations and approval requirements.',
        ],
        [
            'Build a Narrow Prototype',
            'Implement a limited task path with sample data and restricted tools.',
        ],
        [
            'Evaluate Realistic Scenarios',
            'Test ordinary, ambiguous, incomplete and adversarial inputs.',
        ],
        [
            'Pilot with Oversight',
            'Run the agreed workload under review with usage and execution limits.',
        ],
        [
            'Release and Maintain',
            'Hand over controls, permissions and the plan for ongoing evaluation.',
        ],
    ],
    'proof_text' => 'Ask about the tool boundary, evaluation cases and approval design relevant to your task. Our wider website portfolio does not demonstrate an autonomous-agent deployment or guarantee an agent\'s results.',
    'why_heading' => 'Agent Development with an Operating Plan',
    'why_paragraphs' => [
        'We focus on the agent\'s role inside your business software, including the person who reviews its work. A convincing demonstration is not enough if nobody can explain its permissions or stop a failing run.',
        'We distinguish recommendations from authorised actions and treat uncertain outcomes as part of the design. The proposal covers the pilot, production boundaries and ongoing operating responsibilities without implying unlimited autonomy.',
    ],
    'reasons' => [
        [
            'bi-bullseye',
            'Narrow starting scope',
            'Choose a task with clear acceptance criteria.',
        ],
        [
            'bi-key',
            'Explicit authority',
            'Restrict tools and consequential actions.',
        ],
        [
            'bi-clipboard-check',
            'Scenario-based evaluation',
            'Test limitations as well as successful paths.',
        ],
        [
            'bi-stop-circle',
            'Operational controls',
            'Give the team a way to pause and review work.',
        ],
    ],
    'cost_intro' => 'AI agent development cost depends on the number of tools, the variability of the task and the strength of the evaluation and approval requirements. A narrow read-only assistant differs from a system allowed to perform business actions.',
    'cost_factors' => [
        [
            'Task variability',
            'Open-ended work needs more scenarios and explicit stopping conditions.',
        ],
        [
            'Tool interfaces',
            'Each connected operation requires permission and result-handling design.',
        ],
        [
            'Data permissions',
            'Private sources and multiple user roles increase access-control work.',
        ],
        [
            'Approval design',
            'Human review screens and action previews add product requirements.',
        ],
        [
            'Runtime usage',
            'Multi-step runs can increase model, tool and infrastructure costs.',
        ],
        [
            'Ongoing evaluation',
            'Changes to sources, tools or models require a maintenance arrangement.',
        ],
    ],
    'faqs' => [
        [
            'How is an AI agent different from a chatbot?',
            'A chatbot mainly provides a conversational experience. An agent may choose steps and use tools to work towards a task outcome. Either can have a chat interface, so the permissions and behaviour matter more than the label.',
        ],
        [
            'Does every automation need an agent?',
            'No. A fixed workflow or a single AI feature may be simpler and more reliable for a defined task. We assess whether flexible step selection adds enough value to justify the additional complexity.',
        ],
        [
            'Can an agent work without asking us every time?',
            'Only within explicitly approved boundaries. Read-only or low-impact actions may run under a defined policy; consequential actions should use the agreed approval gates.',
        ],
        [
            'Can the agent send emails or change CRM records?',
            'Those capabilities can be scoped as separate tools with application-side checks. We do not enable external sending or important record changes simply because a model suggests them.',
        ],
        [
            'How do you handle prompt injection?',
            'We treat external content as untrusted, constrain tools and arguments, and test misleading inputs. These controls reduce exposure but are not a guarantee that every attack or model error can be prevented.',
        ],
        [
            'What stops an agent from running indefinitely?',
            'The implementation can enforce step, time and usage limits outside the model, with clear stopping conditions and an operator control to pause or disable runs.',
        ],
        [
            'Can it remember information between tasks?',
            'Persistent state is a separate design choice. We define what is retained, who can access it and when it expires; a prototype should not silently accumulate unrestricted customer history.',
        ],
        [
            'What happens when a tool fails halfway through?',
            'The run should report partial completion and preserve enough context for review. Retry behaviour depends on whether an action already occurred and can be repeated safely.',
        ],
        [
            'Can you guarantee that an agent will complete every task?',
            'No. Some tasks will need clarification, additional authority or human judgement. We evaluate a defined task set and report limitations rather than promising universal completion.',
        ],
        [
            'What is a good first agent project?',
            'A bounded, reviewable task using approved information and limited tools is a practical starting point. Share a few safe examples of the inputs, expected results and actions the agent must not take.',
        ],
    ],
    'reference_url' => 'https://www.anthropic.com/engineering/building-effective-agents',
    'reference_label' => 'Anthropic’s guide to workflows, agents and evaluation',
    'cta_heading' => 'Start with One Useful Task and Clear Limits',
    'cta_text' => 'Tell us the result you want, the tools involved and the decisions that must stay with your team. We will help scope a controlled agent pilot.',
];
