<?php
require_once __DIR__ . '/config.php';
require_login();

// Blog tags
$all_tags = [];
try { $all_tags = get_db() ? get_db()->query("SELECT id, name, slug FROM tags ORDER BY name")->fetchAll() : []; } catch(Exception $e) {}

$active_page = 'services';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Quick Import — Chulbul Admin</title>
<link rel="stylesheet" href="assets/admin.css">
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="bg-gray-100 min-h-screen">
<?php include __DIR__ . '/includes/sidebar.php'; ?>

<div class="lg:pl-60 flex flex-col min-h-screen">
    <header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center gap-4 sticky top-0 z-10">
        <button onclick="openSidebar()" class="lg:hidden text-gray-500"><i class="bi bi-list text-2xl"></i></button>
        <a href="services.php" class="text-gray-400 hover:text-gray-600"><i class="bi bi-arrow-left text-lg"></i></a>
        <i class="bi bi-stars text-xl text-[#EE483D]"></i>
        <h1 class="text-xl font-extrabold text-[#1e1e5c]">Quick Import & Publish</h1>
    </header>

    <main class="flex-1 p-6 max-w-2xl mx-auto w-full">

        <!-- How it works -->
        <div class="bg-[#1e1e5c] text-white rounded-2xl p-5 mb-6">
            <p class="font-bold text-sm mb-3"><i class="bi bi-lightbulb-fill text-amber-400 mr-2"></i>Kaise kaam karta hai?</p>
            <div class="flex flex-wrap gap-x-4 gap-y-2 text-xs text-white/70">
                <span class="flex items-center gap-1.5"><span class="w-5 h-5 rounded-full bg-white/20 flex items-center justify-center text-white font-bold text-xs flex-shrink-0">1</span> Topic/instructions ya source do</span>
                <span class="text-white/30">→</span>
                <span class="flex items-center gap-1.5"><span class="w-5 h-5 rounded-full bg-white/20 flex items-center justify-center text-white font-bold text-xs flex-shrink-0">2</span> (Optional) Image upload + keywords</span>
                <span class="text-white/30">→</span>
                <span class="flex items-center gap-1.5"><span class="w-5 h-5 rounded-full bg-white/20 flex items-center justify-center text-white font-bold text-xs flex-shrink-0">3</span> 3000+ word SEO post banao</span>
                <span class="text-white/30">→</span>
                <span class="flex items-center gap-1.5"><span class="w-5 h-5 rounded-full bg-white/20 flex items-center justify-center text-white font-bold text-xs flex-shrink-0">4</span> Post live ✓</span>
            </div>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-6" id="form_card">

            <!-- ── BLOG FIELDS ── -->
            <div id="post_fields">
                <div class="mb-5">
                    <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-2">
                        <i class="bi bi-tags text-[#49499A]"></i> Tags
                        <span class="text-gray-400 font-normal text-xs">(khali chhodo to AI auto-tag karega)</span>
                    </label>
                    <?php if (empty($all_tags)): ?>
                    <p class="text-xs text-gray-400">No tags yet. <a href="blog-tags.php" target="_blank" class="text-indigo-500">Create tags →</a></p>
                    <?php else: ?>
                    <div class="tag-multiselect-wrap" id="tag_ms_wrap">
                        <div class="tag-ms-box" id="tag_ms_box" onclick="toggleTagDropdown()">
                            <span class="tag-ms-placeholder" id="tag_ms_placeholder">Tags chunno (optional)…</span>
                            <div class="tag-ms-selected" id="tag_ms_selected"></div>
                            <i class="bi bi-chevron-down tag-ms-arrow" id="tag_ms_arrow"></i>
                        </div>
                        <div class="tag-ms-dropdown hidden" id="tag_ms_dropdown">
                            <div class="tag-ms-search-wrap">
                                <i class="bi bi-search"></i>
                                <input type="text" id="tag_ms_search" placeholder="Search tags…" oninput="filterTags(this.value)" onclick="event.stopPropagation()">
                            </div>
                            <div class="tag-ms-list" id="tag_ms_list">
                                <?php foreach ($all_tags as $tag): ?>
                                <label class="tag-ms-item" data-name="<?= strtolower(htmlspecialchars($tag['name'])) ?>">
                                    <input type="checkbox" name="tag_ids[]" value="<?= $tag['id'] ?>" class="tag-qi-cb" onchange="updateTagDisplay()">
                                    <span class="tag-ms-check"><i class="bi bi-check2"></i></span>
                                    <span>#<?= htmlspecialchars($tag['name']) ?></span>
                                </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <a href="blog-tags.php" target="_blank" class="text-xs text-indigo-500 flex items-center gap-1 mt-1.5">
                        <i class="bi bi-plus-circle"></i> Manage Tags
                    </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- ── Optional source content ── -->
            <div class="mb-4" id="paste_section">
                <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-1.5">
                    <i class="bi bi-clipboard-fill text-[#49499A]"></i> Source Content
                    <span class="text-gray-400 font-normal text-xs">(optional — HTML, article text ya notes)</span>
                </label>
                <div class="relative">
                    <textarea id="inp_paste_content" rows="4"
                        placeholder="Optional research material:&#10;• Existing article ka text/HTML&#10;• Apne notes, outline ya important facts&#10;• Blank chhod sakte ho — neeche sirf topic/instructions se bhi post banegi"
                        class="text-xs font-mono resize-y min-h-[80px]"
                        oninput="updatePasteInfo(this.value)"
                        style="border-radius:10px;border:1.5px solid #e5e7eb;padding:10px 12px;width:100%;background:#fafafa;color:#374151;"></textarea>
                    <div id="paste_info" class="text-xs text-gray-400 mt-1 hidden">
                        <span id="paste_type_badge" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold"></span>
                        <span id="paste_char_count"></span>
                    </div>
                </div>
            </div>

            <!-- ── Topic / Custom Instructions ── -->
            <div class="mb-4">
                <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-1.5">
                    <i class="bi bi-magic text-[#EE483D]"></i> Topic / AI Instructions
                    <span class="text-gray-400 font-normal text-xs">(source blank ho to required)</span>
                </label>
                <textarea id="inp_instructions" rows="4"
                    placeholder="Example: UX design mistakes that reduce website conversions par detailed post likho. Audience Indian business owners hai. Practical examples, comparison table aur website redesign consultation ka soft CTA add karo."
                    style="border-radius:10px;border:1.5px solid #e5e7eb;padding:10px 12px;width:100%;background:#fafafa;color:#374151;font-size:0.8rem;resize:vertical;min-height:72px;"></textarea>
                <p class="text-xs text-gray-400 mt-1">Sirf topic likhne par bhi AI original 3000+ word SEO post banayega. Source diya ho to use research material ki tarah use karega.</p>
            </div>

            <!-- ── SHARED: Featured Image (optional manual upload) ── -->
            <div class="mb-4">
                <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-1.5">
                    <i class="bi bi-image text-[#49499A]"></i> Featured Image
                    <span class="text-gray-400 font-normal text-xs">(optional — apni image upload karo)</span>
                </label>
                <div id="qi_fi_preview_wrap" class="mb-2 hidden">
                    <img id="qi_fi_preview" src="" alt="preview" style="width:160px;border-radius:8px;border:1px solid #eee">
                </div>
                <input type="file" id="qi_fi_file" accept="image/jpeg,image/png,image/webp,image/gif" class="hidden">
                <button type="button" onclick="document.getElementById('qi_fi_file').click()" id="qi_fi_btn"
                        class="text-sm font-semibold text-[#49499A] border border-[#49499A]/30 rounded-lg px-4 py-2 hover:bg-[#f0f0ff] transition flex items-center gap-2">
                    <i class="bi bi-upload"></i> Upload your own image
                </button>
                <span id="qi_fi_status" class="text-xs text-gray-400 ml-2"></span>
            </div>

            <!-- ── SHARED: Target Keywords (optional) ── -->
            <div class="mb-6">
                <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-1.5">
                    <i class="bi bi-bullseye text-[#EE483D]"></i> Target Keywords
                    <span class="text-gray-400 font-normal text-xs">(optional — AI inhe content mein focus karega)</span>
                </label>
                <input type="text" id="inp_keywords"
                       placeholder="e.g. web design Delhi, affordable website, small business website"
                       class="text-sm">
                <p class="text-xs text-gray-400 mt-1">Comma separated. Primary keyword pehle likhna.</p>
            </div>

            <button id="btn_go" onclick="runImport()"
                    class="w-full bg-[#EE483D] hover:bg-red-600 text-white font-bold py-3.5 rounded-xl transition flex items-center justify-center gap-2">
                <i class="bi bi-stars"></i> Generate &amp; Publish
            </button>
        </div>

        <!-- Progress -->
        <div id="progress_card" class="hidden bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-6">

            <!-- Header row -->
            <div class="flex items-center justify-between mb-4">
                <p class="text-sm font-bold text-[#1e1e5c]">🤖 AI kaam kar raha hai...</p>
                <span id="elapsed_timer" class="text-xs font-mono bg-gray-100 text-gray-500 px-2 py-1 rounded-lg">0:00</span>
            </div>

            <!-- Progress Bar -->
            <div class="bg-gray-100 rounded-full h-3 mb-1.5 overflow-hidden">
                <div id="prog_bar" class="h-full rounded-full transition-all duration-700 ease-out"
                     style="width:2%; background:linear-gradient(90deg,#EE483D,#6366f1)"></div>
            </div>
            <div class="flex justify-between text-xs text-gray-400 mb-4">
                <span id="prog_pct" class="font-bold text-[#EE483D]">0%</span>
                <span id="prog_eta">Calculating...</span>
            </div>

            <!-- Active AI Model Badge -->
            <div id="ai_model_box" class="bg-gradient-to-r from-[#f0f0ff] to-[#fff5f5] border border-indigo-100 rounded-xl px-4 py-3 mb-5 flex items-center gap-3">
                <div class="w-2.5 h-2.5 rounded-full bg-[#EE483D] flex-shrink-0" id="ai_pulse" style="animation:pulse 1s infinite"></div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Active AI Model</p>
                    <p id="ai_model_name" class="text-sm font-bold text-[#1e1e5c] mt-0.5">Starting...</p>
                </div>
            </div>

            <!-- Step indicators -->
            <div class="space-y-2.5" id="steps_list">
                <div id="st_fetch" class="flex items-center gap-3 text-sm text-gray-400">
                    <span class="step-icon w-6 h-6 rounded-full border-2 border-gray-200 flex items-center justify-center flex-shrink-0 text-xs"></span>
                    <span id="st_fetch_label">Topic/source analyze ho raha hai</span>
                </div>
                <div id="st_ai" class="flex items-center gap-3 text-sm text-gray-400">
                    <span class="step-icon w-6 h-6 rounded-full border-2 border-gray-200 flex items-center justify-center flex-shrink-0 text-xs"></span>
                    <span id="st_ai_label">AI content likh raha hai</span>
                </div>
                <div id="st_save" class="flex items-center gap-3 text-sm text-gray-400">
                    <span class="step-icon w-6 h-6 rounded-full border-2 border-gray-200 flex items-center justify-center flex-shrink-0 text-xs"></span>
                    <span id="st_save_label">Database mein save ho raha hai</span>
                </div>
            </div>

            <!-- Detail line -->
            <p id="prog_detail" class="text-xs text-gray-400 mt-4 text-center italic"></p>
        </div>

        <!-- Error -->
        <div id="error_box" class="hidden bg-red-50 border border-red-200 text-red-700 text-sm px-5 py-4 rounded-xl mb-6"></div>

    </main>
</div>

<style>
.step-active .step-icon  { border-color:#EE483D; background:#fff5f5; }
.step-active             { color:#1e1e5c; font-weight:600; }
.step-active .step-icon::after { content:''; display:block; width:8px; height:8px; border-radius:50%; background:#EE483D; margin:auto; animation:pulse 1s infinite; }
.step-done .step-icon    { border-color:#22c55e; background:#f0fdf4; color:#22c55e; }
.step-done               { color:#15803d; }
@keyframes pulse { 0%,100%{opacity:1} 50%{opacity:.4} }
@keyframes shimmer { 0%{background-position:-200% 0} 100%{background-position:200% 0} }
/* ── Tag Multiselect Dropdown ── */
.tag-multiselect-wrap { position:relative; }
.tag-ms-box {
    min-height: 42px; padding: 6px 36px 6px 10px; border: 1.5px solid #e2e8f0;
    border-radius: 10px; background: #fff; cursor: pointer; position: relative;
    display: flex; flex-wrap: wrap; gap: 5px; align-items: center;
    transition: border-color .2s;
}
.tag-ms-box:hover, .tag-ms-box.open { border-color: #49499A; }
.tag-ms-placeholder { font-size:.83rem; color:#94a3b8; pointer-events:none; }
.tag-ms-arrow {
    position:absolute; right:10px; top:50%; transform:translateY(-50%);
    color:#94a3b8; font-size:.75rem; transition:transform .2s; pointer-events:none;
}
.tag-ms-box.open .tag-ms-arrow { transform:translateY(-50%) rotate(180deg); }
.tag-ms-pill {
    display:inline-flex; align-items:center; gap:4px; padding:2px 8px;
    background:#ede9fe; color:#4c1d95; border-radius:99px; font-size:.72rem; font-weight:700;
}
.tag-ms-pill .rm { cursor:pointer; font-size:10px; opacity:.7; }
.tag-ms-pill .rm:hover { opacity:1; }
.tag-ms-dropdown {
    position:absolute; top:calc(100% + 4px); left:0; right:0; z-index:999;
    background:#fff; border:1.5px solid #e2e8f0; border-radius:12px;
    box-shadow:0 8px 24px rgba(0,0,0,.12); overflow:hidden;
}
.tag-ms-search-wrap {
    display:flex; align-items:center; gap:8px; padding:8px 12px;
    border-bottom:1px solid #f1f5f9; background:#f8fafc;
}
.tag-ms-search-wrap i { color:#94a3b8; font-size:.85rem; }
.tag-ms-search-wrap input {
    flex:1; border:none; outline:none; background:transparent;
    font-size:.83rem; color:#334155;
}
.tag-ms-list { max-height:210px; overflow-y:auto; padding:4px 0; }
.tag-ms-item {
    display:flex; align-items:center; gap:8px; padding:7px 14px;
    cursor:pointer; font-size:.83rem; color:#334155; transition:background .15s;
}
.tag-ms-item:hover { background:#f8fafc; }
.tag-ms-item input { display:none; }
.tag-ms-check {
    width:16px; height:16px; border-radius:4px; border:1.5px solid #cbd5e1;
    display:flex; align-items:center; justify-content:center; flex-shrink:0;
    font-size:10px; color:transparent; background:#fff; transition:all .15s;
}
.tag-ms-item input:checked ~ .tag-ms-check {
    background:#49499A; border-color:#49499A; color:#fff;
}
.tag-ms-item.hidden-item { display:none; }
</style>

<script>
let activeTab  = 'post';
let progressInterval = null;
let timerInterval    = null;
let startTime        = 0;

// ── Step → UI mapping ────────────────────────────────────────────────────────
const STEP_MAP = {
    'waiting'    : { step:'st_fetch',  pct_min: 0  },
    'url_fetch'  : { step:'st_fetch',  pct_min: 5  },
    'ai_start'   : { step:'st_ai',     pct_min: 18 },
    'ai_working' : { step:'st_ai',     pct_min: 22 },
    'ai_done'    : { step:'st_ai',     pct_min: 62 },
    'db_save'    : { step:'st_save',   pct_min: 88 },
    'done'       : { step:'st_save',   pct_min: 100 },
};
const STEP_ORDER = ['st_fetch', 'st_ai', 'st_save'];

function updateProgressUI(data) {
    const pct      = data.pct      || 0;
    const model    = data.ai_model || '';
    const detail   = data.detail   || '';
    const stepKey  = data.step     || 'waiting';
    const mapping  = STEP_MAP[stepKey] || STEP_MAP['waiting'];

    // Progress bar
    document.getElementById('prog_bar').style.width = Math.max(pct, 2) + '%';
    document.getElementById('prog_pct').textContent  = pct + '%';

    // AI model badge
    if (model && model !== 'Smart Router selecting model...') {
        document.getElementById('ai_model_name').textContent = model;
    }

    // Detail line
    if (detail) document.getElementById('prog_detail').textContent = detail;

    // ETA
    const elapsed = Math.floor((Date.now() - startTime) / 1000);
    if (pct > 5 && pct < 99) {
        const totalEst  = Math.floor(elapsed / pct * 100);
        const remaining = Math.max(0, totalEst - elapsed);
        document.getElementById('prog_eta').textContent = remaining > 0
            ? '~' + remaining + ' sec baki'
            : 'Almost done!';
    } else if (pct >= 99) {
        document.getElementById('prog_eta').textContent = '✓ Done!';
    }

    // Step state updates
    const currentStepEl = mapping.step;
    const currentIdx    = STEP_ORDER.indexOf(currentStepEl);
    STEP_ORDER.forEach((sid, idx) => {
        if (idx < currentIdx)      setStep(sid, 'done');
        else if (idx === currentIdx) setStep(sid, 'active');
    });

    // Update step label with model name
    if (stepKey === 'ai_working' && model) {
        document.getElementById('st_ai_label').textContent = '✍️ ' + model + ' likh raha hai...';
    }
}

function startPolling(key) {
    progressInterval = setInterval(async () => {
        try {
            const res  = await fetch('import-progress.php?key=' + key + '&t=' + Date.now());
            const data = await res.json();
            if (!res.ok || data.error) {
                stopPolling();
                showError(data.error || 'Progress check failed. Please retry.');
                return;
            }
            updateProgressUI(data);
        } catch(e) { /* network hiccup — ignore */ }
    }, 1500);
}

function startTimer() {
    startTime = Date.now();
    timerInterval = setInterval(() => {
        const s = Math.floor((Date.now() - startTime) / 1000);
        const m = Math.floor(s / 60);
        const sec = String(s % 60).padStart(2, '0');
        document.getElementById('elapsed_timer').textContent = m + ':' + sec;
    }, 1000);
}

function stopPolling() {
    if (progressInterval) { clearInterval(progressInterval); progressInterval = null; }
    if (timerInterval)    { clearInterval(timerInterval);    timerInterval    = null; }
}

function setStep(id, state) {
    const el = document.getElementById(id);
    el.className = el.className.replace(/step-\w+/g, '');
    if (state === 'active') { el.classList.add('flex','items-center','gap-3','text-sm','step-active'); }
    if (state === 'done')   {
        el.classList.add('flex','items-center','gap-3','text-sm','step-done');
        el.querySelector('.step-icon').innerHTML = '<i class="bi bi-check-lg"></i>';
    }
}

function updatePasteInfo(val) {
    const info   = document.getElementById('paste_info');
    const badge  = document.getElementById('paste_type_badge');
    const count  = document.getElementById('paste_char_count');
    if (!val || val.trim().length < 50) { info.classList.add('hidden'); return; }
    info.classList.remove('hidden');
    let type = 'Plain Text', color = 'bg-green-100 text-green-700';
    if (val.includes('__NEXT_DATA__'))          { type = '✨ Next.js HTML (best!)';  color = 'bg-purple-100 text-purple-700'; }
    else if (val.includes('<article') || val.includes('<body')) { type = '🌐 HTML Source'; color = 'bg-blue-100 text-blue-700'; }
    else if (val.includes('##') || val.includes('**')) { type = '📝 Markdown'; color = 'bg-yellow-100 text-yellow-700'; }
    badge.className = 'inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold ' + color;
    badge.textContent = type;
    count.textContent = ' — ' + val.trim().length.toLocaleString() + ' chars';
}

// ── Featured image manual upload (optional) ──────────────────────────────
window.qiManualImage = '';
(function(){
    var fi = document.getElementById('qi_fi_file');
    if (!fi) return;
    fi.addEventListener('change', async function(){
        var f = this.files[0]; if (!f) return;
        var btn = document.getElementById('qi_fi_btn');
        var st  = document.getElementById('qi_fi_status');
        btn.disabled = true; st.textContent = 'Uploading…'; st.className = 'text-xs text-gray-500 ml-2';
        var fd = new FormData(); fd.append('file', f); fd.append('csrf_token', '<?= csrf_token() ?>');
        try {
            var res = await fetch('upload-image.php', { method:'POST', body: fd });
            var data = await res.json();
            if (!data.success) throw new Error(data.error || 'failed');
            window.qiManualImage = data.url;
            document.getElementById('qi_fi_preview').src = data.url;
            document.getElementById('qi_fi_preview_wrap').classList.remove('hidden');
            st.textContent = '✅ Image set'; st.className = 'text-xs text-green-600 ml-2';
        } catch(e) {
            st.textContent = 'Error: ' + e.message; st.className = 'text-xs text-red-600 ml-2';
        } finally { btn.disabled = false; }
    });
})();

async function runImport() {
    const pasteContent = document.getElementById('inp_paste_content').value.trim();
    const keywords     = document.getElementById('inp_keywords').value.trim();
    const instructions = document.getElementById('inp_instructions').value.trim();
    if (!pasteContent && !instructions) {
        alert('Post ka topic/instructions ya source content dein!');
        return;
    }

    // Unique progress key for this import session
    const progressKey = 'pk' + Date.now().toString(36) + Math.random().toString(36).substr(2, 5);

    const fd = new FormData();
    fd.append('csrf_token',    '<?= csrf_token() ?>');
    fd.append('type',          'post');
    fd.append('progress_key',  progressKey);
    if (pasteContent)  fd.append('paste_content', pasteContent);
    if (keywords)     fd.append('target_keywords',    keywords);
    if (instructions) fd.append('custom_instructions', instructions);
    if (window.qiManualImage) fd.append('manual_image', window.qiManualImage);

    // Tags: manual picks → use those; else AI auto-tags
    const checkedTags = document.querySelectorAll('.tag-qi-cb:checked');
    if (checkedTags.length > 0) {
        checkedTags.forEach(cb => fd.append('tag_ids[]', cb.value));
    } else {
        fd.append('auto_tags', '1');
    }

    // Show progress UI
    document.getElementById('form_card').classList.add('hidden');
    document.getElementById('error_box').classList.add('hidden');
    document.getElementById('progress_card').classList.remove('hidden');
    document.getElementById('prog_bar').style.width = '2%';
    document.getElementById('ai_model_name').textContent = 'Starting...';
    setStep('st_fetch', 'active');
    startTimer();

    // Start polling for real-time updates (wait 2s before first poll)
    setTimeout(() => startPolling(progressKey), 2000);

    try {
        // ── STEP 1: Content generation ───────────────────────────────────────
        const res  = await fetch('import-save.php', { method: 'POST', body: fd });
        const text = await res.text();
        console.log('RAW RESPONSE:', text);
        let data;
        try {
            data = JSON.parse(text);
        } catch(pe) {
            stopPolling();
            const timeout = res.status === 408 || res.status === 504 || /request timeout|timed out/i.test(text);
            showError(timeout
                ? 'Server timeout hua. Article ko dobara try karein.'
                : 'Server se invalid response mila (HTTP ' + res.status + ').');
            return;
        }
        if (!res.ok && !data.error) data.error = 'Request failed (HTTP ' + res.status + ').';
        if (data.error) { stopPolling(); showError(data.error); return; }

        // ── ALL DONE ─────────────────────────────────────────────────────────
        stopPolling();
        STEP_ORDER.forEach(sid => setStep(sid, 'done'));
        document.getElementById('prog_bar').style.width      = '100%';
        document.getElementById('prog_pct').textContent      = '100%';
        document.getElementById('prog_eta').textContent      = '✓ Done!';
        document.getElementById('ai_model_name').textContent = '✅ Published!';
        document.getElementById('prog_detail').textContent   = 'Redirect ho raha hai...';

        setTimeout(() => { window.location = 'edit-post.php?id=' + data.post_id + '&imported=1'; }, 1500);

    } catch(e) {
        stopPolling();
        showError('Network error: ' + e.message);
    }
}

function showError(msg) {
    stopPolling();
    document.getElementById('progress_card').classList.add('hidden');
    document.getElementById('form_card').classList.remove('hidden');
    const box = document.getElementById('error_box');
    box.innerHTML = '<i class="bi bi-exclamation-circle-fill mr-2"></i>' + msg;
    box.classList.remove('hidden');
}

// ── Tag Multiselect Dropdown ────────────────────────────────────────────────
function toggleTagDropdown() {
    const dd  = document.getElementById('tag_ms_dropdown');
    const box = document.getElementById('tag_ms_box');
    const isOpen = !dd.classList.contains('hidden');
    dd.classList.toggle('hidden', isOpen);
    box.classList.toggle('open', !isOpen);
    if (!isOpen) document.getElementById('tag_ms_search').focus();
}

function updateTagDisplay() {
    const selected = document.getElementById('tag_ms_selected');
    const placeholder = document.getElementById('tag_ms_placeholder');
    const checked = document.querySelectorAll('.tag-qi-cb:checked');
    selected.innerHTML = '';
    if (checked.length === 0) {
        placeholder.style.display = '';
    } else {
        placeholder.style.display = 'none';
        checked.forEach(function(cb) {
            const label = cb.closest('label');
            const name  = label.querySelector('span:last-child').textContent;
            const pill  = document.createElement('span');
            pill.className = 'tag-ms-pill';
            pill.innerHTML = name + '<span class="rm" data-id="' + cb.value + '">✕</span>';
            pill.querySelector('.rm').addEventListener('click', function(e) {
                e.stopPropagation();
                cb.checked = false;
                updateTagDisplay();
            });
            selected.appendChild(pill);
        });
    }
}

function filterTags(q) {
    const lq = q.toLowerCase().trim();
    document.querySelectorAll('#tag_ms_list .tag-ms-item').forEach(function(item) {
        const name = item.dataset.name || '';
        item.classList.toggle('hidden-item', lq && name.indexOf(lq) === -1);
    });
}

// Close dropdown on outside click
document.addEventListener('click', function(e) {
    const wrap = document.getElementById('tag_ms_wrap');
    if (wrap && !wrap.contains(e.target)) {
        document.getElementById('tag_ms_dropdown')?.classList.add('hidden');
        document.getElementById('tag_ms_box')?.classList.remove('open');
    }
});
</script>
<script src="assets/admin.js"></script>
</body>
</html>
