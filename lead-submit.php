<?php
// ── Public lead form handler (saves to `leads` table) ────────────────────────
require_once __DIR__ . '/includes/lead-store.php';
require_once __DIR__ . '/includes/analytics-outcomes.php';
header('Content-Type: application/json');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('X-Robots-Tag: noindex, nofollow, noarchive');
header('X-Content-Type-Options: nosniff');

function lead_response(array $payload, int $status = 200): void {
    http_response_code($status);
    echo json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Allow: POST');
    lead_response(['success' => false, 'error' => 'POST only'], 405);
}

// Honeypot — bots fill hidden fields; humans don't
if (!empty($_POST['_hp'])) { lead_response(['success' => true]); }

// Reject malformed fields before trimming them; all public forms use scalar text.
foreach (['name','phone','business','email','service','city','industry','page_url','message','source','budget','timeline','form_version'] as $field) {
    if (isset($_POST[$field]) && !is_string($_POST[$field])) {
        lead_response(['success' => false, 'error' => 'Please check the form details and try again.'], 422);
    }
}

$name     = trim($_POST['name']     ?? '');
$phone    = trim($_POST['phone']    ?? '');
$business = trim($_POST['business'] ?? '');
$email    = trim($_POST['email']    ?? '');
$service  = trim($_POST['service']  ?? '');
$city     = trim($_POST['city']     ?? '');
$industry = trim($_POST['industry'] ?? '');
$pageUrl  = trim($_POST['page_url'] ?? '');
$message  = trim($_POST['message']  ?? '');
$source   = preg_replace('/[^a-z0-9_-]/i', '', $_POST['source'] ?? 'landing');
$budget   = trim($_POST['budget'] ?? '');
$timeline = trim($_POST['timeline'] ?? '');

$flexibleContact = ($_POST['form_version'] ?? '') === 'city-service-v3';
if ($name === '' || (!$flexibleContact && $phone === '')) {
    lead_response(['success' => false, 'error' => 'Please enter your name and phone number.'], 422);
}
if ($flexibleContact && $phone === '' && $email === '') {
    lead_response(['success' => false, 'error' => 'Please enter an email address or phone number.'], 422);
}
if (($_POST['form_version'] ?? '') === 'city-project-v2' && $business === '') {
    lead_response(['success' => false, 'error' => 'Please enter your business or company name.'], 422);
}
if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    lead_response(['success' => false, 'error' => 'Please enter a valid email address.'], 422);
}
$phoneDigits = preg_replace('/\D/', '', $phone);
if (str_starts_with($phone, '00')) $phoneDigits = substr($phoneDigits, 2);
if ($phone !== '' && (strlen($phoneDigits) < 7 || strlen($phoneDigits) > 15)) {
    lead_response(['success' => false, 'error' => 'Please enter a valid phone number with its country code.'], 422);
}
if ($phone !== '' && !preg_match('/^\+?[0-9\s().-]+$/', $phone)) {
    lead_response(['success' => false, 'error' => 'Please use digits and a country code for your phone number.'], 422);
}

// Basic sanity limits
$name     = mb_substr($name, 0, 120);
$phone    = (str_starts_with($phone, '+') || str_starts_with($phone, '00') ? '+' : '') . $phoneDigits;
$business = mb_substr($business, 0, 160);
$email    = mb_substr($email, 0, 190);
$service  = mb_substr(strip_tags($service), 0, 120);
$city     = mb_substr(strip_tags($city), 0, 120);
$industry = mb_substr(strip_tags($industry), 0, 120);
$pageUrl  = mb_substr(filter_var($pageUrl, FILTER_SANITIZE_URL), 0, 500);
$source   = mb_substr($source ?: 'landing', 0, 80);
$message  = mb_substr(strip_tags($message), 0, 1000);
$budget   = mb_substr(preg_replace('/[\r\n]+/', ' ', strip_tags($budget)), 0, 100);
$timeline = mb_substr(preg_replace('/[\r\n]+/', ' ', strip_tags($timeline)), 0, 100);
// Only keep a website path for attribution, not query-string personal data.
$pageParts = parse_url($pageUrl);
if (!is_array($pageParts) || !in_array(strtolower($pageParts['scheme'] ?? ''), ['http','https'], true)
    || !in_array(strtolower($pageParts['host'] ?? ''), ['www.chulbuldesign.com','chulbuldesign.com','localhost','127.0.0.1'], true)) {
    $pageUrl = '';
} else {
    $pageUrl = $pageParts['scheme'].'://'.$pageParts['host'].($pageParts['path'] ?? '/');
}

if ($business === '' && $service !== '') $business = $service;
$leadDetails = [];
if ($email !== '')   $leadDetails[] = 'Email: ' . $email;
if ($service !== '') $leadDetails[] = 'Service: ' . $service;
if ($city !== '')    $leadDetails[] = 'City: ' . $city;
if ($industry !== '') $leadDetails[] = 'Industry: ' . $industry;
if ($budget !== '') $leadDetails[] = 'Budget: ' . $budget;
if ($timeline !== '') $leadDetails[] = 'Expected start: ' . $timeline;
if ($pageUrl !== '') $leadDetails[] = 'Page: ' . $pageUrl;
if ($message !== '') $leadDetails[] = 'Message: ' . $message;
$storedMessage = mb_substr(implode("\n", $leadDetails), 0, 2000);

try {
    $savedLeadId = (string)cbd_save_lead($name, $phone, $business, $storedMessage, $source);
} catch (Throwable $e) {
    error_log('Lead save failed: ' . $e->getMessage());
    lead_response(['success' => false, 'error' => 'We could not save your enquiry. Please try again shortly.'], 500);
}

// ── Email notification — lead aate hi aapko email ───────────────────────────
cbd_analytics_outcome('lead_saved', 'lead:' . $savedLeadId, 'Enquiry form', '/lead-submit.php');
$LEAD_EMAILS = cbd_lead_recipients();
$ip = $_SERVER['HTTP_CF_CONNECTING_IP'] ?? ($_SERVER['REMOTE_ADDR'] ?? '');
$digits = preg_replace('/\D/', '', $phone);
// Never turn a US/UK ten-digit number into an Indian WhatsApp number.
$wa = str_starts_with($phone, '+') ? 'https://wa.me/' . $digits : '';
$subjectLabel = $city !== '' ? $city : ($industry !== '' ? $industry : ($source === 'business-starter' ? 'Business Starter Pack' : $source));
$subj = '=?UTF-8?B?' . base64_encode('🔔 Nayi Lead — ' . $subjectLabel) . '?=';
$safe = static fn($value) => htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
$html = '<div style="font-family:Arial,sans-serif;font-size:15px;color:#222">'
      . '<h2 style="color:#1e1e5c;margin:0 0 12px">🔔 Nayi Lead aayi!</h2>'
      . '<table cellpadding="6" style="border-collapse:collapse">'
      . '<tr><td><b>Name</b></td><td>'    . $safe($name)           . '</td></tr>'
      . '<tr><td><b>Phone</b></td><td>'   . $safe($phone)          . '</td></tr>'
      . '<tr><td><b>Email</b></td><td>'   . $safe($email ?: '-')   . '</td></tr>'
      . '<tr><td><b>Business</b></td><td>' . $safe($business ?: '-') . '</td></tr>'
      . '<tr><td><b>Service</b></td><td>' . $safe($service ?: $business ?: '-') . '</td></tr>'
      . '<tr><td><b>City</b></td><td>'    . $safe($city ?: '-')    . '</td></tr>'
      . '<tr><td><b>Industry</b></td><td>' . $safe($industry ?: '-') . '</td></tr>'
      . '<tr><td><b>Budget</b></td><td>' . $safe($budget ?: 'Not provided') . '</td></tr>'
      . '<tr><td><b>Expected start</b></td><td>' . $safe($timeline ?: 'Not provided') . '</td></tr>'
      . '<tr><td><b>Requirements</b></td><td>' . $safe($message ?: '-') . '</td></tr>'
      . '<tr><td><b>Source</b></td><td>'  . $safe($source)         . '</td></tr>'
      . '<tr><td><b>Page</b></td><td>'    . ($pageUrl ? '<a href="' . $safe($pageUrl) . '">' . $safe($pageUrl) . '</a>' : '-') . '</td></tr>'
      . '</table>'
      . ($wa !== '' ? '<p style="margin:16px 0"><a href="' . $wa . '" style="background:#25D366;color:#fff;padding:10px 18px;border-radius:8px;text-decoration:none;font-weight:bold">Contact this lead on WhatsApp</a></p>' : '<p>Confirm the country code before contacting this number on WhatsApp.</p>')
      . '<p style="color:#888;font-size:12px">Time: ' . date('d M Y, h:i A') . ' &middot; IP: ' . $safe($ip ?? '') . '</p>'
      . '</div>';
$headers = "MIME-Version: 1.0\r\n"
         . "Content-Type: text/html; charset=UTF-8\r\n"
         . "From: Chulbul Leads <noreply@chulbuldesign.com>\r\n";
if ($email !== '') $headers .= "Reply-To: " . str_replace(["\r", "\n"], '', $email) . "\r\n";
foreach ($LEAD_EMAILS as $recipient) {
    if (!@mail($recipient, $subj, $html, $headers)) {
        error_log('Lead email notification could not be sent to ' . $recipient . ' for source: ' . $source);
    }
}

$visitorMessage = "Hi Chulbul Design, I have submitted a quote request.\nName: {$name}";
if ($business !== '') $visitorMessage .= "\nBusiness: {$business}";
if ($city !== '') $visitorMessage .= "\nCity: {$city}";
if ($industry !== '') $visitorMessage .= "\nIndustry: {$industry}";
if ($service !== '') $visitorMessage .= "\nService: {$service}";
if ($budget !== '') $visitorMessage .= "\nBudget: {$budget}";
if ($timeline !== '') $visitorMessage .= "\nExpected start: {$timeline}";
if ($message !== '') $visitorMessage .= "\nRequirements: {$message}";
$visitorWhatsapp = 'https://wa.me/919990548795?text=' . rawurlencode($visitorMessage);

lead_response([
    'success' => true,
    'whatsapp_url' => $visitorWhatsapp,
]);
