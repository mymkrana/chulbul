<?php
require_once __DIR__ . '/config.php';
require_login();

$pdo = get_db();
if (!$pdo) {
    http_response_code(503);
    exit('Database unavailable.');
}

$active_page = 'locations';
$flash = get_flash();
$search = trim($_GET['search'] ?? '');
$countryFilter = (int)($_GET['country_id'] ?? 0);
$statusFilter = $_GET['status'] ?? '';
$allowedStatuses = ['draft', 'published', 'archived'];
if (!in_array($statusFilter, $allowedStatuses, true)) {
    $statusFilter = '';
}

$countries = $pdo->query(
    "SELECT co.*,
            COALESCE(cc.city_count, 0) AS city_count,
            COALESCE(cc.published_count, 0) AS published_count,
            COALESCE(cc.draft_count, 0) AS draft_count
     FROM countries co
     LEFT JOIN (
         SELECT country_id,
                COUNT(*) AS city_count,
                SUM(status = 'published') AS published_count,
                SUM(status = 'draft') AS draft_count
         FROM cities
         GROUP BY country_id
     ) cc ON cc.country_id = co.id
     ORDER BY co.sort_order, co.name"
)->fetchAll();

$allCities = $pdo->query(
    "SELECT ci.*, co.name AS country_name, co.iso_code
     FROM cities ci
     INNER JOIN countries co ON co.id = ci.country_id
     ORDER BY co.sort_order, ci.sort_order, ci.name"
)->fetchAll();

$citiesByCountry = [];
foreach ($allCities as $city) {
    $citiesByCountry[(int)$city['country_id']][] = $city;
}

$visible = [];
foreach ($countries as $country) {
    $countryId = (int)$country['id'];
    if ($countryFilter && $countryId !== $countryFilter) {
        continue;
    }

    $countryMatches = $search !== '' && (
        stripos($country['name'], $search) !== false
        || stripos($country['slug'], $search) !== false
        || stripos($country['iso_code'], $search) !== false
    );
    $matchedCities = [];
    foreach ($citiesByCountry[$countryId] ?? [] as $city) {
        if ($statusFilter !== '' && $city['status'] !== $statusFilter) {
            continue;
        }
        $cityMatches = $search === '' || $countryMatches
            || stripos($city['name'], $search) !== false
            || stripos($city['display_name'], $search) !== false
            || stripos($city['slug'], $search) !== false
            || stripos((string)$city['state_region'], $search) !== false;
        if ($cityMatches) {
            $matchedCities[] = $city;
        }
    }

    if ($search !== '' && !$countryMatches && !$matchedCities) {
        continue;
    }
    if ($statusFilter !== '' && !$matchedCities) {
        continue;
    }

    $country['visible_cities'] = $matchedCities;
    $visible[] = $country;
}

$totalCountries = count($countries);
$totalCities = count($allCities);
$publishedCities = count(array_filter($allCities, static fn(array $city): bool => $city['status'] === 'published'));
$draftCities = count(array_filter($allCities, static fn(array $city): bool => $city['status'] === 'draft'));

function locationStatusBadge(string $status): string
{
    return match ($status) {
        'published' => 'badge-green',
        'archived' => 'badge-red',
        default => 'badge-gray',
    };
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Countries &amp; Cities — Chulbul Admin</title>
<link rel="stylesheet" href="assets/admin.css">
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
.country-cities{display:none}.country-cities.open{display:block}
.country-toggle .chevron{transition:transform .2s}.country-toggle.open .chevron{transform:rotate(90deg)}
</style>
</head>
<body class="bg-gray-100 min-h-screen">
<?php include __DIR__ . '/includes/sidebar.php'; ?>

<div class="lg:pl-60 min-h-screen">
    <header class="bg-white border-b border-gray-200 px-6 py-4 flex flex-wrap items-center justify-between gap-3 sticky top-0 z-10">
        <div class="flex items-center gap-4">
            <button onclick="openSidebar()" class="lg:hidden text-gray-500"><i class="bi bi-list text-2xl"></i></button>
            <div>
                <h1 class="text-xl font-extrabold text-[#1e1e5c]">Countries &amp; Cities</h1>
                <p class="text-xs text-gray-400">Manage all location page content from one place</p>
            </div>
        </div>
        <div class="flex gap-2">
            <a href="country-edit.php" class="btn btn-secondary btn-sm"><i class="bi bi-plus-lg"></i> Country</a>
            <a href="city-edit.php" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg"></i> Add City</a>
        </div>
    </header>

    <main class="p-6 max-w-7xl mx-auto">
        <?php if ($flash): ?>
        <div class="flash <?= $flash['type'] === 'success' ? 'flash-success' : 'flash-error' ?>">
            <i class="bi <?= $flash['type'] === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-circle-fill' ?>"></i>
            <?= htmlspecialchars($flash['msg']) ?>
        </div>
        <?php endif; ?>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="admin-card !mb-0 !p-5"><p class="text-xs font-bold uppercase text-gray-400">Countries</p><p class="text-3xl font-extrabold text-[#1e1e5c] mt-2"><?= $totalCountries ?></p></div>
            <div class="admin-card !mb-0 !p-5"><p class="text-xs font-bold uppercase text-gray-400">Total Cities</p><p class="text-3xl font-extrabold text-[#49499A] mt-2"><?= $totalCities ?></p></div>
            <div class="admin-card !mb-0 !p-5"><p class="text-xs font-bold uppercase text-gray-400">Published</p><p class="text-3xl font-extrabold text-green-600 mt-2"><?= $publishedCities ?></p></div>
            <div class="admin-card !mb-0 !p-5"><p class="text-xs font-bold uppercase text-gray-400">Drafts</p><p class="text-3xl font-extrabold text-gray-500 mt-2"><?= $draftCities ?></p></div>
        </div>

        <form method="GET" class="admin-card !p-4 flex flex-wrap gap-3 items-center">
            <div class="flex-1 min-w-56">
                <input type="search" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search city, state, slug or country…">
            </div>
            <div class="w-48">
                <select name="country_id" onchange="this.form.submit()">
                    <option value="0">All Countries</option>
                    <?php foreach ($countries as $country): ?>
                    <option value="<?= $country['id'] ?>" <?= $countryFilter === (int)$country['id'] ? 'selected' : '' ?>><?= htmlspecialchars($country['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="w-44">
                <select name="status" onchange="this.form.submit()">
                    <option value="">All Statuses</option>
                    <?php foreach ($allowedStatuses as $status): ?>
                    <option value="<?= $status ?>" <?= $statusFilter === $status ? 'selected' : '' ?>><?= ucfirst($status) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button class="btn btn-dark btn-sm" type="submit"><i class="bi bi-search"></i> Search</button>
            <?php if ($search !== '' || $countryFilter || $statusFilter !== ''): ?>
            <a href="locations.php" class="btn btn-secondary btn-sm">Clear</a>
            <?php endif; ?>
        </form>

        <?php if (!$visible): ?>
        <div class="admin-card text-center py-16 text-gray-400">
            <i class="bi bi-geo-alt text-5xl block mb-3"></i>
            No matching countries or cities found.
        </div>
        <?php endif; ?>

        <div class="space-y-4">
        <?php foreach ($visible as $index => $country):
            $countryId = (int)$country['id'];
            $targetId = 'country-cities-' . $countryId;
            $open = $search !== '' || $countryFilter || $statusFilter !== '' || $index === 0;
        ?>
            <section class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="country-toggle <?= $open ? 'open' : '' ?> px-5 py-4 flex items-center gap-4 cursor-pointer hover:bg-gray-50 transition"
                     onclick="toggleCountry('<?= $targetId ?>', this)">
                    <i class="bi bi-chevron-right chevron text-gray-400"></i>
                    <div class="w-11 h-11 rounded-xl bg-[#49499A]/10 flex items-center justify-center font-extrabold text-[#49499A]">
                        <?= htmlspecialchars($country['iso_code']) ?>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <h2 class="font-extrabold text-[#1e1e5c] text-lg"><?= htmlspecialchars($country['name']) ?></h2>
                            <span class="badge <?= locationStatusBadge($country['status']) ?>"><?= ucfirst($country['status']) ?></span>
                        </div>
                        <p class="text-xs text-gray-400"><?= (int)$country['city_count'] ?> cities · <?= (int)$country['published_count'] ?> published</p>
                    </div>
                    <div class="flex gap-2" onclick="event.stopPropagation()">
                        <a href="city-edit.php?country_id=<?= $countryId ?>" class="btn btn-secondary btn-sm hidden sm:inline-flex"><i class="bi bi-plus"></i> City</a>
                        <a href="country-edit.php?id=<?= $countryId ?>" class="btn btn-secondary btn-icon"><i class="bi bi-pencil"></i></a>
                    </div>
                </div>

                <div id="<?= $targetId ?>" class="country-cities <?= $open ? 'open' : '' ?> border-t border-gray-100">
                    <?php if (empty($country['visible_cities'])): ?>
                    <div class="px-6 py-10 text-center text-sm text-gray-400">No matching cities in this country.</div>
                    <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="admin-table">
                            <thead><tr><th>City</th><th>Slug</th><th class="hidden md:table-cell">State / Region</th><th>Status</th><th class="text-right">Actions</th></tr></thead>
                            <tbody>
                            <?php foreach ($country['visible_cities'] as $city): ?>
                            <tr>
                                <td><p class="font-bold text-gray-800"><?= htmlspecialchars($city['display_name']) ?></p><p class="text-xs text-gray-400"><?= htmlspecialchars($city['name']) ?></p></td>
                                <td><code class="text-xs bg-gray-100 px-2 py-1 rounded"><?= htmlspecialchars($city['slug']) ?></code></td>
                                <td class="hidden md:table-cell text-gray-500"><?= htmlspecialchars($city['state_region'] ?: '—') ?></td>
                                <td><span class="badge <?= locationStatusBadge($city['status']) ?>"><?= ucfirst($city['status']) ?></span></td>
                                <td>
                                    <div class="flex items-center justify-end gap-2">
                                        <?php if ($city['status'] === 'published'): ?>
                                        <a href="../city.php?slug=<?= urlencode($city['slug']) ?>" target="_blank" class="btn btn-secondary btn-icon" title="Preview"><i class="bi bi-box-arrow-up-right"></i></a>
                                        <?php endif; ?>
                                        <a href="city-edit.php?id=<?= $city['id'] ?>" class="btn btn-secondary btn-icon" title="Edit"><i class="bi bi-pencil"></i></a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>
                </div>
            </section>
        <?php endforeach; ?>
        </div>
    </main>
</div>

<script>
function toggleCountry(id, button) {
    document.getElementById(id).classList.toggle('open');
    button.classList.toggle('open');
}
</script>
</body>
</html>
