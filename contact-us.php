<?php
require_once __DIR__ . '/includes/http.php';
if (cbd_is_direct_script_request('contact-us.php')) cbd_redirect_path('/contact-us');
$base = cbd_base_path();
require_once __DIR__ . '/includes/analytics-outcomes.php';
require_once __DIR__ . '/includes/lead-store.php';
$contactStatus = isset($_POST['submit']) ? 'invalid' : '';
if(isset($_POST['submit'])){
    $validFields = true;
    foreach (['name', 'phone', 'cname', 'mag', 'email'] as $field) {
        if (isset($_POST[$field]) && !is_string($_POST[$field])) $validFields = false;
    }
    if ($validFields) {
        $name  = mb_substr(trim(strip_tags($_POST['name'] ?? '')), 0, 120);
        $phone = mb_substr(trim(strip_tags($_POST['phone'] ?? '')), 0, 20);
        $cname = mb_substr(trim(strip_tags($_POST['cname'] ?? '')), 0, 160);
        $mag   = mb_substr(trim(strip_tags($_POST['mag'] ?? '')), 0, 1500);
        $from  = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
    } else {
        $name = $phone = $cname = $mag = '';
        $from = false;
    }

    if ($from && $name && $mag) {
        try {
            $savedLeadId = cbd_save_lead($name, $phone, $cname, "Email: {$from}\nMessage: {$mag}", 'contact-us');
        } catch (Throwable $error) {
            error_log('Contact enquiry could not be saved: ' . $error->getMessage());
            $contactStatus = 'failed';
        }
    }

    if ($contactStatus !== 'failed' && $from && $name && $mag) {
        $subject = "New Contact Form Submission";
        $message = "Name: $name\nPhone: $phone\nEmail: $from\nCompany: $cname\n\nMessage:\n$mag";
        $headers = "From: noreply@chulbuldesign.com\r\nReply-To: $from\r\nX-Mailer: PHP/" . phpversion();
        $contactStatus = 'accepted';
        foreach (cbd_lead_recipients(true) as $recipient) {
            if (!@mail($recipient, $subject, $message, $headers)) {
                error_log('Contact enquiry email notification could not be sent to ' . $recipient . ' for lead ' . $savedLeadId);
                $contactStatus = 'saved';
            }
        }
        $viewRef = is_string($_POST['_cbd_view'] ?? null) ? $_POST['_cbd_view'] : '';
        cbd_analytics_outcome('lead_saved', 'lead:' . $savedLeadId, 'Contact Us form', '/contact-us');
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="utf-8">
    <title>Contact Us | Get in Touch with Chulbul Design</title>
    <link rel="canonical" href="https://www.chulbuldesign.com/contact-us">
    <meta name="description" content="Want to discuss your development, designing or digital service requirements? Contact us or submit your query! Our experts are available 24*7. @ 9990548795">
    <meta property="og:title" content="Contact Us | Chulbul Design">
    <meta property="og:url" content="https://www.chulbuldesign.com/contact-us">
    <meta property="og:type" content="website">
    <meta property="og:description" content="Contact Chulbul Design — India's leading web design & digital marketing agency. Call +91 9990548795 or WhatsApp us for a free quote.">
    <meta property="og:image" content="https://www.chulbuldesign.com/assets/images/Fb_chulbuldesign.jpg">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:site" content="@chulbuldesign">
    <meta name="twitter:image" content="https://www.chulbuldesign.com/assets/images/Fb_chulbuldesign.jpg">
    <script type="application/ld+json">{"@context":"https://schema.org","@type":"ContactPage","name":"Contact Chulbul Design","url":"https://www.chulbuldesign.com/contact-us","telephone":"+919990548795","email":"info@chulbuldesign.com"}</script>
    <!-- Preload LCP image -->
    <link rel="preload" as="image" href="<?= $base ?>/assets/images/banner/contact.jpg" fetchpriority="high">
    <?php require __DIR__ . '/includes/header.php'; ?>

<?php
$hero = [
    'badge'      => 'Contact Us',
    'breadcrumb' => 'Contact',
    'h1'         => 'Happy to Help. <span class="text-[#EE483D]">Let\'s talk!</span>',
    'desc'       => 'We\'d love to hear from you. Send us a message, give us a call, or chat on WhatsApp — our team is available 24/7.',
    'img'        => '/assets/images/banner/contact.jpg',
    'img_alt'    => 'Contact Chulbul Design',
];
require __DIR__ . '/includes/page-hero.php';
?>

<!-- Contact Section -->
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">

        <!-- Contact Info -->
        <div class="bg-[#EE483D] rounded-2xl p-8 text-white">
            <a href="tel:9990548795" class="block text-2xl font-bold mb-6 hover:underline">+91 999-0548-795</a>

            <div class="mb-5">
                <p class="text-white/60 text-sm uppercase tracking-wide mb-1">General Enquiry</p>
                <a href="mailto:info@chulbuldesign.com" class="text-white hover:underline">info@chulbuldesign.com</a>
            </div>
            <div class="mb-5">
                <p class="text-white/60 text-sm uppercase tracking-wide mb-1">General Support</p>
                <a href="mailto:support@chulbuldesign.com" class="text-white hover:underline">support@chulbuldesign.com</a>
            </div>
            <div class="mb-5">
                <p class="text-white/60 text-sm uppercase tracking-wide mb-1">Sales & Marketing</p>
                <a href="mailto:sale@chulbuldesign.com" class="text-white hover:underline">sale@chulbuldesign.com</a>
            </div>
            <div class="mb-8">
                <p class="text-white/60 text-sm uppercase tracking-wide mb-1">Billing & Invoices</p>
                <a href="mailto:invoice@chulbuldesign.com" class="text-white hover:underline">invoice@chulbuldesign.com</a>
            </div>

            <div class="flex gap-4 text-2xl">
                <a href="https://wa.me/919990548795" target="_blank" rel="noopener noreferrer" class="hover:text-white/70 transition"><i class="bi bi-whatsapp"></i></a>
                <a href="https://www.facebook.com/chulbuldesign/" target="_blank" rel="noopener noreferrer" class="hover:text-white/70 transition"><i class="bi bi-facebook"></i></a>
                <a href="https://www.instagram.com/chulbuldesign/" target="_blank" rel="noopener noreferrer" class="hover:text-white/70 transition"><i class="bi bi-instagram"></i></a>
                <a href="https://twitter.com/ChulbulDesign/" target="_blank" rel="noopener noreferrer" class="hover:text-white/70 transition"><i class="bi bi-twitter-x"></i></a>
                <a href="https://www.linkedin.com/company/chulbuldesign/" target="_blank" rel="noopener noreferrer" class="hover:text-white/70 transition"><i class="bi bi-linkedin"></i></a>
            </div>
        </div>

        <!-- Contact Form -->
        <div class="lg:col-span-2 bg-white rounded-2xl p-8 shadow-sm border border-gray-100">
            <h2 class="text-2xl font-bold text-[#EE483D] mb-6">What can we do for you?</h2>

            <?php if ($contactStatus === 'accepted' || $contactStatus === 'saved'): ?>
            <div class="bg-green-50 border border-green-200 text-green-700 rounded-lg px-4 py-3 mb-6">
                Thank you! Your message has been received. We'll connect with you soon.
            </div>
            <?php elseif ($contactStatus !== ''): ?>
            <div role="alert" class="bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-3 mb-6">
                We could not send your message. Please check your details and try again, or contact us by phone or WhatsApp.
            </div>
            <?php endif; ?>

            <form method="post" class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="name" class="sr-only">Your Name</label>
                        <input type="text" id="name" name="name" placeholder="Your Name *" required
                            class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:border-[#EE483D] focus:ring-1 focus:ring-[#EE483D] transition">
                    </div>
                    <div>
                        <label for="email" class="sr-only">Work Email</label>
                        <input type="email" id="email" name="email" placeholder="Work Email *" required
                            class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:border-[#EE483D] focus:ring-1 focus:ring-[#EE483D] transition">
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="phone" class="sr-only">Phone Number</label>
                        <input type="tel" id="phone" name="phone" placeholder="Phone Number *" required
                            class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:border-[#EE483D] focus:ring-1 focus:ring-[#EE483D] transition">
                    </div>
                    <div>
                        <label for="cname" class="sr-only">Company Name</label>
                        <input type="text" id="cname" name="cname" placeholder="Company Name"
                            class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:border-[#EE483D] focus:ring-1 focus:ring-[#EE483D] transition">
                    </div>
                </div>
                <div>
                    <label for="mag" class="sr-only">Your Requirement</label>
                    <textarea id="mag" name="mag" placeholder="Your requirement *" required rows="4"
                        class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:border-[#EE483D] focus:ring-1 focus:ring-[#EE483D] transition resize-none"></textarea>
                </div>
                <button type="submit" name="submit"
                    class="inline-flex items-center gap-2 bg-[#EE483D] text-white px-8 py-3 rounded-lg font-semibold hover:bg-red-600 transition">
                    <i class="bi bi-send-fill"></i> Send Message
                </button>
            </form>


        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
