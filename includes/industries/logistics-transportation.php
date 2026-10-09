<?php

return [
    'meta_title' => 'Logistics & Transportation Websites | Chulbul Design',
    'meta_desc' => 'Build logistics and transportation websites with structured freight enquiries, shipment visibility, customer portals and clear workflows for delivery exceptions.',
    'badge' => 'Logistics & Transportation',
    'h1' => 'Logistics Website Development for Freight Quotes and Tracking',
    'h1_highlight' => 'for Freight Quotes and Tracking',
    'desc' => 'Shippers need to know whether a service fits their cargo, route and handling requirements. Once freight moves, they need updates explaining what is known and what requires attention. We build logistics websites and tools around those needs, connecting structured quote requests with operational handoffs and shipment information. The scope can support a carrier, forwarder, warehouse operator or delivery network, using your systems and making data freshness and exception ownership visible.',
    'services' => [
        [
            'slug' => 'web-development',
            'desc' => 'Present service coverage, transport modes and enquiry routes in a website customers can navigate. Forms collect shipment characteristics and locations using agreed definitions, while tracking entry points explain which references are accepted and what to do when no matching shipment appears.',
            'features' => ['Coverage pages', 'Freight forms', 'Reference lookup', 'Service distinctions'],
        ],
        [
            'slug' => 'custom-software-development',
            'desc' => 'Develop customer portals and operational workspaces around your shipment lifecycle. Records connect quotations, bookings, milestones and documents, with account permissions and exception queues that reflect customer relationships. The interface makes clear when information comes from staff or an external system.',
            'features' => ['Shipment records', 'Customer permissions', 'Document access', 'Exception queues'],
        ],
        [
            'slug' => 'crm-development',
            'desc' => 'Turn freight enquiries into records that commercial and operations teams can use. Capture lanes, cargo descriptions and requested services, then assign followup according to your organisation. Quotation history keeps assumptions visible when customers revise quantities, collection details or delivery requirements.',
            'features' => ['Lane enquiries', 'Cargo details', 'Quotation revisions', 'Team assignment'],
        ],
        [
            'slug' => 'workflow-automation',
            'desc' => 'Connect shipment events to tasks and customer notifications. Rules can flag missing documents, assign a delivery exception or request clarification before dispatch. We account for duplicate events, changed statuses and notification preferences so staff can review what triggered an action.',
            'features' => ['Event routing', 'Document reminders', 'Exception assignment', 'Notification preferences'],
        ],
        [
            'slug' => 'dashboard-ui-design',
            'desc' => 'Design shipment views that help teams distinguish routine movement from items needing attention. We prioritise milestone timestamps, last update information and exception ownership, with filters based on desk responsibilities. Customer views show the context appropriate to their accounts and enquiries.',
            'features' => ['Milestone views', 'Freshness labels', 'Owner filters', 'Exception context'],
        ],
        [
            'slug' => 'android-app-development',
            'desc' => 'Build an Android workflow for driver or field tasks including stop updates, collection checks and delivery evidence. We define device permissions, offline behaviour and synchronisation feedback around working conditions, with recovery paths when a submission cannot reach the operational system.',
            'features' => ['Stop updates', 'Offline queues', 'Delivery evidence', 'Sync feedback'],
        ],
    ],
    'whyus' => [
        ['icon' => 'bi-truck', 'title' => 'Shipment context', 'desc' => 'We distinguish a quote request, a booking and an active shipment in the interface. Each stage needs different information, so customers understand whether they are exploring a service, awaiting acceptance or checking movement of freight.'],
        ['icon' => 'bi-clock-history', 'title' => 'Visible freshness', 'desc' => 'Tracking information needs context about its source and timing. We make update timestamps and unavailable states part of the design, helping customers distinguish a recent carrier event from a record that has not received another update.'],
        ['icon' => 'bi-exclamation-triangle', 'title' => 'Exception ownership', 'desc' => 'A delayed stop or missing document needs an owner and next step. We shape exception queues around those decisions, linking shipment references, notes and customer communication so staff can see what remains unresolved during handoffs.'],
        ['icon' => 'bi-arrow-left-right', 'title' => 'Operational fit', 'desc' => 'We map transport, warehouse and customer system boundaries before proposing connections. This identifies which team owns each record and how information moves between them, making integration scope concrete enough to review with those running operations.'],
    ],
    'features' => [
        ['category' => 'Freight enquiries', 'icon' => 'bi-box-seam', 'items' => [
            ['text' => 'Lane selection', 'ai' => false],
            ['text' => 'Cargo dimensions', 'ai' => false],
            ['text' => 'Weight units', 'ai' => false],
            ['text' => 'Handling requirements', 'ai' => false],
            ['text' => 'Collection windows', 'ai' => false],
            ['text' => 'Quotation references', 'ai' => false],
        ]],
        ['category' => 'Shipment visibility', 'icon' => 'bi-geo-alt', 'items' => [
            ['text' => 'Milestone history', 'ai' => false],
            ['text' => 'Update timestamps', 'ai' => false],
            ['text' => 'Estimate labels', 'ai' => false],
            ['text' => 'Exception reasons', 'ai' => false],
            ['text' => 'Document retrieval', 'ai' => false],
            ['text' => 'Account permissions', 'ai' => false],
        ]],
        ['category' => 'Field operations', 'icon' => 'bi-phone', 'items' => [
            ['text' => 'Stop checklists', 'ai' => false],
            ['text' => 'Evidence capture', 'ai' => false],
            ['text' => 'Offline submissions', 'ai' => false],
            ['text' => 'Failed attempts', 'ai' => false],
            ['text' => 'Dispatch handoffs', 'ai' => false],
            ['text' => 'Sync status', 'ai' => false],
        ]],
    ],
    'industries' => ['Freight forwarders', 'Road carriers', 'Courier networks', 'Warehouse operators', 'Cold chain operators', 'Last mile delivery', 'Contract logistics', 'Passenger transport'],
    'process' => [
        ['step' => '01', 'title' => 'Map movement', 'desc' => 'We follow shipments from enquiry through collection, transit and delivery. Commercial and dispatch teams identify inputs, system references and exceptions, establishing which customer questions the website or portal should answer at each stage of movement.'],
        ['step' => '02', 'title' => 'Define events', 'desc' => 'We agree milestone meanings, location formats and the distinction between planned and actual times. Tracking views and freight forms help staff review the language customers see, including what happens when a source system has no update.'],
        ['step' => '03', 'title' => 'Connect records', 'desc' => 'We develop enquiry flows, portal views and operational interfaces. Shipment identifiers connect related records, while incoming events are checked for duplicates and ordering issues so updates can be interpreted consistently across customer and staff workflows.'],
        ['step' => '04', 'title' => 'Rehearse exceptions', 'desc' => 'We test missing references, delayed updates, failed delivery attempts and repeated submissions. Field workflows are reviewed under interrupted connectivity, while dispatch staff confirm that messages, ownership and customer notifications reflect the actions they can take.'],
        ['step' => '05', 'title' => 'Prepare dispatch', 'desc' => 'Your team practises handling enquiries, tracking questions and integration exceptions. We document ownership and escalation routes, review customer access and agree how changes will be maintained as carriers, locations or service rules evolve after launch.'],
    ],
    'examples' => [
        ['title' => 'Forwarder enquiries', 'desc' => 'A hypothetical freight forwarder could replace a contact form with lane and cargo questions. The brief would collect dimensions, handling requirements and collection preferences, then create a quotation record where staff can request information and document assumptions before discussing options.', 'features' => ['Lane details', 'Cargo inputs', 'Quotation review']],
        ['title' => 'Customer tracking', 'desc' => 'A hypothetical logistics portal could gather shipment milestones and documents into an account workspace. The project would label update freshness and estimated times, with an exception contact route that includes the shipment reference and the event the customer asks about.', 'features' => ['Account shipments', 'Milestone history', 'Exception enquiries']],
        ['title' => 'Delivery handoffs', 'desc' => 'A hypothetical delivery operator might need an app for stop outcomes and evidence. The brief could include offline queues, dispatch review of failed attempts and synchronisation feedback, so staff can distinguish locally saved information from updates received by the system.', 'features' => ['Stop outcomes', 'Offline capture', 'Dispatch review']],
    ],
    'plans' => [
        ['name' => 'Service presence', 'desc' => 'An indicative scope for service pages, coverage and freight enquiries. Pricing and timelines are defined after discovery of service distinctions, content readiness and commercial routing.', 'features' => ['Service pages', 'Coverage content', 'Cargo forms', 'Enquiry routing', 'Editor guidance']],
        ['name' => 'Customer visibility', 'desc' => 'An indicative scope for shipment views, documents and exception contacts. Pricing and timelines are defined after discovery of event sources, account permissions and operational interfaces.', 'features' => ['Shipment portal', 'Event connection', 'Document access', 'Freshness labels', 'Exception contacts']],
        ['name' => 'Connected operations', 'desc' => 'An indicative scope for field updates, dispatch workflows and notifications. Pricing and timelines are defined after discovery of device needs, connectivity conditions and operational responsibilities.', 'features' => ['Field workflow', 'Dispatch queues', 'Event rules', 'Offline handling', 'Team handover']],
    ],
    'faqs' => [
        ['q' => 'Can tracking connect to our transport management system?', 'a' => 'We review the shipment records and event interfaces your system exposes, then map references and milestone meanings. The connection determines how frequently updates can appear. Tracking pages should display the latest known update and its timestamp, with an explanation when the source cannot currently supply information.'],
        ['q' => 'Does shipment tracking include a live vehicle map?', 'a' => 'Only when suitable location data is available and its use is agreed. Many shipment systems provide milestone events rather than continuous vehicle positions. We design the view around the actual source, identifying update times and avoiding a moving map that implies more precise information than the system provides.'],
        ['q' => 'Which details belong in a freight quotation form?', 'a' => 'The fields depend on the service, but commonly include origin, destination, cargo description, dimensions, weight and collection preferences. Handling requirements may need additional questions. We review sample enquiries with your commercial team to decide what is essential initially and what can be clarified before an offer is prepared.'],
        ['q' => 'How should estimated arrival times be presented?', 'a' => 'We label an estimate as an estimate and identify its source and update time where available. Planned, estimated and actual events should remain distinct. If a provider revises an arrival time, the customer view can explain the change and offer a contact route for shipment specific questions.'],
        ['q' => 'Can drivers record updates without an internet connection?', 'a' => 'A field app can store agreed submissions locally and synchronise them when connectivity returns. The design needs visible pending, sent and failed states, plus rules for repeated submissions and conflicting updates. We agree which tasks can work offline and how staff resolve a record that could not synchronise.'],
        ['q' => 'Who can view proof of delivery and shipment documents?', 'a' => 'Access should follow the customer account and the role assigned to each user. We define which documents are public, authenticated or restricted to operations staff. Download routes need the same permission checks as portal pages, and account changes should remove access according to your agreed handling process.'],
        ['q' => 'What happens when carrier events arrive out of order?', 'a' => 'We retain event timestamps and source identifiers so the workflow can distinguish event time from receipt time. Duplicate and late events need agreed processing rules. A delayed message should not silently move a delivered shipment back into transit; ambiguous sequences can be flagged for staff review.'],
        ['q' => 'Can customers receive notifications about delivery exceptions?', 'a' => 'Yes, using agreed event rules and communication preferences. We define which exception triggers a message, what information it contains and who handles the response. Repeated source events should not create repeated alerts, and the message should provide a shipment reference and a practical next step for the recipient.'],
    ],
    'related' => ['industrial-manufacturing', 'ecommerce', 'automotive', 'travel'],
    'stats' => [
        ['num' => 'Plan', 'label' => 'Freight enquiry requirements'],
        ['num' => 'Build', 'label' => 'Shipment information flows'],
        ['num' => 'Test', 'label' => 'Delivery exception handling'],
        ['num' => 'Care', 'label' => 'Operational connections'],
    ],
];
