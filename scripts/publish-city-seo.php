<?php
declare(strict_types=1);
if (PHP_SAPI !== 'cli' && !defined('CBD_DEPLOY_BLOG_AUTHORIZED')) { http_response_code(404); exit; }
require_once dirname(__DIR__) . '/includes/database.php';

/** Compare database snapshots independently of row IDs and PDO numeric types. */
function cbd_seo_snapshot(array $rows): array {
    $normal = array_map(static function(array $row): string {
        ksort($row);
        foreach ($row as &$value) { if ($value !== null) $value = (string)$value; }
        return json_encode($row, JSON_THROW_ON_ERROR);
    }, $rows);
    sort($normal);
    return $normal;
}
function cbd_publish_city_seo(string $filename): array {
    if ($filename !== 'seo_city_seattle_2026_10_09.json') throw new RuntimeException('Unreviewed city bundle.');
    $b = json_decode((string)file_get_contents(__DIR__.'/'.$filename), true, 512, JSON_THROW_ON_ERROR);
    if (($b['city_slug'] ?? '') !== 'seattle') throw new RuntimeException('City mismatch.');
    $p = cbd_database(); if (!$p) throw new RuntimeException('Database unavailable.');
    $cityFields=['tagline','heading_html','hero_description','introduction','why_local','market_insight','meta_title','meta_description'];
    $tables=['faqs'=>['city_faqs','question'], 'sections'=>['city_query_sections','section_key']];
    $p->beginTransaction();
    try {
        $q=$p->prepare('SELECT id,'.implode(',',$cityFields)." FROM cities WHERE slug=? AND status='published' FOR UPDATE");
        $q->execute(['seattle']); $city=$q->fetch(PDO::FETCH_ASSOC);
        if (!$city) throw new RuntimeException('Published Seattle page missing.');
        $id=$city['id']; unset($city['id']);
        $states=['city'=>[$city]]; $rowsByTable=[];
        foreach($tables as $key=>[$table,$identity]) {
            $fields=array_keys($b[$key]['before'][0]);
            $q=$p->prepare('SELECT id,'.implode(',',$fields)." FROM $table WHERE city_id=? AND status='published' ORDER BY id FOR UPDATE");
            $q->execute([$id]); $rowsByTable[$key]=$q->fetchAll(PDO::FETCH_ASSOC);
            $states[$key]=array_map(static function($row){unset($row['id']);return $row;},$rowsByTable[$key]);
        }
        $currentBlogs=[];
        foreach($b['blogs'] as $blog) {
            $q=$p->prepare("SELECT content FROM posts WHERE slug=? AND status='published' AND deleted_at IS NULL FOR UPDATE");
            $q->execute([$blog['slug']]); $currentBlogs[]=$q->fetchColumn();
        }
        $before=true; $after=true;
        foreach($states as $key=>$rows) {
            $before=$before && cbd_seo_snapshot($rows)===cbd_seo_snapshot($key==='city'?[$b[$key]['before']]:$b[$key]['before']);
            $after=$after && cbd_seo_snapshot($rows)===cbd_seo_snapshot($key==='city'?[$b[$key]['after']]:$b[$key]['after']);
        }
        foreach($currentBlogs as $i=>$content) { $before=$before && $content===$b['blogs'][$i]['before']; $after=$after && $content===$b['blogs'][$i]['after']; }
        if (!$before && !$after) throw new RuntimeException('Seattle content changed since review; refusing to overwrite.');
        if ($before && !$after) {
            $q=$p->prepare('UPDATE cities SET '.implode(',',array_map(static fn($f)=>"$f=?",$cityFields)).' WHERE id=?');
            $q->execute([...array_values($b['city']['after']),$id]);
            foreach($tables as $key=>[$table,$identity]) {
                $used=[];
                foreach($b[$key]['after'] as $row) {
                    $fields=array_keys($row); $existing=null;
                    foreach($rowsByTable[$key] as $candidate) if(!in_array($candidate['id'],$used,true) && $candidate[$identity]===$row[$identity]) {$existing=$candidate['id'];break;}
                    if($existing!==null) {
                        $q=$p->prepare("UPDATE $table SET ".implode(',',array_map(static fn($f)=>"$f=?",$fields)).' WHERE id=? AND city_id=?');
                        $q->execute([...array_values($row),$existing,$id]); $used[]=$existing;
                    } else {
                        $q=$p->prepare("INSERT INTO $table (city_id,".implode(',',$fields).') VALUES ('.implode(',',array_fill(0,count($fields)+1,'?')).')');
                        $q->execute([$id,...array_values($row)]);
                    }
                }
                foreach($rowsByTable[$key] as $old) if(!in_array($old['id'],$used,true)) {
                    $q=$p->prepare("UPDATE $table SET status='archived' WHERE id=? AND city_id=?"); $q->execute([$old['id'],$id]);
                }
            }
            foreach($b['blogs'] as $blog) {$q=$p->prepare('UPDATE posts SET content=?,updated_at=CURRENT_TIMESTAMP WHERE slug=?');$q->execute([$blog['after'],$blog['slug']]);}
        }
        $p->commit();
        return ['success'=>true,'city_slug'=>'seattle','updated'=>!$after,'faq_count'=>count($b['faqs']['after']),'blog_links'=>count($b['blogs'])];
    } catch(Throwable $e) {if($p->inTransaction())$p->rollBack();throw $e;}
}
if(PHP_SAPI==='cli' && realpath((string)($_SERVER['SCRIPT_FILENAME']??''))===__FILE__) {
    try {echo json_encode(cbd_publish_city_seo($argv[1]??''),JSON_THROW_ON_ERROR).PHP_EOL;}
    catch(Throwable $e){fwrite(STDERR,$e->getMessage().PHP_EOL);exit(1);}
}
