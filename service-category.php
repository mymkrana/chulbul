<?php
/**
 * service-category.php
 * Single dynamic file that renders all 8 service hub pages.
 * Routed via .htaccess: /web-design-development, /ecommerce-solutions, etc.
 */
require_once __DIR__ . '/includes/http.php';

$base = cbd_base_path();
$_is_live = cbd_is_production_host();
$domain   = 'https://www.chulbuldesign.com';

// ── Slug from .htaccess rewrite ───────────────────────────────────────────────
$slug = trim($_GET['slug'] ?? '', '/');
if (!preg_match('/^[a-z0-9-]+$/', $slug)) {
    require __DIR__ . '/404.php';
    exit;
}
if (cbd_is_direct_script_request('service-category.php')) {
    cbd_redirect_path('/' . rawurlencode($slug));
}

// Keep the new category renderer available to older rewrite configurations.
if (in_array($slug, ['web-design-development', 'technologies', 'seo-digital-marketing', 'ui-ux-branding', 'website-support', 'ai-automation'], true)) {
    $service_hub_slug = $slug;
    $service_hub_script = '';
    require __DIR__ . '/includes/service-hub-page.php';
    exit;
}

// ── DB Connection ─────────────────────────────────────────────────────────────
$_pdo = cbd_database();

// ── Fetch category from DB ────────────────────────────────────────────────────
$cat = null;
$db_sub_services = [];
if ($_pdo) {
    try {
        $stmt = $_pdo->prepare(
            "SELECT id, name, slug, hub_slug, color FROM categories
             WHERE hub_slug = ? AND status=1 AND deleted_at IS NULL LIMIT 1"
        );
        $stmt->execute([$slug]);
        $cat = $stmt->fetch();

        if ($cat) {
            // Pull sub-services that have a matching published service
            $sub_stmt = $_pdo->prepare(
                "SELECT c.name, c.slug, s.meta_title, s.meta_desc
                 FROM categories c
                 INNER JOIN services s
                   ON CONVERT(s.slug USING utf8mb4) COLLATE utf8mb4_unicode_ci
                    = CONVERT(c.slug USING utf8mb4) COLLATE utf8mb4_unicode_ci
                   AND s.status=1 AND s.deleted_at IS NULL
                 WHERE c.parent_id=? AND c.status=1 AND c.deleted_at IS NULL
                 ORDER BY c.sort_order ASC, c.name ASC"
            );
            $sub_stmt->execute([$cat['id']]);
            $db_sub_services = $sub_stmt->fetchAll();
        }
    } catch (Exception $e) {}
}

// ── Static page data per hub_slug ─────────────────────────────────────────────
$pages = [

    // ── 1. Web Design & Development ──────────────────────────────────────────
    'web-design-development' => [
        'title'       => 'Web Design & Development Services | Chulbul Design',
        'meta_desc'   => 'Professional web design & development company in India. Custom-coded, SEO-ready websites & web apps that convert visitors into customers. Free quote.',
        'og_image'    => '/assets/images/banner/web-desing-banner.jpg',
        'schema_name' => 'Web Design & Development Services',
        'schema_desc' => 'Custom web design and development services for businesses across India.',
        'hero' => [
            'badge'      => 'Web Design & Development',
            'breadcrumb' => 'Web Design & Development',
            'h1'         => 'Web Design &amp; Development Services <span class="text-[#EE483D]">That Drive Growth.</span>',
            'desc'       => 'We don\'t build websites from templates. Every line of code, every pixel, every CTA is engineered specifically for your business — to rank on Google and turn visitors into paying customers.',
            'img'        => '/assets/images/banner/web-desing-banner.jpg',
            'img_alt'    => 'Web Design & Development Services',
        ],
        'intro' => 'There is a big difference between a website that exists and a website that works. At Chulbul Design, we offer custom web development services that go beyond visuals — every project we take on is hand-coded, search-engine-ready and built around a specific business goal. Whether you need a professional website design for a new venture or a complete full stack web development project with custom logic, we treat every project as if it is the most important thing we are building. We have delivered 500+ websites for clients across India, UAE and the UK since 2013.',
        'sections' => [
            [
                'img'    => '/assets/images/chulbuldesign_webdesign.svg',
                'img_alt'=> 'Custom Web Development Services',
                'h2'     => 'Custom Web Development Services Built From Scratch',
                'para'   => 'Every website we build starts with a blank file — not a theme, not a drag-and-drop builder and not a pre-built template bought off ThemeForest. Our custom web development services cover the full stack: PHP backends, React frontends, Node.js APIs and responsive web design that works on every screen size. We have delivered projects ranging from simple service websites to complex portals with user dashboards, payment flows and third-party API connections. A manufacturing client from Ludhiana told us their new site brought in more export enquiries in two weeks than their previous site had generated in a year.<br><em>That is what purpose-built, custom-coded work actually delivers.</em>',
                'bullets'=> ['Custom-coded from zero — no templates, ever','Full stack: PHP, React, Node.js and more','Google PageSpeed 95+ — verified on delivery','10+ years delivering web projects across India'],
                'flip'   => false,
            ],
            [
                'img'    => '/assets/images/responcive.jpg',
                'img_alt'=> 'Responsive Professional Website Design',
                'h2'     => 'Professional Website Design That Converts Visitors',
                'para'   => 'Most visitors decide whether to stay on your website within 3 seconds of landing. That means your professional website design needs to communicate your offer, build trust and point toward a clear next action — all before anyone reads a single paragraph. Our design process starts with mapping the user journey: where they come from, what they already know about you and what question they need answered before they pick up the phone. We place CTAs where eye-tracking data shows users actually look, and we test every mobile view on real devices — not just browser resize.',
                'bullets'=> ['Mobile-first responsive web design on every project','CTA placement backed by user behaviour data','Load time under 2 seconds on Indian 4G','Zero bounce-rate blind spots — every friction point fixed'],
                'flip'   => true,
            ],
            [
                'img'    => '/assets/images/webapp.webp',
                'img_alt'=> 'Full Stack Web Development for Business',
                'h2'     => 'Full Stack Web Development for Complex Business Needs',
                'para'   => 'Not every business need can be solved with a brochure website. When you need a customer portal, a booking system, a CRM tool, or a marketplace with multiple user roles — that requires full stack web development with clean architecture and proper security. We build custom web applications using PHP, React and Node.js that integrate with Razorpay, PayU, Stripe, WhatsApp Business API and any third-party system you already use.',
                'bullets'=> ['Custom web applications, portals and booking systems','Razorpay, PayU and Stripe payment integrations','Secure, scalable architecture built to last','Ongoing support and maintenance included'],
                'flip'   => false,
            ],
        ],
        'stats' => [['val'=>'500+','lbl'=>'Websites Delivered'],['val'=>'95+','lbl'=>'PageSpeed Score'],['val'=>'3x','lbl'=>'More Leads on Avg'],['val'=>'10+','lbl'=>'Years of Experience']],
        'cards_heading' => 'Our Web Design &amp; Development Services',
        'cards_sub'     => 'Custom sites, web apps, landing pages or full rebuilds — we cover the full range.',
        'cards' => [
            ['icon'=>'bi-laptop',                      'color'=>'#EE483D','title'=>'Custom Website Design',    'href'=>'/web-design',          'desc'=>'Custom websites designed around customer journeys, clear content and a responsive visual system — without generic templates.'],
            ['icon'=>'bi-code-slash',                  'color'=>'#49499A','title'=>'Web Development',          'href'=>'/web-development',            'desc'=>'Custom websites, web apps, portals and integrations built around real business requirements.'],
            ['icon'=>'bi-wordpress',                   'color'=>'#2563eb','title'=>'WordPress Development',    'href'=>'/wordpress-development','desc'=>'Custom WordPress websites, lean themes and manageable content systems built for long-term use.'],
            ['icon'=>'bi-layout-text-window-reverse',  'color'=>'#16a34a','title'=>'Landing Page Design',      'href'=>'/landing-page-design', 'desc'=>'Focused landing pages for paid campaigns, lead generation, practical forms and measurable customer actions.'],
            ['icon'=>'bi-arrow-repeat',                'color'=>'#7c3aed','title'=>'Website Redesign',         'href'=>'/website-redesign',    'desc'=>'Audit-led redesign covering customer journeys, responsive UX, content, performance and SEO-safe migration planning.'],
            ['icon'=>'bi-gear-fill',                   'color'=>'#ea580c','title'=>'CMS Development',          'href'=>'/cms-development',     'desc'=>'Structured CMS platforms, editorial workflows, multisite publishing and integrations built around your content operations.'],
        ],
        'process_heading' => 'How We Build Every Website',
        'steps' => [
            ['num'=>'01','icon'=>'bi-chat-dots-fill',     'title'=>'Discovery Call',    'desc'=>'We understand your business goals, target audience and what your current site is doing wrong.'],
            ['num'=>'02','icon'=>'bi-pencil-square',      'title'=>'UI/UX Wireframe',   'desc'=>'A visual blueprint of every key page before a single line of code is written. You approve it first.'],
            ['num'=>'03','icon'=>'bi-chat-left-text-fill','title'=>'Design Approval',   'desc'=>'Full-colour designs with your branding. We revise until everything is exactly right.'],
            ['num'=>'04','icon'=>'bi-code-slash',         'title'=>'Clean Development', 'desc'=>'Hand-coded, SEO-ready and tested on real devices. No page builders, no shortcuts.'],
            ['num'=>'05','icon'=>'bi-rocket-takeoff-fill','title'=>'Launch & Support',  'desc'=>'We go live, test on all devices and stay available for 30 days after launch at no extra cost.'],
        ],
    ],

    // ── 2. Ecommerce Solutions ────────────────────────────────────────────────
    'ecommerce-solutions' => [
        'title'       => 'Ecommerce Website Development Services | Chulbul Design',
        'meta_desc'   => 'Custom ecommerce website development — Shopify, WooCommerce & Magento stores built to maximize sales. Razorpay, Stripe & PayPal integration. Free quote.',
        'og_image'    => '/assets/images/ecomerse-image.jpg',
        'schema_name' => 'Ecommerce Website Development Services',
        'schema_desc' => 'Custom ecommerce development services including Shopify, WooCommerce and Magento store development.',
        'hero' => [
            'badge'      => 'Ecommerce Solutions',
            'breadcrumb' => 'Ecommerce Solutions',
            'h1'         => 'Ecommerce Solutions <span class="text-[#EE483D]">That Actually Sell.</span>',
            'desc'       => 'From Shopify stores to custom WooCommerce platforms — we build ecommerce solutions engineered for maximum conversions, seamless checkout and real sales growth.',
            'img'        => '/assets/images/ecomerse-image.jpg',
            'img_alt'    => 'Ecommerce Website Development Services',
        ],
        'intro' => 'Most ecommerce stores lose customers between landing and checkout — not because of pricing, but because of design friction, slow load times and confusing navigation. At Chulbul Design, our ecommerce website development services focus on one thing: making it as easy as possible for a visitor to become a buyer. We have built over 200 online stores on Shopify, WooCommerce, Magento and fully custom stacks. Our clients average a 40% increase in conversion rate within 90 days of launching their new store.',
        'sections' => [
            [
                'img'    => '/assets/images/ecomerse-image.jpg',
                'img_alt'=> 'Ecommerce Website Development Services',
                'h2'     => 'Ecommerce Website Development Services Built to Convert',
                'para'   => 'A good ecommerce store is not about how many products you list — it is about how smoothly a shopper moves from interest to payment. Our ecommerce development process starts with mapping the exact customer journey for your product category: what information shoppers need before they buy, what trust signals stop them abandoning and what checkout friction costs you the most sales. We have rebuilt stores for fashion, electronics, food, industrial supplies and luxury goods — and the same conversion principles apply across all of them. An apparel brand from Jaipur increased their average order value by 28% after we restructured their product page layout.',
                'bullets'=> ['Product pages built for conversion, not just display','Custom checkout flow with minimal drop-off','Mobile commerce: 70% of Indian shoppers buy on phone','Payment options: Razorpay, PayU, Stripe, PayPal, COD'],
                'flip'   => false,
            ],
            [
                'img'    => '/assets/images/chulbuldesign_webdesign.svg',
                'img_alt'=> 'Shopify WooCommerce Development',
                'h2'     => 'Shopify &amp; WooCommerce Development — The Right Platform for You',
                'para'   => 'Choosing the wrong ecommerce platform is an expensive mistake. Shopify is ideal for businesses that want speed, simplicity and managed hosting without technical overhead. WooCommerce gives you complete control over product logic, pricing rules and integration with custom PHP systems. Magento suits large catalogues with complex B2B pricing. We assess your actual requirements — catalogue size, customisation needs, budget, technical capacity — and recommend the right platform rather than defaulting to the one we find easiest to build on.',
                'bullets'=> ['Platform recommendation based on your real requirements','Custom Shopify themes — no off-the-shelf templates','WooCommerce with custom product logic and pricing','Magento for enterprise B2B and large catalogues'],
                'flip'   => true,
            ],
            [
                'img'    => '/assets/images/webapp.webp',
                'img_alt'=> 'Ecommerce Business Solutions',
                'h2'     => 'E-Commerce Business Solutions Beyond Just a Store',
                'para'   => 'The most profitable ecommerce businesses are not just stores — they are ecosystems. Inventory management that prevents overselling. WhatsApp Business automation that sends order updates and recovers abandoned carts. Analytics dashboards that show exactly which products, campaigns and channels are driving profit. Our ecommerce business solutions cover the full operational picture: we connect your store to your warehouse, your logistics partner, your accounting software and your marketing tools so everything runs without manual intervention.',
                'bullets'=> ['Inventory and order management integration','WhatsApp automation for order updates and cart recovery','Analytics dashboards — revenue, product, channel performance','Logistics integrations: Shiprocket, Delhivery and more'],
                'flip'   => false,
            ],
        ],
        'stats' => [['val'=>'200+','lbl'=>'Stores Launched'],['val'=>'40%','lbl'=>'Avg Conversion Lift'],['val'=>'5+','lbl'=>'Payment Gateways'],['val'=>'10+','lbl'=>'Years in Ecommerce']],
        'cards_heading' => 'Our Ecommerce Services',
        'cards_sub'     => 'From Shopify to custom marketplaces — we build stores that sell around the clock.',
        'cards' => [
            ['icon'=>'bi-shop',           'color'=>'#EE483D','title'=>'Shopify Development',      'href'=>'/shopify-development',      'desc'=>'Custom Shopify stores with bespoke themes, apps and checkout flows — built for conversions, not just looks.'],
            ['icon'=>'bi-wordpress',      'color'=>'#49499A','title'=>'WooCommerce Development',  'href'=>'/woocommerce-development',  'desc'=>'Full-control WooCommerce stores with custom product logic, fast hosting setup and payment gateway integration.'],
            ['icon'=>'bi-bag-fill',       'color'=>'#16a34a','title'=>'Magento Development',      'href'=>'/magento-development',      'desc'=>'Enterprise-grade Magento stores for large catalogues and complex B2B pricing rules.'],
            ['icon'=>'bi-people-fill',    'color'=>'#7c3aed','title'=>'Multi-Vendor Marketplace', 'href'=>'/multi-vendor-marketplace', 'desc'=>'Amazon or Flipkart-style marketplaces where multiple sellers list and sell — with separate dashboards and payouts.'],
            ['icon'=>'bi-cart-check-fill','color'=>'#ea580c','title'=>'Custom Online Store',      'href'=>'/ecommerce-website-design', 'desc'=>'Fully custom-built ecommerce stores when your product rules or business model doesn\'t fit any off-the-shelf platform.'],
            ['icon'=>'bi-headset',        'color'=>'#0ea5e9','title'=>'Ecommerce Support',        'href'=>'/website-maintenance',      'desc'=>'Monthly maintenance for your store — security updates, speed checks, product uploads and WhatsApp support.'],
        ],
        'process_heading' => 'How We Build Every Store',
        'steps' => [
            ['num'=>'01','icon'=>'bi-chat-dots-fill',      'title'=>'Discovery Call',    'desc'=>'We understand your products, target customers, competitors and current pain points.'],
            ['num'=>'02','icon'=>'bi-pencil-square',       'title'=>'Platform & Plan',   'desc'=>'We recommend the right platform and map out the full store structure before building.'],
            ['num'=>'03','icon'=>'bi-palette2',            'title'=>'Design Approval',   'desc'=>'Full design mockups — homepage, category, product and checkout pages. You approve first.'],
            ['num'=>'04','icon'=>'bi-code-slash',          'title'=>'Build & Integrate', 'desc'=>'We build and connect payments, shipping, WhatsApp and all third-party tools.'],
            ['num'=>'05','icon'=>'bi-rocket-takeoff-fill', 'title'=>'Launch & Support',  'desc'=>'Full testing, go-live and 30 days of post-launch support included.'],
        ],
    ],

    // ── 3. App & Software Development ────────────────────────────────────────
    'mobile-software-development' => [
        'title'       => 'App & Software Development Company | Chulbul Design',
        'meta_desc'   => 'Custom Android, iOS, SaaS, CRM, ERP and business software development in India. Plan a secure, scalable product with Chulbul Design.',
        'og_image'    => '/assets/images/webapp.webp',
        'schema_name' => 'App & Software Development Services',
        'schema_desc' => 'Custom Android, iOS, SaaS, CRM, ERP and business software development services.',
        'hero' => [
            'badge'      => 'App & Software Development',
            'breadcrumb' => 'App & Software Development',
            'h1'         => 'App &amp; Software Development for <span class="text-[#EE483D]">Real Business Workflows.</span>',
            'desc'       => 'From Android and iOS apps to SaaS, CRM and ERP platforms, we build software around clear user journeys and operational requirements.',
            'img'        => '/assets/images/webapp.webp',
            'img_alt'    => 'Mobile App & Software Development',
        ],
        'intro' => 'Off-the-shelf software almost fits. Almost. At Chulbul Design, our mobile app and software development services are built around the exact workflows, rules and edge cases of your business — not a generalised version of it. We have delivered over 150 mobile apps and enterprise software platforms for businesses in healthcare, real estate, education, logistics and retail. Our average app store rating across all published applications is 4.8 stars, and we have never missed a committed launch date.',
        'sections' => [
            [
                'img'    => '/assets/images/webapp.webp',
                'img_alt'=> 'Mobile App Development Services',
                'h2'     => 'Mobile App Development Services for iOS &amp; Android',
                'para'   => 'A mobile app that your customers actually use is built from understanding what they are trying to do — not from copying features of competing apps. Our mobile app development services start with user research and journey mapping. We identify the three to five core actions your app needs to support perfectly, and we design the entire experience around those actions. Everything else is secondary. We build native Android apps, native iOS apps and cross-platform React Native and Flutter apps. For most business apps, React Native gives you the best balance of performance and development speed.',
                'bullets'=> ['Native Android (Kotlin) and iOS (Swift) development','React Native and Flutter cross-platform apps','Offline-first architecture for low-connectivity markets','Google Play and App Store submission included'],
                'flip'   => false,
            ],
            [
                'img'    => '/assets/images/chulbuldesign_webdesign.svg',
                'img_alt'=> 'Enterprise Custom Software Development',
                'h2'     => 'Custom Software Development for Enterprise Needs',
                'para'   => 'Enterprise software projects fail most often not because of technical complexity but because of poor requirements definition and scope creep. Our software development process begins with a structured discovery phase: we document every business rule, every exception, every integration requirement before a single line of code is written. The output is a technical specification document that both your team and ours agree on before development begins. This single step eliminates 80% of the rework and budget overruns that plague most enterprise software projects.',
                'bullets'=> ['Structured discovery and technical specification first','Fixed-scope and time-and-materials engagement models','Integration with any existing system via API','Documented, tested code — no black boxes'],
                'flip'   => true,
            ],
            [
                'img'    => '/assets/images/tecnology.jpg',
                'img_alt'=> 'SaaS Platform Development',
                'h2'     => 'SaaS Platforms &amp; Web Application Development',
                'para'   => 'If you have a business idea that depends on software to deliver value — a subscription service, a marketplace, a platform connecting buyers and sellers — you need a software partner who understands both the technical and commercial sides of building a SaaS product. We have built multi-tenant SaaS platforms with subscription billing, role-based access control, usage-based pricing and self-serve onboarding. We help you architect the system correctly from day one so that scaling from 100 to 10,000 customers is a configuration change, not a rewrite.',
                'bullets'=> ['Multi-tenant architecture with proper data isolation','Subscription billing: Razorpay Subscriptions, Stripe Billing','Role-based access control and team management','Cloud-ready: deployed on AWS, GCP or DigitalOcean'],
                'flip'   => false,
            ],
        ],
        'stats' => [['val'=>'150+','lbl'=>'Apps Delivered'],['val'=>'4.8★','lbl'=>'Avg App Store Rating'],['val'=>'6+','lbl'=>'Tech Stacks'],['val'=>'10+','lbl'=>'Years Experience']],
        'cards_heading' => 'Our App &amp; Software Development Services',
        'cards_sub'     => 'Apps, enterprise tools, SaaS platforms and everything in between — built for real use.',
        'cards' => [
            ['icon'=>'bi-android2',       'color'=>'#EE483D','title'=>'Android App Development','href'=>'/android-app-development','desc'=>'Native Android apps built for performance, offline-first use and seamless Google Play submission.'],
            ['icon'=>'bi-apple',          'color'=>'#49499A','title'=>'iOS App Development',    'href'=>'/ios-app-development',    'desc'=>'Clean, fast iOS apps for iPhone and iPad — designed to Apple\'s standards and built to get approved first time.'],
            ['icon'=>'bi-window-stack',   'color'=>'#16a34a','title'=>'Custom Software Development','href'=>'/custom-software-development','desc'=>'Purpose-built business software for workflows that standard products cannot support responsibly.'],
            ['icon'=>'bi-cloud-fill',     'color'=>'#7c3aed','title'=>'SaaS Development',       'href'=>'/saas-development',       'desc'=>'Multi-tenant SaaS platforms with subscription billing, user management and cloud-ready infrastructure.'],
            ['icon'=>'bi-diagram-3-fill', 'color'=>'#ea580c','title'=>'CRM Development',        'href'=>'/crm-development',        'desc'=>'Custom CRM systems that track your leads, automate follow-ups and give your sales team full visibility.'],
            ['icon'=>'bi-building-fill',  'color'=>'#0ea5e9','title'=>'ERP Development',        'href'=>'/erp-software-development','desc'=>'Connected ERP modules for inventory, purchasing, production, finance, people and reporting.'],
        ],
        'process_heading' => 'How We Build Every App &amp; Platform',
        'steps' => [
            ['num'=>'01','icon'=>'bi-chat-dots-fill',     'title'=>'Discovery Call',    'desc'=>'We map your business goals, user needs and technical requirements before any design begins.'],
            ['num'=>'02','icon'=>'bi-pencil-square',      'title'=>'UI/UX Wireframe',   'desc'=>'User flows and screen-level wireframes so you see exactly how the app works before we code it.'],
            ['num'=>'03','icon'=>'bi-chat-left-text-fill','title'=>'Design Approval',   'desc'=>'High-fidelity designs with your branding and real content. Feedback rounds until it\'s right.'],
            ['num'=>'04','icon'=>'bi-code-slash',         'title'=>'Clean Development', 'desc'=>'Documented, tested code built in sprints with regular demos so you always know where things stand.'],
            ['num'=>'05','icon'=>'bi-rocket-takeoff-fill','title'=>'Launch & Support',  'desc'=>'Full QA, store submission and 30-day post-launch support included on every project.'],
        ],
    ],

    // ── 4. Technologies ───────────────────────────────────────────────────────
    'technologies' => [
        'title'       => 'Technology Solutions & Development | Chulbul Design',
        'meta_desc'   => 'Explore Node.js, React, Laravel, PHP, Joomla and Next.js development. Choose a practical technology approach for your website or business application.',
        'og_image'    => '/assets/images/tecnology.jpg',
        'schema_name' => 'Technology Solutions & Development',
        'schema_desc' => 'Node.js, React, Laravel, PHP, Joomla and Next.js development for businesses worldwide.',
        'hero' => [
            'badge'      => 'Technologies',
            'breadcrumb' => 'Technologies',
            'h1'         => 'Technology Solutions <span class="text-[#EE483D]">Built for Your Business.</span>',
            'desc'       => 'Choose a development approach around your users, content and existing systems. We explain the trade-offs between frontend frameworks, backend technologies and content platforms before recommending a build.',
            'img'        => '/assets/images/tecnology.jpg',
            'img_alt'    => 'Technology Solutions & Development',
        ],
        'intro' => 'Technology decisions made early have consequences for years. At Chulbul Design, our technology services are built around one principle: the right tool for your actual requirements, not the most fashionable framework in the market. We have worked with PHP, React, Node.js, Python, Flutter, cloud platforms and dozens of third-party APIs. We help you choose, build and maintain the technology layer that your business runs on.',
        'sections' => [
            [
                'img'    => '/assets/images/tecnology.jpg',
                'img_alt'=> 'Modern Technology Stack',
                'h2'     => 'Modern Tech Stacks — Chosen for Performance, Not Trend',
                'para'   => 'Every technology we recommend has been validated in production across multiple client projects. We use PHP and Laravel for server-side applications where reliability and speed matter most. React and Next.js for frontend applications that need to be fast and SEO-friendly. Node.js for real-time features and APIs. Python for data processing and AI integrations. We do not chase trends — we use the stack that delivers the best result for your specific project.',
                'bullets'=> ['PHP / Laravel for robust backend systems','React / Next.js for fast, SEO-ready frontends','Node.js for real-time features and REST APIs','Python for AI, data processing and automation'],
                'flip'   => false,
            ],
            [
                'img'    => '/assets/images/webapp.webp',
                'img_alt'=> 'Cloud Infrastructure and Hosting',
                'h2'     => 'Cloud Infrastructure &amp; Hosting That Scales',
                'para'   => 'Your hosting infrastructure should grow with your business without requiring a complete rebuild. We architect and deploy cloud environments on AWS, Google Cloud and DigitalOcean — with auto-scaling, load balancing, automated backups and monitoring built in from day one. For businesses running on shared hosting or slow servers, we also handle migrations to cloud-native hosting with zero downtime.',
                'bullets'=> ['AWS, GCP and DigitalOcean deployments','Auto-scaling to handle traffic spikes','Zero-downtime migrations from old hosting','99.9% uptime SLA with monitoring'],
                'flip'   => true,
            ],
            [
                'img'    => '/assets/images/chulbuldesign_webdesign.svg',
                'img_alt'=> 'API Integration Services',
                'h2'     => 'API Integrations — Connect Every System You Use',
                'para'   => 'Modern businesses run on dozens of software tools. When those tools do not talk to each other, your team wastes time on manual data entry and you miss the insights that come from connected data. We build and maintain API integrations between your website, CRM, accounting software, payment gateways, shipping providers, communication tools and any other system in your stack.',
                'bullets'=> ['Payment gateways: Razorpay, Stripe, PayU, PayPal','WhatsApp Business API, SMS and email providers','CRM integrations: Zoho, HubSpot, Salesforce','ERP and inventory system connections'],
                'flip'   => false,
            ],
        ],
        'stats' => [['val'=>'20+','lbl'=>'Tech Stacks Used'],['val'=>'500+','lbl'=>'Projects Delivered'],['val'=>'50+','lbl'=>'APIs Integrated'],['val'=>'10+','lbl'=>'Years Experience']],
        'cards_heading' => 'Our Technology Services',
        'cards_sub'     => 'Compare six focused development services, then follow the option that matches your requirement.',
        'cards' => [
            ['icon'=>'bi-code-slash',         'color'=>'#EE483D','title'=>'Node.js Development', 'href'=>'/nodejs-development', 'desc'=>'Backend APIs, real-time features, integrations and background jobs with defined operating responsibilities.'],
            ['icon'=>'bi-braces',             'color'=>'#49499A','title'=>'React JS Development', 'href'=>'/reactjs-development', 'desc'=>'Responsive interfaces, dashboards and reusable components connected to your application data.'],
            ['icon'=>'bi-code-square',        'color'=>'#16a34a','title'=>'Laravel Development', 'href'=>'/laravel-development', 'desc'=>'Business applications, portals and APIs organized around workflows, roles and data.'],
            ['icon'=>'bi-filetype-php',       'color'=>'#7c3aed','title'=>'PHP Development', 'href'=>'/php-development', 'desc'=>'Custom web functionality, legacy application improvements, integrations and planned upgrades.'],
            ['icon'=>'bi-grid-1x2',           'color'=>'#ea580c','title'=>'Joomla Development', 'href'=>'/joomla', 'desc'=>'Content-rich websites with templates, extensions, publishing roles and multilingual configuration.'],
            ['icon'=>'bi-window-stack',       'color'=>'#0ea5e9','title'=>'Next.js Development', 'href'=>'/nextjs-development', 'desc'=>'Content websites and web applications with deliberate routing, rendering, CMS and data freshness.'],
        ],
        'process_heading' => 'How We Approach Every Technology Project',
        'steps' => [
            ['num'=>'01','icon'=>'bi-chat-dots-fill',     'title'=>'Discovery Call',       'desc'=>'We understand your current stack, pain points and business goals before recommending anything.'],
            ['num'=>'02','icon'=>'bi-pencil-square',      'title'=>'Tech Architecture',    'desc'=>'A written architecture document covering stack, hosting, integrations and data flow — before any code.'],
            ['num'=>'03','icon'=>'bi-chat-left-text-fill','title'=>'Plan Approval',        'desc'=>'You review and approve the architecture, timeline and cost estimate before we start.'],
            ['num'=>'04','icon'=>'bi-code-slash',         'title'=>'Build & Test',         'desc'=>'Documented, tested development in structured sprints with regular demos throughout.'],
            ['num'=>'05','icon'=>'bi-rocket-takeoff-fill','title'=>'Deploy & Support',     'desc'=>'Deployment with full documentation, team training and ongoing support options.'],
        ],
    ],

    // ── 5. SEO & Digital Marketing ────────────────────────────────────────────
    'seo-digital-marketing' => [
        'title' => 'SEO & Digital Marketing Services | Chulbul Design',
        'meta_desc' => 'Explore SEO, local search, technical audits, Google Ads, social media and content marketing. Choose a focused marketing scope for your business with Chulbul Design.',
        'og_image' => '/assets/images/chulbuldesign-social-share-original-logo-2026-09.jpg',
        'schema_name' => 'SEO & Digital Marketing Services',
        'schema_desc' => 'SEO, local search, technical optimization, paid advertising, social media and content services for businesses worldwide.',
        'hero' => [
            'badge' => 'SEO & Digital Marketing',
            'breadcrumb' => 'SEO & Digital Marketing',
            'h1' => 'SEO &amp; Digital Marketing <span class="text-[#EE483D]">With a Clear Business Purpose.</span>',
            'desc' => 'Connect search visibility, useful content and paid campaigns with the customers you want to reach. Start with a focused plan, clear responsibilities and honest measurement.',
            'img' => '/assets/images/ppc-service.webp',
            'img_alt' => 'Illustrative marketing dashboard; sample figures are not client performance results',
        ],
        'intro' => 'Marketing should start with a practical question: what does your business need customers to understand or do next? We help choose the right combination of organic search, local visibility, paid advertising and content around that goal. Chulbul Design works with businesses worldwide from our Gurugram team. The scope defines markets, deliverables, approvals and measurement before work begins, without promising a fixed ranking, lead volume or return.',
        'sections' => [
            [
                'img' => '/assets/images/ppc-service.webp',
                'img_alt' => 'Illustrative analytics dashboard; sample figures are not SEO campaign results',
                'h2' => 'Organic SEO With a Useful Plan Behind Every Page',
                'para' => 'Our SEO services connect relevant search intent, clear content and technical implementation. The starting point is your existing website and the services that matter commercially. General SEO supports wider discovery, Local SEO focuses on genuine locations and service areas, and Technical SEO turns website issues into testable fixes. These scopes can work together without making every page repeat the same message.',
                'bullets' => [
                    'Research and page priorities tied to business services',
                    'Local strategy grounded in actual operations',
                    'Developer-ready technical recommendations',
                    'Progress reviewed with context and clear limitations',
                ],
                'flip' => false,
            ],
            [
                'img' => '/assets/images/ppc-service.webp',
                'img_alt' => 'Sample analytics dashboard illustrating campaign measurement, not actual client results',
                'h2' => 'Paid Campaigns With Relevant Destinations and Clear Costs',
                'para' => 'Google Ads and paid social need an offer, an audience and a useful next step. We review campaign scope, landing pages and measurement before launch. Platform spend is separate from agency management, and additional creative or development work is identified in the proposal. Reporting distinguishes clicks, enquiries and qualified opportunities where the available systems support that distinction.',
                'bullets' => [
                    'Offer and market priorities agreed before launch',
                    'Agency fees and advertising spend separated',
                    'Landing-page and conversion-path validation',
                    'Campaign decisions informed by lead quality',
                ],
                'flip' => true,
            ],
            [
                'img' => '/assets/images/responcive.webp',
                'img_alt' => 'Digital content presented on responsive screens',
                'h2' => 'Content and Social Media Built From Real Business Knowledge',
                'para' => 'Useful marketing content explains what customers need to know. We plan website copy, guides and social themes from genuine expertise, approved examples and recurring questions. The workflow makes writing, design, approvals, publishing and enquiry responses explicit. A busy calendar is not the goal on its own; the material should serve an audience and have a clear role in the customer journey.',
                'bullets' => [
                    'Distinct topics and evidence-led briefs',
                    'A consistent brand voice and approval process',
                    'Publishing and response responsibilities defined',
                    'Existing content reviewed as well as new production',
                ],
                'flip' => false,
            ],
        ],
        'stats' => [
            [
                'val' => 'Search',
                'lbl' => 'Organic visibility',
            ],
            [
                'val' => 'Paid',
                'lbl' => 'Focused campaigns',
            ],
            [
                'val' => 'Content',
                'lbl' => 'Useful answers',
            ],
            [
                'val' => 'Clear',
                'lbl' => 'Delivery scope',
            ],
        ],
        'cards_heading' => 'Our SEO &amp; Digital Marketing Services',
        'cards_sub' => 'Choose the support your business needs, with distinct scopes for each part of the customer journey.',
        'cards' => [
            [
                'icon' => 'bi-search',
                'color' => '#EE483D',
                'title' => 'SEO Services',
                'href' => '/seo-services',
                'desc' => 'SEO services built around relevant searches, useful content and a healthy website. Get a practical organic growth plan for your business with Chulbul Design.',
            ],
            [
                'icon' => 'bi-geo-alt',
                'color' => '#0ea5e9',
                'title' => 'Local SEO',
                'href' => '/local-seo-services',
                'desc' => 'Local SEO for businesses serving real locations and service areas. Improve business profiles, location pages and enquiry journeys with Chulbul Design.',
            ],
            [
                'icon' => 'bi-gear',
                'color' => '#49499A',
                'title' => 'Technical SEO',
                'href' => '/technical-seo',
                'desc' => 'Technical SEO audits and implementation for crawl issues, indexing, redirects, structured data and page performance. Get a prioritized plan from Chulbul Design.',
            ],
            [
                'icon' => 'bi-google',
                'color' => '#16a34a',
                'title' => 'Google Ads / PPC',
                'href' => '/google-ads-ppc',
                'desc' => 'Google Ads and PPC management with focused campaigns, relevant landing pages and clear conversion measurement. Plan your paid search strategy with Chulbul Design.',
            ],
            [
                'icon' => 'bi-instagram',
                'color' => '#7c3aed',
                'title' => 'Social Media Marketing',
                'href' => '/social-media-marketing',
                'desc' => 'Social media strategy, content planning and campaign support for your business. Build a consistent presence with clear approvals and reporting from Chulbul Design.',
            ],
            [
                'icon' => 'bi-file-earmark-text',
                'color' => '#ea580c',
                'title' => 'Content Marketing',
                'href' => '/content-marketing',
                'desc' => 'Content strategy, website copy, articles and case studies built around real customer questions. Plan useful, original business content with Chulbul Design.',
            ],
        ],
        'process_heading' => 'How We Plan the Work',
        'steps' => [
            [
                'num' => '01',
                'icon' => 'bi-chat-dots-fill',
                'title' => 'Understand the Business',
                'desc' => 'Agree the audience, priority services, markets and useful customer actions.',
            ],
            [
                'num' => '02',
                'icon' => 'bi-search',
                'title' => 'Review the Starting Point',
                'desc' => 'Assess relevant website, content, account and measurement evidence.',
            ],
            [
                'num' => '03',
                'icon' => 'bi-pencil-square',
                'title' => 'Agree a Focused Scope',
                'desc' => 'Document the work, costs, responsibilities and approval points.',
            ],
            [
                'num' => '04',
                'icon' => 'bi-check2-circle',
                'title' => 'Deliver and Validate',
                'desc' => 'Implement approved changes and check the affected pages or campaign paths.',
            ],
            [
                'num' => '05',
                'icon' => 'bi-clipboard-data',
                'title' => 'Review and Improve',
                'desc' => 'Use available evidence and business feedback to select the next priorities.',
            ],
        ],
    ],

    // ── 6. UI/UX & Branding ───────────────────────────────────────────────────
    'ui-ux-branding' => [
        'title' => 'UI/UX Design & Branding Services | Chulbul Design',
        'meta_desc' => 'Explore UI/UX, Figma, graphic, logo, brand identity and dashboard design. Plan clear interfaces and a consistent business presence with Chulbul Design.',
        'og_image' => '/assets/images/chulbuldesign-social-share-original-logo-2026-09.jpg',
        'schema_name' => 'UI/UX & Branding Services',
        'schema_desc' => 'Interface design, Figma design, graphic design, logos, brand identity and dashboard UI services for businesses worldwide.',
        'hero' => [
            'badge' => 'UI/UX & Branding',
            'breadcrumb' => 'UI/UX & Branding',
            'h1' => 'UI/UX &amp; Branding <span class="text-[#EE483D]">With a Clear Purpose.</span>',
            'desc' => 'Create clearer digital experiences and a coherent visual identity. Choose the design support your business needs, with practical deliverables and a defined review process.',
            'img' => '/assets/images/ux-vs-ui.webp',
            'img_alt' => 'Designer reviewing interface wireframes, colour samples and layout sketches',
        ],
        'intro' => 'Design should help your audience understand the business and use its products. Our UI/UX and branding services connect interface structure, visual identity and the materials your team creates every day. Chulbul Design works with businesses worldwide from Gurugram. Each engagement defines the brief, deliverables, approvals and handoff; research, design, development and production are separated clearly so the scope fits the actual requirement.',
        'sections' => [
            [
                'img' => '/assets/images/ux-vs-ui.webp',
                'img_alt' => 'Interface planning with mobile wireframes and colour references',
                'h2' => 'Interface Design Built Around Useful Journeys',
                'para' => 'UI/UX design starts with what people need to find, understand and do. We plan information hierarchy, task flows and relevant interaction states before finalizing the visual interface. Figma design can support editable layouts, reusable components and prototypes, while dashboard design addresses roles, data definitions and operational tasks. The handoff explains the decisions developers need to implement; a prototype is not presented as a finished application.',
                'bullets' => [
                    'User journeys and realistic content',
                    'Responsive layouts and relevant interface states',
                    'Organized design files and reusable patterns',
                    'Clear distinction between design and implementation',
                ],
                'flip' => false,
            ],
            [
                'img' => '/assets/images/uploads/pexels-branding--business--marketing-1-1779013584.jpg',
                'img_alt' => 'Brand planning board linking identity, logo, design and strategy',
                'h2' => 'A Brand Identity Your Team Can Use Consistently',
                'para' => 'A logo is one part of the identity, not the whole system. We can define the visual direction, mark, colour, typography and supporting elements appropriate to the business. Practical guidelines and example applications help teams and suppliers use the identity coherently. Existing recognition is reviewed before a refresh, and the proposal states which templates or rollout materials are included.',
                'bullets' => [
                    'A brief connected to the business and audience',
                    'Logo and supporting visual language',
                    'Practical guidelines and approved assets',
                    'Prioritized rollout applications',
                ],
                'flip' => true,
            ],
            [
                'img' => '/assets/images/service-heroes/web-design-hero.webp',
                'img_alt' => 'Illustration of visual design elements and responsive interface layouts',
                'h2' => 'Design Deliverables That Work Beyond the Presentation',
                'para' => 'A design needs to be useful in its final setting. Graphic materials are prepared for agreed print or digital requirements; interface files need states, responsive rules and implementation context. We review realistic applications and organize the approved assets for handoff. Printing, development, licensed assets and specialist services are identified separately when they are outside the design engagement.',
                'bullets' => [
                    'Formats and source delivery agreed upfront',
                    'Content and artwork approvals made explicit',
                    'Realistic application and state checks',
                    'Production and development responsibilities identified',
                ],
                'flip' => false,
            ],
        ],
        'stats' => [
            [
                'val' => 'UX',
                'lbl' => 'Purposeful journeys',
            ],
            [
                'val' => 'UI',
                'lbl' => 'Clear interfaces',
            ],
            [
                'val' => 'Brand',
                'lbl' => 'Coherent identity',
            ],
            [
                'val' => 'Files',
                'lbl' => 'Practical handoff',
            ],
        ],
        'cards_heading' => 'Our UI/UX &amp; Branding Services',
        'cards_sub' => 'Choose a focused design service or connect several into an agreed project scope.',
        'cards' => [
            [
                'icon' => 'bi-palette2',
                'color' => '#EE483D',
                'title' => 'UI/UX Design',
                'href' => '/ui-ux-design',
                'desc' => 'UI/UX design for websites, apps and digital products. Plan user journeys, wireframes, prototypes and clear developer handoff with Chulbul Design.',
            ],
            [
                'icon' => 'bi-bezier2',
                'color' => '#49499A',
                'title' => 'Figma Design',
                'href' => '/figma-design',
                'desc' => 'Figma design services for responsive interfaces, reusable components, interactive prototypes and organized developer handoff. Plan your project with Chulbul Design.',
            ],
            [
                'icon' => 'bi-palette',
                'color' => '#16a34a',
                'title' => 'Graphic Design',
                'href' => '/graphic-design',
                'desc' => 'Graphic design for brochures, social creative, presentations and business collateral. Get clear, brand-consistent print and digital designs from Chulbul Design.',
            ],
            [
                'icon' => 'bi-vector-pen',
                'color' => '#7c3aed',
                'title' => 'Logo Design',
                'href' => '/logo-design',
                'desc' => 'Custom logo design with a clear brief, purposeful concepts and practical digital and print variations. Plan a usable brand mark with Chulbul Design.',
            ],
            [
                'icon' => 'bi-gem',
                'color' => '#ea580c',
                'title' => 'Branding & Identity',
                'href' => '/branding-identity',
                'desc' => 'Branding and identity design for a consistent business presence. Plan your visual direction, logo system and practical brand guidelines with Chulbul Design.',
            ],
            [
                'icon' => 'bi-speedometer2',
                'color' => '#0ea5e9',
                'title' => 'Dashboard UI Design',
                'href' => '/dashboard-ui-design',
                'desc' => 'Dashboard UI design for SaaS products, admin panels and internal tools. Make tables, metrics, filters and operational workflows clearer with Chulbul Design.',
            ],
        ],
        'process_heading' => 'How We Plan and Deliver Design',
        'steps' => [
            [
                'num' => '01',
                'icon' => 'bi-chat-dots-fill',
                'title' => 'Agree the Brief',
                'desc' => 'Clarify audience, purpose, constraints and the deliverables the business needs.',
            ],
            [
                'num' => '02',
                'icon' => 'bi-search',
                'title' => 'Review the Context',
                'desc' => 'Assess existing assets, product information and the questions that need resolving.',
            ],
            [
                'num' => '03',
                'icon' => 'bi-pencil-square',
                'title' => 'Develop the Design',
                'desc' => 'Explore and refine the agreed visual or interface direction.',
            ],
            [
                'num' => '04',
                'icon' => 'bi-check2-circle',
                'title' => 'Review Real Applications',
                'desc' => 'Check content, states and relevant formats before final approval.',
            ],
            [
                'num' => '05',
                'icon' => 'bi-files',
                'title' => 'Handover Clearly',
                'desc' => 'Deliver approved files and practical guidance with the next responsibilities defined.',
            ],
        ],
    ],

    // ── 7. Website Support & Optimization ────────────────────────────────────
    'website-support' => [
        'title' => 'Website Support & Optimization Services | Chulbul Design',
        'meta_desc' => 'Website maintenance, speed, Core Web Vitals, migration, security and performance services. Chulbul Design supports business websites worldwide with clearly scoped work.',
        'og_image' => '/assets/images/chulbuldesign-social-share-original-logo-2026-09.jpg',
        'schema_name' => 'Website Support & Optimization Services',
        'schema_desc' => 'Website care and technical optimization for businesses worldwide, covering maintenance, loading performance, Web Vitals, migration and security.',
        'hero' => [
            'badge' => 'Website Support & Optimization',
            'breadcrumb' => 'Website Support & Optimization',
            'h1' => 'Website Support &amp; Optimization <span class="text-[#EE483D]">With a Clear Plan.</span>',
            'desc' => 'Keep your website useful, maintainable and ready for the next change. Choose the right support scope for routine care, loading issues, a site move or a deeper technical problem.',
            'img' => '/assets/images/service-heroes/web-development-hero.webp',
            'img_alt' => 'Illustration of website systems, responsive screens, security and performance checks',
        ],
        'intro' => 'Different website problems need different kinds of work. Regular updates belong in a maintenance plan; a slow-loading page needs a loading diagnosis; a delayed account workflow may need application profiling. Chulbul Design supports businesses worldwide with clearly scoped technical services. We agree access, priorities and verification before making changes, and explain where hosting, external providers or additional development affect the result.',
        'sections' => [
            [
                'img' => '/assets/images/service-heroes/web-development-hero.webp',
                'img_alt' => 'Illustration of website loading and connected application systems',
                'h2' => 'Choose the Right Kind of Performance Work',
                'para' => 'Speed Optimization focuses on loading delays and resource delivery. Core Web Vitals Optimization examines LCP, INP and CLS with available field evidence and controlled testing. Website Performance Optimization follows broader bottlenecks across browser behaviour, APIs, databases and hosting. We help you choose the appropriate scope before proposing fixes, and report results under the conditions used for testing rather than guaranteeing a score or ranking.',
                'bullets' => [
                    'Loading-speed diagnosis and delivery improvements',
                    'Focused LCP, INP and CLS investigation',
                    'Broader application and database profiling',
                    'Comparable testing and honest limitations',
                ],
                'flip' => false,
            ],
            [
                'img' => '/assets/images/service-heroes/cms-development-hero.webp',
                'img_alt' => 'Illustration of content publishing and website access roles',
                'h2' => 'Keep Maintenance and Security Responsibilities Clear',
                'para' => 'A care plan should identify who updates the software, checks important journeys and owns the recovery process. Security hardening can add access reviews and defensive configuration changes where appropriate. Suspected compromise requires a separate assessment rather than a routine update. We confirm support hours, backup arrangements and reporting responsibilities in the proposal; no continuous staffing or universal response time is implied.',
                'bullets' => [
                    'Agreed maintenance tasks and allowances',
                    'Access and update responsibilities',
                    'Backup and recovery preparation',
                    'Defined reporting and support coverage',
                ],
                'flip' => true,
            ],
            [
                'img' => '/assets/images/service-heroes/custom-software-development-hero.webp',
                'img_alt' => 'Illustration of cloud, database and application dependencies',
                'h2' => 'Plan Website Changes Before They Reach Customers',
                'para' => 'A hosting move or significant technical change needs more than a working preview. We map the dependencies, define the affected business journeys and agree what must be checked before release. The plan includes appropriate backups, an approval point and a recovery route. After the change, we verify the agreed scope and document outstanding dependencies so the handover remains useful to your team.',
                'bullets' => [
                    'Hosting, domain and CMS migration planning',
                    'Relevant URL and content checks',
                    'Approved release and recovery criteria',
                    'Post-change verification and handover',
                ],
                'flip' => false,
            ],
        ],
        'stats' => [
            [
                'val' => 'Scoped',
                'lbl' => 'Agreed Responsibilities',
            ],
            [
                'val' => 'Tested',
                'lbl' => 'Documented Changes',
            ],
            [
                'val' => 'Clear',
                'lbl' => 'Practical Reporting',
            ],
            [
                'val' => 'Global',
                'lbl' => 'Remote Collaboration',
            ],
        ],
        'cards_heading' => 'Our Support &amp; Optimization Services',
        'cards_sub' => 'Choose a focused service or discuss how a one-time project can connect with ongoing website care.',
        'cards' => [
            [
                'icon' => 'bi-tools',
                'color' => '#7c3aed',
                'title' => 'Website Maintenance',
                'href' => '/website-maintenance',
                'desc' => 'Keep your website current with tested updates, backups, issue fixes and practical care plans. Chulbul Design supports business websites worldwide.',
            ],
            [
                'icon' => 'bi-speedometer2',
                'color' => '#EE483D',
                'title' => 'Speed Optimization',
                'href' => '/website-speed-optimization',
                'desc' => 'Improve slow-loading pages with image, script, caching and delivery fixes. Chulbul Design provides practical website speed optimization for businesses worldwide.',
            ],
            [
                'icon' => 'bi-activity',
                'color' => '#16a34a',
                'title' => 'Core Web Vitals Optimization',
                'href' => '/core-web-vitals-optimization',
                'desc' => 'Address LCP, INP and CLS issues with field-data analysis, template-level fixes and validation. Core Web Vitals optimization for business websites worldwide.',
            ],
            [
                'icon' => 'bi-arrow-left-right',
                'color' => '#ea580c',
                'title' => 'Website Migration',
                'href' => '/website-migration',
                'desc' => 'Plan your website move with content checks, URL mapping, backups and launch validation. Chulbul Design handles hosting, domain and CMS migration projects worldwide.',
            ],
            [
                'icon' => 'bi-shield-check',
                'color' => '#49499A',
                'title' => 'Website Security',
                'href' => '/website-security',
                'desc' => 'Reduce avoidable website risks with access reviews, update planning, security hardening and recovery preparation. Website security services for businesses worldwide.',
            ],
            [
                'icon' => 'bi-graph-up-arrow',
                'color' => '#0ea5e9',
                'title' => 'Website Performance Optimization',
                'href' => '/website-performance-optimization',
                'desc' => 'Find bottlenecks across browser, API, database and hosting layers. Chulbul Design improves slow website workflows with measured, scoped performance engineering.',
            ],
        ],
        'process_heading' => 'How We Plan Website Support',
        'steps' => [
            [
                'num' => '01',
                'icon' => 'bi-chat-dots-fill',
                'title' => 'Describe the Need',
                'desc' => 'Share the website, the problem and the business workflow affected.',
            ],
            [
                'num' => '02',
                'icon' => 'bi-list-check',
                'title' => 'Define the Scope',
                'desc' => 'Agree priorities, access, responsibilities and any exclusions.',
            ],
            [
                'num' => '03',
                'icon' => 'bi-shield-check',
                'title' => 'Prepare the Change',
                'desc' => 'Confirm backups, test conditions and the release or review plan.',
            ],
            [
                'num' => '04',
                'icon' => 'bi-tools',
                'title' => 'Implement and Verify',
                'desc' => 'Complete approved work and check the affected journeys.',
            ],
            [
                'num' => '05',
                'icon' => 'bi-file-earmark-text',
                'title' => 'Report and Hand Over',
                'desc' => 'Explain what changed, what was verified and what remains to be decided.',
            ],
        ],
    ],

    // ── 8. AI & Automation ────────────────────────────────────────────────────
    'ai-automation' => [
        'title' => 'AI & Automation Services for Business | Chulbul Design',
        'meta_desc' => 'Business chatbots, WhatsApp workflows, AI integrations, process automation and bounded agents. Chulbul Design builds practical solutions for teams worldwide.',
        'og_image' => '/assets/images/chulbuldesign-social-share-original-logo-2026-09.jpg',
        'schema_name' => 'AI & Automation Services',
        'schema_desc' => 'Business-specific AI and automation services for clients worldwide, with defined data access, approval controls and operational ownership.',
        'hero' => [
            'badge' => 'AI & Automation Services',
            'breadcrumb' => 'AI & Automation',
            'h1' => 'AI &amp; Automation Services <span class="text-[#EE483D]">Built Around Useful Work.</span>',
            'desc' => 'Help customers find answers, connect repetitive tasks and bring useful AI features into your existing software. Start with a clear task, defined permissions and a pilot your team can review.',
            'img' => '/assets/images/service-heroes/custom-software-development-hero.webp',
            'img_alt' => 'Illustration of business software, data connections, cloud services and access roles',
        ],
        'intro' => 'Automation is most useful when the task and its owner are clear. A chatbot can support customer conversations; a workflow can connect predictable steps; an AI feature can help with unstructured information. An agent adds flexible tool use and needs carefully bounded authority. Chulbul Design works with businesses worldwide to choose an appropriate scope, prepare the data and verify the result. We separate implementation from usage costs and define who handles exceptions after launch.',
        'sections' => [
            [
                'img' => '/assets/images/service-heroes/crm-development-hero.webp',
                'img_alt' => 'Illustration of customer profiles, sales stages and communication channels',
                'h2' => 'Make Customer Conversations More Useful',
                'para' => 'AI Chatbot Development focuses on approved knowledge, relevant questions and a clear human handoff. WhatsApp Automation connects business messaging to qualification, saved enquiry records and team routing. We help define the conversation and its destination before implementation, with account eligibility and messaging requirements reviewed where relevant. A reply on screen is not treated as proof that a lead reached the right person.',
                'bullets' => [
                    'Business-specific chatbot knowledge',
                    'Relevant enquiry qualification',
                    'Approved CRM and inbox connections',
                    'Clear human escalation',
                ],
                'flip' => false,
            ],
            [
                'img' => '/assets/images/service-heroes/erp-software-development-hero.webp',
                'img_alt' => 'Illustration of connected operational systems and business reporting',
                'h2' => 'Connect Tasks and Improve End-to-End Processes',
                'para' => 'Workflow Automation handles defined triggers, data mapping and actions between tools. Business Process Automation takes a wider view of intake, responsibilities, approvals and exceptions across teams. We choose the scope based on the actual bottleneck rather than adding AI to every step. Testing includes incomplete inputs, duplicate events and failed handoffs, with a documented owner for work that needs a person\'s decision.',
                'bullets' => [
                    'Rule-based task connections',
                    'Process ownership and approvals',
                    'Duplicate and failure handling',
                    'Phased pilots and team handover',
                ],
                'flip' => true,
            ],
            [
                'img' => '/assets/images/service-heroes/web-development-hero.webp',
                'img_alt' => 'Illustration of website code, data connections and application safeguards',
                'h2' => 'Add AI Capabilities with Explicit Boundaries',
                'para' => 'AI Integration can bring search, summaries, drafting or classification into existing software. AI Agent Development can support tasks that need flexible steps and approved tool use. Both require clear data access and evaluation; agents also need explicit action permissions and stopping controls. We assess a narrow pilot first, make consequential actions reviewable and explain the limitations instead of promising perfect answers or unlimited autonomy.',
                'bullets' => [
                    'Task-focused AI integration',
                    'Permission-aware source access',
                    'Bounded agent tools and usage',
                    'Evaluation and human approval',
                ],
                'flip' => false,
            ],
        ],
        'stats' => [
            [
                'val' => 'Focused',
                'lbl' => 'Defined Business Tasks',
            ],
            [
                'val' => 'Connected',
                'lbl' => 'Approved Integrations',
            ],
            [
                'val' => 'Reviewed',
                'lbl' => 'Tested Pilot Outcomes',
            ],
            [
                'val' => 'Global',
                'lbl' => 'Remote Collaboration',
            ],
        ],
        'cards_heading' => 'Our AI &amp; Automation Services',
        'cards_sub' => 'Choose the service that matches the task, the systems involved and the level of human control required.',
        'cards' => [
            [
                'icon' => 'bi-robot',
                'color' => '#EE483D',
                'title' => 'AI Chatbot Development',
                'href' => '/ai-chatbot-integration',
                'desc' => 'Build a business chatbot with approved knowledge, lead qualification and human handoff. Chulbul Design develops website and support chatbots for teams worldwide.',
            ],
            [
                'icon' => 'bi-whatsapp',
                'color' => '#49499A',
                'title' => 'WhatsApp Automation',
                'href' => '/whatsapp-automation',
                'desc' => 'Connect WhatsApp enquiries to qualification, CRM updates and team handoff. Chulbul Design builds scoped business messaging workflows for clients worldwide.',
            ],
            [
                'icon' => 'bi-diagram-3',
                'color' => '#16a34a',
                'title' => 'Workflow Automation',
                'href' => '/workflow-automation',
                'desc' => 'Connect forms, CRM and everyday business tools with tested triggers, data mapping and error handling. Chulbul Design builds maintainable workflow automation.',
            ],
            [
                'icon' => 'bi-cpu',
                'color' => '#0ea5e9',
                'title' => 'AI Integration',
                'href' => '/ai-integration',
                'desc' => 'Add scoped AI features to your website, CRM or application with data controls, output checks and fallback behaviour. AI integration services for businesses worldwide.',
            ],
            [
                'icon' => 'bi-building-gear',
                'color' => '#7c3aed',
                'title' => 'Business Process Automation',
                'href' => '/ai-business-automation',
                'desc' => 'Improve end-to-end business processes with clear ownership, approvals, connected records and exception handling. Practical process automation for teams worldwide.',
            ],
            [
                'icon' => 'bi-robot',
                'color' => '#ea580c',
                'title' => 'AI Agent Development',
                'href' => '/ai-agent-development',
                'desc' => 'Build task-focused AI agents with scoped tools, approval gates, evaluation and usage limits. Chulbul Design develops controlled business agents for teams worldwide.',
            ],
        ],
        'process_heading' => 'How We Plan an AI or Automation Project',
        'steps' => [
            [
                'num' => '01',
                'icon' => 'bi-chat-dots-fill',
                'title' => 'Define the Task',
                'desc' => 'Agree the business problem, users and a useful completion outcome.',
            ],
            [
                'num' => '02',
                'icon' => 'bi-list-check',
                'title' => 'Map Data and Rules',
                'desc' => 'Review sources, permissions, integrations and decisions that need approval.',
            ],
            [
                'num' => '03',
                'icon' => 'bi-diagram-3',
                'title' => 'Design the Pilot',
                'desc' => 'Choose a focused implementation and representative test cases.',
            ],
            [
                'num' => '04',
                'icon' => 'bi-code-slash',
                'title' => 'Build and Evaluate',
                'desc' => 'Verify outputs, saved records, failure handling and operating limits.',
            ],
            [
                'num' => '05',
                'icon' => 'bi-journal-check',
                'title' => 'Launch and Hand Over',
                'desc' => 'Document controls, ongoing costs, ownership and support arrangements.',
            ],
        ],
    ],
];

// ── 404 if slug not found in pages ────────────────────────────────────────────
if (!isset($pages[$slug])) {
    http_response_code(404);
    include __DIR__ . '/404.php';
    exit;
}

$p = $pages[$slug];
$canonical = $domain . '/' . $slug;
$og_image  = $domain . $p['og_image'];

// ── Merge DB sub-services into cards if available ────────────────────────────
// DB sub-services can override/extend the static cards
$display_cards = $p['cards'];
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="utf-8">
    <title><?= htmlspecialchars($p['title']) ?></title>
    <link rel="canonical" href="<?= $canonical ?>">
    <meta name="description" content="<?= htmlspecialchars($p['meta_desc']) ?>">
    <meta property="og:title" content="<?= htmlspecialchars($p['title']) ?>">
    <meta property="og:url" content="<?= $canonical ?>">
    <meta property="og:type" content="website">
    <meta property="og:description" content="<?= htmlspecialchars($p['meta_desc']) ?>">
    <meta property="og:image" content="<?= $og_image ?>">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:site" content="@chulbuldesign">
    <meta name="twitter:image" content="<?= $og_image ?>">
    <script type="application/ld+json"><?= json_encode([
        '@context'  => 'https://schema.org',
        '@type'     => 'Service',
        'name'      => $p['schema_name'],
        'provider'  => ['@type'=>'ProfessionalService','name'=>'Chulbul Design','url'=>'https://www.chulbuldesign.com','telephone'=>'+919990548795'],
        'areaServed'=> in_array($slug, ['technologies', 'seo-digital-marketing', 'ui-ux-branding', 'website-support', 'ai-automation'], true) ? 'Worldwide' : 'IN',
        'description'=> $p['schema_desc'],
    ], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) ?></script>
    <link rel="preload" as="image" href="<?= $base . $p['hero']['img'] ?>" fetchpriority="high">
    <?php require __DIR__ . '/includes/header.php'; ?>
<?php
$hero = $p['hero'];
require __DIR__ . '/includes/page-hero.php';
?>

<!-- Intro -->
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <p class="text-gray-600 text-center text-lg leading-relaxed max-w-4xl mx-auto">
        <?= $p['intro'] ?>
    </p>
</section>

<?php foreach ($p['sections'] as $si => $sec):
    $is_flip = $sec['flip'];
    $img_side = '<div class="flex-1 flex justify-center"><img src="'.$base.$sec['img'].'" alt="'.htmlspecialchars($sec['img_alt']).'" class="w-full max-w-md rounded-2xl" width="480" height="400" loading="lazy"></div>';
    $txt_side = '<div class="flex-1"><h2 class="text-3xl font-extrabold text-[#EE483D] mb-4">'.$sec['h2'].'</h2><p class="text-gray-600 leading-relaxed mb-4">'.$sec['para'].'</p><ul class="space-y-2 text-gray-600 text-sm">'.implode('',array_map(fn($b)=>'<li class="flex items-center gap-2"><i class="bi bi-check-circle-fill text-[#EE483D]"></i> '.$b.'</li>',$sec['bullets'])).'</ul></div>';
    if ($si % 2 === 0): // white bg
?>
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="flex flex-col <?= $is_flip ? 'lg:flex-row-reverse' : 'lg:flex-row' ?> items-center gap-12">
        <?= $is_flip ? $txt_side . $img_side : $img_side . $txt_side ?>
    </div>
</section>
<?php else: // gray bg ?>
<section class="bg-gray-50 py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col <?= $is_flip ? 'lg:flex-row' : 'lg:flex-row-reverse' ?> items-center gap-12">
            <?= $is_flip ? $img_side . $txt_side : $txt_side . $img_side ?>
        </div>
    </div>
</section>
<?php endif; endforeach; ?>

<!-- Stats Bar -->
<section class="bg-[#1e1e5c] py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-8 text-center">
            <?php foreach ($p['stats'] as $st): ?>
            <div>
                <p class="text-4xl font-extrabold text-[#EE483D] mb-1"><?= $st['val'] ?></p>
                <p class="text-white/70 text-sm"><?= $st['lbl'] ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Service Cards -->
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="text-center mb-12">
        <span class="text-xs font-bold tracking-widest uppercase text-[#EE483D] bg-red-50 px-4 py-1.5 rounded-full">What We Do</span>
        <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 mt-4 mb-3"><?= $p['cards_heading'] ?></h2>
        <p class="text-gray-500 max-w-2xl mx-auto"><?= $p['cards_sub'] ?></p>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($display_cards as $s): ?>
        <a href="<?= $base . $s['href'] ?>" class="bg-white rounded-2xl p-7 shadow-sm border border-gray-100 hover:shadow-md hover:-translate-y-1 transition-all group block no-underline">
            <div class="w-14 h-14 rounded-2xl flex items-center justify-center mb-5 group-hover:scale-110 transition-transform" style="background:<?= $s['color'] ?>1a">
                <i class="bi <?= $s['icon'] ?> text-2xl" style="color:<?= $s['color'] ?>"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2"><?= $s['title'] ?></h3>
            <p class="text-gray-500 text-sm leading-relaxed"><?= $s['desc'] ?></p>
        </a>
        <?php endforeach; ?>
    </div>
</section>

<!-- Process -->
<section class="bg-gray-50 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <span class="text-xs font-bold tracking-widest uppercase text-[#EE483D] bg-red-50 px-4 py-1.5 rounded-full">Our Process</span>
            <h2 class="text-3xl font-extrabold text-gray-900 mt-4 mb-3"><?= $p['process_heading'] ?></h2>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6">
            <?php foreach ($p['steps'] as $step): ?>
            <div class="bg-white rounded-2xl p-6 text-center shadow-sm border border-gray-100">
                <div class="w-12 h-12 rounded-xl bg-[#EE483D] flex items-center justify-center mx-auto mb-3 relative">
                    <i class="bi <?= $step['icon'] ?> text-white text-lg"></i>
                    <span class="absolute -top-2 -right-2 w-5 h-5 rounded-full bg-[#49499A] text-white text-xs font-bold flex items-center justify-center"><?= $step['num'] ?></span>
                </div>
                <h3 class="font-bold text-gray-900 mb-1"><?= $step['title'] ?></h3>
                <p class="text-gray-500 text-xs leading-relaxed"><?= $step['desc'] ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="city-cta-section">
    <div class="city-cta-deco-1" aria-hidden="true"></div>
    <div class="city-cta-deco-2" aria-hidden="true"></div>
    <div class="city-cta-deco-3" aria-hidden="true"></div>
    <div class="city-cta-deco-4" aria-hidden="true"></div>
    <div class="max-w-5xl mx-auto px-4 sm:px-6 city-cta-inner">
        <div class="city-cta-heading-wrap">
            <span class="city-cta-badge"><i class="bi bi-lightning-charge-fill"></i> Let's Work Together</span>
        </div>
        <h2 class="city-cta-title">Your Business Deserves<br><span class="city-cta-title-highlight">Better Results.</span></h2>
        <p class="city-cta-desc">Free consultation — we'll look at your current situation, tell you exactly what we'd do and give you an honest quote. No sales pitch.</p>
        <div class="city-cta-stats">
            <div class="city-cta-stat"><i class="bi bi-patch-check-fill city-cta-stat-icon-green"></i> 500+ Projects Delivered</div>
            <div class="city-cta-stat"><i class="bi bi-file-code city-cta-stat-icon-blue"></i> Custom Work Only</div>
            <div class="city-cta-stat"><i class="bi bi-star-fill city-cta-stat-icon-yellow"></i> 5★ Google Rating</div>
            <div class="city-cta-stat"><i class="bi bi-clock-fill city-cta-stat-icon-pink"></i> Reply within 1 Hour</div>
        </div>
        <div class="city-cta-btns">
            <a href="<?= $base ?>/contact-us" class="city-cta-btn-primary"><i class="bi bi-envelope-fill"></i> Get a Free Quote</a>
            <a href="https://wa.me/919990548795" target="_blank" rel="noopener noreferrer" class="city-cta-btn-wa"><i class="bi bi-whatsapp"></i> WhatsApp Us</a>
        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
