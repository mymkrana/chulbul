<?php
ini_set('display_errors', '0');
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/content-tools.php';
require_login(true);
require_post_request(true);
verify_csrf(true);
// JSON endpoint: never let a PHP warning/notice leak into the response (it corrupts the
// JSON → "JSON parse error"). And if a fatal happens, return JSON with the real reason.
register_shutdown_function(function () {
    $e = error_get_last();
    if ($e && in_array($e['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR], true)) {
        cbd_log_error('import_save.fatal', $e['message'], ['line' => $e['line'], 'file' => basename($e['file'])]);
        if (!headers_sent()) {
            http_response_code(500);
            header('Content-Type: application/json; charset=UTF-8');
        }
        echo json_encode(['success' => false, 'error' => 'Server error while generating the post. Please retry.']);
    }
});
set_time_limit(300); // chunked generation for long articles needs more time
$REQUEST_START = microtime(true); // wall-clock guard — stop adding chunks before the server timeout

$type             = trim($_POST['type']             ?? 'service');
$category         = trim($_POST['category']         ?? '');
$subcategory      = trim($_POST['subcategory']      ?? '');
$url              = trim($_POST['url']              ?? '');
$force_id         = (int)($_POST['post_id']         ?? 0);
$tag_ids          = array_values(array_filter(array_map('intval', $_POST['tag_ids'] ?? [])));
$auto_tags        = !empty($_POST['auto_tags']);
$target_keywords  = trim($_POST['target_keywords'] ?? '');
$progress_key     = preg_replace('/[^a-z0-9_]/', '', strtolower($_POST['progress_key'] ?? ''));
$paste_content    = trim($_POST['paste_content']   ?? '');
$manual_image         = trim($_POST['manual_image']        ?? '');   // user-uploaded featured image (AI image gen removed)
$custom_instructions  = trim($_POST['custom_instructions'] ?? '');

// ── Progress writer — stores current step in a temp file ─────────────────────
function write_progress(string $step, string $detail, int $pct, string $ai_model = ''): void {
    global $progress_key;
    if (!$progress_key) return;
    $file = sys_get_temp_dir() . '/ci_' . $progress_key . '.json';
    file_put_contents($file, json_encode([
        'step'     => $step,
        'detail'   => $detail,
        'pct'      => $pct,
        'ai_model' => $ai_model,
        'ts'       => time(),
    ]));
}

// Blog posts can be generated from either source content, an editorial brief,
// or both. URL fetching remains disabled; pasted HTML/text is treated only as
// optional research material.
$type = 'post';
if ($paste_content === '' && $custom_instructions === '') {
    cbd_json_response(['success' => false, 'error' => 'Post ka topic/instructions ya source content dein.'], 422);
}

// ── Extract competitor image URLs BEFORE any HTML cleaning ────────────────────
// These are downloaded later and inserted into the published post.
$competitor_img_srcs = [];
if (preg_match_all('/<img\b[^>]+\bsrc=["\']([^"\']{12,})["\'][^>]*>/i', $paste_content, $_imx)) {
    foreach (array_unique($_imx[1]) as $_isrc) {
        if (strpos($_isrc, 'http') !== 0) continue;
        // Skip tiny icons, logos, tracking pixels, ads
        if (preg_match('/gravatar|avatar|1x1|pixel|tracking|\.gif(\?|$)|logo|icon|sprite|banner|ad[_\-\d]/i', $_isrc)) continue;
        $competitor_img_srcs[] = $_isrc;
        if (count($competitor_img_srcs) >= 8) break;
    }
}
unset($_imx, $_isrc);

$slug = '';

// ── Paste Content Parser ───────────────────────────────────────────────────────
/**
 * Parse pasted content — handles 4 formats:
 * 1. Next.js __NEXT_DATA__ HTML (Semrush, Vercel sites) → extract article JSON
 * 2. Raw HTML with <article> tag → extract article innerHTML
 * 3. Raw HTML (full body) → extract_blocks_from_html
 * 4. Plain text / Markdown → parse_jina_text
 */
/**
 * HTML → Clean semantic HTML
 * Removes JUNK (nav, scripts, styles, forms, CSS classes) but KEEPS
 * content tags: images, tables, headings, lists, paragraphs.
 * AI gets clean structure so images & tables survive.
 * Works for ANY website: articles, lists, comparison tables, release notes.
 */
function html_to_raw_text(string $html): string {
    // ── Step 1: Remove junk blocks entirely (with their content) ──────────────
    $junk_blocks = ['script','style','nav','header','footer','aside','form',
                    'noscript','iframe','svg','button','select','textarea','video','audio'];
    foreach ($junk_blocks as $tag) {
        $html = preg_replace('/<' . $tag . '\b[^>]*>[\s\S]*?<\/' . $tag . '>/i', '', $html);
        $html = preg_replace('/<' . $tag . '\b[^>]*\/?>/i', '', $html);
    }
    $html = preg_replace('/<!--[\s\S]*?-->/', '', $html);

    // Preserve visual section titles from card/timeline style source HTML.
    // Many design-heavy pages use <div class="card-title"> instead of a real
    // heading. Converting those labels before attributes/tags are stripped gives
    // the long-article chunker safe semantic boundaries to split on.
    $html = preg_replace_callback(
        '/<(?:div|p)\b[^>]*\bclass=["\'][^"\']*(?:card-title|timeline-title|update-title)[^"\']*["\'][^>]*>([\s\S]*?)<\/(?:div|p)>/i',
        static function (array $m): string {
            $title = trim(strip_tags($m[1]));
            return $title === '' ? '' : "\n<h2>" . htmlspecialchars($title, ENT_QUOTES | ENT_HTML5, 'UTF-8') . "</h2>\n";
        },
        $html
    );

    // ── Step 1b: Remove junk CONTAINERS by class/id keyword ───────────────────
    // (share bars, related posts, author box, comments, source TOC, breadcrumbs,
    //  sidebars, newsletter, post navigation, tags, view counters)
    $junk_kw = 'share|social|sharedaddy|dpsp|yarpp|related|author|comment|respond|'
             . 'breadcrumb|ez-toc|toc-|sidebar|widget|newsletter|subscribe|nav-single|'
             . 'post-navigation|navigation|byline|blogger|tptn|more-link';
    // run twice to catch lightly-nested same-type containers
    for ($i = 0; $i < 2; $i++) {
        $html = preg_replace(
            '/<(div|section|aside|ul|ol|nav|p|span)\b[^>]*\b(?:class|id)=["\'][^"\']*(?:' . $junk_kw . ')[^"\']*["\'][^>]*>[\s\S]*?<\/\1>/i',
            '', $html
        );
    }

    // ── Step 2: REMOVE images, keep tables/content inside <figure> ────────────
    $html = preg_replace('/<\/?figure\b[^>]*>/i', '', $html);
    $html = preg_replace('/<\/?picture\b[^>]*>/i', '', $html);
    $html = preg_replace('/<figcaption\b[^>]*>[\s\S]*?<\/figcaption>/i', '', $html);
    $html = preg_replace('/<source\b[^>]*\/?>/i', '', $html);
    $html = preg_replace('/<img\b[^>]*>/i', '', $html);

    // ── Step 3: UNWRAP all <a> links → keep text, DROP href ───────────────────
    // We never carry the source's links (they point to the competitor's site).
    // Our own internal links are added later by the shared content helper.
    $html = preg_replace('/<a\b[^>]*>([\s\S]*?)<\/a>/i', '$1', $html);
    $html = preg_replace('/<a\b[^>]*>/i', '', $html);   // orphan opening tags

    // ── Step 4: Strip attributes from remaining allowed tags ──────────────────
    $allowed = 'h1|h2|h3|h4|h5|h6|p|ul|ol|li|table|thead|tbody|tr|th|td|strong|b|em|i|blockquote|br';
    $html = preg_replace_callback('/<(' . $allowed . ')\b[^>]*>/i', function($m) {
        return '<' . strtolower($m[1]) . '>';
    }, $html);

    // ── Step 5: Strip everything that is NOT an allowed tag ───────────────────
    $keep = '<h1><h2><h3><h4><h5><h6><p><ul><ol><li><table><thead><tbody><tr><th><td><strong><b><em><i><blockquote><br>';
    $html = strip_tags($html, $keep);

    // ── Step 6: Clean whitespace + entities ───────────────────────────────────
    $html = preg_replace('/&nbsp;/i', ' ', $html);
    $html = html_entity_decode($html, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $html = preg_replace('/[ \t]+/', ' ', $html);

    // ── Step 7: Cut off trailing junk (everything after end-of-article markers) ─
    // WordPress blogs end the real article then add share/related/author/comments.
    $cut_markers = ['sharing is caring', 'related posts', 'related post:', 'leave a comment',
                    'leave a reply', 'cancel reply', 'post navigation', '(visited '];
    $low = mb_strtolower($html);
    $cut_at = mb_strlen($html);
    foreach ($cut_markers as $mk) {
        $pos = mb_strpos($low, $mk);
        if ($pos !== false && $pos < $cut_at && $pos > 500) $cut_at = $pos;
    }
    if ($cut_at < mb_strlen($html)) {
        // trim back to the last clean tag boundary before the marker
        $html = mb_substr($html, 0, $cut_at);
        $html = preg_replace('/<[^>]*$/', '', $html); // drop any half-open tag
    }

    // ── Step 8: Drop empty paragraphs / list items ────────────────────────────
    $html = preg_replace('/<(p|li)>\s*<\/\1>/i', '', $html);
    $html = preg_replace('/<li>\s*<\/li>/i', '', $html);
    $html = preg_replace('/(\s*\n\s*){3,}/', "\n\n", $html);
    $html = preg_replace('/>\s+</', ">\n<", $html);

    return trim($html);
}

/**
 * Smart paste content parser — Universal approach:
 * 1. If __NEXT_DATA__ JSON → extract clean article HTML
 * 2. If any HTML → convert to raw text (strip all tags/CSS)
 * 3. If plain text/markdown → use directly
 *
 * Returns raw text that AI can work with directly — no blocks parsing needed
 */
function parse_paste_content(string $paste): array {
    $empty = ['blocks'=>[], 'title'=>'', 'desc'=>'', 'h1'=>'', 'keywords'=>'', 'html'=>'', 'source'=>''];
    if (trim($paste) === '') return $empty;

    // ── Format 1: Next.js __NEXT_DATA__ (Semrush, Vercel sites) ─────────────
    if (strpos($paste, '__NEXT_DATA__') !== false) {
        preg_match('/<script[^>]*id=["\']__NEXT_DATA__["\'][^>]*>([\s\S]*?)<\/script>/i', $paste, $m);
        if (!empty($m[1])) {
            $nd = json_decode($m[1], true);
            $article_html = $nd['props']['pageProps']['page']['critical']['article-content']['content']
                ?? $nd['props']['pageProps']['article']['content']
                ?? $nd['props']['pageProps']['post']['content']
                ?? '';
            if ($article_html && strlen($article_html) > 300) {
                $title = $nd['props']['pageProps']['page']['title']
                      ?? $nd['props']['pageProps']['article']['title'] ?? '';
                $raw_text = html_to_raw_text($article_html);
                return array_merge($empty, [
                    'title'  => $title,
                    'html'   => $raw_text,   // raw text for AI
                    'source' => 'Paste:NextData',
                ]);
            }
        }
    }

    // ── Format 2 & 3: Any HTML (articles, lists, divs, full body) ────────────
    if (stripos($paste, '<') !== false && substr_count($paste, '>') > 5) {
        // Extract page title if present
        $title = '';
        if (preg_match('/<title[^>]*>(.*?)<\/title>/is', $paste, $tm))
            $title = trim(strip_tags($tm[1]));
        if (!$title && preg_match('/<h1[^>]*>(.*?)<\/h1>/is', $paste, $tm))
            $title = trim(strip_tags($tm[1]));
        if (!$title && preg_match('/property=["\']og:title["\'][^>]*content=["\']([^"\']+)/i', $paste, $tm))
            $title = trim($tm[1]);

        $raw_text = html_to_raw_text($paste);

        if (strlen($raw_text) > 200) {
            return array_merge($empty, [
                'title'  => $title,
                'html'   => $raw_text,
                'source' => 'Paste:HTML→Text',
            ]);
        }
    }

    // ── Format 4: Plain text / Markdown — use directly ───────────────────────
    $title = '';
    if (preg_match('/^#\s+(.+)/m', $paste, $tm)) $title = trim($tm[1]);
    if (preg_match('/^Title:\s*(.+)/m', $paste, $tm)) $title = trim($tm[1]);
    return array_merge($empty, [
        'title'  => $title,
        'html'   => $paste,
        'source' => 'Paste:Text',
    ]);
}

// ── 1. Parse optional pasted HTML/text ────────────────────────────────────────
write_progress('url_fetch', $paste_content !== '' ? 'Source content parse ho raha hai...' : 'Instructions analyze ho rahi hain...', 8);
$fetched = parse_paste_content($paste_content);

$html = $fetched['html'];
write_progress(
    'url_fetch',
    $paste_content !== ''
        ? 'Content mila (' . ($fetched['source'] ?: 'source') . ')! Extract ho raha hai...'
        : 'Topic brief mil gaya! SEO plan ban raha hai...',
    12
);

// ── 2. Content Extraction ─────────────────────────────────────────────────────
$service_name             = ucwords(str_replace('-', ' ', $slug));
$content_text             = '';
$page_title               = $fetched['title'];
$meta_desc                = $fetched['desc'];
$h1                       = $fetched['h1'];
$keywords                 = $fetched['keywords'];
$competitor_section_count = 4;
$blocks                   = $fetched['blocks'];

// ── Extract ALL headings from raw content (markdown or HTML) ─────────────────
// Even if body is cut, AI gets full article structure/outline
function extract_all_headings(string $raw): array {
    $headings = [];

    if (strpos($raw, '<h') !== false || strpos($raw, '<H') !== false) {
        // HTML source — extract h1/h2/h3/h4 tags
        preg_match_all('/<h([1-4])[^>]*>\s*(.*?)\s*<\/h\1>/is', $raw, $hm);
        foreach ($hm[2] as $i => $text) {
            $level = (int)$hm[1][$i];
            $text  = trim(strip_tags($text));
            if ($text && strlen($text) > 3 && strlen($text) < 200) {
                $headings[] = ['level' => $level, 'text' => $text];
            }
        }
    } else {
        // Markdown source — extract # lines
        foreach (preg_split('/\r?\n/', $raw) as $line) {
            if (preg_match('/^(#{1,4})\s+(.+)/', trim($line), $m)) {
                $text = trim(preg_replace('/\[([^\]]+)\]\([^)]+\)/', '$1', $m[2])); // strip links
                $text = trim(strip_tags($text));
                if ($text && strlen($text) > 3 && strlen($text) < 200) {
                    $headings[] = ['level' => strlen($m[1]), 'text' => $text];
                }
            }
        }
    }

    // Deduplicate + filter footer/CTA noise
    $footer_skip = ['contact', 'contact us', 'contact sales', 'get in touch', 'thank you',
        'request received', 'subscribe', 'newsletter', 'follow us', 'social media',
        'related guides', 'related posts', 'related articles', 'you may also like',
        'discord', 'community', 'footer', 'cookie', 'privacy policy', 'terms',
        'automation tests', 'sign up', 'get started', 'free trial'];
    $seen = []; $unique = [];
    foreach ($headings as $h) {
        $key = strtolower(trim($h['text']));
        if (isset($seen[$key])) continue;
        $skip = false;
        foreach ($footer_skip as $fs) {
            if (stripos($key, $fs) !== false) { $skip = true; break; }
        }
        if ($skip) continue;
        $seen[$key] = true;
        $unique[] = $h;
    }
    return $unique;
}

// ── Paste source: raw text already in $html — use directly, skip blocks logic ──
$is_paste = str_starts_with($fetched['source'] ?? '', 'Paste');
if ($is_paste && strlen($html) > 200) {
    // Count H2/H3 in raw markdown-ish text for section estimate
    preg_match_all('/^#{2,3}\s+/m', $html, $hm);
    $competitor_section_count = max(3, min(12, count($hm[0]) ?: 5));
    $content_text  = ($page_title ? "PAGE TITLE: {$page_title}\n\n" : '');
    $content_text .= $html;          // raw clean text — AI works on this directly
    $content_text  = mb_substr($content_text, 0, 18000);
    if ($page_title) $service_name = preg_replace('/[-|].*/u', '', $page_title);
}
// Build content_text from blocks (URL/markdown sources)
elseif ($blocks) {
    $section_blocks = array_values(array_filter($blocks, fn($b) =>
        !empty($b['heading']) && (count($b['paras']) > 0 || count($b['bullets']) > 0)));
    $competitor_section_count = max(3, min(10, count($section_blocks) ?: count($blocks)));

    $content_text = "PAGE TITLE: {$page_title}\nMETA DESC: {$meta_desc}\n";
    if (!empty($keywords)) $content_text .= "KEYWORDS: {$keywords}\n";
    if (!empty($h1))       $content_text .= "H1: {$h1}\n";

    // ── Prepend full heading structure so AI knows ALL sections ──────────────
    $all_headings = extract_all_headings($html);
    if (count($all_headings) > 0) {
        $content_text .= "\nARTICLE STRUCTURE (all headings — cover every section listed here):\n";
        foreach ($all_headings as $h) {
            $indent = str_repeat('  ', $h['level'] - 1);
            $prefix = str_repeat('#', $h['level']);
            $content_text .= "{$indent}{$prefix} {$h['text']}\n";
        }
    }

    $content_text .= "\nSECTION COUNT: {$competitor_section_count}\n\n--- CONTENT ---\n\n";

    foreach (array_slice($blocks, 0, 50) as $b) {
        if (mb_strlen($content_text) > 15000) break;
        if ($b['heading']) $content_text .= "## {$b['heading']}\n";
        foreach (array_slice($b['paras'], 0, 5) as $p) $content_text .= "$p\n";
        foreach (array_slice($b['bullets'], 0, 10) as $l) $content_text .= "- $l\n";
        $content_text .= "\n";
    }

    if ($html) {
        preg_match_all('/\b(\d+[\+\%]?\s*(?:years?|projects?|clients?|cities?)?)\b/i', $html, $m);
        $stats_raw = array_slice(array_values(array_unique(array_filter($m[0], fn($s) => strlen(trim($s)) > 1))), 0, 6);
        if ($stats_raw) $content_text .= "STATS: " . implode(', ', $stats_raw) . "\n";
    }
    $content_text = mb_substr($content_text, 0, 15000);
    if ($page_title) $service_name = preg_replace('/[-|].*/u', '', $page_title);
}

if ($paste_content !== '' && mb_strlen(strip_tags($content_text)) < 12 && $custom_instructions === '') {
    cbd_json_response(['success' => false, 'error' => 'Source/notes thode detail mein dein, ya Topic / AI Instructions bhi likhein.'], 422);
}
if ($paste_content === '' && mb_strlen($custom_instructions) < 12) {
    cbd_json_response(['success' => false, 'error' => 'Topic/instructions thodi detail mein likhein (kam se kam 12 characters).'], 422);
}

// ── Shared Functions ──────────────────────────────────────────────────────────
function groq_call(string $model, string $prompt, string $key, float $temperature = 0.85, int $max_tokens = 8192): array {
    $body = json_encode(['model'=>$model,'messages'=>[['role'=>'user','content'=>$prompt]],'temperature'=>$temperature,'max_tokens'=>$max_tokens]);
    $ch = curl_init('https://api.groq.com/openai/v1/chat/completions');
    curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER=>true,CURLOPT_POST=>true,CURLOPT_POSTFIELDS=>$body,CURLOPT_TIMEOUT=>60,CURLOPT_SSL_VERIFYPEER => true,CURLOPT_IPRESOLVE=>CURL_IPRESOLVE_V4,CURLOPT_HTTPHEADER=>['Content-Type: application/json','Authorization: Bearer '.$key]]);
    $resp = curl_exec($ch); $cerr = curl_error($ch); curl_close($ch);
    if ($cerr) return ['text'=>'','error'=>'curl: '.$cerr,'rate_limit'=>false];
    $d = json_decode($resp, true);
    $text = $d['choices'][0]['message']['content'] ?? '';
    $err  = $d['error']['message'] ?? '';
    return ['text'=>$text,'error'=>$err,'rate_limit'=>str_contains($err,'Rate limit')||str_contains($err,'rate_limit')];
}

// ── NVIDIA NIM call (OpenAI-compatible) ───────────────────────────────────────
function nvidia_call(string $model, string $api_key, string $prompt, int $max_tokens = 16384, float $temperature = 0.85): array {
    $body = json_encode([
        'model'       => $model,
        'messages'    => [['role' => 'user', 'content' => $prompt]],
        'temperature' => $temperature,
        'max_tokens'  => $max_tokens,
        'top_p'       => 0.95,
        'stream'      => false,
    ]);
    $ch = curl_init('https://integrate.api.nvidia.com/v1/chat/completions');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $body,
        CURLOPT_TIMEOUT        => 25, // Fast — if slow, next model try karega
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_IPRESOLVE      => CURL_IPRESOLVE_V4,
        CURLOPT_HTTPHEADER     => [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $api_key,
        ],
    ]);
    $resp = curl_exec($ch); $cerr = curl_error($ch); curl_close($ch);
    if ($cerr) return ['text' => '', 'error' => 'curl: ' . $cerr];
    $d    = json_decode($resp, true);
    $text = $d['choices'][0]['message']['content'] ?? '';
    $err  = $d['error']['message'] ?? '';
    return ['text' => $text, 'error' => $err];
}

// ── Token estimator (~4 chars = 1 token for English/HTML mixed content) ───────
function estimate_tokens(string $text): int {
    return (int)(mb_strlen(strip_tags($text)) / 4);
}

function dl_competitor_img(string $src, string $uploadDir, string $uploadBase): string {
    $remote = cbd_safe_remote_get($src, 8 * 1024 * 1024, 15, 3);
    if (!$remote['ok'] || strlen($remote['body'] ?? '') < 4000) return '';
    $data = $remote['body'];
    $mime = (new finfo(FILEINFO_MIME_TYPE))->buffer($data) ?: '';
    $exts = ['image/jpeg'=>'jpg', 'image/png'=>'png', 'image/webp'=>'webp', 'image/gif'=>'gif'];
    if (!isset($exts[$mime]) || @getimagesizefromstring($data) === false) return '';
    if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true)) return '';
    $fname = 'ci-' . substr(hash('sha256', $src), 0, 16) . '-' . bin2hex(random_bytes(4)) . '.' . $exts[$mime];
    if (!file_put_contents($uploadDir . $fname, $data, LOCK_EX)) return '';
    @chmod($uploadDir . $fname, 0644);
    return $uploadBase . $fname;
}


/**
 * Overlay bold title text + bottom gradient on a saved JPEG/PNG thumbnail.
 * Mimics the YouTube-thumbnail style: dark gradient bar at bottom, large
 * white bold text with a deep shadow — no external lib needed, just GD.
 */
function add_title_overlay(string $img_path, string $title): bool {
    if (!extension_loaded('gd')) return false;

    // ── Load image ─────────────────────────────────────────────────────────────
    $info = @getimagesize($img_path);
    if (!$info) return false;

    if ($info[2] === IMAGETYPE_JPEG)      $img = @imagecreatefromjpeg($img_path);
    elseif ($info[2] === IMAGETYPE_PNG)   $img = @imagecreatefrompng($img_path);
    else return false;

    if (!$img) return false;

    $w = imagesx($img);
    $h = imagesy($img);

    // ── Find font (bundled first, then system fallbacks) ────────────────────────
    $font_candidates = [
        __DIR__ . '/assets/fonts/Oswald-Bold.ttf',
        '/System/Library/Fonts/Supplemental/Impact.ttf',
        '/usr/share/fonts/truetype/msttcorefonts/Impact.ttf',
        '/usr/share/fonts/truetype/liberation/LiberationSans-Bold.ttf',
        '/usr/share/fonts/liberation/LiberationSans-Bold.ttf',
    ];
    $font = '';
    foreach ($font_candidates as $fc) {
        if (file_exists($fc)) { $font = $fc; break; }
    }
    if (!$font) { imagedestroy($img); return false; }

    // ── Wrap title into ≤2 lines ────────────────────────────────────────────────
    $title   = mb_strtoupper(trim($title)); // bold caps look great on thumbnails
    $words   = explode(' ', $title);
    $line1   = '';
    $line2   = '';
    $max     = 36; // chars per line — tighter = bigger font
    foreach ($words as $word) {
        $test = $line1 ? $line1 . ' ' . $word : $word;
        if (mb_strlen($test) <= $max || !$line1) {
            $line1 = $test;
        } else {
            $line2 .= ($line2 ? ' ' : '') . $word;
        }
    }
    // If line2 is still too long, truncate with ellipsis
    if (mb_strlen($line2) > $max + 10) {
        $line2 = mb_substr($line2, 0, $max) . '…';
    }
    $lines = array_values(array_filter([$line1, $line2]));

    // ── Auto-size font so longest line fits within 90% of width ────────────────
    $font_size = 62;
    $longest   = $lines[array_search(max(array_map('mb_strlen', $lines)), array_map('mb_strlen', $lines))];
    while ($font_size > 22) {
        $bbox = imagettfbbox($font_size, 0, $font, $longest);
        if (abs($bbox[2] - $bbox[0]) <= $w * 0.88) break;
        $font_size -= 2;
    }

    $line_gap  = (int)($font_size * 0.22);   // spacing between lines
    $line_h    = $font_size + $line_gap;
    $block_h   = count($lines) * $line_h + (int)($font_size * 0.4);  // total text block height

    // ── Bottom gradient overlay (dark → transparent going upward) ──────────────
    $grad_h    = (int)($block_h + $font_size * 1.6);   // extra breathing room above text
    $grad_y    = $h - $grad_h;

    imagefilledrectangle($img, 0, $grad_y, $w, $h,
        imagecolorallocatealpha($img, 0, 0, 0, 127)); // full transparent to start

    for ($y = $grad_y; $y < $h; $y++) {
        // Alpha 127=fully transparent → 0=fully opaque, mapped to gradient
        $progress = ($y - $grad_y) / $grad_h;          // 0.0 to 1.0
        $alpha    = (int)(127 * (1 - $progress * 0.92)); // 127 → ~10
        $col      = imagecolorallocatealpha($img, 0, 0, 0, $alpha);
        imageline($img, 0, $y, $w, $y, $col);
    }

    // ── Draw each line centered with shadow ─────────────────────────────────────
    $white  = imagecolorallocate($img, 255, 255, 255);
    $shadow = imagecolorallocate($img, 0, 0, 0);
    $accent = imagecolorallocate($img, 255, 210, 40);   // golden yellow — optional accent

    // Vertical start: bottom of image minus padding
    $y_base = $h - (int)($font_size * 0.55) - ($block_h - $font_size);
    if (count($lines) === 2) $y_base -= $line_h;

    foreach ($lines as $i => $line) {
        $bbox  = imagettfbbox($font_size, 0, $font, $line);
        $tw    = abs($bbox[2] - $bbox[0]);
        $x     = (int)(($w - $tw) / 2);
        $y     = $y_base + $i * $line_h;

        // 4-direction shadow for crisp outline effect
        foreach ([[-2,-2],[2,-2],[-2,2],[2,2],[0,-3],[0,3],[-3,0],[3,0]] as [$dx,$dy]) {
            imagettftext($img, $font_size, 0, $x + $dx, $y + $dy, $shadow, $font, $line);
        }

        // Last line in accent color (yellow), rest in white
        $color = ($i === count($lines) - 1 && count($lines) > 1) ? $accent : $white;
        imagettftext($img, $font_size, 0, $x, $y, $color, $font, $line);
    }

    // ── Save back as JPEG ────────────────────────────────────────────────────────
    $ok = imagejpeg($img, $img_path, 88);
    imagedestroy($img);
    return $ok;
}

function gemini_call(string $prompt, float $temperature = 0.85, int $max_tokens = 8192): array {
    global $REQUEST_START;
    $generation_config = [
        'temperature'     => $temperature,
        'topP'            => 0.90,
        'maxOutputTokens' => min(16384, max(1024, $max_tokens)),
    ];

    // Gemini's JSON mode prevents unescaped quotes or commentary from corrupting
    // post/meta responses. Content-only chunk prompts must remain plain HTML.
    if (stripos($prompt, 'valid json only') !== false || stripos($prompt, 'output only this json') !== false) {
        $generation_config['responseMimeType'] = 'application/json';
    }

    $body = json_encode([
        'contents'         => [['parts' => [['text' => $prompt]]]],
        'generationConfig' => $generation_config,
    ]);
    // Flash Lite is materially faster for long-form generation on the current
    // API account. Keep the larger Flash model as quality fallback instead of
    // making every article wait on it first.
    $models = ['gemini-3.5-flash-lite', 'gemini-3.6-flash', 'gemini-flash-latest'];
    $last_err = '';
    foreach ($models as $model) {
        $remaining = 50 - (microtime(true) - $REQUEST_START);
        if ($remaining < 8) {
            $last_err .= '[deadline] request time budget exhausted | ';
            break;
        }
        $url = 'https://generativelanguage.googleapis.com/v1beta/models/' . $model . ':generateContent?key=' . GEMINI_API_KEY;
        $ch  = curl_init($url);
        curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER=>true,CURLOPT_POST=>true,CURLOPT_POSTFIELDS=>$body,CURLOPT_TIMEOUT=>(int)max(8,min(45,$remaining)),CURLOPT_SSL_VERIFYPEER => true,CURLOPT_IPRESOLVE=>CURL_IPRESOLVE_V4,CURLOPT_HTTPHEADER=>['Content-Type: application/json','x-goog-api-key: '.GEMINI_API_KEY]]);
        $resp = curl_exec($ch); $cerr = curl_error($ch); curl_close($ch);
        if ($cerr) { $last_err .= "[$model] curl:$cerr | "; continue; }
        $d = json_decode($resp, true);
        // check api-level error
        if (!empty($d['error']['message'])) { $last_err .= "[$model] ".$d['error']['message']." | "; continue; }
        // try standard path
        $text = $d['candidates'][0]['content']['parts'][0]['text'] ?? '';
        if ($text) return ['text'=>$text,'error'=>''];
        // capture finish reason + raw for debug
        $finish = $d['candidates'][0]['finishReason'] ?? '';
        $last_err .= "[$model] empty text, finish=$finish, raw=".mb_substr($resp,0,200)." | ";
    }
    return ['text'=>'','error'=>rtrim($last_err,' |')];
}

/**
 * Run independent Gemini requests concurrently. Long blog imports use this for
 * meta + content chunks so the browser does not wait for 4-5 serial AI calls.
 */
function gemini_batch_call(array $requests): array {
    global $REQUEST_START;
    $multi   = curl_multi_init();
    $handles = [];
    $results = [];
    $model   = 'gemini-3.5-flash-lite';

    foreach ($requests as $i => $request) {
        $prompt      = (string)($request['prompt'] ?? '');
        $temperature = (float)($request['temperature'] ?? 0.85);
        $max_tokens  = min(16384, max(1024, (int)($request['max_tokens'] ?? 8192)));
        $config      = [
            'temperature'     => $temperature,
            'topP'            => 0.90,
            'maxOutputTokens' => $max_tokens,
        ];
        if (stripos($prompt, 'valid json only') !== false || stripos($prompt, 'output only this json') !== false) {
            $config['responseMimeType'] = 'application/json';
        }

        $body = json_encode([
            'contents'         => [['parts' => [['text' => $prompt]]]],
            'generationConfig' => $config,
        ]);
        $url = 'https://generativelanguage.googleapis.com/v1beta/models/' . $model . ':generateContent?key=' . GEMINI_API_KEY;
        $ch  = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $body,
            CURLOPT_TIMEOUT        => 45,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_IPRESOLVE      => CURL_IPRESOLVE_V4,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json', 'x-goog-api-key: ' . GEMINI_API_KEY],
        ]);
        curl_multi_add_handle($multi, $ch);
        $handles[$i] = $ch;
    }

    do {
        $status = curl_multi_exec($multi, $running);
        if ($running) {
            $selected = curl_multi_select($multi, 1.0);
            if ($selected === -1) usleep(10000);
        }
    } while ($running && $status === CURLM_OK);

    foreach ($handles as $i => $ch) {
        $resp = curl_multi_getcontent($ch);
        $cerr = curl_error($ch);
        $d    = json_decode($resp, true);
        $text = $d['candidates'][0]['content']['parts'][0]['text'] ?? '';
        $err  = $d['error']['message'] ?? $cerr;

        curl_multi_remove_handle($multi, $ch);
        curl_close($ch);

        // Retry a failed item through the normal Gemini model fallback chain.
        if ($text === '' && (microtime(true) - $REQUEST_START) < 38) {
            $fallback = gemini_call(
                (string)($requests[$i]['prompt'] ?? ''),
                (float)($requests[$i]['temperature'] ?? 0.85),
                (int)($requests[$i]['max_tokens'] ?? 8192)
            );
            $text = $fallback['text'];
            $err  = $fallback['error'] ?: $err;
        }
        $results[$i] = ['text' => $text, 'error' => $err];
    }
    curl_multi_close($multi);
    ksort($results);
    return $results;
}

/**
 * Smart AI Router — picks the best model based on estimated output token need.
 *
 * ROUTING LOGIC:
 *  ≤ 5,000 tokens → Groq 70B first (fastest, free) → NVIDIA fallback
 *  > 5,000 tokens → Skip Groq (too small), go straight to NVIDIA 16k models
 *
 * @param string $prompt
 * @param float  $temperature
 * @param int    $estimated_output_tokens  Estimated output size in tokens
 */
function sambanova_call(string $prompt, float $temperature = 0.85, int $max_tokens = 8192): array {
    $body = json_encode([
        'model'       => 'Meta-Llama-3.3-70B-Instruct',
        'messages'    => [['role' => 'user', 'content' => $prompt]],
        'temperature' => $temperature,
        'max_tokens'  => $max_tokens,
    ]);
    $ch = curl_init('https://api.sambanova.ai/v1/chat/completions');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $body,
        CURLOPT_TIMEOUT        => 20,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_IPRESOLVE      => CURL_IPRESOLVE_V4,
        CURLOPT_HTTPHEADER     => ['Content-Type: application/json', 'Authorization: Bearer ' . SAMBANOVA_API_KEY],
    ]);
    $resp = curl_exec($ch); $cerr = curl_error($ch); curl_close($ch);
    if ($cerr) return ['text' => '', 'error' => 'curl: ' . $cerr];
    $d    = json_decode($resp, true);
    $text = $d['choices'][0]['message']['content'] ?? '';
    $err  = $d['error']['message'] ?? '';
    return ['text' => $text, 'error' => $err];
}

function run_ai(string $prompt, float $temperature = 0.85, int $estimated_output_tokens = 3500, bool $track = false): string {
    global $last_error, $REQUEST_START;
    $last_error = '';

    // ── TIER 0: Gemini Flash — primary provider ─────────────────────────────────
    // Keep the currently working Gemini alias first so failed/restricted backup
    // providers do not delay normal post generation.
    if ($track) write_progress('ai_working', 'Content likh raha hai...', 25, 'Google Gemini Flash');
    $rg = gemini_call($prompt, $temperature, min(16384, $estimated_output_tokens + 1000));
    if ($rg['text']) return $rg['text'];
    $last_error = 'Gemini: ' . $rg['error'];
    if ((microtime(true) - $REQUEST_START) > 45) return '';

    // ── TIER 1: SambaNova fallback ───────────────────────────────────────────────
    if ($track) write_progress('ai_working', 'Content likh raha hai...', 25, 'SambaNova Llama-3.3-70B');
    $sn = sambanova_call($prompt, $temperature, min(16000, max($estimated_output_tokens + 500, 4000)));
    if ($sn['text']) return $sn['text'];
    $last_error .= ' | SambaNova: ' . $sn['error'];
    if ((microtime(true) - $REQUEST_START) > 48) return '';

    // ── TIER 2: Groq (only if output fits in 8k) ────────────────────────────────
    if ($estimated_output_tokens <= 5000) {
        $groq_max = min(8192, max($estimated_output_tokens + 1000, 4096));
        if ($track) write_progress('ai_working', 'Content likh raha hai...', 25, 'Groq llama-3.3-70b-versatile');

        $r = groq_call('llama-3.3-70b-versatile', $prompt, GROQ_API_KEY, $temperature, $groq_max);
        if ($r['text']) return $r['text'];
        if ($r['rate_limit']) {
            sleep(12);
            if ($track) write_progress('ai_working', 'Rate limit — retry ho raha hai...', 27, 'Groq llama-3.3-70b-versatile');
            $r2 = groq_call('llama-3.3-70b-versatile', $prompt, GROQ_API_KEY, $temperature, $groq_max);
            if ($r2['text']) return $r2['text'];
            $last_error = 'Groq 70B (retry): ' . $r2['error'];
        } else {
            $last_error = 'Groq 70B: ' . $r['error'];
        }

        if ($track) write_progress('ai_working', 'Backup model try kar raha hai...', 30, 'Groq llama-3.1-8b-instant');
        $r3 = groq_call('llama-3.1-8b-instant', $prompt, GROQ_API_KEY, $temperature, $groq_max);
        if ($r3['text']) return $r3['text'];
        $last_error .= ' | Groq 8B: ' . $r3['error'];
    } else {
        if ($track) write_progress('ai_working', 'Bada content — powerful model use ho raha hai...', 22, 'Smart Router');
        $last_error = '[Smart Router] Output ~' . $estimated_output_tokens . ' tokens → Groq skipped (limit too low)';
    }

    // ── TIER 3: NVIDIA NIM — ordered by quality + capacity ──────────────────────
    $nvidia_models = [
        ['deepseek-ai/deepseek-v4-flash',                      NVIDIA_API_KEY_4,  16384, 'DeepSeek v4 Flash'],
        ['deepseek-ai/deepseek-v4-flash',                      NVIDIA_API_KEY_5,  16384, 'DeepSeek v4 Flash (2)'],
        ['moonshotai/kimi-k2.6',                               NVIDIA_API_KEY_10, 16384, 'Kimi K2.6'],
        ['mistralai/mistral-medium-3.5-128b',                  NVIDIA_API_KEY_11, 16384, 'Mistral Medium 3.5-128B'],
        ['qwen/qwen3.5-397b-a17b',                             NVIDIA_API_KEY_7,  16384, 'Qwen 3.5 — 397B'],
        ['deepseek-ai/deepseek-v4-pro',                        NVIDIA_API_KEY,    16384, 'DeepSeek v4 Pro'],
        ['openai/gpt-oss-120b',                                NVIDIA_API_KEY_13,  4096, 'GPT OSS 120B'],
        ['mistralai/mistral-large-3-675b-instruct-2512',       NVIDIA_API_KEY_6,   2048, 'Mistral Large 675B'],
    ];

    foreach ($nvidia_models as [$model, $key, $max_tok, $label]) {
        if ($max_tok < $estimated_output_tokens) {
            $last_error .= " | $label: skipped (cap $max_tok < needed $estimated_output_tokens)";
            continue;
        }
        if ($track) write_progress('ai_working', 'Content likh raha hai...', 32, $label);
        $r = nvidia_call($model, $key, $prompt, $max_tok, $temperature);
        if ($r['text']) return $r['text'];
        $last_error .= " | $label: " . $r['error'];
    }

    // ── TIER 4: OpenRouter free models ───────────────────────────────────────────
    $or_models = [
        'deepseek/deepseek-chat-v3-0324:free',
        'mistralai/mistral-7b-instruct:free',
        'qwen/qwen-2.5-72b-instruct:free',
        'meta-llama/llama-3.3-70b-instruct:free',
        'microsoft/phi-4-reasoning-plus:free',
    ];
    foreach ($or_models as $or_model) {
        $or_label = explode('/', $or_model)[1] ?? $or_model;
        if ($track) write_progress('ai_working', 'OpenRouter try kar raha hai...', 42, $or_label);
        $or_body = json_encode(['model'=>$or_model,'messages'=>[['role'=>'user','content'=>$prompt]],'temperature'=>$temperature,'max_tokens'=>7000]);
        $ch4 = curl_init('https://openrouter.ai/api/v1/chat/completions');
        curl_setopt_array($ch4, [CURLOPT_RETURNTRANSFER=>true,CURLOPT_POST=>true,CURLOPT_POSTFIELDS=>$or_body,CURLOPT_TIMEOUT=>90,CURLOPT_SSL_VERIFYPEER => true,CURLOPT_IPRESOLVE=>CURL_IPRESOLVE_V4,CURLOPT_HTTPHEADER=>['Content-Type: application/json','Authorization: Bearer '.OPENROUTER_API_KEY,'HTTP-Referer: https://chulbuldesign.com']]);
        $resp4 = curl_exec($ch4); $cerr4 = curl_error($ch4); curl_close($ch4);
        if ($cerr4) { $last_error .= " | OR[$or_model] curl: $cerr4"; continue; }
        $d4 = json_decode($resp4, true);
        $t  = $d4['choices'][0]['message']['content'] ?? '';
        if ($t) return $t;
        $last_error .= " | OR[$or_model]: " . ($d4['error']['message'] ?? mb_substr($resp4, 0, 100));
    }

    return '';
}

function parse_ai_json(string $raw): array {
    $raw = preg_replace('/<think>[\s\S]*?<\/think>/i', '', $raw);
    $raw = preg_replace('/<reasoning>[\s\S]*?<\/reasoning>/i', '', $raw);
    $raw = preg_replace('/^```(?:json)?\s*/m', '', $raw);
    $raw = preg_replace('/```\s*$/m', '', $raw);
    preg_match('/\{[\s\S]*\}/m', $raw, $m);
    $json_str = $m[0] ?? '{}';

    // Try direct decode first
    $result = json_decode($json_str, true);
    if ($result) return $result;

    // JSON truncated — try to rescue individual fields via regex
    $rescued = [];
    $simple_fields = ['meta_title','meta_desc','title','slug','excerpt','read_time','pexels_query'];
    foreach ($simple_fields as $field) {
        if (preg_match('/"' . $field . '"\s*:\s*"((?:[^"\\\\]|\\\\.)*)"/s', $json_str, $fm)) {
            $rescued[$field] = json_decode('"' . $fm[1] . '"') ?? $fm[1];
        }
    }
    // Extract content field — everything between "content": " and the last </ul> or </p>
    if (preg_match('/"content"\s*:\s*"([\s\S]+?)(?:",\s*"|"\s*})/s', $json_str, $cm)) {
        $rescued['content'] = stripslashes($cm[1]);
    } elseif (preg_match('/"content"\s*:\s*"([\s\S]+)$/s', $json_str, $cm)) {
        // Truncated content — take what we have, close any open tags
        $partial = stripslashes($cm[1]);
        // Close last open tag if truncated
        if (!preg_match('/<\/(?:p|ul|li|h2)>\s*$/', $partial)) {
            foreach (['</li></ul>','</p>'] as $closer) {
                if (substr_count($partial, '<ul') > substr_count($partial, '</ul>')) { $partial .= '</li></ul>'; break; }
                if (substr_count($partial, '<p') > substr_count($partial, '</p>'))  { $partial .= '</p>'; break; }
            }
        }
        $rescued['content'] = $partial;
    }

    return $rescued ?: [];
}

/** Count readable words in generated HTML (Unicode-safe). */
function cbd_blog_word_count(string $html): int {
    $text = html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    preg_match_all("/[\\p{L}\\p{N}]+(?:[’'\\-][\\p{L}\\p{N}]+)*/u", $text, $matches);
    return count($matches[0] ?? []);
}

/** Insert depth-building sections before the closing brand paragraph/FAQ. */
function cbd_insert_blog_expansion(string $content, string $addition): string {
    $addition = trim($addition);
    if ($addition === '') return $content;

    $brand_faq = '/(<p\b[^>]*>[^<]*(?:<[^>]+>[^<]*)*Chulbul Design[\s\S]*?<\/p>\s*<h2\b[^>]*>\s*Frequently Asked Questions\s*<\/h2>)/i';
    if (preg_match($brand_faq, $content)) {
        return preg_replace($brand_faq, $addition . "\n$1", $content, 1) ?? ($content . "\n" . $addition);
    }

    $faq = '/(<h2\b[^>]*>\s*Frequently Asked Questions\s*<\/h2>)/i';
    if (preg_match($faq, $content)) {
        return preg_replace($faq, $addition . "\n$1", $content, 1) ?? ($content . "\n" . $addition);
    }
    return $content . "\n" . $addition;
}

/** Keep generated SEO fields clean and within practical display limits. */
function cbd_trim_seo_text(string $value, int $max): string {
    $value = trim(preg_replace('/\s+/u', ' ', html_entity_decode(strip_tags($value), ENT_QUOTES | ENT_HTML5, 'UTF-8')) ?? '');
    if (mb_strlen($value) <= $max) return $value;
    $value = rtrim(mb_substr($value, 0, $max + 1));
    $last_space = mb_strrpos($value, ' ');
    if ($last_space !== false && $last_space >= (int)($max * 0.72)) {
        $value = mb_substr($value, 0, $last_space);
    }
    return rtrim($value, " .,-|–—");
}

$last_error  = '';
$upload_dir  = dirname(__DIR__) . '/assets/images/uploads/';
$upload_base = '/assets/images/uploads/';

// ════════════════════════════════════════════════════════════════════════════
// BLOG POST BRANCH
// ════════════════════════════════════════════════════════════════════════════
if ($type === 'post') {
    write_progress('url_fetch', 'Topic aur content analyze ho gaya!', 15);

    // ── Full clean markdown for blog — no truncation ──────────────────────────
    // ── Determine best content source for AI ─────────────────────────────────
    $has_source = $paste_content !== '';
    $full_blog_content = $has_source
        ? ($fetched['html'] ?: ($content_text ?: $paste_content))
        : $custom_instructions;

    // Dynamic token estimate — HTML output is larger than plain text input
    // HTML tags add ~30% overhead, so output needs more tokens than input chars suggest
    $input_chars      = mb_strlen($full_blog_content);
    // A 3,200-4,000 word article normally needs about 5k-7k output tokens.
    // Asking one model for 11k-16k tokens made otherwise modest source imports
    // run long enough for LiteSpeed/shared-hosting request timeouts.
    $estimated_output = max(6000, min((int)($input_chars * 0.55), 9000));

    $assignment_intro = $has_source
        ? "I am giving you source material as RESEARCH. Learn its useful facts and sub-topics, then write a brand-new, original article in your own structure and words. Never copy sentences or unsupported promotional claims from the source."
        : "No source article was provided. Create a brand-new, original, expert-level article from the editor's topic/brief below. Build the search intent, outline, examples and explanations yourself. Do not invent statistics, named studies, client results, prices, dates or quotations; if a claim cannot be safely verified, explain the principle without a fake number.";

    $depth_rules = $has_source
        ? "• Cover every useful point and sub-topic in the source, but verify logic and drop source-site promotional junk.\n• Go deeper with practical examples, step-by-step guidance, common mistakes, decision criteria and implementation advice.\n• If the source is shorter than 3,000 words, expand it into a genuinely useful 3,200+ word guide. If it is longer, preserve its depth without padding or repetition.\n• Add missing sections that directly help the reader complete the task or make a buying decision."
        : "• Infer the reader's main search intent and answer it early.\n• Build a complete outline with at least 8 substantial H2 sections, supporting H3s, practical examples, step-by-step guidance, common mistakes, comparisons and decision criteria.\n• Write a genuinely useful 3,200+ word guide without padding, repeated ideas or generic filler.\n• Where the topic relates to websites, UX, ecommerce, apps, SEO or digital growth, connect advice naturally to business outcomes such as qualified leads, trust, usability and conversions.";
    $material_heading = $has_source
        ? 'SOURCE MATERIAL (cleaned text/HTML used only for research)'
        : 'EDITORIAL BRIEF (build the complete article from this instruction)';

    $custom_block = $custom_instructions
        ? "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\nSPECIAL INSTRUCTIONS FROM EDITOR (follow these exactly):\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n{$custom_instructions}"
        : '';

$blog_prompt = <<<PROMPT
You are an expert SEO strategist and conversion-focused content editor for "Chulbul Design" — a web design and development agency serving businesses in India and international markets.

$custom_block

$assignment_intro

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
DEPTH & PEOPLE-FIRST VALUE (NON-NEGOTIABLE):
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
$depth_rules
• MINIMUM LENGTH: The final article body must contain at least 3,000 readable words; target 3,200-4,000 words. Depth must come from useful detail, not repetition.
• Accuracy: Never fabricate research, statistics, quotes, awards, client names or case-study results. Use cautious wording when a current fact is not supplied.
• Search intent: Give the direct answer near the beginning, then cover evaluation, implementation and next steps comprehensively.
• Lists and tables: Use them only when they make scanning or comparison easier; explain important points in full paragraphs too.

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
ORIGINALITY & EXPERTISE:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
• Use your own structure, headings, examples and explanations. When source material exists, understand it first; never rewrite it sentence-by-sentence.
• Add practical value a generic article would miss: decision frameworks, realistic scenarios, implementation details and measurable outcomes.
• Use Indian or international business context only when it fits the editor's brief. Never force Gurugram or another location into a generic topic.
• Write every sentence fresh. The result must be useful as a standalone expert article, not a stitched or lightly rephrased source.

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
BRAND & SEO RULES:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
• Brand Voice: Remove competitor brand names and competitor calls-to-action. Never present a source company's claims as Chulbul Design's claims.
• Brand Neutrality (IMPORTANT): When the article compares two products/tools/services, stay NEUTRAL. Present each option's points factually. The brand "Chulbul Design" must NEVER declare a winner or say "we recommend X over Y". Just explain the trade-offs and let the reader decide.
• Conversion: Write for a reader who may need professional website design, development, UX, ecommerce, app or SEO help. Connect advice to leads, trust, usability, revenue or conversions only where genuinely relevant.
• Brand Mention: Mention "Chulbul Design" only ONCE, as a useful soft-consultation closing <p> immediately BEFORE the FAQ heading. Explain how the team can help with the topic and invite the reader to discuss the project—no hype, fake urgency or unsupported result. Never put the brand inside an FAQ answer.
• Primary query: Infer one clear primary search phrase from the brief/source when the editor did not provide keywords. Match the real search intent—informational, commercial investigation or transactional.
• On-page SEO: Use the primary phrase naturally in the title, meta title, meta description, slug, opening section and one relevant heading where it reads well. Use close semantic terms and entities throughout without repeating an exact phrase unnaturally.
• Opening: In the first 120 words, confirm what the article covers and give the reader a direct, useful answer. Do not start with history, filler or a generic digital-landscape statement.
• Internal-link readiness: Naturally use 2-4 relevant service phrases such as web design, web development, ecommerce website design, UX design, mobile app development or SEO services only when they fit. The publishing system will link matching real service pages automatically.
• Location discipline: Never add a city/country keyword unless it appears in the editor's brief, target keywords or source context.
• Tone: Write like an authoritative human expert — clear, conversational, direct. Opinions about the topic are fine; brand-as-judge is not.

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
NATURAL WRITING STYLE:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
• Mix sentence lengths wildly — "Wrong." then a 40-word multi-clause sentence
• Some paragraphs = 1 sentence. Others = 5 sentences. Never uniform.
• Raw opinions: "Honestly, this is where most teams fail.", "That's a mistake."
• Contractions: it's, you'll, we've, don't, that's, here's, isn't
• Em-dashes (—) and parentheses for mid-sentence thoughts
• Address reader as "you/your"

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
CRITICAL RULES & BANNED WORDS (instant failure if used):
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
"Furthermore" / "In conclusion" / "seamlessly" / "leverage" / "cutting-edge"
"dive into" / "game-changer" / "testament" / "delve" / "moreover" / "tapestry"
"In today's digital landscape" / "It is worth noting" / "plays a crucial role"
"innovative solutions" / "it all comes down to" / "at the end of the day"
H2/H3 headings: "Conclusion" / "Final Thoughts" / "Key Takeaways" / "Summary"

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
ANTI-SUMMARIZATION RULES (CRITICAL):
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
• NEVER compress or summarize any section. If the source has 4 paragraphs under an H4, write 4 paragraphs — not 1.
• Every stat, every brand example, every expert quote must appear — reworded, not removed.
• "Pro tip" boxes or callout boxes in source → recreate as <p><strong>Pro tip:</strong> ...</p>
• Every H4 subsection must have the same depth as the source — not a skeleton summary.
• If you are running out of tokens, continue from where you stopped in the next pass — do NOT compress earlier sections to fit.

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
IMAGE HANDLING:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
• Do not invent image URLs or output image tags. Featured/source images are handled separately by the publishing system.

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
HTML OUTPUT RULES:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Allowed tags: <h2> <h3> <h4> <p> <ul><li> <ol><li> <strong> <em> <table> <thead> <tbody> <tr> <th> <td> <pre> <code> <blockquote>
No <h1>. No <br>. No wrapper <div>. No inline styles.
CODE / EXAMPLES (CRITICAL — page breaks otherwise): Put EVERY code, JSX, HTML or markup example inside <pre><code> ... </code></pre>. Inside code you MUST escape < as &lt;, > as &gt;, and & as &amp;. NEVER output a raw tag (like <div>, <title>, <meta>, <input>, <button>, or <SomeComponent>) as article content — always escape it. Inline code (a function/prop/tag name) goes in <code>...</code>, also escaped.
NO LINKS: do NOT output any <a> tags or URLs at all — no external links, no source links. (Internal links are added automatically later.)
IGNORE SOURCE JUNK: never include share buttons, "Sharing is caring", "Related posts", author bios, comment sections, "Leave a comment", or visit counters — these are not article content.

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
CONTENT TYPE DETECTION (do this first):
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Before writing, analyze the source or brief:
• Brief/topic only → infer search intent and create a complete original long-form guide with at least 8 substantial H2 sections
• Single long article → write your OWN original, equally-detailed article covering all its points (your structure, your words — not a paragraph-by-paragraph reword)
• Multiple updates/release notes (separated by ---) → write ONE comprehensive article. CRITICAL RULES for this type:
    - EVERY single update in the source becomes its OWN <h2> section. If there are 8 updates, write 8 H2 sections.
    - The H2 must be the REAL update/feature name (e.g. "Figma Make", "Bulk Edit in Figma Buzz", "The Figma Agent") — never a keyword phrase.
    - Under each H2, write a FULL paragraph (4-6 sentences) plus a bullet list of that update's specific features — exactly as detailed as the source. Do NOT shrink an update to 1-2 lines.
    - If the source update has bullet points (features list), recreate ALL of them as <ul><li>.
    - Count the updates in the source first, then make sure your output has the same number of H2 sections.
• News items list → each item = one H2 section, full detail each
• Product features page → structure as guide with H2 per feature group

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
MANDATORY FAQ SECTION (the LAST thing in the article):
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
First write the single Chulbul Design closing <p> (see Brand Mention rule),
THEN end the article with: <h2>Frequently Asked Questions</h2>
Then 5-7 real questions a reader would Google about this topic, each as:
  <h3>Question?</h3><p>A genuine 2-3 sentence answer that actually answers the question.</p>
Rules:
- Every FAQ answer must really answer its question with useful info — NOT a brand pitch.
- NEVER put "Chulbul Design" or any closing CTA inside an FAQ answer.
- Questions natural and specific (great for Google's featured snippets).

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
$material_heading:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
$full_blog_content

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
SEO META REQUIRED (hard character limits):
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
• meta_title: 50-60 chars. Primary keyword first. No brand name.
• meta_desc: 140-155 chars. Compelling, keyword-included, soft CTA at end.
• title: specific, natural and intent-matched; include the primary topic without clickbait.
• excerpt: 150-160 chars. Punchy, human, no AI phrases.
• slug: lowercase, hyphens, 3-6 words max.
• read_time: e.g. "6 min read"
• pexels_query: 2-3 image search keywords

OUTPUT: Valid JSON only. No markdown fences. No text before or after. Ensure all internal double quotes inside the "content" HTML are properly escaped (\\") so the JSON stays perfectly valid.
{"meta_title":"","meta_desc":"","title":"","slug":"","excerpt":"","read_time":"X min read","content":"<h2>...</h2>...","pexels_query":"2-3 keywords"}
PROMPT;

    // Inject target keywords as hard numbered rules (not just appended — AI must see these early)
    if ($target_keywords) {
        $trimmed_kw = trim($target_keywords);

        // ── Detect format: numbered list (1. "keyword" ...) vs plain list ───────
        $is_numbered = (bool)preg_match('/^\s*\d+[\.\)]/m', $trimmed_kw);

        if ($is_numbered) {
            // ── Numbered format with placement instructions — pass directly to AI ──
            // e.g. 1. "Google ranking factors" (Primary) - Place in Title, H1...
            $kw_rule = "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
                . "\nTARGET KEYWORDS — FOLLOW EACH INSTRUCTION EXACTLY:"
                . "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n"
                . $trimmed_kw
                . "\n\nPlace every keyword ONLY where instructed above. No stuffing. Flow naturally.";
        } else {
            // ── Plain list format — weave naturally, do NOT force as H2 headings ──
            $kw_lines      = array_values(array_filter(array_map('trim', preg_split('/[\n,]+/', $trimmed_kw))));
            $primary_kw    = $kw_lines[0] ?? '';
            $secondary_kws = array_slice($kw_lines, 1);

            $kw_rule = "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
                . "\nKEYWORD ENFORCEMENT (weave naturally — DO NOT force as headings):"
                . "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
                . "\nPRIMARY KEYWORD: \"{$primary_kw}\""
                . "\n  → Use in: meta_title (start), meta_desc, slug, opening paragraph, and 2-3× naturally in body text.";

            if ($secondary_kws) {
                $kw_list = implode(', ', array_map(fn($kw) => "\"{$kw}\"", $secondary_kws));
                $kw_rule .= "\nSECONDARY KEYWORDS: {$kw_list}"
                    . "\n  → Sprinkle these naturally INSIDE paragraphs and meta_desc where they fit."
                    . "\n  → DO NOT make these into H2/H3 headings."
                    . "\n  → DO NOT write them word-for-word as section titles.";
            }

            $kw_rule .= "\n\nIMPORTANT: Your H2/H3 headings must describe the ACTUAL content "
                . "(e.g. real feature names, topics) — NOT the keywords above. "
                . "Keywords belong inside sentences, not as headings.";
        }

        // Insert keyword block before SEO RULES section
        $blog_prompt = str_replace(
            'SEO META REQUIRED',
            $kw_rule . "\n\nSEO META REQUIRED",
            $blog_prompt
        );
    }

    write_progress('ai_start', 'AI ko kaam de raha hai...', 20, 'Smart Router selecting model...');

    // ── Decide: single-shot vs chunked generation ────────────────────────────
    // Long sources can't be reproduced in full detail in one AI call (output
    // token limit → summarizing). So we chunk them and combine.
    $src_len  = mb_strlen($full_blog_content);
    $is_large = $src_len > 5000;    // chunk earlier so one request stays host-safe

    if (!$is_large) {
        // ── Single call (normal, for short/medium articles) ──────────────────
        $raw_text = run_ai($blog_prompt, 0.85, $estimated_output, true);
        if (!$raw_text) { cbd_json_response(['success' => false, 'error' => 'AI: Sabhi providers fail. ' . $last_error], 502); }
        $g = parse_ai_json($raw_text);
        if (!$g) {
            cbd_log_error('import_save.ai_parse', 'AI response could not be parsed', ['sample' => mb_substr($raw_text, 0, 300)]);
            cbd_json_response(['success' => false, 'error' => 'AI response parse nahi hua. Please retry.'], 502);
        }
    } else {
        // ── Chunked generation (long articles — full detail, no summarizing) ──
        $kw_note = $target_keywords ? "\nWeave these keywords naturally (no stuffing): {$target_keywords}\n" : '';
        $kw_note .= $custom_instructions ? "\nSPECIAL INSTRUCTIONS (follow exactly): {$custom_instructions}\n" : '';

        // Split at semantic heading boundaries. Design-heavy pasted pages have
        // their visual card titles normalised to H2 by html_to_raw_text().
        $parts  = preg_split('/(?=<h[2-4]\b|\n#{1,4}\s)/i', $full_blog_content);
        $chunks = []; $cur = '';
        foreach ($parts as $part) {
            if (mb_strlen($cur) + mb_strlen($part) > 2500 && $cur !== '') { $chunks[] = $cur; $cur = $part; }
            else { $cur .= $part; }
        }
        if (trim($cur) !== '') $chunks[] = $cur;

        // Cap at 4 chunks and merge any tail; requests run concurrently below.
        if (count($chunks) > 4) {
            $tail = implode('', array_slice($chunks, 3));
            $chunks = array_slice($chunks, 0, 3);
            $chunks[] = $tail;
        }
        $total_chunks = count($chunks);
        $target_words_per_chunk = max(850, (int)ceil(3400 / max(1, $total_chunks)));

        // 1) META call — small JSON only
        write_progress('ai_working', 'SEO meta ban raha hai...', 24, 'Meta');
        $meta_prompt = <<<MP
You are an SEO editor. From the article below, output ONLY this JSON (no other text, no markdown fences):
{"meta_title":"50-60 chars, primary keyword first, no brand name","meta_desc":"140-155 chars, compelling, soft CTA","title":"engaging article title","slug":"3-6 words lowercase hyphens","excerpt":"150-160 chars punchy","read_time":"X min read","pexels_query":"2-3 image keywords"}
{$kw_note}
ARTICLE:
{$full_blog_content}
MP;
        // 2) Build content prompts, then send meta + all chunks in parallel.
        $banned = '"Furthermore" / "In conclusion" / "seamlessly" / "leverage" / "cutting-edge" / "dive into" / "game-changer" / "testament" / "delve" / "moreover" / "tapestry"';
        $batch_requests = [[
            'prompt'      => $meta_prompt,
            'temperature' => 0.6,
            'max_tokens'  => 1600,
        ]];
        foreach ($chunks as $ci => $chunk) {
            $is_last  = ($ci === $total_chunks - 1);
            $ci_human = $ci + 1;

            $closing = $is_last
                ? "\nAFTER the final section: add ONE short closing <p> that mentions \"Chulbul Design\" exactly once as a soft CTA, then add <h2>Frequently Asked Questions</h2> followed by 3-4 <h3>Question?</h3><p>genuine answer</p> pairs (never put the brand inside an FAQ answer)."
                : "\nThis is a MIDDLE part of a bigger article — do NOT write any intro or conclusion. Just rewrite these sections and stop.";

            $chunk_prompt = <<<CP
You are an expert content writer for Chulbul Design (a premium web design agency). Your task is to take the article SECTION below as a RESEARCH BASE and write a far more detailed, comprehensive HTML version of it — in your own original words and structure.

RULES:
• Cover every point, fact, stat, and bullet from the source — do NOT skip anything.
• Go DEEPER: if the source has 3 sentences on a point, you write 2-3 full paragraphs. Add real examples, step-by-step breakdowns, expert tips, and common mistakes.
• Write MORE: this section should be at least 1.8× longer than the source section. Never match the source length — always exceed it with genuine depth.
• Expand every list: keep all source bullets AND add extra bullets with more context, examples, or related points.
• Add value the source missed: if an obvious subtopic or example is missing, include it.
• LENGTH TARGET: write at least {$target_words_per_chunk} useful readable words for this part. Do not use repetition or filler to reach the target.

OUTPUT: ONLY clean HTML using <h2> <h3> <h4> <p> <ul><li> <ol><li> <strong> <em> <table><thead><tbody><tr><th><td> <pre><code> <blockquote>. No <h1>, no markdown, no JSON, no code fences, no commentary before or after.
CODE (CRITICAL): wrap EVERY code/JSX/HTML example in <pre><code>...</code></pre> and escape < as &lt;, > as &gt;, & as &amp;. NEVER output a raw tag like <div>, <title>, <input>, or <Component> as content — it breaks the page layout.
NO LINKS: do NOT output any <a> tags or URLs. IGNORE any share buttons, "Related posts", author bios, or comment text from the source — those are not article content.
BANNED WORDS (never use): {$banned}
Write like a human expert — vary sentence length, use contractions, stay neutral (never say "we recommend X over Y").{$kw_note}{$closing}

SECTION (part {$ci_human} of {$total_chunks}) TO REWRITE IN FULL DETAIL:
{$chunk}
CP;
            $batch_requests[] = [
                'prompt'      => $chunk_prompt,
                'temperature' => 0.85,
                'max_tokens'  => 5000,
            ];
        }

        write_progress('ai_working', "Long article ke {$total_chunks} parts ek saath likh raha hai...", 30, 'Gemini 3.5 Flash Lite');
        $batch_results = gemini_batch_call($batch_requests);

        $meta_raw = $batch_results[0]['text'] ?? '';
        $g = parse_ai_json($meta_raw);
        if (!is_array($g)) $g = [];
        if (empty($g['title']))      $g['title']      = $page_title ?: 'Untitled';
        if (empty($g['meta_title'])) $g['meta_title'] = mb_substr($g['title'], 0, 60);
        if (empty($g['read_time']))  $g['read_time']  = '12 min read';

        $all_content = '';
        $failed_parts = [];
        foreach ($chunks as $ci => $_chunk) {
            $chunk_html = $batch_results[$ci + 1]['text'] ?? '';
            if (trim($chunk_html) === '') {
                $failed_parts[] = $ci + 1;
                continue;
            }
            // strip accidental code fences
            $chunk_html = preg_replace('/```(?:html)?/i', '', (string)$chunk_html);
            $all_content .= trim($chunk_html) . "\n";
        }

        if ($failed_parts) {
            cbd_json_response(['success' => false, 'error' => 'Gemini article part ' . implode(', ', $failed_parts) . ' generate nahi kar saka. Please retry.'], 502);
        }
        if (trim($all_content) === '') { cbd_json_response(['success' => false, 'error' => 'AI: content generation fail. ' . $last_error], 502); }
        $g['content'] = $all_content;
    }

    // ── Quality gate: every AI-created post must be deep enough to publish ────
    $minimum_words = 3000;
    $generated_html = (string)($g['content'] ?? '');
    $quality_attempt = 0;

    while ($quality_attempt < 2) {
        // Return a useful JSON quality error instead of letting the web server
        // replace the response with an HTML 504 timeout page.
        if ((microtime(true) - $REQUEST_START) > 48) break;
        $word_count = cbd_blog_word_count($generated_html);
        preg_match_all('/<h2\b/i', $generated_html, $h2_matches);
        $h2_count = count($h2_matches[0] ?? []);
        if ($word_count >= $minimum_words && $h2_count >= 6) break;

        $quality_attempt++;
        $missing_words = max(650, ($minimum_words - $word_count) + 250);
        $missing_words = min(2200, $missing_words);
        preg_match_all('/<h[23]\b[^>]*>(.*?)<\/h[23]>/is', $generated_html, $heading_matches);
        $existing_headings = implode(' | ', array_map(
            static fn(string $heading): string => trim(strip_tags($heading)),
            array_slice($heading_matches[1] ?? [], 0, 30)
        ));
        $brief_for_expansion = mb_substr($custom_instructions ?: $full_blog_content, 0, 5000);

        write_progress(
            'ai_working',
            "SEO quality check: {$word_count}/{$minimum_words} words — useful sections expand ho rahe hain...",
            68,
            'Google Gemini Flash'
        );

        $expansion_prompt = <<<EP
You are improving a long-form Chulbul Design blog article before publication.

TOPIC / EDITOR BRIEF:
{$brief_for_expansion}

TARGET KEYWORDS:
{$target_keywords}

SECTIONS ALREADY PRESENT:
{$existing_headings}

Create approximately {$missing_words} additional readable words as NEW, non-overlapping sections that add genuine practical value. Fill the most important gaps for the reader's search intent: examples, implementation steps, decision criteria, common mistakes, measurement, costs/factors, or a concise comparison where relevant.

Conversion goal: help a business owner understand the value of strong web design, development, UX, ecommerce, SEO or digital strategy where relevant, but do not turn the article into an advertisement. Do not mention Chulbul Design in these added sections.

Accuracy rules: do not invent statistics, studies, quotes, client names, prices, dates, certifications or results. Do not repeat an existing heading or idea. Do not add an introduction, conclusion, CTA or FAQ.

OUTPUT ONLY clean HTML using <h2> <h3> <h4> <p> <ul><li> <ol><li> <strong> <em> <table><thead><tbody><tr><th><td> <blockquote>. No markdown fences, no <h1>, no links, no wrapper div, and no commentary.
EP;
        $addition = run_ai(
            $expansion_prompt,
            0.75,
            min(10000, max(4500, (int)ceil($missing_words * 1.8))),
            false
        );
        if (trim($addition) === '') break;
        $addition = preg_replace('/```(?:html)?/i', '', $addition) ?? $addition;
        $generated_html = cbd_insert_blog_expansion($generated_html, $addition);
    }

    $final_word_count = cbd_blog_word_count($generated_html);
    preg_match_all('/<h2\b/i', $generated_html, $final_h2_matches);
    if ($final_word_count < $minimum_words || count($final_h2_matches[0] ?? []) < 6) {
        cbd_json_response([
            'success' => false,
            'error'   => "SEO quality check fail: post {$final_word_count} words ki bani. Minimum {$minimum_words} words aur proper sections ke bina save nahi ki gayi. Please retry.",
        ], 502);
    }

    $g['content'] = $generated_html;
    $g['title'] = cbd_trim_seo_text((string)($g['title'] ?? ''), 90);
    if ($g['title'] === '') {
        cbd_json_response(['success' => false, 'error' => 'AI ne valid post title generate nahi kiya. Please retry.'], 502);
    }
    $plain_summary = trim(preg_replace('/\s+/u', ' ', strip_tags($generated_html)) ?? '');
    $raw_meta_title = trim((string)($g['meta_title'] ?? ''));
    if (mb_strlen($raw_meta_title) < 35) $raw_meta_title = $g['title'];
    $g['meta_title'] = cbd_trim_seo_text($raw_meta_title, 60);
    $raw_meta_desc = trim((string)($g['meta_desc'] ?? ''));
    if (mb_strlen($raw_meta_desc) < 110) $raw_meta_desc = $plain_summary;
    $g['meta_desc'] = cbd_trim_seo_text($raw_meta_desc, 155);
    $raw_excerpt = trim((string)($g['excerpt'] ?? ''));
    if (mb_strlen($raw_excerpt) < 110) $raw_excerpt = $g['meta_desc'] ?: $plain_summary;
    $g['excerpt'] = cbd_trim_seo_text($raw_excerpt, 160);
    $g['read_time'] = max(1, (int)ceil($final_word_count / 220)) . ' min read';

    write_progress('ai_done', "SEO-ready content ({$final_word_count} words) ready! Save ho raha hai...", 75);

    $featured_img = '';

    // Build slug — editing mode mein existing slug rakho (conflict avoid)
    if ($force_id > 0) {
        $existing_slug_row = $pdo ? $pdo->prepare("SELECT slug FROM posts WHERE id=? LIMIT 1") : null;
        if ($existing_slug_row) { $existing_slug_row->execute([$force_id]); $post_slug = $existing_slug_row->fetchColumn() ?: ''; }
        if (!$post_slug) {
            $post_slug = preg_replace('/[^a-z0-9\-]/', '', strtolower($g['slug'] ?? ''));
            $post_slug = preg_replace('/-+/', '-', trim($post_slug, '-'));
        }
    } else {
        $post_slug = preg_replace('/[^a-z0-9\-]/', '', strtolower($g['slug'] ?? ''));
        if (!$post_slug) $post_slug = preg_replace('/[^a-z0-9\-]/', '', strtolower(str_replace(' ', '-', $g['title'] ?? 'post')));
        $post_slug = preg_replace('/-+/', '-', trim($post_slug, '-'));
    }
    $slug_parts = array_values(array_filter(explode('-', $post_slug)));
    if (count($slug_parts) > 6) $post_slug = implode('-', array_slice($slug_parts, 0, 6));

    // Sanitize first: escape any non-blog tag (e.g. <title>, <meta>, <input>, raw JSX)
    // so a stray tag in the content can never break the page layout.
    $content_html = cbd_sanitize_blog_html($g['content'] ?? '');

    // ── Download competitor images and inject into content ────────────────────
    if ($competitor_img_srcs) {
        $uploadDir  = dirname(__DIR__) . '/assets/images/uploads/';
        $uploadBase = '/assets/images/uploads/';
        $dl_imgs = [];
        write_progress('db_save', 'Source images download ho rahi hain...', 86);
        foreach ($competitor_img_srcs as $_csrc) {
            $loc = dl_competitor_img($_csrc, $uploadDir, $uploadBase);
            if ($loc) $dl_imgs[] = $loc;
            if (count($dl_imgs) >= 5) break;
        }
        if ($dl_imgs) {
            // First downloaded image → featured (if no manual upload)
            if ($manual_image === '') {
                $manual_image = array_shift($dl_imgs);
            }
            // Remaining: insert before alternate H2s in content (skip FAQ)
            if ($dl_imgs && preg_match_all('/<h2\b[^>]*>.*?<\/h2>/is', $content_html, $_hm2, PREG_OFFSET_CAPTURE)) {
                $_h2list = $_hm2[0];
                $_inserts = []; $_img_i = 0;
                for ($_hi = 1; $_hi < count($_h2list) && $_img_i < count($dl_imgs); $_hi += 2) {
                    if (stripos($_h2list[$_hi][0], 'frequently asked') !== false) continue;
                    $_inserts[$_h2list[$_hi][1]] = '<img src="' . htmlspecialchars($dl_imgs[$_img_i]) . '" alt="" class="w-full rounded-xl my-6 shadow-sm" loading="lazy">' . "\n";
                    $_img_i++;
                }
                krsort($_inserts);
                foreach ($_inserts as $_off => $_tag) {
                    $content_html = substr($content_html, 0, $_off) . $_tag . substr($content_html, $_off);
                }
                unset($_hm2, $_h2list, $_inserts, $_img_i, $_hi, $_off, $_tag);
            }
        }
    }

    // Prepend the user-uploaded or first-downloaded featured image (if any).
    if ($manual_image !== '') {
        $mi = preg_replace('#^https?://[^/]+#', '', $manual_image);
        if (strpos($mi, '/assets/') !== false) $mi = substr($mi, strpos($mi, '/assets/'));
        $content_html = '<img src="' . htmlspecialchars($mi) . '" alt="' . htmlspecialchars($g['title'] ?? '') . '" class="w-full rounded-2xl shadow-sm mb-8" loading="lazy">' . "\n" . $content_html;
    }

    // DB save
    $pdo = get_db();
    if (!$pdo) { cbd_json_response(['success' => false, 'error' => 'DB connection failed.'], 503); }

    // Inject internal service links from DB
    $content_html = cbd_inject_service_links($content_html, $pdo);

    // If editing existing post by ID, always update that row
    if ($force_id > 0) {
        $existing_id = $force_id;
    } else {
        $existing = $pdo->prepare("SELECT id FROM posts WHERE slug=? LIMIT 1");
        $existing->execute([$post_slug]);
        $existing_id = $existing->fetchColumn();
    }

    try {
        if ($existing_id) {
            $pdo->prepare("UPDATE posts SET slug=?,title=?,meta_title=?,meta_desc=?,date=?,read_time=?,excerpt=?,content=?,status='published' WHERE id=?")->execute([
                $post_slug, $g['title']??'', $g['meta_title']??'', $g['meta_desc']??'',
                date('Y-m-d'), $g['read_time']??'5 min read', $g['excerpt']??'', $content_html, $existing_id,
            ]);
            $post_id = $existing_id;
        } else {
            $pdo->prepare("INSERT INTO posts (slug,title,meta_title,meta_desc,date,read_time,excerpt,content,status) VALUES (?,?,?,?,?,?,?,?,'published')")->execute([
                $post_slug, $g['title']??'', $g['meta_title']??'', $g['meta_desc']??'',
                date('Y-m-d'), $g['read_time']??'5 min read', $g['excerpt']??'', $content_html,
            ]);
            $post_id = (int)$pdo->lastInsertId();
        }
    } catch (PDOException $e) {
        cbd_log_error('import_save.database', $e->getMessage());
        cbd_json_response(['success' => false, 'error' => 'Post database mein save nahi ho saki. Please retry.'], 500);
    }

    // ── Tags handling ──────────────────────────────────────────────────────────
    $saved_tag_ids = [];

    // Skip AI auto-tagging if we're already near the server's time limit. The post is
    // already saved by now — tags are optional, and a 2nd AI call here can blow the timeout.
    if ($auto_tags && (microtime(true) - $REQUEST_START) > 35) {
        $auto_tags = false;
    }
    if ($auto_tags) {
        // AI auto-tag: fetch available tags, ask AI to pick relevant ones
        try {
            $avail_tags = $pdo->query("SELECT id, name, slug FROM tags ORDER BY name")->fetchAll();
            if ($avail_tags) {
                $tag_list_str = implode(', ', array_map(fn($t) => $t['name'] . ' (id:' . $t['id'] . ')', $avail_tags));
                $post_title   = $g['title'] ?? '';
                $post_excerpt = mb_substr(strip_tags($g['content'] ?? ''), 0, 400);

                $tag_prompt = "You are a blog post tagger. Based on the post title and content excerpt below, choose the most relevant tags from the available list.\n\nPost Title: {$post_title}\nContent: {$post_excerpt}\n\nAvailable Tags: {$tag_list_str}\n\nReturn ONLY a JSON array of tag IDs (integers) that are relevant. Maximum 3 tags. Example: [1,5,8]\nIf no tags are relevant, return: []";

                $tag_raw = run_ai($tag_prompt, 0.3);
                // Extract JSON array from response
                if (preg_match('/\[[\d,\s]*\]/', $tag_raw, $tm)) {
                    $ai_tag_ids = json_decode($tm[0], true);
                    if (is_array($ai_tag_ids)) {
                        // Validate — only keep IDs that exist in available tags
                        $valid_ids = array_column($avail_tags, 'id');
                        $saved_tag_ids = array_values(array_filter($ai_tag_ids, fn($id) => in_array((int)$id, $valid_ids)));
                        $saved_tag_ids = array_map('intval', array_slice($saved_tag_ids, 0, 3));
                    }
                }
                // Save to post_tags
                if ($saved_tag_ids) {
                    $pdo->prepare('DELETE FROM post_tags WHERE post_id = ?')->execute([$post_id]);
                    $ti = $pdo->prepare('INSERT IGNORE INTO post_tags (post_id, tag_id) VALUES (?, ?)');
                    foreach ($saved_tag_ids as $tid) $ti->execute([$post_id, $tid]);
                }
            }
        } catch (Exception $_te) { /* tags table missing — skip */ }

    } elseif ($tag_ids) {
        // Manual tag_ids sent from quick-import
        try {
            $pdo->prepare('DELETE FROM post_tags WHERE post_id = ?')->execute([$post_id]);
            $ti = $pdo->prepare('INSERT IGNORE INTO post_tags (post_id, tag_id) VALUES (?, ?)');
            foreach ($tag_ids as $tid) $ti->execute([$post_id, $tid]);
            $saved_tag_ids = $tag_ids;
        } catch (Exception $_te) { /* skip */ }
    }

    // Fire social media webhook
    try {
        $trigger_tag_names = [];
        if ($saved_tag_ids && $pdo) {
            $in = implode(',', array_fill(0, count($saved_tag_ids), '?'));
            $ts = $pdo->prepare("SELECT name FROM tags WHERE id IN ($in)");
            $ts->execute(array_values($saved_tag_ids));
            $trigger_tag_names = $ts->fetchAll(PDO::FETCH_COLUMN);
        }
        trigger_social_post($g['title'] ?? '', $g['excerpt'] ?? '', $post_slug, $content_html, $trigger_tag_names);
    } catch (Exception $_se) {}

    write_progress('done', 'Post publish ho gayi!', 100);
    if ($progress_key) @unlink(sys_get_temp_dir() . '/ci_' . $progress_key . '.json');
    cbd_json_response([
        'success'     => true,
        'post_id'     => $post_id,
        'action'      => $existing_id ? 'updated' : 'created',
        'tag_ids'     => $saved_tag_ids,
        'needs_image' => false,
        'post_title'  => $g['title'] ?? '',
        'word_count'  => $final_word_count,
    ]);
}
