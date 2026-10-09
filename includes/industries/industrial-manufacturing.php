<?php

return [
    'meta_title' => 'Industrial & Manufacturing Websites | Chulbul Design',
    'meta_desc' => 'Develop industrial and manufacturing websites with technical product catalogues, structured quotation requests, document control and practical team workflows.',
    'badge' => 'Industrial & Manufacturing',
    'h1' => 'Manufacturing Website Design for Product Catalogues and RFQs',
    'h1_highlight' => 'for Product Catalogues and RFQs',
    'desc' => 'Industrial buyers arrive with specifications, drawings and questions about suitability. Sales and engineering teams need context before discussing a product or preparing a quotation. We build manufacturing websites and tools around this exchange. Technical catalogues explain product differences, while structured enquiries capture details needed for review. The approach accommodates standard components, configured equipment and contract manufacturing, with responsibilities for product information, document revisions and handoffs between commercial and technical teams.',
    'services' => [
        [
            'slug' => 'web-development',
            'desc' => 'Build technical catalogues that let buyers search by meaningful product attributes and submit referenced enquiries. We structure product families, specification tables and document downloads so visitors can identify relevant options, then send selected items and application details to the appropriate team.',
            'features' => ['Attribute filters', 'Specification tables', 'Document downloads', 'Referenced enquiries'],
        ],
        [
            'slug' => 'cms-development',
            'desc' => 'Give product owners a controlled way to maintain technical information. Reusable fields support consistent units, document references and product relationships, while review stages help engineering check changes before publication. Editors can distinguish current materials from superseded documents within the agreed catalogue structure.',
            'features' => ['Product families', 'Unit definitions', 'Revision records', 'Engineering review'],
        ],
        [
            'slug' => 'custom-software-development',
            'desc' => 'Develop focused tools for workflows that do not fit a standard catalogue. A specification builder, drawing submission workspace or distributor portal can collect agreed inputs and expose exceptions for staff review, keeping technical acceptance with the people responsible for evaluating the request.',
            'features' => ['Specification forms', 'Drawing submissions', 'Distributor access', 'Exception queues'],
        ],
        [
            'slug' => 'erp-software-development',
            'desc' => 'Connect selected website or portal workflows with product and order information held in your operational systems. We define identifiers, ownership and update direction before developing interfaces, including how rejected records are reviewed and which commercial details may be shown to authenticated buyers.',
            'features' => ['Item mapping', 'Order references', 'Update ownership', 'Rejected records'],
        ],
        [
            'slug' => 'crm-development',
            'desc' => 'Organise industrial enquiries around application requirements, product references and responsible sales engineers. Quotation stages can distinguish missing information, technical review and commercial followup, helping colleagues understand what has been discussed and what still needs a decision before an offer can progress.',
            'features' => ['Application briefs', 'Engineer assignment', 'Quotation stages', 'Account history'],
        ],
        [
            'slug' => 'workflow-automation',
            'desc' => 'Route specification requests and product updates through defined review steps. Rules can assign an enquiry by product family, request missing information or flag a document awaiting approval. Exceptions remain visible to staff, with records of the event that initiated each task.',
            'features' => ['Family routing', 'Missing inputs', 'Review reminders', 'Task records'],
        ],
    ],
    'whyus' => [
        ['icon' => 'bi-rulers', 'title' => 'Technical structure', 'desc' => 'We define units, attributes and product relationships before building catalogue pages. This makes technical differences easier to present consistently and gives editors a structure for maintaining details buyers use when assessing a component or capability.'],
        ['icon' => 'bi-file-earmark-text', 'title' => 'Document ownership', 'desc' => 'A drawing needs an owner and revision context. We discuss how materials are approved, replaced and retired, then reflect that process in the editing tools and buyer experience so document handling supports your technical responsibilities.'],
        ['icon' => 'bi-person-gear', 'title' => 'Engineering handoffs', 'desc' => 'We separate requirements collection from technical acceptance. Enquiry forms gather application details, while routing and review states help sales engineers identify missing inputs and decide when a request is ready for quotation or further discussion.'],
        ['icon' => 'bi-link-45deg', 'title' => 'System boundaries', 'desc' => 'We identify which system owns product descriptions, availability and order records before connecting them. This makes integration choices reviewable and helps your team understand where corrections belong when a portal record differs from source information.'],
    ],
    'features' => [
        ['category' => 'Technical catalogue', 'icon' => 'bi-grid', 'items' => [
            ['text' => 'Material filters', 'ai' => false],
            ['text' => 'Dimensional attributes', 'ai' => false],
            ['text' => 'Unit labels', 'ai' => false],
            ['text' => 'Product variants', 'ai' => false],
            ['text' => 'Drawing references', 'ai' => false],
            ['text' => 'Supersession links', 'ai' => false],
        ]],
        ['category' => 'Quotation intake', 'icon' => 'bi-clipboard-data', 'items' => [
            ['text' => 'Application fields', 'ai' => false],
            ['text' => 'Quantity bands', 'ai' => false],
            ['text' => 'Drawing uploads', 'ai' => false],
            ['text' => 'Revision references', 'ai' => false],
            ['text' => 'Delivery requirements', 'ai' => false],
            ['text' => 'Engineering queues', 'ai' => false],
        ]],
        ['category' => 'Operational access', 'icon' => 'bi-gear', 'items' => [
            ['text' => 'Distributor accounts', 'ai' => false],
            ['text' => 'Document permissions', 'ai' => false],
            ['text' => 'Order lookup', 'ai' => false],
            ['text' => 'Import exceptions', 'ai' => false],
            ['text' => 'Approval history', 'ai' => false],
            ['text' => 'Change notifications', 'ai' => false],
        ]],
    ],
    'industries' => ['Component manufacturers', 'Machine builders', 'Metal fabricators', 'Contract manufacturers', 'Packaging producers', 'Process equipment', 'Industrial distributors', 'Tooling suppliers'],
    'process' => [
        ['step' => '01', 'title' => 'Trace enquiries', 'desc' => 'We review typical quotation requests with sales and engineering, identifying the information needed to evaluate them. Sample product records, drawings and system exports reveal how technical data is maintained and where incomplete requests need clarification.'],
        ['step' => '02', 'title' => 'Define attributes', 'desc' => 'We group products by attributes buyers use to distinguish them. Units, document relationships and enquiry fields are agreed with product owners, then reviewed through catalogue pages and forms before the structure extends across the range.'],
        ['step' => '03', 'title' => 'Build workflows', 'desc' => 'We implement catalogue editing, quotation intake and operational connections. Review queues preserve product and drawing references, while integration rules identify the source of each field and route rejected updates to staff who can resolve them.'],
        ['step' => '04', 'title' => 'Check variations', 'desc' => 'We test product families, mixed units, revised drawings and incomplete requests. Sales engineers review briefs for usefulness, and editors rehearse document replacement so published technical information and enquiry references behave as expected when records change.'],
        ['step' => '05', 'title' => 'Equip owners', 'desc' => 'We prepare product owners and the commercial team to maintain the catalogue and handle enquiries. Handover covers revision routines, integration exceptions and responsibility for source data, alongside a process for product additions and support requests.'],
    ],
    'examples' => [
        ['title' => 'Component catalogue', 'desc' => 'A hypothetical component manufacturer could need a searchable range organised by material and dimensions. The brief would connect product variants to drawings and allow buyers to submit a selection for quotation, with application questions routed to the relevant sales engineer.', 'features' => ['Dimensional search', 'Variant drawings', 'Selection enquiries']],
        ['title' => 'Fabrication intake', 'desc' => 'A hypothetical fabrication business might need a quotation workspace for drawings, quantities and finishing requirements. The project could track which revision was submitted, request specifications and separate engineering questions from commercial discussion before staff prepare an offer from the brief.', 'features' => ['Drawing revisions', 'Finishing details', 'Review stages']],
        ['title' => 'Distributor workspace', 'desc' => 'A hypothetical equipment supplier could provide distributors with authenticated access to selected documents and order references. The brief would define account permissions and information ownership, with a support route for records that need clarification from the sales or operations team.', 'features' => ['Account permissions', 'Document access', 'Order references']],
    ],
    'plans' => [
        ['name' => 'Technical presence', 'desc' => 'An indicative scope for capability pages, products and quotation forms. Pricing and timelines are defined after discovery of product families, document readiness and editorial responsibilities.', 'features' => ['Capability pages', 'Product templates', 'Document library', 'Quotation intake', 'Editor training']],
        ['name' => 'Connected catalogue', 'desc' => 'An indicative scope for search, documents and enquiry routing. Pricing and timelines are defined after discovery of catalogue quality, review requirements and your source systems.', 'features' => ['Attribute search', 'Revision controls', 'Sales routing', 'Import mapping', 'Exception handling']],
        ['name' => 'Industrial workspace', 'desc' => 'An indicative scope for distributor access, specification tools and connections. Pricing and timelines are defined after discovery of account rules, engineering decisions and integration access.', 'features' => ['Distributor portal', 'Specification tools', 'System interfaces', 'Review queues', 'Operational handover']],
    ],
    'faqs' => [
        ['q' => 'How do you organise products with different specifications?', 'a' => 'We define shared fields across the catalogue and additional attributes for each product family. Units and allowed values are agreed with your product owners. This lets search filters reflect useful technical distinctions without forcing unrelated products into the same table or making every field mandatory.'],
        ['q' => 'Can buyers attach drawings to a quotation request?', 'a' => 'Yes, with agreed file types, size limits and access rules. The request should retain the submitted filename and revision reference alongside quantities and application details. Staff need a way to request clarification, and uploaded materials should be available only to the people authorised to review that enquiry.'],
        ['q' => 'Can the site recommend a technically suitable component?', 'a' => 'We can implement selection rules supplied and approved by your technical team. The interface should explain required inputs and identify when a result still needs engineering review. Suitability depends on the application, so the workflow must preserve assumptions and provide a route for questions outside the agreed rules.'],
        ['q' => 'How are superseded drawings and datasheets managed?', 'a' => 'Documents can carry an owner, revision identifier and publication state. Replacing a file should update the relevant product references and identify any archived version according to your policy. We agree whether previous versions remain accessible internally and how visitors following older links reach an appropriate explanation.'],
        ['q' => 'Can our ERP supply product and order information?', 'a' => 'That depends on its supported interfaces and the records you want to expose. We map item identifiers and field ownership before developing a connection. Update direction, account permissions and rejected records need explicit handling, especially when the public catalogue uses descriptions that differ from internal operational labels.'],
        ['q' => 'How do you handle minimum quantities and variants?', 'a' => 'We model minimum quantities, packaging increments and variant choices using your commercial rules. A quotation form can flag unusual requests for discussion rather than silently rejecting them. Where quantities depend on configuration or availability, the page explains that staff will review the requirement before preparing an offer.'],
        ['q' => 'Can distributors see documents unavailable to public visitors?', 'a' => 'An authenticated workspace can grant document access according to the distributor account and agreed permissions. We define who approves access and how it is withdrawn. Public pages can still provide useful product summaries, while account specific materials remain in a separate area with an identifiable support contact.'],
        ['q' => 'What should engineering prepare for the initial review?', 'a' => 'Bring representative product families, current drawings, attribute definitions and examples of enquiries that required clarification. Your team should identify document owners and technical decisions that cannot be automated. These inputs help us define a useful catalogue structure and collect the information engineers actually need to evaluate a request.'],
    ],
    'related' => ['logistics-transportation', 'automotive', 'ecommerce', 'professional-services'],
    'stats' => [
        ['num' => 'Plan', 'label' => 'Technical data ownership'],
        ['num' => 'Build', 'label' => 'Specification enquiries'],
        ['num' => 'Test', 'label' => 'Product and drawing revisions'],
        ['num' => 'Care', 'label' => 'Catalogue maintenance'],
    ],
];
