<?php

/**
 * Shared helpers for AI-created and manually maintained blog content.
 * Keep content cleanup/linking rules here so import, preview and bulk tools
 * always produce the same HTML.
 */

function cbd_sanitize_blog_html(string $html): string
{
    static $allowed = [
        'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p', 'ul', 'ol', 'li',
        'strong', 'b', 'em', 'i', 'a', 'img', 'table', 'thead', 'tbody',
        'tr', 'th', 'td', 'blockquote', 'pre', 'code', 'br', 'hr',
    ];

    $clean = preg_replace_callback(
        '/<(\/?)([a-zA-Z][a-zA-Z0-9]*)((?:\s[^<>]*)?)\/?>/s',
        static function (array $match) use ($allowed): string {
            if (!in_array(strtolower($match[2]), $allowed, true)) {
                return htmlspecialchars($match[0], ENT_QUOTES, 'UTF-8');
            }
            if ($match[1] === '/') return $match[0];

            // Keep useful presentation attributes, but remove executable HTML.
            $tag = preg_replace(
                '/\s+(?:on[a-z0-9_-]+|srcdoc)\s*=\s*(?:"[^"]*"|\'[^\']*\'|[^\s>]+)/iu',
                '',
                $match[0]
            ) ?? $match[0];
            $tag = preg_replace(
                '/\s+(?:href|src)\s*=\s*(?:"\s*(?:javascript|data:text\/html)[^"]*"|\'\s*(?:javascript|data:text\/html)[^\']*\'|(?:javascript|data:text\/html)[^\s>]*)/iu',
                '',
                $tag
            ) ?? $tag;
            return $tag;
        },
        $html
    );

    return is_string($clean) ? $clean : $html;
}

function cbd_service_link_map(PDO $pdo): array
{
    try {
        $slugs = $pdo->query(
            "SELECT slug FROM services WHERE status=1 AND deleted_at IS NULL"
        )->fetchAll(PDO::FETCH_COLUMN);
    } catch (Throwable $error) {
        cbd_log_error('content_tools.service_links', $error->getMessage());
        return [];
    }

    $base = cbd_base_path();
    $map = [];
    $generic = ['web', 'app', 'api', 'cms', 'crm', 'erp', 'seo', 'ui', 'ux'];

    foreach ($slugs as $slug) {
        $slug = trim((string)$slug);
        if ($slug === '') continue;

        $url = $base . '/' . $slug;
        $words = array_values(array_filter(explode('-', $slug)));
        if (!$words) continue;

        $phrases = [implode(' ', $words)];
        if (count($words) >= 3) $phrases[] = implode(' ', array_slice($words, 0, 3));
        if (count($words) >= 2) $phrases[] = implode(' ', array_slice($words, 0, 2));
        if (strlen($words[0]) > 3 && !in_array($words[0], $generic, true)) $phrases[] = $words[0];

        foreach (array_unique($phrases) as $keyword) {
            if (!isset($map[$keyword])) $map[$keyword] = $url;
        }
    }

    uksort($map, static fn(string $a, string $b): int => strlen($b) <=> strlen($a));
    return $map;
}

function cbd_inject_service_links(string $html, PDO $pdo, int $max_links = 4): string
{
    $map = cbd_service_link_map($pdo);
    if (!$map || $max_links < 1) return $html;

    $linked_urls = [];
    $linked = preg_replace_callback(
        '#<(p|li)(\s[^>]*)?>(.+?)</\1>#si',
        static function (array $match) use ($map, &$linked_urls, $max_links): string {
            $tag = $match[1];
            $attributes = $match[2] ?? '';
            $content = $match[3];

            if (stripos($content, '<a ') !== false || stripos($content, '<img') !== false) return $match[0];
            if (count($linked_urls) >= $max_links) return $match[0];

            foreach ($map as $keyword => $url) {
                if (in_array($url, $linked_urls, true)) continue;
                $pattern = '/(?<![\'"\->])(\b' . preg_quote($keyword, '/') . '\b)(?![^<]*<\/a>)/iu';
                if (!preg_match($pattern, $content)) continue;

                $replacement = '<a href="' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8')
                    . '" style="color:#EE483D;font-weight:600;text-decoration:underline;text-underline-offset:3px;">$1</a>';
                $content = preg_replace($pattern, $replacement, $content, 1) ?? $content;
                $linked_urls[] = $url;
                break;
            }

            return "<{$tag}{$attributes}>{$content}</{$tag}>";
        },
        $html
    );

    return is_string($linked) ? $linked : $html;
}

function cbd_is_relinkable_internal_href(string $href): bool
{
    $href = trim(html_entity_decode($href, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    if ($href === '' || $href[0] === '#') return false;
    if (preg_match('#^(?:mailto|tel|javascript|data):#i', $href)) return false;

    $parts = parse_url($href);
    if ($parts === false) return false;

    $host = strtolower((string)($parts['host'] ?? ''));
    $requestHost = strtolower(preg_replace('/:\d+$/', '', (string)($_SERVER['HTTP_HOST'] ?? '')));
    $ownHosts = array_filter(['chulbuldesign.com', 'www.chulbuldesign.com', $requestHost]);
    if ($host !== '' && !in_array($host, $ownHosts, true)) return false;

    $path = '/' . ltrim((string)($parts['path'] ?? ''), '/');
    $base = cbd_base_path();
    if ($base !== '' && str_starts_with($path, $base . '/')) {
        $path = substr($path, strlen($base));
    }

    return !preg_match('#^/blog(?:/|$)#i', $path);
}

function cbd_strip_internal_content_links(string $html): string
{
    $clean = preg_replace_callback(
        '#<a\s[^>]*href=["\']([^"\']+)["\'][^>]*>(.*?)</a>#is',
        static fn(array $match): string => cbd_is_relinkable_internal_href($match[1]) ? $match[2] : $match[0],
        $html
    );

    return is_string($clean) ? $clean : $html;
}

function cbd_count_internal_content_links(string $html): int
{
    preg_match_all('#<a\s[^>]*href=["\']([^"\']+)["\']#i', $html, $matches);
    return count(array_filter(
        $matches[1] ?? [],
        static fn(string $href): bool => cbd_is_relinkable_internal_href($href)
    ));
}
