<?php // Short enquiry form used only on the four Seattle service pages. ?>
<aside id="project-enquiry" class="cbd-seattle-enquiry" aria-labelledby="seattle-enquiry-title">
    <span class="cbd-spoke-section-label">Free project discussion</span>
    <h2 id="seattle-enquiry-title">Get a website quote</h2>
    <p>Share your email or phone. We will reply about your project during business hours.</p>
    <form id="cbdSpokeQuoteForm" class="cbd-spoke-form" action="<?= $base ?>/lead-submit.php" method="post">
        <input type="text" name="_hp" value="" tabindex="-1" autocomplete="off" class="cbd-spoke-honeypot" aria-hidden="true">
        <input type="hidden" name="source" value="<?= $escape($formSource) ?>">
        <input type="hidden" name="city" value="<?= $escape($city['name']) ?>">
        <input type="hidden" name="service" value="<?= $escape($formService) ?>">
        <input type="hidden" name="page_url" value="<?= $escape($page['canonical_url']) ?>">
        <input type="hidden" name="form_version" value="city-service-v3">
        <label>Name <input type="text" name="name" autocomplete="name" maxlength="120" required></label>
        <p id="seattle-contact-help" class="cbd-seattle-contact-help">Enter at least one: email or phone.</p>
        <label>Email <input type="email" name="email" autocomplete="email" maxlength="190" aria-describedby="seattle-contact-help"></label>
        <label>Phone / WhatsApp (optional if email supplied) <input type="tel" name="phone" autocomplete="tel" maxlength="24" placeholder="e.g. +1 206 555 0123" aria-describedby="seattle-contact-help"></label>
        <details class="cbd-seattle-project-details">
            <summary>Add project details (optional)</summary>
            <label>Business / company <input type="text" name="business" autocomplete="organization" maxlength="160"></label>
            <label><?= $escape($contentText('form_message_label', 'What are you planning?')) ?><textarea name="message" rows="3" maxlength="1000" placeholder="<?= $escape($contentText('form_message_placeholder', 'Current website, goals and required features')) ?>"></textarea></label>
        </details>
        <button id="cbdSpokeQuoteSubmit" class="cbd-spoke-btn cbd-spoke-btn-primary" type="submit"><i class="bi bi-send-fill" aria-hidden="true"></i> Get My Project Quote</button>
        <p class="cbd-seattle-contact-help">No obligation. Your details are used to respond to this enquiry.</p>
        <div id="cbdSpokeQuoteMessage" class="cbd-spoke-form-message" role="status" aria-live="polite" hidden></div>
    </form>
    <a class="cbd-seattle-contact-link" href="https://wa.me/919990548795?text=<?= rawurlencode('Hi Chulbul Design, I would like to discuss ' . $formService . '.') ?>" target="_blank" rel="noopener noreferrer">Prefer WhatsApp? Discuss your project</a>
</aside>
