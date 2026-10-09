<?php
declare(strict_types=1);
if (PHP_SAPI !== 'cli' && !defined('CBD_DEPLOY_BLOG_AUTHORIZED')) { http_response_code(404); exit; }
require_once __DIR__.'/publish-city-seo.php';
function cbd_seattle_lead_health(PDO $p): array {
    $health=[];
    try {
        $q=$p->query("SELECT COALESCE(SUM(created_at >= NOW() - INTERVAL 30 DAY),0) AS last_30_days, COALESCE(SUM(created_at < NOW() - INTERVAL 30 DAY),0) AS previous_30_days FROM leads WHERE created_at >= NOW() - INTERVAL 60 DAY");
        $health['all_saved_leads']=$q->fetch(PDO::FETCH_ASSOC);
        $q=$p->query("SELECT COUNT(*) FROM leads WHERE created_at >= NOW() - INTERVAL 30 DAY AND source LIKE 'city-seattle%'");
        $health['seattle_saved_leads_last_30_days']=(int)$q->fetchColumn();
        $q=$p->query("SELECT COUNT(*) FROM analytics_pageviews WHERE started_at >= NOW() - INTERVAL 30 DAY AND page_path LIKE '/city/seattle%'");
        $health['seattle_pageviews_last_30_days']=(int)$q->fetchColumn();
        $q=$p->query("SELECT event_type, COUNT(*) AS count FROM analytics_events WHERE created_at >= NOW() - INTERVAL 30 DAY AND page_path LIKE '/city/seattle%' AND event_type IN ('form_start','form_submit','lead_saved','conversion') GROUP BY event_type");
        $health['seattle_events_last_30_days']=$q->fetchAll(PDO::FETCH_ASSOC);
    } catch(Throwable $e) {$health['history_available']=false;}
    // Exercise production storage without sending mail or retaining a test lead.
    try {
        $engine=$p->query("SELECT ENGINE FROM information_schema.TABLES WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='leads'")->fetchColumn();
        if(strtoupper((string)$engine)!=='INNODB')throw new RuntimeException('Transactional lead storage required.');
        $p->beginTransaction();
        $q=$p->prepare('INSERT INTO leads (name,phone,business,message,source,ip) VALUES (?,?,?,?,?,?)');
        $q->execute(['Storage verification','','QA','Temporary transaction; no notification','qa-seattle-storage-probe','']);
        $health['lead_storage_probe']=$p->lastInsertId() ? 'passed' : 'failed';
        $p->rollBack();
    } catch(Throwable $e) {
        if($p->inTransaction())$p->rollBack();
        $health['lead_storage_probe']='failed';
    }
    return $health;
}
function cbd_publish_seattle_spokes(string $filename): array {
    if($filename!=='seo_spokes_seattle_2026_10_09.json') throw new RuntimeException('Unreviewed service bundle.');
    $bundle=json_decode((string)file_get_contents(__DIR__.'/'.$filename),true,512,JSON_THROW_ON_ERROR);
    $slugs=['contractor-web-design','ecommerce-development','small-business-web-design','wordpress-developer'];
    if(array_column($bundle,'slug')!==$slugs)throw new RuntimeException('Service scope mismatch.');
    $fields=['meta_title','meta_description','heading_html','hero_description','introduction','content_json','faqs_json','schema_service_name','schema_service_type','cta_title','cta_description'];
    $p=cbd_database();if(!$p)throw new RuntimeException('Database unavailable.');
    $p->beginTransaction();$updated=0;
    try {
        $q=$p->prepare('SELECT sp.id,'.implode(',',array_map(static fn($f)=>'sp.'.$f,$fields))." FROM city_spokes sp JOIN cities ci ON ci.id=sp.city_id WHERE ci.slug='seattle' AND sp.slug=? AND sp.status='published' FOR UPDATE");
        $existing=[];
        foreach($bundle as $b) {
            if(array_keys($b['before'])!==$fields || array_keys($b['after'])!==$fields)throw new RuntimeException('Invalid service fields.');
            foreach(['content_json','faqs_json'] as $f)json_decode($b['after'][$f],true,512,JSON_THROW_ON_ERROR);
            $q->execute([$b['slug']]);$row=$q->fetch(PDO::FETCH_ASSOC);if(!$row)throw new RuntimeException('Published service missing.');
            $id=$row['id'];unset($row['id']);
            $before=cbd_seo_snapshot([$row])===cbd_seo_snapshot([$b['before']]);
            $after=cbd_seo_snapshot([$row])===cbd_seo_snapshot([$b['after']]);
            if(!$before&&!$after)throw new RuntimeException('Service changed since review; refusing to overwrite: '.$b['slug']);
            $existing[]=['id'=>$id,'needs_update'=>!$after];
        }
        $update=$p->prepare('UPDATE city_spokes SET '.implode(',',array_map(static fn($f)=>"$f=?",$fields)).' WHERE id=?');
        foreach($bundle as $i=>$b) if($existing[$i]['needs_update']){$update->execute([...array_values($b['after']),$existing[$i]['id']]);$updated++;}
        $p->commit();
    }catch(Throwable $e){if($p->inTransaction())$p->rollBack();throw $e;}
    return ['success'=>true,'city_slug'=>'seattle','slugs'=>$slugs,'updated'=>$updated,'lead_health'=>cbd_seattle_lead_health($p)];
}
if(PHP_SAPI==='cli' && realpath((string)($_SERVER['SCRIPT_FILENAME']??''))===__FILE__) {
    try{echo json_encode(cbd_publish_seattle_spokes($argv[1]??''),JSON_THROW_ON_ERROR).PHP_EOL;}
    catch(Throwable $e){fwrite(STDERR,$e->getMessage().PHP_EOL);exit(1);}
}
