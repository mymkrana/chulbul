<?php
if (!defined('CBD_ANALYTICS_REPORT')) { http_response_code(403); exit; }
$upgradeReady = $schemaReady && (bool)analytics_value($pdo, "SHOW COLUMNS FROM analytics_pageviews LIKE 'view_token_hash'", [], false)
    && (bool)analytics_value($pdo, "SHOW COLUMNS FROM analytics_events LIKE 'event_key'", [], false);
$funnelPages = $suggestions = [];
$savedEnquiries = $acceptedMail = $contactClicks = $submitAttempts = 0;
$milestones = ['Page views' => $pageviews, 'Engaged views' => 0, 'Form started' => 0, 'Submit attempted' => 0, 'Confirmed outcome' => 0];
if ($upgradeReady) {
    $savedEnquiries = (int)analytics_value($pdo, "SELECT COUNT(*) FROM analytics_events WHERE event_type='lead_saved' AND created_at >= {$since} AND created_at <= NOW()");
    $acceptedMail = (int)analytics_value($pdo, "SELECT COUNT(*) FROM analytics_events WHERE event_type='mail_accepted' AND created_at >= {$since} AND created_at <= NOW()");
    $contactClicks = (int)analytics_value($pdo, "SELECT COUNT(*) FROM analytics_events WHERE event_type='conversion' AND created_at >= {$since} AND created_at <= NOW()");
    $submitAttempts = (int)analytics_value($pdo, "SELECT COUNT(*) FROM analytics_events WHERE event_type='form_submit' AND created_at >= {$since} AND created_at <= NOW()");
    // Aggregate events by view BEFORE joining, so clicks cannot multiply page-view counts.
    $funnelPages = analytics_rows($pdo, "
        SELECT pv.page_path, COUNT(*) views,
            SUM(pv.engaged_seconds >= 10 OR pv.max_scroll >= 50) engaged,
            SUM(COALESCE(ev.started,0)) started, SUM(COALESCE(ev.attempted,0)) attempted,
            SUM(COALESCE(ev.completed,0)) completed, SUM(COALESCE(ev.saved,0)) saved,
            SUM(COALESCE(ev.mailed,0)) mailed, SUM(COALESCE(ev.contact_clicks,0)) contact_clicks,
            SUM(COALESCE(ev.whatsapp,0)) whatsapp, SUM(COALESCE(ev.phone,0)) phone,
            ROUND(AVG(pv.engaged_seconds)) avg_time, ROUND(AVG(pv.max_scroll)) avg_scroll
        FROM analytics_pageviews pv
        LEFT JOIN (
            SELECT view_id, MAX(event_type='form_start') started, MAX(event_type='form_submit') attempted,
                MAX(event_type IN ('lead_saved','mail_accepted')) completed,
                SUM(event_type='lead_saved') saved, SUM(event_type='mail_accepted') mailed,
                SUM(event_type='conversion') contact_clicks,
                SUM(event_type='conversion' AND target_path='whatsapp') whatsapp,
                SUM(event_type='conversion' AND target_path='telephone') phone
            FROM analytics_events WHERE created_at >= {$since} AND created_at <= NOW() GROUP BY view_id
        ) ev ON ev.view_id=pv.view_id
        WHERE pv.started_at >= {$since} AND pv.started_at <= NOW()
        GROUP BY pv.page_path ORDER BY views DESC, pv.page_path ASC
    ");
    foreach ($funnelPages as $row) {
        $milestones['Engaged views'] += (int)$row['engaged'];
        $milestones['Form started'] += (int)$row['started'];
        $milestones['Submit attempted'] += (int)$row['attempted'];
        $milestones['Confirmed outcome'] += (int)$row['completed'];
        if ((int)$row['views'] < 30 || count($suggestions) >= 3) continue;
        if ((int)$row['attempted'] >= 5 && (int)$row['completed'] === 0) {
            $suggestions[] = [$row['page_path'], 'Submit attempts, but no tracked outcome', 'Test validation and delivery on this page. Tracking blockers or the date boundary can also explain the gap.'];
        } elseif ((float)$row['avg_scroll'] < 25 && (int)$row['avg_time'] < 10) {
            $suggestions[] = [$row['page_path'], 'Low measured engagement', 'Review mobile loading, the opening message and the first CTA. This is a review signal, not a proven cause.'];
        } elseif ((int)$row['completed'] >= 3) {
            $suggestions[] = [$row['page_path'], 'This page is generating enquiries', 'Review its offer, traffic source and enquiry quality before applying the same approach elsewhere.'];
        }
    }
}
if (($_GET['export'] ?? '') === 'conversions' && $upgradeReady) {
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="page-conversions.csv"');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['Page','Views','Form started (views)','Submit attempted (views)','Confirmed outcome (views)','Saved enquiries','Mail accepted','WhatsApp clicks','Phone clicks','Outcome rate %','Period']);
    foreach ($funnelPages as $row) {
        $path = preg_match('/^[=+@\-\t\r\n]/', $row['page_path']) ? "'" . $row['page_path'] : $row['page_path'];
        fputcsv($out, [$path,$row['views'],$row['started'],$row['attempted'],$row['completed'],$row['saved'],$row['mailed'],$row['whatsapp'],$row['phone'],
            $row['views'] ? round(100*$row['completed']/$row['views'],2) : 0, $days === 0 ? 'All time' : $days . ' calendar day(s)']);
    }
    fclose($out); exit;
}
