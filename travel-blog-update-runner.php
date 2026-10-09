<?php
declare(strict_types=1);

/**
 * Travel Blog Production SEO Migration Runner
 * Updates travel-agency-website-cost title, meta tags, pricing table & FAQs in production DB.
 */

const CBD_SEO_PUBLISH_SECRET = 'cbd_travel_blog_seo_2026_x871a';

$token = (string)($_GET['token'] ?? $_POST['token'] ?? $_SERVER['HTTP_X_CBD_PUBLISH_TOKEN'] ?? '');
if (PHP_SAPI !== 'cli' && !hash_equals(CBD_SEO_PUBLISH_SECRET, $token)) {
    http_response_code(403);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Access denied'], JSON_THROW_ON_ERROR);
    exit;
}

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
header('X-Robots-Tag: noindex, nofollow');

require_once __DIR__ . '/includes/database.php';

$pdo = cbd_database();
if (!$pdo) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database connection failed'], JSON_THROW_ON_ERROR);
    exit;
}

try {
    $row = $pdo->query("SELECT id, content FROM posts WHERE slug = 'travel-agency-website-cost'")->fetch(PDO::FETCH_ASSOC);
    if (!$row) {
        throw new RuntimeException("Post not found");
    }

    $content = $row['content'];

    // Don't re-insert if already present
    if (!str_contains($content, 'How Much Does a Travel Agency or Tour Operator Website Cost in 2026?')) {
        $snippet_html = <<<'HTML'

<div class="bg-gradient-to-r from-red-50 to-orange-50 border-l-4 border-[#EE483D] p-6 rounded-r-2xl mb-8 shadow-sm my-6">
    <h2 class="text-2xl font-bold text-gray-900 mb-3">How Much Does a Travel Agency or Tour Operator Website Cost in 2026?</h2>
    <p class="text-gray-700 leading-relaxed mb-4">
        In 2026, building a <strong>travel agency or tour operator website typically costs between $1,500 and $15,000+</strong> (or <strong>£1,200 – £12,000 / A$2,200 – A$22,000</strong>). A starter brochure travel website with inquiry forms costs <strong>$1,500 – $3,500</strong>. A dynamic tour operator website with online booking, availability calendars, and payment gateways costs <strong>$3,500 – $8,500</strong>. Advanced travel portals with live flight, hotel, or GDS/API integrations range from <strong>$8,500 to $25,000+</strong>.
    </p>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse bg-white rounded-xl shadow-sm overflow-hidden text-sm my-2">
            <thead>
                <tr class="bg-[#49499A] text-white">
                    <th class="py-3 px-4 font-semibold">Website Tier</th>
                    <th class="py-3 px-4 font-semibold">Average Cost (USD)</th>
                    <th class="py-3 px-4 font-semibold">Timeline</th>
                    <th class="py-3 px-4 font-semibold">Key Features Included</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-gray-700">
                <tr>
                    <td class="py-3 px-4 font-medium text-gray-900">Starter Travel Agency Website</td>
                    <td class="py-3 px-4 font-bold text-[#EE483D]">$1,500 – $3,500</td>
                    <td class="py-3 px-4">2 – 3 weeks</td>
                    <td class="py-3 px-4">Custom responsive design, destination guides, itinerary downloads, inquiry & WhatsApp forms, mobile-ready.</td>
                </tr>
                <tr class="bg-gray-50/50">
                    <td class="py-3 px-4 font-medium text-gray-900">Tour Operator with Online Booking</td>
                    <td class="py-3 px-4 font-bold text-[#EE483D]">$3,500 – $8,500</td>
                    <td class="py-3 px-4">3 – 5 weeks</td>
                    <td class="py-3 px-4">Real-time availability calendars, deposit payments, multi-currency checkout (Stripe/PayPal), automated email confirmations.</td>
                </tr>
                <tr>
                    <td class="py-3 px-4 font-medium text-gray-900">Full Custom OTA / Travel Portal</td>
                    <td class="py-3 px-4 font-bold text-[#EE483D]">$8,500 – $25,000+</td>
                    <td class="py-3 px-4">6 – 10 weeks</td>
                    <td class="py-3 px-4">Flight/Hotel GDS APIs (Amadeus, Sabre, Viator), multi-vendor supplier management, CRM integration, custom booking engine.</td>
                </tr>
                <tr class="bg-gray-50/50">
                    <td class="py-3 px-4 font-medium text-gray-900">Ongoing Maintenance & Support</td>
                    <td class="py-3 px-4 font-bold text-gray-800">$50 – $300 / mo</td>
                    <td class="py-3 px-4">Monthly</td>
                    <td class="py-3 px-4">Ultra-fast cloud hosting, security patches, itinerary updates, SSL, speed optimization & daily backups.</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
HTML;

        $img_end_pos = strpos($content, '</p>');
        if ($img_end_pos !== false) {
            $content = substr($content, 0, $img_end_pos + 4) . "\n" . $snippet_html . "\n" . substr($content, $img_end_pos + 4);
        } else {
            $content = $snippet_html . "\n" . $content;
        }
    }

    if (!str_contains($content, 'How much does a tour operator website cost?')) {
        $faq_header = 'Frequently Asked Questions</h2>';
        $faq_new = <<<'HTML'

<h3>How much does a tour operator website cost?</h3>
<p>A tour operator website typically costs between $3,500 and $8,500 for a complete online booking system with interactive tour itineraries, seasonal pricing, calendar availability, and automated payment gateway processing. Simpler catalog websites for tour operators start around $1,500 to $2,500.</p>
HTML;

        if (strpos($content, $faq_header) !== false) {
            $content = str_replace($faq_header, $faq_header . "\n" . $faq_new, $content);
        }
    }

    $new_title = 'Travel Agency & Tour Operator Website Cost in 2026: Complete Pricing Guide';
    $new_meta_title = 'Travel Agency & Tour Operator Website Cost 2026 (Pricing Guide)';
    $new_meta_desc = 'How much does a travel agency or tour operator website cost in 2026? Realistic pricing from $1,500 to $15,000+ with booking engines, itineraries & API integrations.';

    $stmt = $pdo->prepare('UPDATE posts SET title = ?, meta_title = ?, meta_desc = ?, content = ?, updated_at = NOW() WHERE slug = ?');
    $stmt->execute([$new_title, $new_meta_title, $new_meta_desc, $content, 'travel-agency-website-cost']);

    echo json_encode([
        'success' => true,
        'message' => 'Travel agency blog updated successfully on live server',
        'affected_rows' => $stmt->rowCount(),
        'timestamp' => date('c'),
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
}
