<?php

/**
 * Supplemental, page-specific content used to complete the established
 * ten-card service pattern and ten-question FAQ pattern on ecommerce pages.
 */
return [
    'ecommerce-development' => [
        'solutions' => [
            ['bi-journal-richtext', 'Content-Led Commerce', 'Editorial guides, buying advice and product information organized as one useful discovery experience.'],
            ['bi-shop-window', 'Omnichannel Retail', 'Storefront, inventory, fulfilment and customer data aligned with physical retail or distribution operations.'],
            ['bi-file-earmark-lock', 'Digital Product Commerce', 'Secure purchase, account and delivery journeys for downloads, licences, courses or protected resources.'],
            ['bi-braces', 'Headless Commerce', 'Independent storefront experiences connected to commerce and content services through documented APIs.'],
        ],
        'faqs' => [
            ['q' => 'How long does a complete eCommerce development project take?', 'a' => 'A focused store can take several weeks, while migrations, B2B workflows, marketplaces and custom integrations require longer. The dependable timeline comes after reviewing catalogue size, content readiness, design scope, data migration, third-party approvals and testing. We divide the work into reviewable milestones so delays and dependencies remain visible.'],
            ['q' => 'Can an eCommerce store connect with our ERP, CRM or accounting software?', 'a' => 'Yes, when the existing system provides a suitable API, file exchange or supported connector. Discovery identifies which system owns products, inventory, customers, orders and financial records. We then document sync direction, timing, errors and operational responsibility before implementing the integration.'],
            ['q' => 'Should we use a platform or build a custom eCommerce solution?', 'a' => 'A maintained platform is usually preferable when its native features and reliable extensions cover the business model. Custom development becomes appropriate when product rules, customer roles, pricing or operational workflows cannot be represented cleanly. We compare capability, ownership cost and future flexibility before making a recommendation.'],
            ['q' => 'Do you provide eCommerce maintenance after launch?', 'a' => 'Yes. Support can include platform and extension updates, backups, monitoring, defect resolution, performance review and planned enhancements. The exact responsibility is agreed before launch because managed platforms, open-source stores and custom commerce systems have different maintenance needs.'],
            ['q' => 'Will we own the store, design and project assets?', 'a' => 'Ownership and third-party licences are documented in the proposal. After agreed payments, clients receive the project assets and access included in the scope. Platform subscriptions, premium apps, fonts, stock media and external services remain subject to their providers’ licence terms.'],
        ],
    ],

    'ecommerce-website-development' => [
        'solutions' => [
            ['bi-building', 'B2B Storefronts', 'Account-aware catalogues, trade pricing, quote requests and repeat ordering for business customers.'],
            ['bi-journal-richtext', 'Content-Rich Stores', 'Buying guides, editorial content and product discovery connected through a manageable content structure.'],
            ['bi-cloud-download', 'Digital Goods Stores', 'Purchase, account and protected delivery flows for files, licences, resources and online products.'],
            ['bi-calendar2-check', 'Service & Booking Commerce', 'Service selection, availability, deposits and confirmation journeys designed around real booking rules.'],
        ],
        'faqs' => [
            ['q' => 'What content and product information do you need before development?', 'a' => 'We begin with representative products, categories, variants, pricing, shipping rules, policies and brand material. The full catalogue does not always need to be complete on day one, but realistic samples are essential for designing reusable templates and identifying missing data before bulk entry or migration.'],
            ['q' => 'How do you make an eCommerce website secure?', 'a' => 'Security includes maintained software, controlled access, secure hosting configuration, HTTPS, validated input, backups and responsible payment integration. Card details should normally be handled by a compliant payment provider rather than stored on the website. Responsibilities and update procedures are documented during handover.'],
            ['q' => 'Will the online store be accessible to keyboard and mobile users?', 'a' => 'Accessibility and responsive usability are considered across navigation, product options, forms, cart and checkout. We use semantic structure, visible labels, focus states, usable controls and readable feedback. Formal conformance requirements should be stated during scoping so the appropriate testing depth can be planned.'],
            ['q' => 'Can the website support multiple currencies or languages?', 'a' => 'Yes, provided the selected platform and operating model support the required markets. Currency, language, tax, catalogue, domains, translation ownership, payment and fulfilment must be planned together. Automatic translation or currency display alone does not create a complete international commerce setup.'],
            ['q' => 'Can our team manage products and orders without a developer?', 'a' => 'That is a core implementation goal. We configure agreed product, content and order workflows for the people who will operate the store, then provide access and handover guidance. Highly custom rules or system integrations may still require technical support for later changes.'],
        ],
    ],

    'custom-ecommerce-development' => [
        'solutions' => [
            ['bi-calendar-week', 'Booking & Reservation Commerce', 'Availability, capacity, deposits and cancellation rules implemented around the actual service workflow.'],
            ['bi-person-workspace', 'Customer Self-Service Portals', 'Orders, quotations, documents, subscriptions and support actions organized for authenticated customers.'],
            ['bi-key', 'Licence & Entitlement Commerce', 'Purchases connected with access rights, renewals, usage rules and account-level product delivery.'],
            ['bi-arrow-repeat', 'Legacy Commerce Modernization', 'Older commerce workflows separated, documented and rebuilt in controlled releases without a risky big-bang switch.'],
        ],
        'faqs' => [
            ['q' => 'How long does custom eCommerce development usually take?', 'a' => 'A custom project is estimated after workflows, integrations, roles and acceptance criteria are understood. A focused first release may take a few months; complex B2B or multi-system platforms take longer. We recommend phased delivery so the first complete business journey can be tested before secondary features expand the scope.'],
            ['q' => 'Which technology stack do you use for custom commerce?', 'a' => 'The stack is chosen from product requirements, integrations, hosting, internal capability and long-term ownership—not from a fixed preference. Architecture may combine a modern frontend, a commerce service, a CMS and custom APIs. The proposed components and reasons are documented before implementation.'],
            ['q' => 'Can you build an MVP first and add features later?', 'a' => 'Yes. We define the smallest release that completes a real customer and operational journey, including administration and failure states. Later phases can add automation, channels or advanced reporting after the core model has been tested. An MVP should be smaller, but it should not be structurally disposable.'],
            ['q' => 'How do you test custom pricing and order workflows?', 'a' => 'We turn business rules into reviewable scenarios covering roles, product combinations, pricing, tax, payment, order states and exceptions. Automated tests can protect stable logic, while browser and operational testing validate the complete journey. Acceptance criteria are agreed before final release testing.'],
            ['q' => 'What ongoing support does a custom commerce platform require?', 'a' => 'Custom systems need monitored hosting, backups, dependency and security updates, integration oversight, incident handling and planned improvements. Support scope depends on business criticality and the systems involved. We document environments, dependencies, release steps and ownership so the platform is not dependent on undocumented knowledge.'],
        ],
    ],

    'shopify-development' => [
        'solutions' => [
            ['bi-plug', 'Shopify App Integrations', 'Maintained apps and custom connections selected around clear functionality, data and performance requirements.'],
            ['bi-credit-card-2-front', 'Checkout Extensibility', 'Supported Shopify checkout and post-purchase customization planned within the merchant’s available capabilities.'],
            ['bi-box2-heart', 'Bundles & Product Options', 'Bundles, subscriptions and product options structured without making catalogue management unnecessarily fragile.'],
            ['bi-speedometer2', 'Shopify Performance Recovery', 'Theme, Liquid, media, scripts and app impact measured on important storefront templates.'],
        ],
        'faqs' => [
            ['q' => 'Should I customize an existing Shopify theme or build a new one?', 'a' => 'A good maintained theme can be efficient when its structure fits the brand and catalogue. A custom theme is useful when the required hierarchy, components or merchandising differ substantially. We assess flexibility, accessibility, performance and update implications before recommending either route.'],
            ['q' => 'How many Shopify apps should a store use?', 'a' => 'There is no ideal number. Each app should solve a clear requirement and justify its recurring cost, permissions, script weight and maintenance dependency. We review native Shopify capability first, then check for overlap before adding or retaining an app.'],
            ['q' => 'Will a Shopify migration preserve our SEO URLs?', 'a' => 'Where Shopify permits the same path, important URLs can be retained. Changed product, collection or content URLs are mapped to the most relevant destination with permanent redirects. Metadata, canonicals, indexability, internal links and analytics are checked as part of migration rather than handled after launch.'],
            ['q' => 'When does a business need Shopify Plus?', 'a' => 'Shopify Plus may be justified by enterprise operations, B2B requirements, organizational scale or specific checkout and automation capabilities. It should not be selected only because a store expects growth. We compare the required features and ownership cost with standard Shopify and other suitable options.'],
            ['q' => 'Do you provide Shopify support after the store launches?', 'a' => 'Yes. Support can cover theme changes, app review, merchandising components, performance, analytics and planned enhancements. Shopify manages the core hosted platform, while the merchant and development team remain responsible for theme code, apps, content, configuration and connected services.'],
        ],
    ],

    'woocommerce-development' => [
        'solutions' => [
            ['bi-box2-heart', 'Bundles & Configurable Products', 'Product add-ons, bundles and guided options implemented with a maintainable data and extension strategy.'],
            ['bi-arrow-repeat', 'Subscriptions & Recurring Orders', 'Plans, renewals, account actions and payment behaviour organized around ongoing customer relationships.'],
            ['bi-calendar2-check', 'WooCommerce Bookings', 'Availability, capacity, deposits and notifications connected with products or services in WordPress.'],
            ['bi-globe2', 'Multilingual WooCommerce', 'Language, currency, translated product data and SEO URLs planned as one international store system.'],
        ],
        'faqs' => [
            ['q' => 'Which hosting is suitable for WooCommerce?', 'a' => 'Hosting should match catalogue size, traffic, logged-in activity, integrations and operational importance. Useful capabilities include current PHP and database support, caching, backups, staging, monitoring and responsive technical support. We assess the existing environment before recommending migration.'],
            ['q' => 'How many WooCommerce plugins are too many?', 'a' => 'Plugin quality and overlap matter more than a fixed count. A single poorly implemented extension can create more risk than several focused ones. We review purpose, support, update history, database impact, frontend scripts and compatibility, then remove duplication where it is safe to do so.'],
            ['q' => 'Can WooCommerce support subscriptions, bookings or memberships?', 'a' => 'Yes, through suitable maintained extensions or validated custom development. The choice depends on billing, availability, access and account rules. Renewal failures, cancellations, refunds and administrative workflows need to be considered alongside the visible customer experience.'],
            ['q' => 'How do you secure and maintain a WooCommerce store?', 'a' => 'Responsible maintenance includes controlled administrator access, updates through staging, backups, monitoring, secure hosting and review of payment and integration responsibilities. WordPress core, theme, extensions and infrastructure must be treated as one connected system rather than updated without testing.'],
            ['q' => 'Is WooCommerce suitable for a large product catalogue?', 'a' => 'It can be, but suitability depends on product data, search, variations, traffic, integrations and hosting—not product count alone. We review queries, indexing, administration and operational workflows before recommending WooCommerce for a substantial catalogue.'],
        ],
    ],

    'magento-development' => [
        'solutions' => [
            ['bi-diagram-3', 'Headless Magento Storefronts', 'Independent frontend experiences connected to Magento through supported commerce APIs and disciplined caching.'],
            ['bi-phone', 'Magento Mobile Commerce', 'Responsive and progressive storefront journeys designed around mobile catalogue and account tasks.'],
            ['bi-percent', 'Advanced Promotions & Pricing', 'Customer, catalogue and cart rules modeled carefully to avoid conflicting discounts and unclear totals.'],
            ['bi-shield-check', 'Magento Support & Security', 'Patches, upgrades, monitoring and release procedures organized for a business-critical commerce platform.'],
        ],
        'faqs' => [
            ['q' => 'What is the difference between Magento Open Source and Adobe Commerce?', 'a' => 'Both share the Magento foundation, while Adobe Commerce adds licensed enterprise capabilities and support options, including features useful to some B2B and large organizations. The decision should follow required workflows, edition costs, infrastructure and internal ownership rather than brand preference.'],
            ['q' => 'Can you improve Magento performance and Core Web Vitals?', 'a' => 'Yes. We first measure storefront templates, media, third-party scripts, caching, indexing, database activity and infrastructure. Improvements are prioritized around real bottlenecks. A dependable target requires measurement because catalogue complexity, extensions and external services affect the achievable result.'],
            ['q' => 'Do you develop upgrade-compatible Magento extensions?', 'a' => 'Custom modules can be developed using Magento conventions with separated responsibilities and documented configuration. Compatibility still needs testing for every relevant platform or PHP upgrade. We avoid modifying core files and assess maintained extensions before creating custom functionality.'],
            ['q' => 'How long does a Magento implementation take?', 'a' => 'Timeline depends on catalogue preparation, B2B rules, storefronts, integrations, migration and organizational review. Enterprise implementations commonly require phased delivery over several months. Discovery and a representative data rehearsal are necessary before committing to a realistic release plan.'],
            ['q' => 'Do you provide ongoing Magento maintenance and support?', 'a' => 'Yes. A support plan can include security patches, platform upgrades, monitoring, incident response, performance review and planned enhancements. Responsibility for hosting, extensions, integrations and deployment is documented so production changes follow an agreed process.'],
        ],
    ],

    'multi-vendor-marketplace' => [
        'solutions' => [
            ['bi-mortarboard', 'Learning Marketplaces', 'Instructor onboarding, course discovery, enrolment and revenue-share workflows for education platforms.'],
            ['bi-house-door', 'Rental Marketplaces', 'Availability, deposits, booking states and owner-guest responsibilities modeled for rentable assets.'],
            ['bi-briefcase', 'Talent & Expert Marketplaces', 'Profiles, requirements, proposals and engagement workflows connecting clients with verified specialists.'],
            ['bi-cloud-download', 'Digital Asset Marketplaces', 'Seller uploads, licences, previews, protected delivery and payout records for downloadable products.'],
        ],
        'faqs' => [
            ['q' => 'Which technology is best for a multi-vendor marketplace?', 'a' => 'The answer depends on whether the marketplace handles products, services, bookings, leads or digital assets, plus the required roles and money flow. A maintained marketplace platform may suit a focused model; complex operations can require custom development. We map the transaction before selecting technology.'],
            ['q' => 'How long does marketplace website development take?', 'a' => 'A marketplace takes longer than a standard store because buyer, seller and operator journeys must work together. A focused first release may take a few months; complex payments, verification or mobile apps add time. We recommend launching the smallest complete transaction before secondary features.'],
            ['q' => 'Can marketplace listings and sellers require approval?', 'a' => 'Yes. Onboarding and catalogue workflows can include draft, review, approved, rejected, suspended and archived states. Required documents, moderation responsibility and seller feedback are designed around the marketplace’s actual risk and policy requirements.'],
            ['q' => 'How are refunds and disputes handled in a marketplace?', 'a' => 'The platform must define who approves a refund, how commissions and seller balances change, when payouts become eligible and what evidence support teams can review. Provider capabilities and local legal obligations influence the final process, so these rules are established before payment implementation.'],
            ['q' => 'Can a marketplace support subscriptions as well as commissions?', 'a' => 'Yes. Seller plans, listing fees, commissions or lead fees can be combined when the business model and payment provider support them. Access entitlements, billing failures, refunds, reporting and tax responsibilities must be represented clearly for sellers and operators.'],
        ],
    ],
];
