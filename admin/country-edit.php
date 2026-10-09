<?php
require_once __DIR__ . '/config.php';
require_login();

$pdo = get_db();
if (!$pdo) {
    http_response_code(503);
    exit('Database unavailable.');
}

$id = (int)($_GET['id'] ?? 0);
$isEdit = false;
$country = null;
if ($id) {
    $statement = $pdo->prepare('SELECT * FROM countries WHERE id = ?');
    $statement->execute([$id]);
    $country = $statement->fetch();
    $isEdit = (bool)$country;
    if (!$isEdit) {
        http_response_code(404);
        exit('Country not found.');
    }
}
$wasEdit = $isEdit;

function countrySlugify(string $value): string
{
    $value = strtolower(trim($value));
    $value = preg_replace('/[^a-z0-9]+/', '-', $value) ?? '';
    return trim($value, '-');
}

function countryParallelRows(array $fields, string $requiredField): array
{
    $count = 0;
    foreach ($fields as $values) {
        $count = max($count, count($values));
    }
    $rows = [];
    for ($index = 0; $index < $count; $index++) {
        $row = [];
        foreach ($fields as $name => $values) {
            $row[$name] = trim((string)($values[$index] ?? ''));
        }
        if (($row[$requiredField] ?? '') !== '') {
            $rows[] = $row;
        }
    }
    return $rows;
}

$stats = $highlights = $services = $testimonials = [];
if ($isEdit) {
    $statement = $pdo->prepare('SELECT value_text, label FROM country_stats WHERE country_id = ? ORDER BY sort_order, id');
    $statement->execute([$id]);
    $stats = $statement->fetchAll();

    $statement = $pdo->prepare('SELECT highlight FROM country_highlights WHERE country_id = ? ORDER BY sort_order, id');
    $statement->execute([$id]);
    $highlights = $statement->fetchAll();

    $statement = $pdo->prepare('SELECT service_key, icon_class, title, href, description FROM country_services WHERE country_id = ? ORDER BY sort_order, id');
    $statement->execute([$id]);
    $services = $statement->fetchAll();

    $statement = $pdo->prepare('SELECT client_name, client_role, rating, testimonial FROM country_testimonials WHERE country_id = ? ORDER BY sort_order, id');
    $statement->execute([$id]);
    $testimonials = $statement->fetchAll();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $text = static fn(string $key): string => trim((string)($_POST[$key] ?? ''));

    $form = [
        'name' => $text('name'),
        'slug' => countrySlugify($text('slug')),
        'iso_code' => strtoupper($text('iso_code')),
        'currency_code' => strtoupper($text('currency_code')),
        'currency_symbol' => (string)($_POST['currency_symbol'] ?? ''),
        'currency_label' => $text('currency_label'),
        'hreflang' => $text('hreflang'),
        'og_locale' => $text('og_locale'),
        'price_range' => $text('price_range'),
        'delivery_label' => $text('delivery_label'),
        'geo_region' => $text('geo_region'),
        'geo_position' => $text('geo_position'),
        'geo_icbm' => $text('geo_icbm'),
        'status' => $text('status'),
        'sort_order' => max(0, (int)($_POST['sort_order'] ?? 0)),
    ];
    $country = array_merge($country ?: [], $form);

    $stats = countryParallelRows([
        'value_text' => $_POST['stat_value'] ?? [],
        'label' => $_POST['stat_label'] ?? [],
    ], 'value_text');
    $highlights = array_map(
        static fn(string $value): array => ['highlight' => trim($value)],
        array_values(array_filter($_POST['highlight'] ?? [], static fn($value): bool => trim((string)$value) !== ''))
    );
    $services = countryParallelRows([
        'service_key' => $_POST['service_key'] ?? [],
        'icon_class' => $_POST['service_icon'] ?? [],
        'title' => $_POST['service_title'] ?? [],
        'href' => $_POST['service_href'] ?? [],
        'description' => $_POST['service_description'] ?? [],
    ], 'title');
    foreach ($services as &$service) {
        if ($service['service_key'] === '') {
            $service['service_key'] = countrySlugify($service['href'] ?: $service['title']);
        }
    }
    unset($service);
    $testimonials = countryParallelRows([
        'client_name' => $_POST['testimonial_name'] ?? [],
        'client_role' => $_POST['testimonial_role'] ?? [],
        'rating' => $_POST['testimonial_rating'] ?? [],
        'testimonial' => $_POST['testimonial_text'] ?? [],
    ], 'client_name');
    foreach ($testimonials as &$testimonial) {
        $testimonial['rating'] = min(5, max(1, (int)($testimonial['rating'] ?: 5)));
    }
    unset($testimonial);

    $invalidStats = count(array_filter($stats, static fn(array $row): bool => $row['label'] === '')) > 0;
    $invalidServices = count(array_filter($services, static fn(array $row): bool =>
        $row['service_key'] === '' || $row['href'] === '' || $row['description'] === ''
    )) > 0;
    $invalidTestimonials = count(array_filter($testimonials, static fn(array $row): bool => $row['testimonial'] === '')) > 0;
    $serviceKeys = array_column($services, 'service_key');
    $duplicateServiceKeys = count($serviceKeys) !== count(array_unique($serviceKeys));

    $allowedStatuses = ['draft', 'published', 'archived'];
    if ($form['name'] === '' || $form['slug'] === '' || !preg_match('/^[A-Z]{2}$/', $form['iso_code'])) {
        $error = 'Country name, valid slug and 2-letter ISO code are required.';
    } elseif (!preg_match('/^[A-Z]{3}$/', $form['currency_code'])) {
        $error = 'Currency code must contain exactly 3 letters.';
    } elseif ($form['currency_symbol'] === '' || $form['currency_label'] === '' || $form['hreflang'] === '' || $form['og_locale'] === '') {
        $error = 'Currency and locale fields are required.';
    } elseif (!in_array($form['status'], $allowedStatuses, true)) {
        $error = 'Invalid country status.';
    } elseif ($invalidStats || $invalidServices || $invalidTestimonials || $duplicateServiceKeys) {
        $error = 'Complete every entered row and keep each service key unique.';
    } elseif ($form['status'] === 'published' && (!$stats || !$highlights || !$services || !$testimonials)) {
        $error = 'Published countries need stats, highlights, services and testimonials.';
    } else {
        try {
            $pdo->beginTransaction();
            $values = [
                $form['name'], $form['slug'], $form['iso_code'], $form['currency_code'],
                $form['currency_symbol'], $form['currency_label'], $form['hreflang'],
                $form['og_locale'], $form['price_range'] ?: null,
                $form['delivery_label'] ?: null, $form['geo_region'] ?: null,
                $form['geo_position'] ?: null, $form['geo_icbm'] ?: null,
                $form['status'], $form['sort_order'],
            ];
            if ($isEdit) {
                $values[] = $id;
                $pdo->prepare(
                    'UPDATE countries SET name=?, slug=?, iso_code=?, currency_code=?, currency_symbol=?,
                     currency_label=?, hreflang=?, og_locale=?, price_range=?, delivery_label=?,
                     geo_region=?, geo_position=?, geo_icbm=?, status=?, sort_order=? WHERE id=?'
                )->execute($values);
            } else {
                $pdo->prepare(
                    'INSERT INTO countries (name, slug, iso_code, currency_code, currency_symbol,
                     currency_label, hreflang, og_locale, price_range, delivery_label,
                     geo_region, geo_position, geo_icbm, status, sort_order)
                     VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)'
                )->execute($values);
                $id = (int)$pdo->lastInsertId();
                $isEdit = true;
            }

            foreach (['country_stats', 'country_highlights', 'country_services', 'country_testimonials'] as $table) {
                $delete = $pdo->prepare("DELETE FROM `{$table}` WHERE country_id = ?");
                $delete->execute([$id]);
            }

            $insert = $pdo->prepare('INSERT INTO country_stats (country_id, value_text, label, sort_order) VALUES (?,?,?,?)');
            foreach ($stats as $sort => $row) {
                if ($row['label'] === '') continue;
                $insert->execute([$id, $row['value_text'], $row['label'], $sort]);
            }
            $insert = $pdo->prepare('INSERT INTO country_highlights (country_id, highlight, sort_order) VALUES (?,?,?)');
            foreach ($highlights as $sort => $row) {
                $insert->execute([$id, $row['highlight'], $sort]);
            }
            $insert = $pdo->prepare(
                "INSERT INTO country_services (country_id, service_key, icon_class, title, href, description, status, sort_order)
                 VALUES (?,?,?,?,?,?,'published',?)"
            );
            foreach ($services as $sort => $row) {
                if ($row['href'] === '' || $row['description'] === '') continue;
                $insert->execute([$id, $row['service_key'], $row['icon_class'] ?: null, $row['title'], $row['href'], $row['description'], $sort]);
            }
            $insert = $pdo->prepare(
                "INSERT INTO country_testimonials (country_id, client_name, client_role, rating, testimonial, status, sort_order)
                 VALUES (?,?,?,?,?,'published',?)"
            );
            foreach ($testimonials as $sort => $row) {
                if ($row['testimonial'] === '') continue;
                $insert->execute([$id, $row['client_name'], $row['client_role'] ?: null, $row['rating'], $row['testimonial'], $sort]);
            }

            $pdo->commit();
            audit_log($wasEdit ? 'update' : 'create', 'country', $id, $form['name']);
            set_flash('Country saved successfully: ' . $form['name'], 'success');
            header('Location: locations.php');
            exit;
        } catch (Throwable $exception) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            cbd_log_error('country-save', $exception->getMessage(), ['country_id' => $id]);
            $error = 'Country could not be saved. Check that the slug and ISO code are unique.';
        }
    }
}

if (!$country) {
    $country = [
        'name' => '', 'slug' => '', 'iso_code' => '', 'currency_code' => '',
        'currency_symbol' => '', 'currency_label' => '', 'hreflang' => 'en-US',
        'og_locale' => 'en_US', 'price_range' => '', 'delivery_label' => '',
        'geo_region' => '', 'geo_position' => '', 'geo_icbm' => '',
        'status' => 'draft', 'sort_order' => count($pdo->query('SELECT id FROM countries')->fetchAll()),
    ];
}
$escape = static fn($value): string => htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
$active_page = 'locations';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $isEdit ? 'Edit Country' : 'Add Country' ?> — Chulbul Admin</title>
<link rel="stylesheet" href="assets/admin.css">
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>.repeat-row{position:relative}.remove-row{position:absolute;right:.75rem;top:.75rem}</style>
</head>
<body class="bg-gray-100 min-h-screen">
<?php include __DIR__ . '/includes/sidebar.php'; ?>
<div class="lg:pl-60 min-h-screen">
    <header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between gap-4 sticky top-0 z-10">
        <div class="flex items-center gap-3">
            <button onclick="openSidebar()" class="lg:hidden text-gray-500"><i class="bi bi-list text-2xl"></i></button>
            <a href="locations.php" class="text-gray-400 hover:text-gray-700"><i class="bi bi-arrow-left text-lg"></i></a>
            <div><h1 class="text-xl font-extrabold text-[#1e1e5c]"><?= $isEdit ? 'Edit Country' : 'Add Country' ?></h1><p class="text-xs text-gray-400">Country-level content shared by its city pages</p></div>
        </div>
        <?php if ($isEdit): ?><a href="city-edit.php?country_id=<?= $id ?>" class="btn btn-secondary btn-sm"><i class="bi bi-plus"></i> Add City</a><?php endif; ?>
    </header>

    <main class="p-6 max-w-5xl mx-auto">
        <?php if ($error): ?><div class="flash flash-error"><i class="bi bi-exclamation-circle-fill"></i><?= $escape($error) ?></div><?php endif; ?>
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

            <section class="admin-card" id="general">
                <h2 class="font-extrabold text-[#1e1e5c] mb-5"><i class="bi bi-globe2 text-[#EE483D] mr-2"></i>Country Details</h2>
                <div class="grid-2">
                    <div class="field"><label>Country Name *</label><input type="text" name="name" value="<?= $escape($country['name']) ?>" required oninput="autoCountrySlug(this.value)"></div>
                    <div class="field"><label>Slug *</label><input type="text" id="country-slug" name="slug" value="<?= $escape($country['slug']) ?>" required></div>
                    <div class="field"><label>ISO Code *</label><input type="text" name="iso_code" value="<?= $escape($country['iso_code']) ?>" maxlength="2" placeholder="IN" required></div>
                    <div class="field"><label>Status</label><select name="status"><?php foreach (['draft','published','archived'] as $status): ?><option value="<?= $status ?>" <?= $country['status'] === $status ? 'selected' : '' ?>><?= ucfirst($status) ?></option><?php endforeach; ?></select></div>
                    <div class="field"><label>Sort Order</label><input type="number" name="sort_order" min="0" value="<?= (int)$country['sort_order'] ?>"></div>
                </div>
            </section>

            <section class="admin-card" id="currency">
                <h2 class="font-extrabold text-[#1e1e5c] mb-5"><i class="bi bi-cash-coin text-[#EE483D] mr-2"></i>Currency &amp; Locale</h2>
                <div class="grid-3">
                    <div class="field"><label>Currency Code *</label><input type="text" name="currency_code" maxlength="3" value="<?= $escape($country['currency_code']) ?>" placeholder="INR" required></div>
                    <div class="field"><label>Currency Symbol *</label><input type="text" name="currency_symbol" value="<?= $escape($country['currency_symbol']) ?>" placeholder="₹" required></div>
                    <div class="field"><label>Price Range</label><input type="text" name="price_range" value="<?= $escape($country['price_range']) ?>" placeholder="₹₹"></div>
                    <div class="field"><label>Currency Label *</label><input type="text" name="currency_label" value="<?= $escape($country['currency_label']) ?>" placeholder="Indian Rupees (INR)" required></div>
                    <div class="field"><label>Hreflang *</label><input type="text" name="hreflang" value="<?= $escape($country['hreflang']) ?>" placeholder="en-IN" required></div>
                    <div class="field"><label>Open Graph Locale *</label><input type="text" name="og_locale" value="<?= $escape($country['og_locale']) ?>" placeholder="en_IN" required></div>
                </div>
                <div class="field"><label>Delivery Label</label><input type="text" name="delivery_label" value="<?= $escape($country['delivery_label']) ?>" placeholder="Delivered Across India"></div>
            </section>

            <section class="admin-card" id="geo">
                <h2 class="font-extrabold text-[#1e1e5c] mb-5"><i class="bi bi-pin-map text-[#EE483D] mr-2"></i>Geo Defaults</h2>
                <div class="grid-3">
                    <div class="field"><label>Geo Region</label><input type="text" name="geo_region" value="<?= $escape($country['geo_region']) ?>" placeholder="US"></div>
                    <div class="field"><label>Geo Position</label><input type="text" name="geo_position" value="<?= $escape($country['geo_position']) ?>" placeholder="37.0902;-95.7129"></div>
                    <div class="field"><label>ICBM</label><input type="text" name="geo_icbm" value="<?= $escape($country['geo_icbm']) ?>" placeholder="37.0902, -95.7129"></div>
                </div>
            </section>

            <section class="admin-card" id="stats">
                <div class="flex items-center justify-between mb-5"><h2 class="font-extrabold text-[#1e1e5c]"><i class="bi bi-bar-chart-fill text-[#EE483D] mr-2"></i>Country Stats</h2><button type="button" class="btn btn-secondary btn-sm" onclick="addTemplate('stat-template','stats-list')"><i class="bi bi-plus"></i> Stat</button></div>
                <div id="stats-list" class="space-y-3">
                    <?php foreach ($stats as $row): ?><div class="item-box repeat-row grid-2 pr-12"><input type="text" name="stat_value[]" value="<?= $escape($row['value_text']) ?>" placeholder="600M+"><input type="text" name="stat_label[]" value="<?= $escape($row['label']) ?>" placeholder="Internet Users"><button type="button" class="remove-row text-red-400" onclick="this.closest('.repeat-row').remove()"><i class="bi bi-x-lg"></i></button></div><?php endforeach; ?>
                </div>
            </section>

            <section class="admin-card" id="highlights">
                <div class="flex items-center justify-between mb-5"><h2 class="font-extrabold text-[#1e1e5c]"><i class="bi bi-patch-check-fill text-[#EE483D] mr-2"></i>Service Highlights</h2><button type="button" class="btn btn-secondary btn-sm" onclick="addTemplate('highlight-template','highlights-list')"><i class="bi bi-plus"></i> Highlight</button></div>
                <div id="highlights-list" class="space-y-3">
                    <?php foreach ($highlights as $row): ?><div class="item-box repeat-row pr-12"><input type="text" name="highlight[]" value="<?= $escape($row['highlight']) ?>" placeholder="WhatsApp Integration"><button type="button" class="remove-row text-red-400" onclick="this.closest('.repeat-row').remove()"><i class="bi bi-x-lg"></i></button></div><?php endforeach; ?>
                </div>
            </section>

            <section class="admin-card" id="services">
                <div class="flex items-center justify-between mb-5"><h2 class="font-extrabold text-[#1e1e5c]"><i class="bi bi-grid-fill text-[#EE483D] mr-2"></i>Service Cards</h2><button type="button" class="btn btn-secondary btn-sm" onclick="addTemplate('service-template','services-list')"><i class="bi bi-plus"></i> Service</button></div>
                <div id="services-list" class="space-y-4">
                    <?php foreach ($services as $row): ?><div class="item-box repeat-row pr-12"><div class="grid-2 mb-3"><input type="text" name="service_title[]" value="<?= $escape($row['title']) ?>" placeholder="Service title"><input type="text" name="service_key[]" value="<?= $escape($row['service_key']) ?>" placeholder="service-key"><input type="text" name="service_icon[]" value="<?= $escape($row['icon_class']) ?>" placeholder="bi-laptop"><input type="text" name="service_href[]" value="<?= $escape($row['href']) ?>" placeholder="/web-design-development"></div><textarea name="service_description[]" rows="3" placeholder="Service description"><?= $escape($row['description']) ?></textarea><button type="button" class="remove-row text-red-400" onclick="this.closest('.repeat-row').remove()"><i class="bi bi-x-lg"></i></button></div><?php endforeach; ?>
                </div>
            </section>

            <section class="admin-card" id="testimonials">
                <div class="flex items-center justify-between mb-5"><h2 class="font-extrabold text-[#1e1e5c]"><i class="bi bi-chat-quote-fill text-[#EE483D] mr-2"></i>Testimonials</h2><button type="button" class="btn btn-secondary btn-sm" onclick="addTemplate('testimonial-template','testimonials-list')"><i class="bi bi-plus"></i> Testimonial</button></div>
                <div id="testimonials-list" class="space-y-4">
                    <?php foreach ($testimonials as $row): ?><div class="item-box repeat-row pr-12"><div class="grid-3 mb-3"><input type="text" name="testimonial_name[]" value="<?= $escape($row['client_name']) ?>" placeholder="Client name"><input type="text" name="testimonial_role[]" value="<?= $escape($row['client_role']) ?>" placeholder="Role / company"><input type="number" name="testimonial_rating[]" min="1" max="5" value="<?= (int)$row['rating'] ?>"></div><textarea name="testimonial_text[]" rows="3" placeholder="Client testimonial"><?= $escape($row['testimonial']) ?></textarea><button type="button" class="remove-row text-red-400" onclick="this.closest('.repeat-row').remove()"><i class="bi bi-x-lg"></i></button></div><?php endforeach; ?>
                </div>
            </section>

            <div class="sticky bottom-4 bg-white/95 backdrop-blur border border-gray-200 rounded-2xl shadow-xl p-4 flex items-center justify-between gap-3">
                <p class="text-xs text-gray-400 hidden sm:block">Changes affect every published city in this country.</p>
                <div class="flex gap-3 ml-auto"><a href="locations.php" class="btn btn-secondary">Cancel</a><button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Save Country</button></div>
            </div>
        </form>
    </main>
</div>

<template id="stat-template"><div class="item-box repeat-row grid-2 pr-12"><input type="text" name="stat_value[]" placeholder="600M+"><input type="text" name="stat_label[]" placeholder="Internet Users"><button type="button" class="remove-row text-red-400" onclick="this.closest('.repeat-row').remove()"><i class="bi bi-x-lg"></i></button></div></template>
<template id="highlight-template"><div class="item-box repeat-row pr-12"><input type="text" name="highlight[]" placeholder="WhatsApp Integration"><button type="button" class="remove-row text-red-400" onclick="this.closest('.repeat-row').remove()"><i class="bi bi-x-lg"></i></button></div></template>
<template id="service-template"><div class="item-box repeat-row pr-12"><div class="grid-2 mb-3"><input type="text" name="service_title[]" placeholder="Service title"><input type="text" name="service_key[]" placeholder="service-key"><input type="text" name="service_icon[]" placeholder="bi-laptop"><input type="text" name="service_href[]" placeholder="/service-url"></div><textarea name="service_description[]" rows="3" placeholder="Service description"></textarea><button type="button" class="remove-row text-red-400" onclick="this.closest('.repeat-row').remove()"><i class="bi bi-x-lg"></i></button></div></template>
<template id="testimonial-template"><div class="item-box repeat-row pr-12"><div class="grid-3 mb-3"><input type="text" name="testimonial_name[]" placeholder="Client name"><input type="text" name="testimonial_role[]" placeholder="Role / company"><input type="number" name="testimonial_rating[]" min="1" max="5" value="5"></div><textarea name="testimonial_text[]" rows="3" placeholder="Client testimonial"></textarea><button type="button" class="remove-row text-red-400" onclick="this.closest('.repeat-row').remove()"><i class="bi bi-x-lg"></i></button></div></template>

<script>
function addTemplate(templateId, targetId) {
    document.getElementById(targetId).appendChild(document.getElementById(templateId).content.cloneNode(true));
}
function autoCountrySlug(value) {
    const field = document.getElementById('country-slug');
    if (field.dataset.manual === '1' || <?= $isEdit ? 'true' : 'false' ?>) return;
    field.value = value.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');
}
document.getElementById('country-slug').addEventListener('input', function(){ this.dataset.manual = '1'; });
</script>
</body>
</html>
