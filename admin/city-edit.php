<?php
require_once __DIR__ . '/config.php';
require_login();

$pdo = get_db();
if (!$pdo) {
    http_response_code(503);
    exit('Database unavailable.');
}

function citySlugify(string $value): string
{
    $value = strtolower(trim($value));
    $value = preg_replace('/[^a-z0-9]+/', '-', $value) ?? '';
    return trim($value, '-');
}

function cityTextLines(string $value): array
{
    return array_values(array_filter(
        array_map('trim', preg_split('/\R/u', $value) ?: []),
        static fn(string $line): bool => $line !== ''
    ));
}

function cityParallelRows(array $fields, string $requiredField): array
{
    $count = 0;
    foreach ($fields as $values) $count = max($count, count($values));
    $rows = [];
    for ($index = 0; $index < $count; $index++) {
        $row = [];
        foreach ($fields as $name => $values) {
            $row[$name] = trim((string)($values[$index] ?? ''));
        }
        if (($row[$requiredField] ?? '') !== '') $rows[] = $row;
    }
    return $rows;
}

$countries = $pdo->query('SELECT id, name, iso_code, currency_symbol, status FROM countries ORDER BY sort_order, name')->fetchAll();
if (!$countries) {
    set_flash('Create a country before adding a city.', 'error');
    header('Location: country-edit.php');
    exit;
}
$countryLookup = [];
foreach ($countries as $countryRow) $countryLookup[(int)$countryRow['id']] = $countryRow;

$id = (int)($_GET['id'] ?? 0);
$requestedCountryId = (int)($_GET['country_id'] ?? 0);
$isEdit = false;
$city = null;
if ($id) {
    $statement = $pdo->prepare('SELECT * FROM cities WHERE id = ?');
    $statement->execute([$id]);
    $city = $statement->fetch();
    $isEdit = (bool)$city;
    if (!$isEdit) {
        http_response_code(404);
        exit('City not found.');
    }
}
$wasEdit = $isEdit;

$stats = $areas = $industries = $faqs = $redirectSlugs = [];
$plans = [];
if ($isEdit) {
    $statement = $pdo->prepare('SELECT value_text, label FROM city_stats WHERE city_id = ? ORDER BY sort_order, id');
    $statement->execute([$id]);
    $stats = $statement->fetchAll();

    $statement = $pdo->prepare('SELECT area_name FROM city_areas WHERE city_id = ? ORDER BY sort_order, id');
    $statement->execute([$id]);
    $areas = array_column($statement->fetchAll(), 'area_name');

    $statement = $pdo->prepare('SELECT industry_name FROM city_industries WHERE city_id = ? ORDER BY sort_order, id');
    $statement->execute([$id]);
    $industries = array_column($statement->fetchAll(), 'industry_name');

    $statement = $pdo->prepare("SELECT question, answer FROM city_faqs WHERE city_id = ? ORDER BY sort_order, id");
    $statement->execute([$id]);
    $faqs = $statement->fetchAll();

    $statement = $pdo->prepare('SELECT old_slug FROM city_redirects WHERE city_id = ? ORDER BY old_slug');
    $statement->execute([$id]);
    $redirectSlugs = $statement->fetchAll(PDO::FETCH_COLUMN);

    $statement = $pdo->prepare(
        "SELECT p.*, f.feature_text
         FROM city_pricing_plans p
         LEFT JOIN city_plan_features f ON f.pricing_plan_id = p.id
         WHERE p.city_id = ?
         ORDER BY p.sort_order, p.id, f.sort_order, f.id"
    );
    $statement->execute([$id]);
    foreach ($statement->fetchAll() as $row) {
        $key = $row['plan_key'];
        if (!isset($plans[$key])) {
            $plans[$key] = [
                'plan_name' => $row['plan_name'], 'price_text' => $row['price_text'],
                'short_description' => $row['short_description'],
                'billing_label' => $row['billing_label'], 'is_popular' => (int)$row['is_popular'],
                'features' => [],
            ];
        }
        if ($row['feature_text'] !== null) $plans[$key]['features'][] = $row['feature_text'];
    }
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $text = static fn(string $key): string => trim((string)($_POST[$key] ?? ''));
    $countryId = (int)($_POST['country_id'] ?? 0);
    $postedSlug = citySlugify($text('slug'));
    $slug = $wasEdit ? (string)$city['slug'] : $postedSlug;
    $status = $text('status');

    $form = [
        'country_id' => $countryId,
        'name' => $text('name'),
        'display_name' => $text('display_name'),
        'state_region' => $text('state_region'),
        'slug' => $slug,
        'tagline' => $text('tagline'),
        'heading_html' => trim((string)($_POST['heading_html'] ?? '')),
        'hero_description' => trim((string)($_POST['hero_description'] ?? '')),
        'introduction' => trim((string)($_POST['introduction'] ?? '')),
        'why_local' => trim((string)($_POST['why_local'] ?? '')),
        'market_insight' => trim((string)($_POST['market_insight'] ?? '')),
        'meta_title' => $text('meta_title'),
        'meta_description' => trim((string)($_POST['meta_description'] ?? '')),
        'canonical_url' => 'https://www.chulbuldesign.com/city/' . $slug,
        'schema_city_name' => $text('schema_city_name'),
        'geo_region' => $text('geo_region'),
        'geo_position' => $text('geo_position'),
        'geo_icbm' => $text('geo_icbm'),
        'status' => $status,
        'sort_order' => max(0, (int)($_POST['sort_order'] ?? 0)),
    ];
    $city = array_merge($city ?: [], $form);

    $areas = cityTextLines((string)($_POST['areas'] ?? ''));
    $industries = cityTextLines((string)($_POST['industries'] ?? ''));
    $redirectSlugs = array_values(array_unique(array_filter(array_map(
        'citySlugify',
        cityTextLines((string)($_POST['redirect_slugs'] ?? ''))
    ))));
    $stats = cityParallelRows([
        'value_text' => $_POST['stat_value'] ?? [],
        'label' => $_POST['stat_label'] ?? [],
    ], 'value_text');
    $faqs = cityParallelRows([
        'question' => $_POST['faq_question'] ?? [],
        'answer' => $_POST['faq_answer'] ?? [],
    ], 'question');

    $plans = [];
    foreach (['starter', 'growth', 'enterprise'] as $planKey) {
        $plans[$planKey] = [
            'plan_name' => trim((string)($_POST['plan_name'][$planKey] ?? ucfirst($planKey))),
            'price_text' => trim((string)($_POST['plan_price'][$planKey] ?? '')),
            'short_description' => trim((string)($_POST['plan_description'][$planKey] ?? '')),
            'billing_label' => trim((string)($_POST['plan_billing'][$planKey] ?? '')),
            'is_popular' => isset($_POST['plan_popular'][$planKey]) ? 1 : 0,
            'features' => cityTextLines((string)($_POST['plan_features'][$planKey] ?? '')),
        ];
    }

    $required = ['name','display_name','slug','tagline','heading_html','hero_description','introduction','why_local','meta_title','meta_description','canonical_url','schema_city_name'];
    $missing = [];
    foreach ($required as $field) if (($form[$field] ?? '') === '') $missing[] = $field;
    $allowedStatuses = ['draft', 'published', 'archived'];
    $invalidFaq = count(array_filter($faqs, static fn(array $faq): bool => $faq['answer'] === '')) > 0;
    $invalidStat = count(array_filter($stats, static fn(array $stat): bool => $stat['label'] === '')) > 0;
    $invalidPlan = count(array_filter($plans, static fn(array $plan): bool => $plan['plan_name'] === '' || $plan['price_text'] === '' || !$plan['features'])) > 0;
    $geoMissing = $status === 'published' && (
        $form['geo_region'] === '' || $form['geo_position'] === '' || $form['geo_icbm'] === ''
    );
    $geoPositionInvalid = $form['geo_position'] !== '' && !preg_match('/^-?\d{1,3}(?:\.\d+)?;-?\d{1,3}(?:\.\d+)?$/', $form['geo_position']);
    $geoIcbmInvalid = $form['geo_icbm'] !== '' && !preg_match('/^-?\d{1,3}(?:\.\d+)?,\s*-?\d{1,3}(?:\.\d+)?$/', $form['geo_icbm']);
    $redirectConflict = in_array($slug, $redirectSlugs, true);
    if (!$redirectConflict && $redirectSlugs) {
        $placeholders = implode(',', array_fill(0, count($redirectSlugs), '?'));
        $parameters = array_merge($redirectSlugs, [$id]);
        $statement = $pdo->prepare("SELECT slug FROM cities WHERE slug IN ({$placeholders}) AND id <> ? LIMIT 1");
        $statement->execute($parameters);
        $redirectConflict = (bool)$statement->fetchColumn();
    }

    if (!isset($countryLookup[$countryId])) {
        $error = 'Please choose a valid country.';
    } elseif ($missing) {
        $error = 'Please complete all required city, hero, SEO and content fields.';
    } elseif (!preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $slug)) {
        $error = 'City slug may contain lowercase letters, numbers and hyphens only.';
    } elseif (!filter_var($form['canonical_url'], FILTER_VALIDATE_URL)) {
        $error = 'Please enter a valid canonical URL.';
    } elseif ($redirectConflict) {
        $error = 'An old URL slug cannot be the current slug of this or another city.';
    } elseif (!in_array($status, $allowedStatuses, true)) {
        $error = 'Invalid city status.';
    } elseif ($geoPositionInvalid || $geoIcbmInvalid) {
        $error = 'Geo Position must use latitude;longitude and ICBM must use latitude, longitude.';
    } elseif ($geoMissing) {
        $error = 'Published cities need a Geo Region, city-level Geo Position and ICBM coordinates.';
    } elseif ($invalidFaq || $invalidStat) {
        $error = 'Every entered stat and FAQ needs both fields completed.';
    } elseif ($status === 'published' && (!$areas || !$industries || !$faqs || $invalidPlan || $form['market_insight'] === '')) {
        $error = 'Published cities need areas, industries, FAQs, market insight and all three pricing plans.';
    } else {
        try {
            $pdo->beginTransaction();
            $values = [
                $form['country_id'], $form['name'], $form['display_name'], $form['state_region'] ?: null,
                $form['slug'], $form['tagline'], $form['heading_html'], $form['hero_description'],
                $form['introduction'], $form['why_local'], $form['market_insight'] ?: null,
                $form['meta_title'], $form['meta_description'], $form['canonical_url'],
                $form['schema_city_name'], $form['geo_region'] ?: null,
                $form['geo_position'] ?: null, $form['geo_icbm'] ?: null,
                $form['status'], $form['sort_order'],
            ];
            if ($wasEdit) {
                $values[] = $form['status'];
                $values[] = $id;
                $pdo->prepare(
                    "UPDATE cities SET country_id=?, name=?, display_name=?, state_region=?, slug=?, tagline=?,
                     heading_html=?, hero_description=?, introduction=?, why_local=?, market_insight=?,
                     meta_title=?, meta_description=?, canonical_url=?, schema_city_name=?, geo_region=?,
                     geo_position=?, geo_icbm=?, status=?, sort_order=?,
                     published_at=CASE WHEN ?='published' THEN COALESCE(published_at,NOW()) ELSE published_at END
                     WHERE id=?"
                )->execute($values);
            } else {
                $values[] = $form['status'];
                $pdo->prepare(
                    "INSERT INTO cities
                     (country_id,name,display_name,state_region,slug,tagline,heading_html,hero_description,
                      introduction,why_local,market_insight,meta_title,meta_description,canonical_url,
                      schema_city_name,geo_region,geo_position,geo_icbm,status,sort_order,published_at)
                     VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,CASE WHEN ?='published' THEN NOW() ELSE NULL END)"
                )->execute($values);
                $id = (int)$pdo->lastInsertId();
                $isEdit = true;
            }

            foreach (['city_stats', 'city_areas', 'city_industries', 'city_faqs', 'city_pricing_plans'] as $table) {
                $delete = $pdo->prepare("DELETE FROM `{$table}` WHERE city_id = ?");
                $delete->execute([$id]);
            }

            $insert = $pdo->prepare('INSERT INTO city_stats (city_id,value_text,label,sort_order) VALUES (?,?,?,?)');
            foreach ($stats as $sort => $row) $insert->execute([$id, $row['value_text'], $row['label'], $sort]);
            $insert = $pdo->prepare('INSERT INTO city_areas (city_id,area_name,sort_order) VALUES (?,?,?)');
            foreach ($areas as $sort => $area) $insert->execute([$id, $area, $sort]);
            $insert = $pdo->prepare('INSERT INTO city_industries (city_id,industry_name,sort_order) VALUES (?,?,?)');
            foreach ($industries as $sort => $industry) $insert->execute([$id, $industry, $sort]);
            $insert = $pdo->prepare("INSERT INTO city_faqs (city_id,question,answer,status,sort_order) VALUES (?,?,?,'published',?)");
            foreach ($faqs as $sort => $faq) $insert->execute([$id, $faq['question'], $faq['answer'], $sort]);

            $planInsert = $pdo->prepare(
                "INSERT INTO city_pricing_plans
                 (city_id,plan_key,plan_name,price_text,short_description,billing_label,is_popular,status,sort_order)
                 VALUES (?,?,?,?,?,?,?,'published',?)"
            );
            $featureInsert = $pdo->prepare('INSERT INTO city_plan_features (pricing_plan_id,feature_text,sort_order) VALUES (?,?,?)');
            foreach (['starter', 'growth', 'enterprise'] as $sort => $planKey) {
                $plan = $plans[$planKey];
                $planInsert->execute([$id, $planKey, $plan['plan_name'], $plan['price_text'], $plan['short_description'] ?: null, $plan['billing_label'] ?: null, $plan['is_popular'], $sort]);
                $planId = (int)$pdo->lastInsertId();
                foreach ($plan['features'] as $featureSort => $feature) $featureInsert->execute([$planId, $feature, $featureSort]);
            }

            $pdo->prepare('DELETE FROM city_redirects WHERE city_id = ?')->execute([$id]);
            $redirectInsert = $pdo->prepare('INSERT INTO city_redirects (city_id,old_slug,redirect_code) VALUES (?,?,301)');
            foreach ($redirectSlugs as $redirectSlug) {
                $redirectInsert->execute([$id, $redirectSlug]);
            }

            $pdo->commit();
            audit_log($wasEdit ? 'update' : 'create', 'city', $id, $form['name']);
            set_flash('City saved successfully: ' . $form['name'], 'success');
            header('Location: locations.php?country_id=' . $form['country_id']);
            exit;
        } catch (Throwable $exception) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            cbd_log_error('city-save', $exception->getMessage(), ['city_id' => $id]);
            $error = 'City could not be saved. Check that the slug and canonical URL are unique.';
        }
    }
}

if (!$city) {
    $defaultCountryId = isset($countryLookup[$requestedCountryId]) ? $requestedCountryId : (int)$countries[0]['id'];
    $nextSort = $pdo->prepare('SELECT COALESCE(MAX(sort_order), -1) + 1 FROM cities WHERE country_id = ?');
    $nextSort->execute([$defaultCountryId]);
    $city = [
        'country_id' => $defaultCountryId, 'name' => '', 'display_name' => '', 'state_region' => '',
        'slug' => '', 'tagline' => '', 'heading_html' => '', 'hero_description' => '',
        'introduction' => '', 'why_local' => '', 'market_insight' => '', 'meta_title' => '',
        'meta_description' => '', 'canonical_url' => '', 'schema_city_name' => '',
        'geo_region' => '', 'geo_position' => '', 'geo_icbm' => '', 'status' => 'draft',
        'sort_order' => (int)$nextSort->fetchColumn(),
    ];
}

$defaultPlans = [
    'starter' => ['plan_name'=>'Starter','price_text'=>'','short_description'=>'For small businesses going online','billing_label'=>'One-time cost','is_popular'=>0,'features'=>[]],
    'growth' => ['plan_name'=>'Growth','price_text'=>'','short_description'=>'Most popular for growing businesses','billing_label'=>'One-time cost','is_popular'=>1,'features'=>[]],
    'enterprise' => ['plan_name'=>'Enterprise','price_text'=>'Custom','short_description'=>'For established brands & portals','billing_label'=>'Based on requirements','is_popular'=>0,'features'=>[]],
];
foreach ($defaultPlans as $key => $default) if (!isset($plans[$key])) $plans[$key] = $default;

if ($city['slug'] !== '') {
    $city['canonical_url'] = 'https://www.chulbuldesign.com/city/' . $city['slug'];
}

$escape = static fn($value): string => htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
$active_page = 'locations';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $wasEdit ? 'Edit City' : 'Add City' ?> — Chulbul Admin</title>
<link rel="stylesheet" href="assets/admin.css">
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
.repeat-row{position:relative}.remove-row{position:absolute;right:.75rem;top:.75rem}
.repeat-row>input+textarea{margin-top:.75rem!important}
.section-nav{scrollbar-width:none}.section-nav::-webkit-scrollbar{display:none}
</style>
</head>
<body class="bg-gray-100 min-h-screen">
<?php include __DIR__ . '/includes/sidebar.php'; ?>
<div class="lg:pl-60 min-h-screen">
    <header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between gap-4 sticky top-0 z-20">
        <div class="flex items-center gap-3">
            <button onclick="openSidebar()" class="lg:hidden text-gray-500"><i class="bi bi-list text-2xl"></i></button>
            <a href="locations.php" class="text-gray-400 hover:text-gray-700"><i class="bi bi-arrow-left text-lg"></i></a>
            <div><h1 class="text-xl font-extrabold text-[#1e1e5c]"><?= $wasEdit ? 'Edit City' : 'Add City' ?></h1><p class="text-xs text-gray-400"><?= $wasEdit ? $escape($city['display_name']) : 'Create a new location page' ?></p></div>
        </div>
        <?php if ($wasEdit && $city['status'] === 'published'): ?><a href="../city/<?= urlencode($city['slug']) ?>" target="_blank" class="btn btn-secondary btn-sm"><i class="bi bi-box-arrow-up-right"></i> Preview</a><?php endif; ?>
    </header>

    <nav class="section-nav bg-white border-b border-gray-200 px-6 py-2 flex gap-2 overflow-x-auto sticky top-[79px] z-10">
        <?php foreach (['general'=>'General','hero'=>'Hero','seo'=>'SEO','content'=>'Content','local'=>'Local Data','faqs'=>'FAQs','pricing'=>'Pricing'] as $anchor=>$label): ?><a href="#<?= $anchor ?>" class="text-xs font-bold text-gray-500 hover:text-[#EE483D] px-3 py-2 rounded-lg whitespace-nowrap"><?= $label ?></a><?php endforeach; ?>
    </nav>

    <main class="p-6 max-w-5xl mx-auto">
        <?php if ($error): ?><div class="flash flash-error"><i class="bi bi-exclamation-circle-fill"></i><?= $escape($error) ?></div><?php endif; ?>
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

            <section class="admin-card scroll-mt-32" id="general">
                <h2 class="font-extrabold text-[#1e1e5c] mb-5"><i class="bi bi-geo-alt-fill text-[#EE483D] mr-2"></i>General Details</h2>
                <div class="grid-2">
                    <div class="field"><label>Country *</label><select name="country_id" required><?php foreach ($countries as $countryRow): ?><option value="<?= $countryRow['id'] ?>" data-symbol="<?= $escape($countryRow['currency_symbol']) ?>" <?= (int)$city['country_id'] === (int)$countryRow['id'] ? 'selected' : '' ?>><?= $escape($countryRow['name']) ?> (<?= $escape($countryRow['iso_code']) ?>)</option><?php endforeach; ?></select></div>
                    <div class="field"><label>Status</label><select name="status"><?php foreach (['draft','published','archived'] as $status): ?><option value="<?= $status ?>" <?= $city['status'] === $status ? 'selected' : '' ?>><?= ucfirst($status) ?></option><?php endforeach; ?></select></div>
                    <div class="field"><label>City Name *</label><input type="text" name="name" value="<?= $escape($city['name']) ?>" required oninput="autoCityFields(this.value)"></div>
                    <div class="field"><label>Display Name *</label><input type="text" id="display-name" name="display_name" value="<?= $escape($city['display_name']) ?>" required></div>
                    <div class="field"><label>State / Region</label><input type="text" name="state_region" value="<?= $escape($city['state_region']) ?>"></div>
                    <div class="field"><label>Schema City Name *</label><input type="text" id="schema-city" name="schema_city_name" value="<?= $escape($city['schema_city_name']) ?>" required></div>
                    <div class="field"><label>URL Slug *</label><input type="text" id="city-slug" name="slug" value="<?= $escape($city['slug']) ?>" <?= $wasEdit ? 'readonly' : '' ?> required><p class="field-hint"><?= $wasEdit ? 'Locked to protect the existing SEO URL.' : 'Used in /city/your-slug' ?></p></div>
                    <div class="field"><label>Sort Order</label><input type="number" name="sort_order" min="0" value="<?= (int)$city['sort_order'] ?>"></div>
                </div>
            </section>

            <section class="admin-card scroll-mt-32" id="hero">
                <h2 class="font-extrabold text-[#1e1e5c] mb-5"><i class="bi bi-stars text-[#EE483D] mr-2"></i>Hero Content</h2>
                <div class="field"><label>Tagline *</label><input type="text" name="tagline" value="<?= $escape($city['tagline']) ?>" required></div>
                <div class="field"><label>H1 Heading HTML *</label><textarea name="heading_html" rows="3" class="field-mono" required><?= $escape($city['heading_html']) ?></textarea><p class="field-hint">Trusted HTML is allowed for the highlighted city span.</p></div>
                <div class="field"><label>Hero Description *</label><textarea name="hero_description" rows="4" required><?= $escape($city['hero_description']) ?></textarea></div>
            </section>

            <section class="admin-card scroll-mt-32" id="seo">
                <h2 class="font-extrabold text-[#1e1e5c] mb-5"><i class="bi bi-search text-[#EE483D] mr-2"></i>SEO &amp; Canonical</h2>
                <div class="field"><label>Meta Title *</label><input type="text" name="meta_title" value="<?= $escape($city['meta_title']) ?>" required></div>
                <div class="field"><label>Meta Description *</label><textarea name="meta_description" rows="4" required><?= $escape($city['meta_description']) ?></textarea></div>
                <div class="field"><label>Canonical URL *</label><input type="url" id="canonical-url" name="canonical_url" value="<?= $escape($city['canonical_url']) ?>" readonly><p class="field-hint">Automatically protected as /city/<?= $escape($city['slug'] ?: 'your-slug') ?>.</p></div>
                <div class="field"><label>Old City Slugs — one per line</label><textarea name="redirect_slugs" rows="4" placeholder="old-city-name"><?= $escape(implode("\n", $redirectSlugs)) ?></textarea><p class="field-hint">Each old /city/ URL will permanently redirect to this city.</p></div>
                <div class="grid-3">
                    <div class="field"><label>Geo Region <?= $city['status'] === 'published' ? '*' : '' ?></label><input type="text" name="geo_region" value="<?= $escape($city['geo_region']) ?>" placeholder="US-WA"><p class="field-hint">ISO country-region code.</p></div>
                    <div class="field"><label>Geo Position <?= $city['status'] === 'published' ? '*' : '' ?></label><input type="text" name="geo_position" value="<?= $escape($city['geo_position']) ?>" placeholder="47.6062;-122.3321"><p class="field-hint">City latitude;longitude.</p></div>
                    <div class="field"><label>ICBM <?= $city['status'] === 'published' ? '*' : '' ?></label><input type="text" name="geo_icbm" value="<?= $escape($city['geo_icbm']) ?>" placeholder="47.6062, -122.3321"><p class="field-hint">City latitude, longitude.</p></div>
                </div>
            </section>

            <section class="admin-card scroll-mt-32" id="content">
                <h2 class="font-extrabold text-[#1e1e5c] mb-5"><i class="bi bi-file-richtext text-[#EE483D] mr-2"></i>City Content</h2>
                <div class="field"><label>Introduction *</label><textarea name="introduction" rows="7" required><?= $escape($city['introduction']) ?></textarea></div>
                <div class="field"><label>Why Local *</label><textarea name="why_local" rows="6" required><?= $escape($city['why_local']) ?></textarea></div>
                <div class="field"><label>Market Insight <?= $city['status'] === 'published' ? '*' : '' ?></label><textarea name="market_insight" rows="6"><?= $escape($city['market_insight']) ?></textarea></div>
            </section>

            <section class="admin-card scroll-mt-32" id="local">
                <h2 class="font-extrabold text-[#1e1e5c] mb-5"><i class="bi bi-buildings-fill text-[#EE483D] mr-2"></i>Areas &amp; Industries</h2>
                <div class="grid-2">
                    <div class="field"><label>Areas — one per line</label><textarea name="areas" rows="9"><?= $escape(implode("\n", $areas)) ?></textarea></div>
                    <div class="field"><label>Industries — one per line</label><textarea name="industries" rows="9"><?= $escape(implode("\n", $industries)) ?></textarea></div>
                </div>
                <div class="flex items-center justify-between mt-5 mb-3"><div><h3 class="font-bold text-gray-800">City-specific Stats</h3><p class="text-xs text-gray-400">Leave empty to use country stats.</p></div><button type="button" class="btn btn-secondary btn-sm" onclick="addTemplate('city-stat-template','city-stats-list')"><i class="bi bi-plus"></i> Stat</button></div>
                <div id="city-stats-list" class="space-y-3"><?php foreach ($stats as $row): ?><div class="item-box repeat-row grid-2 pr-12"><input type="text" name="stat_value[]" value="<?= $escape($row['value_text']) ?>" placeholder="20M+"><input type="text" name="stat_label[]" value="<?= $escape($row['label']) ?>" placeholder="Local businesses"><button type="button" class="remove-row text-red-400" onclick="this.closest('.repeat-row').remove()"><i class="bi bi-x-lg"></i></button></div><?php endforeach; ?></div>
            </section>

            <section class="admin-card scroll-mt-32" id="faqs">
                <div class="flex items-center justify-between mb-5"><h2 class="font-extrabold text-[#1e1e5c]"><i class="bi bi-question-circle-fill text-[#EE483D] mr-2"></i>FAQs</h2><button type="button" class="btn btn-secondary btn-sm" onclick="addTemplate('faq-template','faqs-list')"><i class="bi bi-plus"></i> FAQ</button></div>
                <div id="faqs-list" class="space-y-4"><?php foreach ($faqs as $faq): ?><div class="item-box repeat-row pr-12"><input type="text" name="faq_question[]" value="<?= $escape($faq['question']) ?>" placeholder="Question" class="mb-3"><textarea name="faq_answer[]" rows="4" placeholder="Answer"><?= $escape($faq['answer']) ?></textarea><button type="button" class="remove-row text-red-400" onclick="this.closest('.repeat-row').remove()"><i class="bi bi-x-lg"></i></button></div><?php endforeach; ?></div>
            </section>

            <section class="admin-card scroll-mt-32" id="pricing">
                <h2 class="font-extrabold text-[#1e1e5c] mb-5"><i class="bi bi-tags-fill text-[#EE483D] mr-2"></i>Pricing Plans</h2>
                <div class="space-y-5">
                <?php foreach (['starter','growth','enterprise'] as $planKey): $plan = $plans[$planKey]; ?>
                    <div class="item-box">
                        <div class="flex items-center justify-between mb-4"><h3 class="font-extrabold text-[#49499A]"><?= ucfirst($planKey) ?></h3><label class="!mb-0 flex items-center gap-2"><input type="checkbox" name="plan_popular[<?= $planKey ?>]" value="1" <?= $plan['is_popular'] ? 'checked' : '' ?>> Popular</label></div>
                        <div class="grid-2">
                            <div class="field"><label>Plan Name</label><input type="text" name="plan_name[<?= $planKey ?>]" value="<?= $escape($plan['plan_name']) ?>"></div>
                            <div class="field"><label>Price Text</label><input type="text" name="plan_price[<?= $planKey ?>]" value="<?= $escape($plan['price_text']) ?>" placeholder="₹18,000 or Custom"></div>
                            <div class="field"><label>Short Description</label><input type="text" name="plan_description[<?= $planKey ?>]" value="<?= $escape($plan['short_description']) ?>"></div>
                            <div class="field"><label>Billing Label</label><input type="text" name="plan_billing[<?= $planKey ?>]" value="<?= $escape($plan['billing_label']) ?>"></div>
                        </div>
                        <div class="field"><label>Features — one per line</label><textarea name="plan_features[<?= $planKey ?>]" rows="8"><?= $escape(implode("\n", $plan['features'])) ?></textarea></div>
                    </div>
                <?php endforeach; ?>
                </div>
            </section>

            <div class="sticky bottom-4 bg-white/95 backdrop-blur border border-gray-200 rounded-2xl shadow-xl p-4 flex items-center justify-between gap-3 z-10">
                <p class="text-xs text-gray-400 hidden sm:block">Published changes appear on the city page immediately.</p>
                <div class="flex gap-3 ml-auto"><a href="locations.php" class="btn btn-secondary">Cancel</a><button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Save City</button></div>
            </div>
        </form>
    </main>
</div>

<template id="city-stat-template"><div class="item-box repeat-row grid-2 pr-12"><input type="text" name="stat_value[]" placeholder="20M+"><input type="text" name="stat_label[]" placeholder="Local businesses"><button type="button" class="remove-row text-red-400" onclick="this.closest('.repeat-row').remove()"><i class="bi bi-x-lg"></i></button></div></template>
<template id="faq-template"><div class="item-box repeat-row pr-12"><input type="text" name="faq_question[]" placeholder="Question" class="mb-3"><textarea name="faq_answer[]" rows="4" placeholder="Answer"></textarea><button type="button" class="remove-row text-red-400" onclick="this.closest('.repeat-row').remove()"><i class="bi bi-x-lg"></i></button></div></template>

<script>
function addTemplate(templateId, targetId) { document.getElementById(targetId).appendChild(document.getElementById(templateId).content.cloneNode(true)); }
function autoCityFields(value) {
    const display = document.getElementById('display-name');
    const schema = document.getElementById('schema-city');
    const slug = document.getElementById('city-slug');
    if (!display.dataset.manual) display.value = value;
    if (!schema.dataset.manual) schema.value = value;
    if (!slug.readOnly && !slug.dataset.manual) {
        slug.value = value.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');
        const canonical = document.getElementById('canonical-url');
        if (!canonical.dataset.manual) canonical.value = 'https://www.chulbuldesign.com/city/' + slug.value;
    }
}
['display-name','schema-city','city-slug'].forEach(function(id){ document.getElementById(id).addEventListener('input', function(){ this.dataset.manual='1'; }); });
</script>
</body>
</html>
