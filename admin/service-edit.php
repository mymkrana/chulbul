<?php
require_once __DIR__ . '/config.php';
require_login();

$pdo = get_db();
$id  = (int)($_GET['id'] ?? 0);
$svc = null;
$is_edit = false;

if ($id && $pdo) {
    $stmt = $pdo->prepare("SELECT * FROM services WHERE id = ?");
    $stmt->execute([$id]);
    $svc = $stmt->fetch();
    if ($svc) $is_edit = true;
}

// Load categories from DB
$all_cats    = $pdo ? $pdo->query("SELECT * FROM categories WHERE deleted_at IS NULL AND status=1 ORDER BY parent_id ASC, sort_order ASC, name ASC")->fetchAll() : [];
$cat_parents = array_values(array_filter($all_cats, fn($c) => is_null($c['parent_id'])));
$cat_children = [];
foreach ($all_cats as $c) {
    if ($c['parent_id']) $cat_children[$c['parent_id']][] = $c;
}
// Build JS map: parent_slug -> [{slug, name}, ...]
$cat_js_map = [];
foreach ($cat_parents as $par) {
    $cat_js_map[$par['slug']] = array_map(fn($c) => ['slug'=>$c['slug'],'name'=>$c['name']], $cat_children[$par['id']] ?? []);
}

// ── Schema auto-generator ───────────────────────────────────────────────────
function generate_schema(array $d, array $cat_map): string {
    $slug     = $d['slug'] ?? '';
    $name     = $d['meta_title'] ?: ($d['hero_h1'] ?: $slug);
    $desc     = $d['meta_desc'] ?? '';
    $image    = $d['meta_image'] ?: ($d['hero_img'] ?? '');
    $svcType  = '';
    // find subcategory display name from map
    foreach ($cat_map as $children) {
        foreach ($children as $child) {
            if ($child['slug'] === ($d['subcategory'] ?? '')) {
                $svcType = $child['name'];
                break 2;
            }
        }
    }
    $origin = 'https://www.chulbuldesign.com';
    $url = $origin . '/' . $slug;

    $schema = [
        '@context'    => 'https://schema.org',
        '@type'       => 'Service',
        'name'        => $name,
        'description' => $desc,
        'url'         => $url,
        'provider'    => [
            '@type'  => 'Organization',
            'name'   => 'Chulbul Design',
            'url'    => $origin,
            'logo'   => $origin . '/assets/images/logo/chulbuldesign.svg',
        ],
        'areaServed'  => ['@type' => 'Country', 'name' => 'India'],
    ];

    if ($svcType) $schema['serviceType'] = $svcType;
    if ($image)   $schema['image']       = (str_starts_with($image, 'http') ? '' : $origin) . $image;

    // Breadcrumb
    $schema['breadcrumb'] = [
        '@type'           => 'BreadcrumbList',
        'itemListElement' => [
            ['@type'=>'ListItem','position'=>1,'name'=>'Home',    'item'=>$origin . '/'],
            ['@type'=>'ListItem','position'=>2,'name'=>$name,     'item'=>$url],
        ],
    ];

    return str_replace(
        'https://chulbuldesign.com',
        $origin,
        json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
    );
}

// ── SAVE ────────────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    // helpers
    $t  = fn($k) => trim($_POST[$k] ?? '');
    $bullets = fn($k) => json_encode(array_values(array_filter(array_map('trim', explode("\n", $_POST[$k] ?? '')))));
    $cards_json = function () {
        $cards = [];
        $icons   = $_POST['card_icon']  ?? [];
        $colors  = $_POST['card_color'] ?? [];
        $titles  = $_POST['card_title'] ?? [];
        $descs   = $_POST['card_desc']  ?? [];
        for ($i = 0; $i < 6; $i++) {
            if (!empty($titles[$i])) {
                $cards[] = ['icon'=>trim($icons[$i]??''), 'color'=>trim($colors[$i]??'#EE483D'), 'title'=>trim($titles[$i]??''), 'desc'=>trim($descs[$i]??'')];
            }
        }
        return json_encode($cards);
    };
    $steps_json = function () {
        $steps = [];
        $nums   = $_POST['step_num']   ?? [];
        $icons  = $_POST['step_icon']  ?? [];
        $titles = $_POST['step_title'] ?? [];
        $descs  = $_POST['step_desc']  ?? [];
        for ($i = 0; $i < 5; $i++) {
            if (!empty($titles[$i])) {
                $steps[] = ['num'=>trim($nums[$i]??($i+1)),'icon'=>trim($icons[$i]??'bi-check-lg'),'title'=>trim($titles[$i]??''),'desc'=>trim($descs[$i]??'')];
            }
        }
        return json_encode($steps);
    };
    $sections_json = function () {
        $sections = [];
        $h2s    = $_POST['sec_h2']      ?? [];
        $paras  = $_POST['sec_para']    ?? [];
        $blts   = $_POST['sec_bullets'] ?? [];
        $imgs   = $_POST['sec_img']     ?? [];
        $alts   = $_POST['sec_img_alt'] ?? [];
        $count  = count($h2s);
        for ($i = 0; $i < $count; $i++) {
            if (trim($h2s[$i]) || trim($paras[$i] ?? '')) {
                $bullets = array_values(array_filter(array_map('trim', explode("\n", $blts[$i] ?? ''))));
                $sections[] = [
                    'h2'      => trim($h2s[$i]),
                    'para'    => trim($paras[$i] ?? ''),
                    'bullets' => $bullets,
                    'img'     => trim($imgs[$i] ?? ''),
                    'img_alt' => trim($alts[$i] ?? ''),
                ];
            }
        }
        return json_encode($sections);
    };

    $data = [
        $t('slug'), $t('category'), $t('subcategory'), (int)($t('status') ?: 1),
        $t('meta_title'), $t('meta_desc'), $t('meta_image'),
        generate_schema([
            'slug'        => $t('slug'),
            'meta_title'  => $t('meta_title'),
            'meta_desc'   => $t('meta_desc'),
            'meta_image'  => $t('meta_image'),
            'hero_h1'     => $t('hero_h1'),
            'hero_img'    => $t('hero_img'),
            'subcategory' => $t('subcategory'),
        ], $cat_js_map),
        $t('hero_badge'), $t('hero_breadcrumb'), $t('hero_h1'), $t('hero_desc'), $t('hero_img'), $t('hero_img_alt'),
        $t('intro_text'),
        $sections_json(),
        $t('stat1_val'), $t('stat1_label'), $t('stat2_val'), $t('stat2_label'),
        $t('stat3_val'), $t('stat3_label'), $t('stat4_val'), $t('stat4_label'),
        $t('cards_heading'), $t('cards_subtitle'), $cards_json(),
        $t('process_heading'), $steps_json(),
    ];

    try {
        if ($is_edit) {
            $data[] = $id;
            $pdo->prepare("UPDATE services SET
                slug=?,category=?,subcategory=?,status=?,
                meta_title=?,meta_desc=?,meta_image=?,schema_json=?,
                hero_badge=?,hero_breadcrumb=?,hero_h1=?,hero_desc=?,hero_img=?,hero_img_alt=?,
                intro_text=?,
                sections=?,
                stat1_val=?,stat1_label=?,stat2_val=?,stat2_label=?,
                stat3_val=?,stat3_label=?,stat4_val=?,stat4_label=?,
                cards_heading=?,cards_subtitle=?,cards=?,
                process_heading=?,steps=?
                WHERE id=?")->execute($data);
            set_flash('Service updated successfully.', 'success');
        } else {
            $pdo->prepare("INSERT INTO services
                (slug,category,subcategory,status,
                meta_title,meta_desc,meta_image,schema_json,
                hero_badge,hero_breadcrumb,hero_h1,hero_desc,hero_img,hero_img_alt,
                intro_text,
                sections,
                stat1_val,stat1_label,stat2_val,stat2_label,
                stat3_val,stat3_label,stat4_val,stat4_label,
                cards_heading,cards_subtitle,cards,
                process_heading,steps)
                VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)")->execute($data);
            set_flash('Service added successfully.', 'success');
        }
        header('Location: services.php');
        exit;
    } catch (PDOException $e) {
        $save_error = $e->getMessage();
    }
}

// ── Helpers for form pre-fill ────────────────────────────────────────────────
$v = fn($k) => htmlspecialchars($svc[$k] ?? ($_POST[$k] ?? ''));
$bullets_str = fn($k) => htmlspecialchars(implode("\n", json_decode($svc[$k] ?? '[]', true) ?: []));

// Load dynamic sections
$sections_data = json_decode($svc['sections'] ?? '[]', true) ?: [];
if (empty($sections_data)) {
    foreach ([1,2,3] as $n) {
        if (!empty($svc["s{$n}_h2"]) || !empty($svc["s{$n}_para"])) {
            $sections_data[] = [
                'h2'      => $svc["s{$n}_h2"]      ?? '',
                'para'    => $svc["s{$n}_para"]     ?? '',
                'bullets' => json_decode($svc["s{$n}_bullets"] ?? '[]', true) ?: [],
                'img'     => $svc["s{$n}_img"]      ?? '',
                'img_alt' => $svc["s{$n}_img_alt"]  ?? '',
            ];
        }
    }
}
if (empty($sections_data)) {
    $sections_data = [['h2'=>'','para'=>'','bullets'=>[],'img'=>'','img_alt'=>'']];
}
$get_cards = function () use ($svc) {
    $c = json_decode($svc['cards'] ?? '[]', true) ?: [];
    while (count($c) < 6) $c[] = ['icon'=>'','color'=>'#EE483D','title'=>'','desc'=>''];
    return $c;
};
$get_steps = function () use ($svc) {
    $s = json_decode($svc['steps'] ?? '[]', true) ?: [];
    for ($i = count($s); $i < 5; $i++) $s[] = ['num'=>$i+1,'icon'=>'bi-check-lg','title'=>'','desc'=>''];
    return $s;
};
$cards_data = $is_edit ? $get_cards() : array_fill(0, 6, ['icon'=>'','color'=>'#EE483D','title'=>'','desc'=>'']);
$steps_data = $is_edit ? $get_steps() : array_map(fn($i)=>['num'=>$i+1,'icon'=>'bi-check-lg','title'=>'','desc'=>''], range(0,4));

$active_page = $is_edit ? 'services' : 'service-add';

// Helper: icon field — BS icon class OR SVG from media library
function iconField(string $name, string $value, string $color = '#EE483D'): string {
    static $idx = 0; $idx++;
    $uid   = 'icon_f' . $idx;
    $val   = htmlspecialchars($value);
    $isSvg = $value && !str_starts_with($value, 'bi-');

    if ($isSvg) {
        $previewHtml = '<img src="' . $val . '" style="width:24px;height:24px;object-fit:contain" alt="">';
    } elseif ($value) {
        $previewHtml = '<i class="bi ' . $val . '" style="color:' . htmlspecialchars($color) . '"></i>';
    } else {
        $previewHtml = '<i class="bi bi-image" style="color:#d1d5db"></i>';
    }

    return '
    <div class="icon-field">
        <div class="icon-preview-box" id="prev_' . $uid . '">' . $previewHtml . '</div>
        <input type="text" name="' . $name . '" id="' . $uid . '"
               value="' . $val . '" placeholder="bi-globe2"
               oninput="updateIconPreview(\'' . $uid . '\', this.value, \'' . htmlspecialchars($color) . '\')">
        <button type="button" class="icon-upload-btn" onclick="openMedia(\'' . $uid . '\', \'svg\')" title="Browse / Upload SVG">
            <i class="bi bi-images"></i> SVG
        </button>
    </div>';
}

// Helper: image field — path input + media library button + preview
function imgField(string $name, string $value, string $placeholder = 'Image path'): string {
    static $idx = 0; $idx++;
    $uid = 'img_f' . $idx;
    $preview = $value
        ? '<div class="img-preview" id="prev_' . $uid . '" style="display:block">
               <img src="' . htmlspecialchars($value) . '" alt="preview" onerror="this.parentElement.style.display=\'none\'">
           </div>'
        : '<div class="img-preview" id="prev_' . $uid . '"></div>';

    return '
    <div class="img-field">
        <input type="text" name="' . $name . '" id="' . $uid . '"
               value="' . htmlspecialchars($value) . '"
               placeholder="' . htmlspecialchars($placeholder) . '"
               oninput="updatePreview(\'' . $uid . '\')">
        <button type="button" class="img-upload-btn" onclick="openMedia(\'' . $uid . '\', \'img\')">
            <i class="bi bi-images"></i> Media
        </button>
    </div>
    ' . $preview;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $is_edit ? 'Edit Service' : 'Add Service' ?> — Chulbul Admin</title>
<link rel="stylesheet" href="assets/admin.css">
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
/* page-specific only */

/* ── Tabs ── */
.tab-btn{transition:all .2s;}
.tab-btn.active{background:#EE483D;color:#fff;border-color:#EE483D;}

/* ── Section card ── */
.section-card{background:#fff;border-radius:1rem;padding:1.75rem;border:1px solid #eef0f6;margin-bottom:1.25rem;box-shadow:0 1px 4px rgba(0,0,0,.04);}

/* ── Monospace for code fields ── */
.field-mono{font-family:'SFMono-Regular',Consolas,'Liberation Mono',Menlo,monospace;font-size:.8rem;}

/* ── Image upload widget ── */
.img-field{display:flex;gap:.5rem;align-items:center;}
.img-field input[type=text]{flex:1;}
.img-upload-btn{flex-shrink:0;display:inline-flex;align-items:center;gap:.4rem;
    height:46px !important;padding:0 1rem !important;
    background:#f3f4f6;border:1.5px solid #e5e7eb;border-radius:.75rem;
    font-size:.8125rem;font-weight:600;color:#374151;cursor:pointer;
    white-space:nowrap;transition:all .18s;}
.img-upload-btn:hover{background:#e5e7eb;border-color:#d1d5db;}
.img-upload-btn input[type=file]{display:none;}
.img-preview{margin-top:.5rem;display:none;}
.img-preview img{max-height:120px;max-width:100%;border-radius:.625rem;border:1.5px solid #e5e7eb;object-fit:cover;}

/* ── Media Library Modal ── */
.media-overlay{position:fixed;inset:0;background:rgba(0,0,0,.55);z-index:1000;display:none;align-items:center;justify-content:center;padding:1rem;}
.media-overlay.open{display:flex;}
.media-modal{background:#fff;border-radius:1.25rem;width:100%;max-width:900px;max-height:90vh;display:flex;flex-direction:column;overflow:hidden;box-shadow:0 25px 60px rgba(0,0,0,.25);}
.media-header{display:flex;align-items:center;justify-content:space-between;padding:1.25rem 1.5rem;border-bottom:1px solid #f0f0f5;}
.media-header h3{font-size:1rem;font-weight:800;color:#1e1e5c;margin:0;}
.media-close{background:none;border:none;font-size:1.25rem;color:#6b7280;cursor:pointer;line-height:1;padding:.25rem;}
.media-close:hover{color:#111;}
.media-toolbar{display:flex;gap:.75rem;padding:1rem 1.5rem;border-bottom:1px solid #f0f0f5;align-items:center;flex-wrap:wrap;}
.media-upload-zone{border:2px dashed #e5e7eb;border-radius:.875rem;padding:1rem 1.5rem;display:flex;align-items:center;gap:.75rem;cursor:pointer;transition:all .18s;flex:1;min-width:200px;}
.media-upload-zone:hover,.media-upload-zone.drag{border-color:#EE483D;background:#fff5f5;}
.media-upload-zone input{display:none;}
.media-upload-zone span{font-size:.8125rem;font-weight:600;color:#6b7280;}
.media-search{height:40px;padding:0 .875rem;border:1.5px solid #e5e7eb;border-radius:.75rem;font-size:.8125rem;width:200px;outline:none;}
.media-search:focus{border-color:#EE483D;}
.media-body{flex:1;overflow-y:auto;padding:1.25rem 1.5rem;}
.media-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(130px,1fr));gap:.875rem;}
.media-item{position:relative;border-radius:.75rem;overflow:hidden;border:2px solid #f0f0f5;cursor:pointer;transition:all .18s;background:#f8f9fc;aspect-ratio:1;}
.media-item:hover{border-color:#EE483D;box-shadow:0 4px 16px rgba(238,72,61,.15);}
.media-item.selected{border-color:#EE483D;box-shadow:0 0 0 3px rgba(238,72,61,.2);}
.media-item img,.media-item .svg-thumb{width:100%;height:100%;object-fit:cover;display:block;}
.media-item .svg-thumb{display:flex;align-items:center;justify-content:center;font-size:2rem;color:#6b7280;background:#f3f4f6;}
.media-item .media-check{position:absolute;top:.35rem;right:.35rem;width:22px;height:22px;background:#EE483D;border-radius:50%;display:none;align-items:center;justify-content:center;color:#fff;font-size:.75rem;}
.media-item.selected .media-check{display:flex;}
.media-item .media-del{position:absolute;top:.35rem;left:.35rem;width:22px;height:22px;background:rgba(0,0,0,.5);border-radius:50%;display:none;align-items:center;justify-content:center;color:#fff;font-size:.65rem;cursor:pointer;}
.media-item:hover .media-del{display:flex;}
.media-name{font-size:.65rem;color:#6b7280;padding:.3rem .4rem;background:#fff;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;border-top:1px solid #f0f0f5;}
.media-footer{display:flex;align-items:center;justify-content:space-between;padding:1rem 1.5rem;border-top:1px solid #f0f0f5;gap:1rem;}
.media-selected-info{font-size:.8125rem;color:#6b7280;flex:1;}
.media-select-btn{background:#EE483D;color:#fff;border:none;border-radius:.75rem;padding:.65rem 1.5rem;font-size:.875rem;font-weight:700;cursor:pointer;transition:background .18s;}
.media-select-btn:hover{background:#d63027;}
.media-select-btn:disabled{background:#e5e7eb;color:#9ca3af;cursor:not-allowed;}
.media-empty{text-align:center;padding:3rem 1rem;color:#9ca3af;}
.media-uploading{opacity:.5;pointer-events:none;}
.folder-tab{padding:.3rem .85rem;border-radius:999px;font-size:.75rem;font-weight:600;border:1.5px solid #e5e7eb;background:#fff;color:#6b7280;cursor:pointer;transition:all .15s;white-space:nowrap;}
.folder-tab:hover{border-color:#EE483D;color:#EE483D;}
.folder-tab.active{background:#EE483D;border-color:#EE483D;color:#fff;}
.media-item-folder{position:absolute;bottom:26px;left:0;right:0;font-size:.6rem;background:rgba(0,0,0,.45);color:#fff;padding:.15rem .35rem;text-align:center;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}

/* ── Icon field widget ── */
.icon-field{display:flex;gap:.5rem;align-items:center;}
.icon-field input[type=text]{flex:1;}
.icon-preview-box{
    flex-shrink:0;width:46px;height:46px;
    display:flex;align-items:center;justify-content:center;
    background:#f3f4f6;border:1.5px solid #e5e7eb;border-radius:.75rem;
    font-size:1.25rem;color:#374151;overflow:hidden;}
.icon-preview-box img{width:24px;height:24px;object-fit:contain;}
.icon-upload-btn{flex-shrink:0;display:inline-flex;align-items:center;gap:.35rem;
    height:46px !important;padding:0 .75rem !important;
    background:#f3f4f6;border:1.5px solid #e5e7eb;border-radius:.75rem;
    font-size:.8125rem;font-weight:600;color:#374151;cursor:pointer;
    white-space:nowrap;transition:all .18s;}
.icon-upload-btn:hover{background:#e5e7eb;border-color:#d1d5db;}
.icon-upload-btn input[type=file]{display:none;}

/* ── Stat value — smaller width ── */
.input-sm{width:7rem!important;}

/* ── Grids ── */
.grid-2{display:grid;grid-template-columns:1fr 1fr;gap:1rem;}
.grid-3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:.85rem;}
.grid-4{display:grid;grid-template-columns:repeat(4,1fr);gap:.85rem;}
@media(max-width:640px){.grid-2,.grid-3,.grid-4{grid-template-columns:1fr;}}

/* ── Card / Step box ── */
.item-box{background:#f8f9fc;border:1.5px solid #eef0f6;border-radius:.875rem;padding:1rem;}
</style>
</head>
<body class="bg-gray-100 min-h-screen">
<?php include __DIR__ . '/includes/sidebar.php'; ?>

<div class="lg:pl-60 flex flex-col min-h-screen">
    <!-- Topbar -->
    <header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center gap-4 sticky top-0 z-10">
        <button onclick="openSidebar()" class="lg:hidden text-gray-500"><i class="bi bi-list text-2xl"></i></button>
        <a href="services.php" class="text-gray-400 hover:text-gray-600 transition"><i class="bi bi-arrow-left text-lg"></i></a>
        <h1 class="text-xl font-extrabold text-[#1e1e5c]">
            <?= $is_edit ? 'Edit: <span class="text-[#EE483D]">' . htmlspecialchars($svc['slug']) . '</span>' : 'Add New Service' ?>
        </h1>
    </header>

    <main class="flex-1 p-6 max-w-5xl mx-auto w-full">
        <?php if (!empty($_GET['imported'])): ?>
        <div class="mb-4 bg-green-50 border border-green-200 text-green-700 text-sm px-5 py-3 rounded-xl flex items-center gap-3">
            <i class="bi bi-stars text-[#EE483D]"></i>
            <strong>AI Import successful!</strong>&nbsp; Content aur images fill ho gaye hain. Review karo aur zaroorat ho to edit karo.
        </div>
        <?php endif; ?>
        <?php if (!empty($save_error)): ?>
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 text-sm px-5 py-3 rounded-xl">
            <strong>Error:</strong> <?= htmlspecialchars($save_error) ?>
        </div>
        <?php endif; ?>

        <!-- ── AI Import (fill fields only) ──────────────────────────── -->
        <div class="bg-[#1e1e5c] rounded-2xl p-4 mb-4 flex flex-wrap items-center gap-3">
            <i class="bi bi-stars text-amber-400 text-xl flex-shrink-0"></i>
            <span class="text-white text-sm font-bold flex-shrink-0">AI Import</span>
            <input type="url" id="import_url" placeholder="Competitor page URL paste karo…"
                   class="flex-1 min-w-48 bg-white/10 border-white/20 text-white placeholder-white/40 text-sm"
                   style="color:#fff!important;background:rgba(255,255,255,.1)!important;border-color:rgba(255,255,255,.2)!important;">
            <button type="button" onclick="runImport()"
                    id="import_btn"
                    class="flex-shrink-0 flex items-center gap-2 bg-[#EE483D] hover:bg-red-500 text-white text-sm font-bold px-5 py-2.5 rounded-xl transition">
                <i class="bi bi-lightning-fill"></i> <span id="import_btn_txt">Fill Fields</span>
            </button>
            <p id="import_status" class="w-full text-xs text-white/60 hidden"></p>
        </div>

        <?php if ($is_edit): ?>
        <!-- ── AI Rewrite & Save (edit only) ───────────────────────────── -->
        <div class="bg-amber-50 border border-amber-200 rounded-2xl mb-6 overflow-hidden">
            <!-- Toggle header -->
            <button type="button" onclick="toggleRewritePanel()"
                    class="w-full flex items-center gap-3 px-5 py-3.5 text-left hover:bg-amber-100 transition">
                <i class="bi bi-arrow-repeat text-amber-600 text-lg flex-shrink-0"></i>
                <div class="flex-1">
                    <span class="text-sm font-extrabold text-amber-800">Rewrite with AI & Save</span>
                    <span class="text-xs text-amber-600 ml-2">— competitor URL se content replace karo, images bhi</span>
                </div>
                <i class="bi bi-chevron-down text-amber-600 text-sm transition-transform" id="rewrite_chevron"></i>
            </button>

            <!-- Expandable body -->
            <div id="rewrite_panel" class="hidden border-t border-amber-200 px-5 pb-5 pt-4">

                <!-- Input row -->
                <div id="rewrite_form" class="flex flex-wrap gap-3 items-end">
                    <div class="flex-1 min-w-64">
                        <label class="text-xs font-bold text-amber-800 mb-1.5 block">Competitor / Reference Page URL</label>
                        <input type="url" id="rewrite_url" placeholder="https://competitor.com/service-page"
                               class="w-full" style="border-color:#fcd34d;">
                    </div>
                    <button type="button" onclick="runRewrite()"
                            id="rewrite_btn"
                            class="flex items-center gap-2 bg-amber-500 hover:bg-amber-600 text-white font-bold px-6 py-3 rounded-xl transition flex-shrink-0">
                        <i class="bi bi-arrow-repeat"></i> <span id="rewrite_btn_txt">Rewrite & Save</span>
                    </button>
                </div>

                <!-- Warning -->
                <p class="text-xs text-amber-700 mt-3 flex items-start gap-1.5">
                    <i class="bi bi-exclamation-triangle-fill flex-shrink-0 mt-0.5"></i>
                    Yeh existing content ko <strong>replace</strong> karega aur database mein save karega. Form ke unsaved changes overwrite ho jayenge.
                </p>

                <!-- Progress steps -->
                <div id="rewrite_progress" class="hidden mt-4 space-y-2.5">
                    <div id="rw_st_fetch"  class="rw-step flex items-center gap-3 text-sm text-gray-400">
                        <span class="rw-step-icon w-6 h-6 rounded-full border-2 border-gray-200 flex items-center justify-center flex-shrink-0 text-xs"></span>
                        <span>Competitor URL se content fetch ho raha hai…</span>
                    </div>
                    <div id="rw_st_ai"    class="rw-step flex items-center gap-3 text-sm text-gray-400">
                        <span class="rw-step-icon w-6 h-6 rounded-full border-2 border-gray-200 flex items-center justify-center flex-shrink-0 text-xs"></span>
                        <span>AI content adapt kar raha hai…</span>
                    </div>
                    <div id="rw_st_images" class="rw-step flex items-center gap-3 text-sm text-gray-400">
                        <span class="rw-step-icon w-6 h-6 rounded-full border-2 border-gray-200 flex items-center justify-center flex-shrink-0 text-xs"></span>
                        <span>Pexels se images download ho rahi hain…</span>
                    </div>
                    <div id="rw_st_save"  class="rw-step flex items-center gap-3 text-sm text-gray-400">
                        <span class="rw-step-icon w-6 h-6 rounded-full border-2 border-gray-200 flex items-center justify-center flex-shrink-0 text-xs"></span>
                        <span>Database mein save ho raha hai…</span>
                    </div>
                </div>

                <!-- Result message -->
                <div id="rewrite_result" class="hidden mt-3 text-sm rounded-xl px-4 py-3"></div>
            </div>
        </div>

        <style>
        .rw-step-active .rw-step-icon { border-color:#f59e0b; background:#fffbeb; }
        .rw-step-active { color:#92400e; font-weight:600; }
        .rw-step-active .rw-step-icon::after { content:''; display:block; width:8px; height:8px; border-radius:50%; background:#f59e0b; margin:auto; animation:rwpulse 1s infinite; }
        .rw-step-done .rw-step-icon { border-color:#22c55e; background:#f0fdf4; color:#22c55e; }
        .rw-step-done { color:#15803d; }
        @keyframes rwpulse { 0%,100%{opacity:1} 50%{opacity:.35} }
        </style>
        <?php endif; ?>

        <!-- Image Picker (shown after import) -->
        <div id="img_picker" class="hidden bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mb-6">
            <div class="flex items-center justify-between mb-3">
                <p class="text-sm font-extrabold text-[#1e1e5c]"><i class="bi bi-images mr-2 text-[#EE483D]"></i>Pexels Photos — Assign karo</p>
                <button type="button" onclick="document.getElementById('img_picker').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 text-lg leading-none">&times;</button>
            </div>
            <div id="img_grid" class="grid grid-cols-3 gap-3 mb-3" style="grid-template-columns:repeat(auto-fill,minmax(150px,1fr))"></div>
            <p class="text-xs text-gray-400"><i class="bi bi-camera mr-1"></i>Photos by <a href="https://www.pexels.com" target="_blank" class="underline">Pexels</a></p>
        </div>

        <!-- Tabs -->
        <div class="flex flex-wrap gap-2 mb-6">
            <?php
            $tabs = [
                ['id'=>'meta',    'label'=>'Meta & SEO',   'icon'=>'bi-search'],
                ['id'=>'hero',    'label'=>'Hero',          'icon'=>'bi-house-fill'],
                ['id'=>'content', 'label'=>'Sections',       'icon'=>'bi-layout-text-window'],
                ['id'=>'stats',   'label'=>'Stats Bar',     'icon'=>'bi-bar-chart-fill'],
                ['id'=>'cards',   'label'=>'6 Cards',       'icon'=>'bi-grid-3x3-gap-fill'],
                ['id'=>'process', 'label'=>'5 Steps',       'icon'=>'bi-list-ol'],
            ];
            foreach ($tabs as $t): ?>
            <button type="button" onclick="showTab('<?= $t['id'] ?>')"
                    id="tab-btn-<?= $t['id'] ?>"
                    class="tab-btn flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-semibold border border-gray-200 bg-white text-gray-600 hover:border-[#EE483D] hover:text-[#EE483D]">
                <i class="bi <?= $t['icon'] ?>"></i> <?= $t['label'] ?>
            </button>
            <?php endforeach; ?>
        </div>

        <form method="POST" id="svc-form">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

            <!-- ── TAB: META ────────────────────────────────────────────── -->
            <div id="tab-meta" class="tab-content">
                <div class="section-card">
                    <h2 class="text-base font-extrabold text-[#1e1e5c] mb-4"><i class="bi bi-search mr-2 text-[#EE483D]"></i>Meta & SEO</h2>
                    <div class="grid-3 mb-5">
                        <div>
                            <label>Slug <span class="text-red-500">*</span></label>
                            <input type="text" name="slug" value="<?= $v('slug') ?>" placeholder="web-design" required>
                            <p class="field-hint">URL: chulbuldesign.com/<strong>slug</strong></p>
                        </div>
                        <div>
                            <label>Category <span class="text-red-500">*</span></label>
                            <select name="category" id="sel_category" onchange="filterSubcategory(this.value)" required>
                                <option value="">— Select Category —</option>
                                <?php foreach ($cat_parents as $par): ?>
                                <option value="<?= htmlspecialchars($par['slug']) ?>"
                                    <?= ($svc['category']??'') === $par['slug'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($par['name']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label>Subcategory</label>
                            <select name="subcategory" id="sel_subcategory">
                                <option value="">— Select Category First —</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-5">
                        <label>Status</label>
                        <select name="status" style="width:10rem">
                            <option value="1" <?= ($svc['status'] ?? 1) == 1 ? 'selected' : '' ?>>Active</option>
                            <option value="0" <?= ($svc['status'] ?? 1) == 0 ? 'selected' : '' ?>>Inactive</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label>Meta Title</label>
                        <input type="text" name="meta_title" value="<?= $v('meta_title') ?>" placeholder="Web Design Company in India | Chulbul Design">
                    </div>
                    <div class="mb-4">
                        <label>Meta Description</label>
                        <textarea name="meta_desc" rows="3" placeholder="Best web design company in India..."><?= $v('meta_desc') ?></textarea>
                    </div>
                    <div class="mb-4">
                        <label>OG / Twitter Image</label>
                        <?= imgField('meta_image', $v('meta_image'), 'OG/Twitter image') ?>
                    </div>
                    <div>
                        <label>Schema JSON (ld+json)
                            <span class="text-[#EE483D] font-normal text-xs ml-1">— auto-generated on save</span>
                        </label>
                        <?php if (!empty($svc['schema_json'])): ?>
                        <pre class="field-mono text-xs bg-gray-50 border border-gray-200 rounded-xl p-3 overflow-x-auto text-gray-500 max-h-40"><?= htmlspecialchars($svc['schema_json']) ?></pre>
                        <?php else: ?>
                        <p class="text-xs text-gray-400 bg-gray-50 border border-gray-200 rounded-xl px-4 py-3">
                            <i class="bi bi-magic mr-1 text-[#EE483D]"></i>
                            Will be auto-generated when you save the service.
                        </p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- ── TAB: HERO ────────────────────────────────────────────── -->
            <div id="tab-hero" class="tab-content hidden">
                <div class="section-card">
                    <h2 class="text-base font-extrabold text-[#1e1e5c] mb-4"><i class="bi bi-house-fill mr-2 text-[#EE483D]"></i>Hero Section</h2>
                    <div class="grid-2 mb-4">
                        <div>
                            <label>Badge Text</label>
                            <input type="text" name="hero_badge" value="<?= $v('hero_badge') ?>" placeholder="Web Design Services">
                        </div>
                        <div>
                            <label>Breadcrumb Label</label>
                            <input type="text" name="hero_breadcrumb" value="<?= $v('hero_breadcrumb') ?>" placeholder="Web Design">
                        </div>
                    </div>
                    <div class="mb-4">
                        <label>H1 Heading <span class="text-xs text-gray-400">(HTML allowed — use &lt;span class="text-[#EE483D]"&gt; for red text)</span></label>
                        <input type="text" name="hero_h1" value="<?= $v('hero_h1') ?>" placeholder='Web Design Company <span class="text-[#EE483D]">in India.</span>'>
                    </div>
                    <div class="mb-4">
                        <label>Hero Description</label>
                        <textarea name="hero_desc" rows="2" placeholder="Custom, mobile-first websites built from the ground up..."><?= $v('hero_desc') ?></textarea>
                    </div>
                    <div class="mb-4">
                        <label>Hero Image</label>
                        <?= imgField('hero_img', $v('hero_img'), 'Hero section image') ?>
                    </div>
                    <div>
                        <label>Image Alt Text</label>
                        <input type="text" name="hero_img_alt" value="<?= $v('hero_img_alt') ?>" placeholder="Web Design Services">
                    </div>
                </div>
                <div class="section-card">
                    <h2 class="text-base font-extrabold text-[#1e1e5c] mb-4"><i class="bi bi-paragraph mr-2 text-[#EE483D]"></i>Intro Paragraph</h2>
                    <div>
                        <label>Intro Text (shown below hero, centered)</label>
                        <textarea name="intro_text" rows="5" placeholder="Choosing the right web design company in India..."><?= $v('intro_text') ?></textarea>
                    </div>
                </div>
            </div>

            <!-- ── TAB: DYNAMIC CONTENT SECTIONS ───────────────────────── -->
            <div id="tab-content" class="tab-content hidden">
                <div id="sections_wrap">
                <?php foreach ($sections_data as $si => $sec):
                    $imgLeft = ($si % 2 === 0);
                    $layout  = $imgLeft ? 'Image LEFT, Text RIGHT' : 'Text LEFT, Image RIGHT (gray bg)';
                    $bStr    = is_array($sec['bullets']) ? implode("\n", $sec['bullets']) : '';
                ?>
                <div class="section-card" id="sec_card_<?= $si ?>">
                    <div class="flex items-center justify-between mb-3">
                        <h2 class="text-base font-extrabold text-[#1e1e5c]">
                            <i class="bi bi-layout-text-window mr-2 text-[#EE483D]"></i>
                            Section <span class="sec-num"><?= $si + 1 ?></span>
                            <span class="text-xs text-gray-400 font-normal ml-2 sec-layout"><?= $layout ?></span>
                        </h2>
                        <button type="button" onclick="removeSection(this)"
                                class="text-xs text-red-400 hover:text-red-600 border border-red-200 hover:border-red-400 px-3 py-1 rounded-lg transition flex items-center gap-1">
                            <i class="bi bi-trash"></i> Remove
                        </button>
                    </div>
                    <div class="mb-4">
                        <label>H2 Heading</label>
                        <input type="text" name="sec_h2[]" value="<?= htmlspecialchars($sec['h2'] ?? '') ?>" placeholder="Section heading">
                    </div>
                    <div class="mb-4">
                        <label>Paragraph</label>
                        <textarea name="sec_para[]" rows="5"><?= htmlspecialchars($sec['para'] ?? '') ?></textarea>
                    </div>
                    <div class="mb-4">
                        <label>Bullet Points <span class="text-xs text-gray-400">(one per line)</span></label>
                        <textarea name="sec_bullets[]" rows="5" placeholder="Bullet 1&#10;Bullet 2&#10;Bullet 3"><?= htmlspecialchars($bStr) ?></textarea>
                    </div>
                    <div class="mb-4">
                        <label>Section Image</label>
                        <?= imgField('sec_img[]', $sec['img'] ?? '', 'Section image') ?>
                    </div>
                    <div>
                        <label>Image Alt Text</label>
                        <input type="text" name="sec_img_alt[]" value="<?= htmlspecialchars($sec['img_alt'] ?? '') ?>" placeholder="Alt text">
                    </div>
                </div>
                <?php endforeach; ?>
                </div>

                <!-- Add Section button -->
                <button type="button" onclick="addSection()"
                        class="w-full border-2 border-dashed border-[#EE483D]/40 hover:border-[#EE483D] text-[#EE483D] font-bold py-4 rounded-2xl transition flex items-center justify-center gap-2 mt-2 hover:bg-red-50">
                    <i class="bi bi-plus-lg text-lg"></i> Add Section
                </button>
            </div>

            <!-- ── TAB: STATS ────────────────────────────────────────────── -->
            <div id="tab-stats" class="tab-content hidden">
                <div class="section-card">
                    <h2 class="text-base font-extrabold text-[#1e1e5c] mb-4"><i class="bi bi-bar-chart-fill mr-2 text-[#EE483D]"></i>Stats Bar (dark blue background)</h2>
                    <div class="grid-2">
                        <?php for ($i=1;$i<=4;$i++): ?>
                        <div class="item-box">
                            <p class="text-xs font-bold text-gray-500 mb-3 uppercase tracking-wider">Stat <?= $i ?></p>
                            <div class="mb-3">
                                <label>Value</label>
                                <input type="text" name="stat<?= $i ?>_val" value="<?= $v("stat{$i}_val") ?>" placeholder="500+">
                            </div>
                            <div>
                                <label>Label</label>
                                <input type="text" name="stat<?= $i ?>_label" value="<?= $v("stat{$i}_label") ?>" placeholder="Websites Delivered">
                            </div>
                        </div>
                        <?php endfor; ?>
                    </div>
                </div>
            </div>

            <!-- ── TAB: CARDS ────────────────────────────────────────────── -->
            <div id="tab-cards" class="tab-content hidden">
                <div class="section-card">
                    <h2 class="text-base font-extrabold text-[#1e1e5c] mb-4"><i class="bi bi-grid-3x3-gap-fill mr-2 text-[#EE483D]"></i>Cards Section (6 cards)</h2>
                    <div class="grid-2 mb-6">
                        <div>
                            <label>Section Heading</label>
                            <input type="text" name="cards_heading" value="<?= $v('cards_heading') ?>" placeholder="Our Web Design Services">
                        </div>
                        <div>
                            <label>Section Subtitle</label>
                            <input type="text" name="cards_subtitle" value="<?= $v('cards_subtitle') ?>" placeholder="New site, full redesign...">
                        </div>
                    </div>
                    <?php foreach ($cards_data as $ci => $card): ?>
                    <div class="item-box mb-3">
                        <p class="text-xs font-bold text-gray-500 mb-3 uppercase tracking-wider">Card <?= $ci+1 ?></p>
                        <div class="grid-2 mb-3">
                            <div>
                                <label>Icon <span class="text-xs text-gray-400 font-normal">(BS class or upload SVG)</span></label>
                                <?= iconField('card_icon[]', $card['icon'], $card['color'] ?: '#EE483D') ?>
                            </div>
                            <div>
                                <label>Title</label>
                                <input type="text" name="card_title[]" value="<?= htmlspecialchars($card['title']) ?>" placeholder="Card Title">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label>Icon Color (hex)</label>
                            <input type="text" name="card_color[]" value="<?= htmlspecialchars($card['color']) ?>" placeholder="#EE483D" style="width:10rem">
                        </div>
                        <div>
                            <label>Description</label>
                            <textarea name="card_desc[]" rows="2" placeholder="Card description..."><?= htmlspecialchars($card['desc']) ?></textarea>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- ── TAB: PROCESS ──────────────────────────────────────────── -->
            <div id="tab-process" class="tab-content hidden">
                <div class="section-card">
                    <h2 class="text-base font-extrabold text-[#1e1e5c] mb-4"><i class="bi bi-list-ol mr-2 text-[#EE483D]"></i>Process Section (5 steps)</h2>
                    <div class="mb-6">
                        <label>Section Heading</label>
                        <input type="text" name="process_heading" value="<?= $v('process_heading') ?>" placeholder="How We Build Every Website">
                    </div>
                    <?php foreach ($steps_data as $si => $step): ?>
                    <div class="item-box mb-3">
                        <p class="text-xs font-bold text-gray-500 mb-3 uppercase tracking-wider">Step <?= $si+1 ?></p>
                        <div class="grid-3 mb-3">
                            <div>
                                <label>Step Number</label>
                                <input type="text" name="step_num[]" value="<?= htmlspecialchars($step['num']) ?>" placeholder="01">
                            </div>
                            <div>
                                <label>Icon <span class="text-xs text-gray-400 font-normal">(BS class or SVG)</span></label>
                                <?= iconField('step_icon[]', $step['icon']) ?>
                            </div>
                            <div>
                                <label>Title</label>
                                <input type="text" name="step_title[]" value="<?= htmlspecialchars($step['title']) ?>" placeholder="Discovery Call">
                            </div>
                        </div>
                        <div class="mt-3">
                            <label>Description</label>
                            <textarea name="step_desc[]" rows="2" placeholder="Step description..."><?= htmlspecialchars($step['desc']) ?></textarea>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Save Button (sticky) -->
            <div class="sticky bottom-0 bg-white border-t border-gray-100 -mx-6 px-6 py-4 mt-4 flex items-center justify-between gap-4 shadow-lg">
                <a href="services.php" class="text-gray-500 hover:text-gray-700 text-sm font-medium transition">
                    <i class="bi bi-arrow-left mr-1"></i> Cancel
                </a>
                <button type="submit"
                        class="flex items-center gap-2 bg-[#EE483D] hover:bg-red-600 text-white font-bold px-8 py-3 rounded-xl transition shadow-lg shadow-red-200">
                    <i class="bi bi-check-lg text-lg"></i>
                    <?= $is_edit ? 'Update Service' : 'Save Service' ?>
                </button>
            </div>
        </form>
    </main>
</div>

<!-- ── Media Library Modal ──────────────────────────────────────────────── -->
<div class="media-overlay" id="mediaOverlay" onclick="mediaOverlayClick(event)">
  <div class="media-modal" onclick="event.stopPropagation()">

    <!-- Header -->
    <div class="media-header">
      <h3><i class="bi bi-images mr-2 text-[#EE483D]"></i>Media Library</h3>
      <button class="media-close" onclick="closeMedia()"><i class="bi bi-x-lg"></i></button>
    </div>

    <!-- Toolbar -->
    <div class="media-toolbar">
      <label class="media-upload-zone" id="mediaDropZone">
        <i class="bi bi-cloud-upload text-xl text-[#EE483D]"></i>
        <span id="mediaUploadLabel">Upload new image — click or drag & drop</span>
        <input type="file" id="mediaFileInput" accept="image/*" multiple onchange="mediaUploadFiles(this.files)">
      </label>
      <input type="text" class="media-search" id="mediaSearchInput" placeholder="Search files…" oninput="mediaFilter()">
    </div>
    <!-- Folder tabs -->
    <div id="mediaFolderTabs" style="display:flex;gap:.4rem;padding:.5rem 1.5rem;flex-wrap:wrap;border-bottom:1px solid #f0f0f5;background:#fafafa;"></div>

    <!-- Grid -->
    <div class="media-body">
      <div class="media-grid" id="mediaGrid">
        <div class="media-empty"><i class="bi bi-hourglass-split text-2xl block mb-2"></i>Loading…</div>
      </div>
    </div>

    <!-- Footer -->
    <div class="media-footer">
      <div class="media-selected-info" id="mediaSelectedInfo">No file selected</div>
      <button class="media-select-btn" id="mediaSelectBtn" disabled onclick="mediaConfirmSelect()">
        <i class="bi bi-check-lg mr-1"></i> Use Selected
      </button>
    </div>

  </div>
</div>

<script>
const tabs = ['meta','hero','content','stats','cards','process'];
function showTab(id) {
    tabs.forEach(t => {
        document.getElementById('tab-' + t).classList.add('hidden');
        document.getElementById('tab-btn-' + t).classList.remove('active');
        document.getElementById('tab-btn-' + t).classList.add('bg-white','text-gray-600');
        document.getElementById('tab-btn-' + t).classList.remove('text-white');
    });
    document.getElementById('tab-' + id).classList.remove('hidden');
    document.getElementById('tab-btn-' + id).classList.add('active');
    document.getElementById('tab-btn-' + id).classList.remove('bg-white','text-gray-600');
    document.getElementById('tab-btn-' + id).classList.add('text-white');
    localStorage.setItem('svc_tab', id);
}
// restore last tab
const lastTab = localStorage.getItem('svc_tab') || 'meta';
showTab(lastTab);

// ── Icon live preview (BS class or SVG path) ─────────────────────────────────
function updateIconPreview(uid, val, color) {
    const box = document.getElementById('prev_' + uid);
    if (!box) return;
    val = val.trim();
    if (!val) {
        box.innerHTML = '<i class="bi bi-image" style="color:#d1d5db"></i>';
    } else if (val.startsWith('bi-')) {
        box.innerHTML = '<i class="bi ' + val + '" style="color:' + (color||'#EE483D') + '"></i>';
    } else {
        box.innerHTML = '<img src="' + val + '" style="width:24px;height:24px;object-fit:contain" alt="">';
    }
}

// ── Image preview helpers ────────────────────────────────────────────────────
function showPreview(uid, src) {
    const box = document.getElementById('prev_' + uid);
    if (!box) return;
    box.style.display = 'block';
    let img = box.querySelector('img');
    if (!img) { img = document.createElement('img'); box.appendChild(img); }
    img.src = src;
    img.onerror = () => box.style.display = 'none';
}
function updatePreview(uid) {
    const val = document.getElementById(uid).value.trim();
    if (val) showPreview(uid, val);
    else { const b = document.getElementById('prev_' + uid); if(b) b.style.display='none'; }
}

// ── Media Library ────────────────────────────────────────────────────────────
let _mediaTargetUid   = null;
let _mediaTargetType  = 'img';
let _mediaSelected    = null;
let _mediaAllFiles    = [];
let _mediaActiveFolder = 'all';

function openMedia(uid, type) {
    _mediaTargetUid   = uid;
    _mediaTargetType  = type || 'img';
    _mediaSelected    = null;
    _mediaActiveFolder = 'all';
    document.getElementById('mediaOverlay').classList.add('open');
    document.getElementById('mediaSelectBtn').disabled = true;
    document.getElementById('mediaSelectedInfo').textContent = 'No file selected';
    document.getElementById('mediaSearchInput').value = '';
    document.getElementById('mediaFileInput').accept = type === 'svg' ? '.svg,image/svg+xml' : 'image/*';
    mediaLoadFiles();
}
function closeMedia() {
    document.getElementById('mediaOverlay').classList.remove('open');
    _mediaTargetUid = null; _mediaSelected = null;
}
function mediaOverlayClick(e) {
    if (e.target === document.getElementById('mediaOverlay')) closeMedia();
}

async function mediaLoadFiles() {
    const grid = document.getElementById('mediaGrid');
    grid.innerHTML = '<div class="media-empty"><i class="bi bi-hourglass-split text-2xl block mb-2"></i>Loading…</div>';
    try {
        const res  = await fetch('media-files.php');
        const data = await res.json();
        _mediaAllFiles = data.files || [];
        mediaRenderFolderTabs(data.folders || []);
        mediaFilter();
    } catch(e) {
        grid.innerHTML = '<div class="media-empty">Failed to load files</div>';
    }
}

function mediaRenderFolderTabs(folders) {
    const bar = document.getElementById('mediaFolderTabs');
    bar.innerHTML = '';
    const all = ['all', ...folders];
    all.forEach(f => {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'folder-tab' + (f === _mediaActiveFolder ? ' active' : '');
        btn.textContent = f === 'all' ? 'All' : f;
        btn.onclick = () => { _mediaActiveFolder = f; mediaFilter(); mediaRenderFolderTabs(folders); };
        bar.appendChild(btn);
    });
}

function mediaFilter() {
    const q = (document.getElementById('mediaSearchInput').value || '').toLowerCase();
    const filtered = _mediaAllFiles.filter(f => {
        const folderOk = _mediaActiveFolder === 'all' || f.folder === _mediaActiveFolder;
        const searchOk = !q || f.name.toLowerCase().includes(q) || f.folder.toLowerCase().includes(q);
        return folderOk && searchOk;
    });
    mediaRenderGrid(filtered);
}

function mediaRenderGrid(files) {
    const grid = document.getElementById('mediaGrid');
    if (!files.length) {
        grid.innerHTML = '<div class="media-empty"><i class="bi bi-inbox text-3xl block mb-2"></i>No images found.</div>';
        return;
    }
    grid.innerHTML = '';
    files.forEach(f => {
        const isSvg = f.name.endsWith('.svg');
        const thumb = isSvg
            ? `<div class="svg-thumb"><img src="${f.path}" style="width:48px;height:48px;object-fit:contain" alt=""></div>`
            : `<img src="${f.path}" alt="${f.name}" loading="lazy">`;
        const el = document.createElement('div');
        el.className = 'media-item';
        el.dataset.path = f.path;
        el.dataset.name = f.name;
        el.innerHTML = `
            ${thumb}
            <div class="media-item-folder">${f.folder}</div>
            <div class="media-name" title="${f.name}">${f.name}</div>
            <div class="media-check"><i class="bi bi-check"></i></div>
            ${f.deletable ? `<div class="media-del" title="Delete" onclick="mediaDelete(event,'${f.name}',this.closest('.media-item'))"><i class="bi bi-trash3"></i></div>` : ''}
        `;
        el.addEventListener('click', () => mediaSelectItem(el));
        grid.appendChild(el);
    });
}

function mediaSelectItem(el) {
    document.querySelectorAll('.media-item.selected').forEach(e => e.classList.remove('selected'));
    el.classList.add('selected');
    _mediaSelected = el.dataset.path;
    document.getElementById('mediaSelectBtn').disabled = false;
    document.getElementById('mediaSelectedInfo').textContent = el.dataset.name;
}

function mediaConfirmSelect() {
    if (!_mediaSelected || !_mediaTargetUid) return;
    const inp = document.getElementById(_mediaTargetUid);
    if (!inp) return;
    inp.value = _mediaSelected;
    if (_mediaTargetType === 'svg') {
        updateIconPreview(_mediaTargetUid, _mediaSelected, '');
    } else {
        showPreview(_mediaTargetUid, _mediaSelected);
    }
    closeMedia();
}

async function mediaUploadFiles(files) {
    if (!files.length) return;
    const label = document.getElementById('mediaUploadLabel');
    const grid  = document.getElementById('mediaGrid');
    grid.classList.add('media-uploading');
    for (const file of files) {
        label.textContent = 'Uploading ' + file.name + '…';
        const fd = new FormData();
        fd.append('file', file);
        fd.append('csrf_token', document.querySelector('[name=csrf_token]').value);
        try {
            const res  = await fetch('media-files.php', {method:'POST', body:fd});
            const data = await res.json();
            if (data.success) _mediaAllFiles.unshift(data.file);
            else alert(data.error || 'Upload failed');
        } catch(e) { alert('Upload error'); }
    }
    label.textContent = 'Upload new image — click or drag & drop';
    grid.classList.remove('media-uploading');
    _mediaActiveFolder = 'uploads';
    mediaFilter();
    document.getElementById('mediaFileInput').value = '';
}

async function mediaDelete(e, name, el) {
    e.stopPropagation();
    if (!confirm('Delete "' + name + '"?')) return;
    const fd = new FormData();
    fd.append('delete', name);
    fd.append('csrf_token', document.querySelector('[name=csrf_token]').value);
    try {
        const res  = await fetch('media-files.php', {method:'POST', body:fd});
        const data = await res.json();
        if (data.success) {
            el.remove();
            _mediaAllFiles = _mediaAllFiles.filter(f => f.name !== name);
            if (_mediaSelected && _mediaSelected.includes(name)) {
                _mediaSelected = null;
                document.getElementById('mediaSelectBtn').disabled = true;
                document.getElementById('mediaSelectedInfo').textContent = 'No file selected';
            }
        } else { alert(data.error); }
    } catch(e) {}
}

// drag-drop
const dropZone = document.getElementById('mediaDropZone');
dropZone.addEventListener('dragover',  e => { e.preventDefault(); dropZone.classList.add('drag'); });
dropZone.addEventListener('dragleave', () => dropZone.classList.remove('drag'));
dropZone.addEventListener('drop', e => {
    e.preventDefault(); dropZone.classList.remove('drag');
    mediaUploadFiles(e.dataTransfer.files);
});

// ── Category → Subcategory filtering ─────────────────────────────────────────
const catMap = <?= json_encode($cat_js_map) ?>;
const currentSubcat = <?= json_encode($svc['subcategory'] ?? '') ?>;

function syncBreadcrumb() {
    const sel = document.getElementById('sel_subcategory');
    const bc  = document.querySelector('[name="hero_breadcrumb"]');
    if (!bc || bc.dataset.manualEdit === '1') return;
    const opt = sel.options[sel.selectedIndex];
    if (opt && opt.value) bc.value = opt.textContent.trim();
}

function filterSubcategory(parentSlug) {
    const sel = document.getElementById('sel_subcategory');
    sel.innerHTML = '<option value="">— None / Select —</option>';
    const subs = catMap[parentSlug] || [];
    subs.forEach(s => {
        const opt = document.createElement('option');
        opt.value = s.slug;
        opt.textContent = s.name;
        if (s.slug === currentSubcat) opt.selected = true;
        sel.appendChild(opt);
    });
    sel.disabled = subs.length === 0;
    syncBreadcrumb();
}

// Init on page load
(function() {
    const catSel = document.getElementById('sel_category');
    const subSel = document.getElementById('sel_subcategory');
    const bc     = document.querySelector('[name="hero_breadcrumb"]');

    if (catSel.value) filterSubcategory(catSel.value);

    // Auto-fill on subcategory change
    subSel.addEventListener('change', syncBreadcrumb);

    // If user manually edits breadcrumb, stop auto-filling
    if (bc) bc.addEventListener('input', () => { bc.dataset.manualEdit = bc.value ? '1' : '0'; });
})();

// ── AI Import ────────────────────────────────────────────────────────────────
async function runImport() {
    const url = document.getElementById('import_url').value.trim();
    if (!url) { alert('URL daalo pehle.'); return; }

    const btn    = document.getElementById('import_btn');
    const btnTxt = document.getElementById('import_btn_txt');
    const status = document.getElementById('import_status');

    btn.disabled = true;
    btnTxt.textContent = 'Fetching…';
    status.textContent = 'URL se content fetch ho raha hai…';
    status.classList.remove('hidden');

    const timer = setTimeout(() => { status.textContent = 'Groq AI content likh raha hai…'; }, 5000);

    try {
        const fd = new FormData();
        fd.append('url', url);
        const res  = await fetch('import-fetch.php', { method: 'POST', body: fd });
        const data = await res.json();
        clearTimeout(timer);

        if (data.error) {
            status.textContent = '❌ ' + data.error;
            status.style.color = '#fca5a5';
            btn.disabled = false;
            btnTxt.textContent = 'Fill Fields';
            return;
        }

        console.log('Import debug:', data.debug);
        console.log('hero_img:', data.generated?.hero_img);
        console.log('sections:', data.generated?.sections?.map(s=>s.img));
        fillFields(data.generated);
        if (data.images && data.images.length) showImagePicker(data.images);
        status.textContent = '✅ Content aur images fill ho gaye! Review karo aur save karo.';
        status.style.color = '#86efac';
    } catch(e) {
        clearTimeout(timer);
        status.textContent = '❌ Network error: ' + e.message;
        status.style.color = '#fca5a5';
    }

    btn.disabled = false;
    btnTxt.textContent = 'Fill Fields';
}

// ── Dynamic Sections ─────────────────────────────────────────────────────────
let _secCounter = <?= count($sections_data) ?>;

function addSection(data) {
    const wrap = document.getElementById('sections_wrap');
    const idx  = wrap.querySelectorAll('.section-card').length;
    const imgLeft = (idx % 2 === 0);
    const layout  = imgLeft ? 'Image LEFT, Text RIGHT' : 'Text LEFT, Image RIGHT (gray bg)';
    const uid = 'sec_img_dyn_' + (++_secCounter);

    const d = data || {};
    const esc = s => (s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    const bulletsStr = Array.isArray(d.bullets) ? d.bullets.join('\n') : (d.bullets || '');

    const card = document.createElement('div');
    card.className = 'section-card';
    card.innerHTML = `
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-base font-extrabold text-[#1e1e5c]">
                <i class="bi bi-layout-text-window mr-2 text-[#EE483D]"></i>
                Section <span class="sec-num">${idx + 1}</span>
                <span class="text-xs text-gray-400 font-normal ml-2 sec-layout">${layout}</span>
            </h2>
            <button type="button" onclick="removeSection(this)"
                    class="text-xs text-red-400 hover:text-red-600 border border-red-200 hover:border-red-400 px-3 py-1 rounded-lg transition flex items-center gap-1">
                <i class="bi bi-trash"></i> Remove
            </button>
        </div>
        <div class="mb-4">
            <label>H2 Heading</label>
            <input type="text" name="sec_h2[]" value="${esc(d.h2)}" placeholder="Section heading">
        </div>
        <div class="mb-4">
            <label>Paragraph</label>
            <textarea name="sec_para[]" rows="5">${esc(d.para)}</textarea>
        </div>
        <div class="mb-4">
            <label>Bullet Points <span class="text-xs text-gray-400">(one per line)</span></label>
            <textarea name="sec_bullets[]" rows="5" placeholder="Bullet 1&#10;Bullet 2&#10;Bullet 3">${esc(bulletsStr)}</textarea>
        </div>
        <div class="mb-4">
            <label>Section Image</label>
            <div class="img-field">
                <input type="text" name="sec_img[]" id="${uid}" value="${esc(d.img)}"
                       placeholder="Section image" oninput="updatePreview('${uid}')">
                <button type="button" class="img-upload-btn" onclick="openMedia('${uid}', 'img')">
                    <i class="bi bi-images"></i> Media
                </button>
            </div>
            <div class="img-preview" id="prev_${uid}" ${d.img ? 'style="display:block"' : ''}>
                ${d.img ? '<img src="' + esc(d.img) + '" alt="preview" onerror="this.parentElement.style.display=\'none\'">' : ''}
            </div>
        </div>
        <div>
            <label>Image Alt Text</label>
            <input type="text" name="sec_img_alt[]" value="${esc(d.img_alt)}" placeholder="Alt text">
        </div>
    `;
    wrap.appendChild(card);
    renumberSections();
    card.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

function removeSection(btn) {
    const card = btn.closest('.section-card');
    if (!card) return;
    if (document.querySelectorAll('#sections_wrap .section-card').length <= 1) {
        alert('At least one section is required.');
        return;
    }
    card.remove();
    renumberSections();
}

function renumberSections() {
    document.querySelectorAll('#sections_wrap .section-card').forEach(function(card, idx) {
        const numEl    = card.querySelector('.sec-num');
        const layoutEl = card.querySelector('.sec-layout');
        if (numEl)    numEl.textContent = idx + 1;
        if (layoutEl) layoutEl.textContent = (idx % 2 === 0)
            ? 'Image LEFT, Text RIGHT'
            : 'Text LEFT, Image RIGHT (gray bg)';
    });
}

function fillFields(g) {
    const set = (name, val) => {
        const el = document.querySelector('[name="' + name + '"]');
        if (el && val !== undefined && val !== null) el.value = val;
    };
    const setTA = (name, val) => {
        const el = document.querySelector('textarea[name="' + name + '"]');
        if (el && val !== undefined && val !== null) el.value = val;
    };

    // Meta
    set('meta_title',  g.meta_title);
    setTA('meta_desc', g.meta_desc);
    if (g.meta_image) { set('meta_image', g.meta_image); const mi = document.querySelector('[name="meta_image"]'); if (mi && mi.id) showPreview(mi.id, g.meta_image); }

    // Hero
    set('hero_h1',    g.hero_h1);
    set('hero_badge', g.hero_badge);
    setTA('hero_desc', g.hero_desc);
    if (g.hero_img) { set('hero_img', g.hero_img); const hi = document.querySelector('[name="hero_img"]'); if (hi && hi.id) showPreview(hi.id, g.hero_img); }

    // Intro
    setTA('intro_text', g.intro_text);

    // Dynamic Sections
    const wrap = document.getElementById('sections_wrap');
    const srcSections = Array.isArray(g.sections) && g.sections.length
        ? g.sections
        : (function() {
            // Fallback: build from old s1/s2/s3 format
            const arr = [];
            ['s1','s2','s3'].forEach(function(s) {
                if (g[s+'_h2'] || g[s+'_para']) {
                    arr.push({ h2: g[s+'_h2']||'', para: g[s+'_para']||'', bullets: g[s+'_bullets']||[], img:'', img_alt:'' });
                }
            });
            return arr;
        })();

    if (srcSections.length > 0) {
        wrap.innerHTML = '';
        _secCounter = 0;
        srcSections.forEach(function(sec) { addSection(sec); });
        showTab('content');
    }

    // Stats
    for (var i = 1; i <= 4; i++) {
        set('stat' + i + '_val',   g['stat' + i + '_val']);
        set('stat' + i + '_label', g['stat' + i + '_label']);
    }

    // Cards heading
    set('cards_heading', g.cards_heading);

    // Cards (up to 6)
    if (Array.isArray(g.cards)) {
        const icons   = document.querySelectorAll('[name="card_icon[]"]');
        const titles  = document.querySelectorAll('[name="card_title[]"]');
        const descs   = document.querySelectorAll('textarea[name="card_desc[]"]');
        g.cards.forEach(function(card, i) {
            if (titles[i])  titles[i].value  = card.title || '';
            if (descs[i])   descs[i].value   = card.desc  || '';
            if (icons[i])   { icons[i].value = card.icon  || ''; updateIconPreview(icons[i].id, icons[i].value, '#EE483D'); }
        });
    }

    // Process heading
    set('process_heading', g.process_heading);

    // Steps (up to 5)
    if (Array.isArray(g.steps)) {
        const snums   = document.querySelectorAll('[name="step_num[]"]');
        const sicons  = document.querySelectorAll('[name="step_icon[]"]');
        const stitles = document.querySelectorAll('[name="step_title[]"]');
        const sdescs  = document.querySelectorAll('textarea[name="step_desc[]"]');
        g.steps.forEach(function(step, i) {
            if (snums[i])   snums[i].value   = step.num   || (i + 1);
            if (stitles[i]) stitles[i].value = step.title || '';
            if (sdescs[i])  sdescs[i].value  = step.desc  || '';
            if (sicons[i])  { sicons[i].value = step.icon || ''; updateIconPreview(sicons[i].id, sicons[i].value, '#EE483D'); }
        });
    }
}

function showImagePicker(images) {
    const grid = document.getElementById('img_grid');
    grid.innerHTML = '';

    // Build field list dynamically: hero + all current section img inputs + meta_image
    function getImgFields() {
        const fields = [{ val: 'hero_img', label: 'Hero', byName: true }];
        document.querySelectorAll('#sections_wrap .section-card').forEach(function(card, idx) {
            const inp = card.querySelector('[name="sec_img[]"]');
            if (inp) fields.push({ id: inp.id, label: 'Section ' + (idx + 1), byName: false });
        });
        fields.push({ val: 'meta_image', label: 'OG Image', byName: true });
        return fields;
    }

    images.forEach(function(img, i) {
        const fields = getImgFields();
        const opts = fields.map(function(f, fi) {
            return '<option value="' + fi + '">' + f.label + '</option>';
        }).join('');
        const div = document.createElement('div');
        div.className = 'rounded-xl overflow-hidden border-2 border-gray-100 hover:border-[#EE483D] transition group relative';
        div.innerHTML =
            '<img src="' + img.thumb + '" class="w-full object-cover" style="height:100px" loading="lazy">' +
            '<div class="p-2 bg-white">' +
              '<p class="text-xs text-gray-400 truncate mb-1.5">' + (img.photographer || '') + '</p>' +
              '<div class="flex gap-1.5">' +
                '<select id="img_target_' + i + '" class="flex-1 text-xs" style="height:30px!important;padding:0 .4rem!important;font-size:.7rem!important;border-radius:.5rem!important;">' + opts + '</select>' +
                '<button type="button" onclick="assignImage(' + i + ',\'' + img.full.replace(/\\/g,'\\\\').replace(/'/g,"\\'") + '\')" ' +
                  'class="flex-shrink-0 bg-[#EE483D] text-white text-xs font-bold px-2 rounded-lg hover:bg-red-600 transition" style="height:30px">Use</button>' +
              '</div>' +
            '</div>';
        grid.appendChild(div);
    });
    document.getElementById('img_picker').classList.remove('hidden');
    document.getElementById('img_picker').scrollIntoView({ behavior: 'smooth' });
}

// ── AI Rewrite & Save ────────────────────────────────────────────────────────
function toggleRewritePanel() {
    const panel   = document.getElementById('rewrite_panel');
    const chevron = document.getElementById('rewrite_chevron');
    if (!panel) return;
    const open = !panel.classList.contains('hidden');
    panel.classList.toggle('hidden', open);
    chevron.style.transform = open ? '' : 'rotate(180deg)';
}

function rwSetStep(id, state) {
    const el = document.getElementById(id);
    if (!el) return;
    el.className = el.className.replace(/rw-step-\w+/g, '');
    el.classList.add('rw-step', 'flex', 'items-center', 'gap-3', 'text-sm');
    if (state === 'active') el.classList.add('rw-step-active');
    if (state === 'done') {
        el.classList.add('rw-step-done');
        el.querySelector('.rw-step-icon').innerHTML = '<i class="bi bi-check-lg"></i>';
    }
}

async function runRewrite() {
    const url = (document.getElementById('rewrite_url')?.value || '').trim();
    if (!url) { alert('Competitor URL daalo.'); return; }

    const cat = document.getElementById('sel_category')?.value;
    const sub = document.getElementById('sel_subcategory')?.value;
    if (!cat || !sub) {
        alert('Pehle Category aur Subcategory select karo (Meta tab mein).');
        return;
    }

    if (!confirm('Yeh action existing content ko overwrite karega aur save karega. Continue karo?')) return;

    const btn    = document.getElementById('rewrite_btn');
    const btnTxt = document.getElementById('rewrite_btn_txt');
    const prog   = document.getElementById('rewrite_progress');
    const result = document.getElementById('rewrite_result');
    const form   = document.getElementById('rewrite_form');

    btn.disabled = true;
    btnTxt.textContent = 'Running…';
    form.classList.add('opacity-50', 'pointer-events-none');
    prog.classList.remove('hidden');
    result.classList.add('hidden');

    rwSetStep('rw_st_fetch', 'active');

    const t1 = setTimeout(() => rwSetStep('rw_st_ai',     'active'), 8000);
    const t2 = setTimeout(() => rwSetStep('rw_st_images', 'active'), 35000);
    const t3 = setTimeout(() => rwSetStep('rw_st_save',   'active'), 50000);

    try {
        const fd = new FormData();
        fd.append('csrf_token', document.querySelector('[name=csrf_token]').value);
        fd.append('url',         url);
        fd.append('category',    cat);
        fd.append('subcategory', sub);

        const res  = await fetch('import-save.php', { method: 'POST', body: fd });
        const text = await res.text();
        clearTimeout(t1); clearTimeout(t2); clearTimeout(t3);

        let data;
        try { data = JSON.parse(text); }
        catch(e) {
            showRewriteError('JSON parse error: ' + text.substring(0, 300));
            return;
        }

        if (data.error) { showRewriteError(data.error); return; }

        rwSetStep('rw_st_fetch',  'done');
        rwSetStep('rw_st_ai',     'done');
        rwSetStep('rw_st_images', 'done');
        rwSetStep('rw_st_save',   'done');

        result.className = 'mt-3 text-sm rounded-xl px-4 py-3 bg-green-50 border border-green-200 text-green-800';
        result.innerHTML = '<i class="bi bi-check-circle-fill mr-2"></i><strong>Done!</strong> Content rewrite aur save ho gaya. Refreshing…';
        result.classList.remove('hidden');

        setTimeout(() => {
            window.location = 'service-edit.php?id=' + data.service_id + '&imported=1';
        }, 2000);

    } catch(e) {
        clearTimeout(t1); clearTimeout(t2); clearTimeout(t3);
        showRewriteError('Network error: ' + e.message);
    }
}

function showRewriteError(msg) {
    const btn    = document.getElementById('rewrite_btn');
    const btnTxt = document.getElementById('rewrite_btn_txt');
    const form   = document.getElementById('rewrite_form');
    const result = document.getElementById('rewrite_result');
    btn.disabled = false;
    btnTxt.textContent = 'Rewrite & Save';
    form.classList.remove('opacity-50', 'pointer-events-none');
    result.className = 'mt-3 text-sm rounded-xl px-4 py-3 bg-red-50 border border-red-200 text-red-700';
    result.innerHTML = '<i class="bi bi-exclamation-circle-fill mr-2"></i>' + msg;
    result.classList.remove('hidden');
}

function assignImage(idx, fullUrl) {
    const fields = (function() {
        const f = [{ val: 'hero_img', byName: true }];
        document.querySelectorAll('#sections_wrap .section-card').forEach(function(card) {
            const inp = card.querySelector('[name="sec_img[]"]');
            if (inp) f.push({ id: inp.id, byName: false });
        });
        f.push({ val: 'meta_image', byName: true });
        return f;
    })();

    const fi = parseInt(document.getElementById('img_target_' + idx).value);
    const field = fields[fi];
    if (!field) return;

    const input = field.byName
        ? document.querySelector('[name="' + field.val + '"]')
        : document.getElementById(field.id);
    if (!input) return;
    input.value = fullUrl;
    const uid = input.id;
    if (uid) {
        const prev = document.getElementById('prev_' + uid);
        if (prev) {
            prev.style.display = 'block';
            const imgEl = prev.querySelector('img');
            if (imgEl) imgEl.src = fullUrl;
            else { prev.innerHTML = '<img src="' + fullUrl + '" alt="preview" style="max-height:120px;max-width:100%;border-radius:.625rem;border:1.5px solid #e5e7eb;object-fit:cover">'; }
        }
    }
}
</script>
</body>
</html>
