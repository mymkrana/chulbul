<?php if (!defined('CBD_ANALYTICS_REPORT')) { http_response_code(403); exit; } ?>
<?php if (!$upgradeReady): ?>
<div class="aq-notice" role="status">For confirmed outcomes and improved tracking, import <code>database/migrations/2026-09-09-analytics-quality.sql</code>. Existing reports are still available.</div>
<?php else: ?>
<section class="aq-panel" aria-labelledby="outcome-heading">
    <div class="aq-heading"><div><p class="aq-eyebrow">Enquiry intelligence</p><h2 id="outcome-heading">What led to a real enquiry?</h2><p>Separate intent from results. No contact details are stored in analytics.</p></div><a class="aq-button" href="landing-analytics.php">View saved leads <span aria-hidden="true">→</span></a></div>
    <div class="aq-stats">
        <div><span>Saved enquiries</span><strong><?= $savedEnquiries ?></strong><small>Confirmed in the leads database</small></div>
        <div><span>Contact email accepted</span><strong><?= $acceptedMail ?></strong><small>Server accepted it; inbox delivery not verified</small></div>
        <div><span>Submit attempts</span><strong><?= $submitAttempts ?></strong><small>Not necessarily saved or sent</small></div>
        <div><span>WhatsApp / call / email clicks</span><strong><?= $contactClicks ?></strong><small>Intent only; not a confirmed conversation</small></div>
    </div>
    <h3>Enquiry milestones</h3>
    <div class="aq-milestones">
        <?php foreach ($milestones as $label => $total): ?>
        <div><span><?= analytics_h($label) ?></span><strong><?= $total ?></strong><meter min="0" max="<?= max(1,$pageviews) ?>" value="<?= min($pageviews,$total) ?>" aria-label="<?= analytics_h($label) ?>"></meter><small><?= $pageviews ? round(100*$total/$pageviews,1) : 0 ?>% of page views</small></div>
        <?php endforeach; ?>
    </div>
    <p class="aq-note">Each milestone counts page views independently, not an enforced sequence. Autofill, blocked tracking and visits across period boundaries can leave gaps. Confirmed outcome = saved enquiry OR contact email accepted, not a sale.</p>
</section>
<section class="aq-panel" aria-labelledby="page-conversions">
    <div class="aq-heading"><div><h2 id="page-conversions">Page-wise enquiry report</h2><p>Which pages get attention, and which produce an outcome?</p></div><a class="aq-button" href="?days=<?= $days ?>&amp;export=conversions">Download CSV</a></div>
    <div class="aq-table-wrap"><table class="aq-table"><thead><tr><th>Page</th><th>Views</th><th>Form starts</th><th>Attempts</th><th>Outcomes</th><th>Rate</th><th>WhatsApp</th><th>Calls</th></tr></thead><tbody>
        <?php foreach (array_slice($funnelPages,0,100) as $row): ?>
        <tr><th scope="row"><a href="?days=<?= $days ?>&amp;page=<?= rawurlencode($row['page_path']) ?>#click-map"><?= analytics_h($row['page_path']) ?></a></th><td><?= (int)$row['views'] ?></td><td><?= (int)$row['started'] ?></td><td><?= (int)$row['attempted'] ?></td><td><?= (int)$row['completed'] ?></td><td><?= round(100*$row['completed']/max(1,$row['views']),1) ?>%</td><td><?= (int)$row['whatsapp'] ?></td><td><?= (int)$row['phone'] ?></td></tr>
        <?php endforeach; ?>
        <?php if (!$funnelPages): ?><tr><td colspan="8" class="aq-empty">No tracked visits in this period yet. New visits and enquiries will populate this report.</td></tr><?php endif; ?>
    </tbody></table></div>
    <p class="aq-note">Starts, attempts and outcomes are unique page views, not repeat button presses. Rate = page views with an outcome ÷ page views. CSV includes all pages and separate saved/mail counts. Historical visits cannot gain the new events retroactively.</p>
</section>
<section class="aq-panel" aria-labelledby="review-signals"><h2 id="review-signals">Pages worth reviewing</h2><p class="aq-note">Rule-based signals, shown only after at least 30 recorded views per page. These are not SEO diagnoses.</p>
    <div class="aq-signals"><?php foreach ($suggestions as $signal): ?><article><h3><?= analytics_h($signal[1]) ?></h3><p class="aq-path"><?= analytics_h($signal[0]) ?></p><p><?= analytics_h($signal[2]) ?></p></article><?php endforeach; ?></div>
    <?php if (!$suggestions): ?><p class="aq-empty">No review signal yet. Collect more real traffic; do not change a page based on a few test visits.</p><?php endif; ?>
</section>
<?php endif; ?>
