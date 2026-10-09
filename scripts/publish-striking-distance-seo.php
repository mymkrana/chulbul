<?php
declare(strict_types=1);

/**
 * Striking Distance SEO Migration Endpoint
 * Safely updates Perth, Montreal, and Toronto SEO metadata in the production database.
 */

const CBD_SEO_PUBLISH_SECRET = 'cbd_seo_live_2026_9b8c71';

if (PHP_SAPI !== 'cli') {
    $token = (string)($_GET['token'] ?? $_POST['token'] ?? $_SERVER['HTTP_X_CBD_PUBLISH_TOKEN'] ?? '');
    if (!hash_equals(CBD_SEO_PUBLISH_SECRET, $token)) {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Access denied'], JSON_THROW_ON_ERROR);
        exit;
    }
}

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
header('X-Robots-Tag: noindex, nofollow');

require_once dirname(__DIR__) . '/includes/database.php';

$pdo = cbd_database();
if (!$pdo) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database connection failed'], JSON_THROW_ON_ERROR);
    exit;
}

try {
    $pdo->beginTransaction();
    $updates = [];

    // 1. PERTH ECOMMERCE SPOKE (Target: "shopify developer perth" Rank: 4.2)
    $stmt = $pdo->prepare("
        UPDATE city_spokes cs
        JOIN cities c ON cs.city_id = c.id
        SET 
            cs.meta_title = 'Shopify Developer Perth | WooCommerce & Ecommerce Experts – Chulbul Design',
            cs.meta_description = 'Hire expert Shopify & WooCommerce developers in Perth. High-converting online stores with Afterpay, Zip & Stripe integration. Fast 2-4 week delivery from A$1,299.',
            cs.heading_html = 'Shopify & WooCommerce Developer in Perth <span>Custom High-Converting Online Stores</span>',
            cs.hero_description = 'Looking for a dedicated Shopify developer or WooCommerce specialist in Perth? We engineer fast, conversion-driven online stores with Afterpay, Zip, Stripe Australia, and automated logistics—tailored for Western Australia retailers.',
            cs.faqs_json = '[{\"question\":\"Can I hire a dedicated Shopify or WooCommerce developer in Perth?\",\"answer\":\"Yes! We provide dedicated Shopify and WooCommerce developers in Perth for custom storefront builds, theme development, third-party integrations (Afterpay, Australia Post, ERPs), and speed optimization.\"},{\"question\":\"How much does an ecommerce website cost in Perth?\",\"answer\":\"Our custom ecommerce website development for Perth businesses starts from A$1,299 for clean, high-speed Shopify or WooCommerce stores, scaling based on catalog volume and third-party integrations.\"},{\"question\":\"Should our Perth business choose Shopify or WooCommerce?\",\"answer\":\"Shopify is ideal for direct-to-consumer retailers seeking zero maintenance and high checkout reliability. WooCommerce is best for businesses requiring full database ownership, deep content integration, or custom wholesale pricing.\"},{\"question\":\"Which payment gateways and shipping methods do you integrate?\",\"answer\":\"We configure Stripe Australia, Afterpay, Zip, and Apple Pay, alongside Australia Post eParcel, StarTrack, and Sendle with automated customer order status notifications.\"},{\"question\":\"How are project milestones managed across timezones?\",\"answer\":\"Our technical team aligns sprint calls, progress demos, and milestone sign-offs to AWST (Western Time) Business Hours, with daily asynchronous progress tracking via Slack or WhatsApp.\"}]',
            cs.updated_at = NOW()
        WHERE c.slug = 'perth' AND cs.slug = 'ecommerce-development'
    ");
    $stmt->execute();
    $updates['perth_ecommerce_spoke'] = $stmt->rowCount();

    // 2. PERTH MAIN CITY PAGE (Target: "small business web design perth", "affordable web design perth")
    $stmt = $pdo->prepare("
        UPDATE cities SET
            meta_title = 'Web Design Agency Perth | Small Business Websites from A$699 – Chulbul Design',
            meta_description = 'Top web design agency in Perth. Affordable, custom & mobile-first websites for small businesses from A$699. Serving Perth CBD, Fremantle & WA. Free consultation today!',
            heading_html = 'Web Design Agency in Perth <span class=\"text-[#EE483D]\">Affordable Small Business Websites</span>',
            updated_at = NOW()
        WHERE slug = 'perth'
    ");
    $stmt->execute();
    $updates['perth_city'] = $stmt->rowCount();

    // 3. MONTREAL MAIN CITY PAGE (Target: "bilingual websites montreal" Rank: 5.78, "bilingual web design montreal" Rank: 6.22)
    $stmt = $pdo->prepare("
        UPDATE cities SET
            meta_title = 'Bilingual Web Design Montreal | English & French Websites – Chulbul Design',
            meta_description = 'Expert bilingual web design in Montreal, Quebec. Bill 96 compliant, fast English & French websites from C$699. Serving Downtown, Plateau & Greater Montreal. Free quote!',
            heading_html = 'Bilingual Web Design Agency in Montreal <span class=\"text-[#EE483D]\">English & French Websites</span>',
            hero_description = 'Montreal\\'s bilingual business ecosystem demands websites that seamlessly convert in both English and French. We build high-performance, Bill 96-compliant bilingual websites that rank on Google Canada and convert local visitors.',
            updated_at = NOW()
        WHERE slug = 'montreal'
    ");
    $stmt->execute();
    $updates['montreal_city'] = $stmt->rowCount();

    // 4. MONTREAL SMALL BUSINESS SPOKE (Target: "bilingual website design quebec")
    $stmt = $pdo->prepare("
        UPDATE city_spokes cs
        JOIN cities c ON cs.city_id = c.id
        SET 
            cs.meta_title = 'Small Business Web Design Montreal | Bilingual Websites from C$699 – Chulbul Design',
            cs.meta_description = 'Affordable small business web design in Montreal, Quebec. Bill 96 compliant bilingual websites with fast 2-4 week delivery, local SEO, and fixed pricing from C$699.',
            cs.updated_at = NOW()
        WHERE c.slug = 'montreal' AND cs.slug = 'small-business-web-design'
    ");
    $stmt->execute();
    $updates['montreal_small_business_spoke'] = $stmt->rowCount();

    // 5. TORONTO MAIN CITY PAGE (Target: "web agency toronto" Rank: 4.62, "toronto web design agency" Rank: 7.18)
    $stmt = $pdo->prepare("
        UPDATE cities SET
            meta_title = 'Top Web Design Agency Toronto | Web Agency from C$699 – Chulbul Design',
            meta_description = 'Looking for a top web design agency in Toronto? Custom websites, WordPress & ecommerce platforms for GTA businesses from C$699. 2-4 week delivery. Get a free proposal!',
            heading_html = 'Top Web Design Agency <span class=\"text-[#EE483D]\">in Toronto & GTA</span>',
            hero_description = 'Ranked among the leading web agencies in Toronto, we craft custom, high-converting websites and SEO-driven digital platforms for GTA startups and enterprises from C$699.',
            updated_at = NOW()
        WHERE slug = 'toronto'
    ");
    $stmt->execute();
    $updates['toronto_city'] = $stmt->rowCount();

    // 6. TORONTO SMALL BUSINESS SPOKE (Target: "toronto web design agency", "website design agency toronto")
    $stmt = $pdo->prepare("
        UPDATE city_spokes cs
        JOIN cities c ON cs.city_id = c.id
        SET 
            cs.meta_title = 'Toronto Web Design Agency for Small Business | Custom Websites – Chulbul Design',
            cs.meta_description = 'Top small business web design agency in Toronto. High-converting custom websites from C$699 with fast 2-4 week delivery, local SEO setup & full mobile responsiveness.',
            cs.updated_at = NOW()
        WHERE c.slug = 'toronto' AND cs.slug = 'small-business-web-design'
    ");
    $stmt->execute();
    $updates['toronto_small_business_spoke'] = $stmt->rowCount();

    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Striking distance SEO updates applied successfully',
        'details' => $updates,
        'timestamp' => date('c'),
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
}
