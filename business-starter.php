<?php
// ── Business Starter Pack — Meta-ads lead-gen landing page (CRO-optimized) ────
require_once __DIR__ . '/includes/http.php';
require_once __DIR__ . '/includes/seo.php';
if (cbd_is_direct_script_request('business-starter.php')) cbd_redirect_path('/business-starter');
$WHATSAPP = '919990548795';
$PHONE    = '+919990548795';
$PRICE    = '22,000';
$VALUE    = '42,000';   // value-stack anchor
$WA_MSG   = rawurlencode("Hi Chulbul Design! Mujhe Business Starter Pack ke baare me free consultation chahiye. Mera business hai: ");
$WA_LINK  = "https://wa.me/{$WHATSAPP}?text={$WA_MSG}";
$base     = cbd_base_path();
$canonical = cbd_canonical_url('/business-starter');
$meta_pixel_id = trim(cbd_env('META_PIXEL_ID'));
$meta_pixel_enabled = cbd_is_production_host() && preg_match('/^\d{5,25}$/', $meta_pixel_id);
$CITIES   = ['Gurugram','Delhi','Noida','Ghaziabad','Faridabad'];
$faqs = [
  ['₹22,000 me sach me sab kuch aa jata hai?','Haan — GMB, Facebook + Instagram, 5 posts, 1-page website, basic SEO, WhatsApp/form sab isi me. Koi hidden charge nahi. (Domain/hosting agar chahiye to alag, hum sasta option bata denge.)'],
  ['Mujhe advance dena padega?','Nahi. Pehle free consultation me hum plan + quote dikhate hain — bilkul free, koi obligation nahi. Pasand aaye to ₹22,000 ka kaam milestone-based payment pe (ek saath pura advance nahi).'],
  ['Kitne din me ready ho jayega?','7 working days. Photos &amp; details milte hi hum start kar dete hain.'],
  ['Account aur website ka owner kaun?','Aap. Sab kuch aapke naam pe banta hai, full access aapko milta hai.'],
  ['Kaam acha hoga, kaise bharosa karu?','2013 se 500+ businesses ke liye kaam kiya hai. Free plan + samples dekh ke aap khud decide karoge — tabhi aage badhenge.'],
  ['Mujhe technical kuch karna padega?','Nahi. Aap sirf business detail + photos do. Baaki sab hum karte hain.'],
  ['Photos aur content kaun dega?','Aapke paas jo hai wo le lenge; nahi hai to hum guide + basic content bana denge.'],
  ['Baad me changes/support milega?','Haan — 1 month free support (chhote changes). Bade kaam ke liye sasta plan bata denge.'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>GMB, Social Media &amp; Website Setup — Gurugram, Delhi NCR, Noida | Chulbul Design</title>
<meta name="description" content="Google Business Profile + Facebook + Instagram + 1-page Website setup for local businesses in Gurugram, Delhi, Noida, Ghaziabad &amp; Faridabad — ready in 7 days. Free consultation, ₹22,000 all-inclusive.">
<meta name="robots" content="index, follow">
<meta name="keywords" content="GMB setup Gurugram, Google Business Profile Delhi NCR, social media setup Noida, business website Ghaziabad, online setup Faridabad, local business digital setup">
<link rel="canonical" href="<?= htmlspecialchars($canonical) ?>">
<meta name="geo.region" content="IN-HR">
<meta name="geo.placename" content="Gurugram, Delhi NCR">
<meta property="og:title" content="Apna Business Online Laao — 7 Din Me | GMB + Social + Website">
<meta property="og:description" content="Google + Instagram + Website — sab ready in 7 days. Gurugram, Delhi NCR. Free consultation.">
<meta property="og:type" content="website">
<meta property="og:url" content="<?= htmlspecialchars($canonical) ?>">
<meta property="og:image" content="<?= CBD_CANONICAL_ORIGIN ?>/assets/images/Fb_chulbuldesign.jpg">
<link rel="icon" href="<?= $base ?>/assets/images/logo/favicon.svg">
<link rel="stylesheet" href="<?= $base ?>/assets/css/business-starter.css">
<link rel="preload" as="style" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" onload="this.onload=null;this.rel='stylesheet'">
<noscript><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"></noscript>
<style>
  :root{--navy:#1e1e5c;--red:#EE483D;--purple:#49499A}
  html{scroll-behavior:smooth} body{font-family:system-ui,-apple-system,'Segoe UI',sans-serif}
  .hero-grad{background:linear-gradient(135deg,#1e1e5c 0%,#49499A 55%,#1e1e5c 100%)}
  .txt-grad{background:linear-gradient(90deg,#EE483D,#ffb347);-webkit-background-clip:text;background-clip:text;color:transparent}
  .card-hover{transition:transform .25s,box-shadow .25s}
  .card-hover:hover{transform:translateY(-4px);box-shadow:0 20px 40px -12px rgba(30,30,92,.18)}
  .pulse-dot{animation:pulse 1.4s infinite}@keyframes pulse{0%,100%{opacity:1}50%{opacity:.35}}
  .faq-q.open .faq-ic{transform:rotate(180deg)}
  .faq-a{max-height:0;overflow:hidden;transition:max-height .3s ease}.faq-a.open{max-height:320px}
  .float-card{box-shadow:0 18px 40px -12px rgba(0,0,0,.35)}
</style>

<?php if ($meta_pixel_enabled): ?>
<!-- Meta Pixel (production only) -->
<script>
!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
document,'script','https://connect.facebook.net/en_US/fbevents.js');
fbq('init',<?= json_encode($meta_pixel_id) ?>); fbq('track','PageView');
</script>
<?php endif; ?>

<!-- ── SEO: Schema.org JSON-LD (Service + LocalBusiness + Offer + FAQPage) ── -->
<?php
$ld = ['@context'=>'https://schema.org','@graph'=>[
  ['@type'=>'Service','name'=>'Business Starter Pack — GMB, Social Media & Website Setup','serviceType'=>'Local business online setup','description'=>'Google Business Profile + Facebook + Instagram + 1-page website setup for local businesses, ready in 7 days.',
   'provider'=>['@type'=>'LocalBusiness','name'=>'Chulbul Design','telephone'=>'+919990548795','url'=>CBD_CANONICAL_ORIGIN,'image'=>CBD_CANONICAL_ORIGIN.'/assets/images/logo/chulbuldesign.svg','priceRange'=>'₹₹','address'=>['@type'=>'PostalAddress','addressLocality'=>'Gurugram','addressRegion'=>'Haryana','postalCode'=>'122001','addressCountry'=>'IN']],
   'areaServed'=>array_map(fn($c)=>['@type'=>'City','name'=>$c],$CITIES),
   'offers'=>['@type'=>'Offer','price'=>'22000','priceCurrency'=>'INR','url'=>$canonical,'availability'=>'https://schema.org/InStock']],
  ['@type'=>'FAQPage','mainEntity'=>array_map(fn($f)=>['@type'=>'Question','name'=>html_entity_decode(strip_tags($f[0]),ENT_QUOTES),'acceptedAnswer'=>['@type'=>'Answer','text'=>html_entity_decode(strip_tags($f[1]),ENT_QUOTES)]],$faqs)]
]];
echo '<script type="application/ld+json">'.json_encode($ld, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE).'</script>';
?>
</head>
<body class="bg-white text-gray-800">

<!-- ── Scarcity bar ── -->
<div class="bg-[#EE483D] text-white text-center text-xs sm:text-sm font-semibold py-2 px-3">
  <i class="bi bi-lightning-charge-fill"></i> Is month sirf <b>10 businesses</b> onboard kar rahe hain — apni jagah pakki karo
</div>

<!-- ── Sticky header ── -->
<header class="sticky top-0 z-40 bg-white/95 backdrop-blur border-b border-gray-100">
  <div class="max-w-5xl mx-auto px-4 h-16 flex items-center justify-between gap-4">
    <img src="<?= $base ?>/assets/images/logo/chulbuldesign.svg" alt="Chulbul Design" class="h-8 flex-shrink-0">
    <nav class="hidden md:flex items-center gap-7 text-sm font-semibold text-gray-600">
      <a href="#package" class="hover:text-[#EE483D] transition">Kya Milega</a>
      <a href="#pricing" class="hover:text-[#EE483D] transition">Price</a>
      <a href="#faq" class="hover:text-[#EE483D] transition">FAQ</a>
      <a href="#lead" class="hover:text-[#EE483D] transition">Free Consult</a>
    </nav>
    <div class="flex items-center gap-2 flex-shrink-0">
      <a href="tel:<?= $PHONE ?>" onclick="lead('call_top')" class="hidden lg:inline-flex items-center gap-1.5 text-sm font-semibold text-[#1e1e5c] px-3 py-2 rounded-lg hover:bg-gray-100 transition"><i class="bi bi-telephone-fill"></i> Call</a>
      <a href="<?= $WA_LINK ?>" target="_blank" rel="noopener" onclick="lead('wa_top')" class="inline-flex items-center gap-1.5 text-sm font-bold text-white bg-green-500 hover:bg-green-600 px-4 py-2 rounded-lg transition"><i class="bi bi-whatsapp"></i> WhatsApp</a>
    </div>
  </div>
</header>

<!-- ── HERO ── -->
<section class="hero-grad text-white relative overflow-hidden">
  <div class="absolute -top-24 -right-24 w-96 h-96 bg-white/5 rounded-full"></div>
  <div class="absolute -bottom-32 -left-20 w-96 h-96 bg-[#EE483D]/10 rounded-full"></div>
  <div class="max-w-5xl mx-auto px-4 py-12 sm:py-16 relative grid lg:grid-cols-2 gap-10 items-center">
    <!-- copy -->
    <div>
      <div class="inline-flex items-center gap-2 bg-white/10 border border-white/20 rounded-full px-4 py-1.5 text-xs font-semibold mb-5">
        <span class="w-2 h-2 rounded-full bg-green-400 pulse-dot"></span> Gurugram · Delhi · Noida · Ghaziabad · Faridabad
      </div>
      <h1 class="text-3xl sm:text-5xl font-extrabold leading-tight mb-4">
        Customer Aapko Google Pe <span class="txt-grad">Dhoond Raha Hai</span> — Par Aap Wahan Ho?
      </h1>
      <p class="text-white/85 text-lg mb-6 max-w-xl">
        Google + Instagram + Website — aapke business ka poora online setup, sirf <strong>7 din</strong> me.
        Done-for-you. Aapko kuch technical nahi karna.
      </p>
      <div class="flex flex-wrap gap-3 mb-4">
        <a href="<?= $WA_LINK ?>" target="_blank" rel="noopener" onclick="lead('wa_hero')" class="inline-flex items-center gap-2 bg-green-500 hover:bg-green-600 text-white font-bold px-6 py-4 rounded-2xl shadow-lg transition text-lg"><i class="bi bi-whatsapp text-xl"></i> Free Consultation — WhatsApp</a>
        <a href="#lead" class="inline-flex items-center gap-2 bg-white text-[#1e1e5c] hover:bg-gray-100 font-bold px-6 py-4 rounded-2xl shadow-lg transition text-lg"><i class="bi bi-pencil-square"></i> Free Consultation</a>
      </div>
      <p class="text-white/70 text-sm mb-5"><i class="bi bi-shield-check text-green-400"></i> Pehle <b>free consultation</b> — hum plan + price batayenge. Pasand aaye tabhi aage, advance nahi.</p>
      <div class="flex flex-wrap items-center gap-x-5 gap-y-2 text-sm text-white/75">
        <span><i class="bi bi-star-fill text-yellow-400"></i> 5★ Google Rating</span>
        <span><i class="bi bi-patch-check-fill text-green-400"></i> 500+ Projects</span>
        <span><i class="bi bi-geo-alt-fill text-[#EE483D]"></i> Gurugram-based</span>
        <span><i class="bi bi-clock-fill text-sky-300"></i> Since 2013</span>
      </div>
    </div>
    <!-- visual: 3 deliverable cards -->
    <div class="hidden lg:block relative h-80">
      <div class="float-card absolute top-0 right-6 w-64 bg-white text-gray-800 rounded-2xl p-4 rotate-3">
        <div class="flex items-center gap-2 mb-2"><i class="bi bi-geo-alt-fill text-[#EE483D] text-xl"></i><b class="text-sm">Google Business</b></div>
        <div class="h-2 bg-gray-100 rounded w-3/4 mb-1.5"></div><div class="h-2 bg-gray-100 rounded w-1/2 mb-2"></div>
        <div class="flex gap-1"><span class="text-yellow-400 text-xs">★★★★★</span><span class="text-[10px] text-gray-400">Maps pe live</span></div>
      </div>
      <div class="float-card absolute top-28 right-0 w-60 bg-white text-gray-800 rounded-2xl p-4 -rotate-2">
        <div class="flex items-center gap-2 mb-2"><i class="bi bi-instagram text-[#EE483D] text-xl"></i><b class="text-sm">Instagram + Facebook</b></div>
        <div class="grid grid-cols-3 gap-1"><div class="aspect-square bg-gradient-to-br from-[#f0f0ff] to-[#fff5f5] rounded"></div><div class="aspect-square bg-gradient-to-br from-[#fff5f5] to-[#f0f0ff] rounded"></div><div class="aspect-square bg-gradient-to-br from-[#f0f0ff] to-[#eefaf0] rounded"></div></div>
      </div>
      <div class="float-card absolute top-52 right-16 w-64 bg-white text-gray-800 rounded-2xl p-4 rotate-1">
        <div class="flex items-center gap-2 mb-2"><i class="bi bi-window-desktop text-green-600 text-xl"></i><b class="text-sm">1-Page Website</b></div>
        <div class="h-2 bg-gray-100 rounded w-full mb-1.5"></div><div class="h-2 bg-gray-100 rounded w-2/3 mb-2"></div>
        <span class="inline-block bg-green-500 text-white text-[10px] font-bold px-2 py-0.5 rounded">WhatsApp + Call</span>
      </div>
    </div>
  </div>
</section>

<!-- ── WHO IS THIS FOR ── -->
<section class="bg-gray-50 border-b border-gray-100">
  <div class="max-w-5xl mx-auto px-4 py-8">
    <p class="text-center text-sm font-semibold text-gray-400 mb-5">Perfect for local businesses:</p>
    <div class="flex flex-wrap justify-center gap-x-8 gap-y-4 text-gray-600">
      <?php foreach ([['bi-heart-pulse','Doctors & Clinics'],['bi-emoji-smile','Dentists'],['bi-scissors','Salons'],['bi-cup-hot','Restaurants'],['bi-bag','Boutiques'],['bi-mortarboard','Coaches'],['bi-briefcase','Consultants'],['bi-shop','Shops']] as $w): ?>
      <div class="flex items-center gap-2 text-sm font-medium"><i class="bi <?= $w[0] ?> text-[#49499A] text-lg"></i> <?= $w[1] ?></div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ── PROBLEM ── -->
<section class="max-w-3xl mx-auto px-4 py-14 text-center">
  <h2 class="text-2xl sm:text-3xl font-extrabold text-[#1e1e5c] mb-4">Aaj Customer Pehle <span class="text-[#EE483D]">Online</span> Check Karta Hai</h2>
  <p class="text-gray-600 text-lg leading-relaxed">
    Koi bhi service chahiye — log Google pe <b>"near me"</b> search karte hain, Instagram pe dekhte hain, website check karte hain.
    Agar aap wahan <b>nahi mile</b>… to customer seedha aapke <b>competitor</b> ke paas chala jaata hai.
  </p>
  <p class="text-[#1e1e5c] font-bold text-lg mt-4">Jo dikhta nahi, wo bikta nahi. 😕</p>
</section>

<!-- ── WHAT YOU GET (3 services) ── -->
<section id="package" style="scroll-margin-top:80px" class="bg-gray-50 border-y border-gray-100">
  <div class="max-w-5xl mx-auto px-4 py-14 sm:py-16">
    <div class="text-center mb-10">
      <h2 class="text-2xl sm:text-4xl font-extrabold text-[#1e1e5c] mb-3">Ek Package — Poora Online Setup</h2>
      <p class="text-gray-500">Online dikhne ke liye jo chahiye, sab done-for-you.</p>
    </div>
    <div class="grid md:grid-cols-3 gap-6">
      <div class="card-hover bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
        <div class="w-12 h-12 rounded-xl bg-[#f0f0ff] flex items-center justify-center text-[#49499A] text-2xl mb-4"><i class="bi bi-geo-alt-fill"></i></div>
        <h3 class="text-lg font-extrabold text-[#1e1e5c] mb-3">Google Business (GMB)</h3>
        <ul class="space-y-2 text-sm text-gray-600">
          <li class="flex gap-2"><i class="bi bi-check-circle-fill text-green-500 mt-0.5"></i> Profile + verification help</li>
          <li class="flex gap-2"><i class="bi bi-check-circle-fill text-green-500 mt-0.5"></i> Categories, hours, services</li>
          <li class="flex gap-2"><i class="bi bi-check-circle-fill text-green-500 mt-0.5"></i> 10+ optimized photos</li>
          <li class="flex gap-2"><i class="bi bi-check-circle-fill text-green-500 mt-0.5"></i> Google Maps pe live</li>
          <li class="flex gap-2"><i class="bi bi-check-circle-fill text-green-500 mt-0.5"></i> Reviews collect karne ka setup</li>
        </ul>
      </div>
      <div class="card-hover bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
        <div class="w-12 h-12 rounded-xl bg-[#fff5f5] flex items-center justify-center text-[#EE483D] text-2xl mb-4"><i class="bi bi-instagram"></i></div>
        <h3 class="text-lg font-extrabold text-[#1e1e5c] mb-3">Facebook + Instagram</h3>
        <ul class="space-y-2 text-sm text-gray-600">
          <li class="flex gap-2"><i class="bi bi-check-circle-fill text-green-500 mt-0.5"></i> FB Page — full setup + branding</li>
          <li class="flex gap-2"><i class="bi bi-check-circle-fill text-green-500 mt-0.5"></i> Instagram Business profile</li>
          <li class="flex gap-2"><i class="bi bi-check-circle-fill text-green-500 mt-0.5"></i> Profile + cover design</li>
          <li class="flex gap-2"><i class="bi bi-check-circle-fill text-green-500 mt-0.5"></i> Bio, contact &amp; CTA buttons</li>
          <li class="flex gap-2"><i class="bi bi-check-circle-fill text-green-500 mt-0.5"></i> 5 starter posts design + publish</li>
        </ul>
      </div>
      <div class="card-hover bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
        <div class="w-12 h-12 rounded-xl bg-[#eefaf0] flex items-center justify-center text-green-600 text-2xl mb-4"><i class="bi bi-window-desktop"></i></div>
        <h3 class="text-lg font-extrabold text-[#1e1e5c] mb-3">1-Page Website</h3>
        <ul class="space-y-2 text-sm text-gray-600">
          <li class="flex gap-2"><i class="bi bi-check-circle-fill text-green-500 mt-0.5"></i> Mobile-responsive design</li>
          <li class="flex gap-2"><i class="bi bi-check-circle-fill text-green-500 mt-0.5"></i> About, Services, Gallery, Contact</li>
          <li class="flex gap-2"><i class="bi bi-check-circle-fill text-green-500 mt-0.5"></i> WhatsApp + call button</li>
          <li class="flex gap-2"><i class="bi bi-check-circle-fill text-green-500 mt-0.5"></i> Lead form (enquiry aapko milegi)</li>
          <li class="flex gap-2"><i class="bi bi-check-circle-fill text-green-500 mt-0.5"></i> Basic SEO + domain connect</li>
        </ul>
      </div>
    </div>
  </div>
</section>

<!-- ── VALUE STACK + PRICE ── -->
<section id="pricing" style="scroll-margin-top:80px" class="max-w-3xl mx-auto px-4 py-14 sm:py-16">
  <div class="text-center mb-8">
    <h2 class="text-2xl sm:text-4xl font-extrabold text-[#1e1e5c]">Alag-Alag Karwao to ₹<?= $VALUE ?>+</h2>
    <p class="text-gray-500 mt-2">…par poora package ek saath, aadhe se bhi kam me 👇</p>
  </div>
  <div class="bg-white border border-gray-100 rounded-3xl shadow-xl overflow-hidden">
    <div class="p-6 sm:p-8 space-y-3 text-sm">
      <?php foreach ([
        ['Google Business Profile — setup &amp; optimization','8,000'],
        ['Facebook + Instagram — branded setup','7,000'],
        ['5 Social Media Posts (designed)','3,000'],
        ['1-Page Professional Website','18,000'],
        ['Basic SEO Setup','4,000'],
        ['WhatsApp + Lead Form Integration','2,000'],
      ] as $row): ?>
      <div class="flex items-center justify-between border-b border-dashed border-gray-100 pb-2.5">
        <span class="text-gray-700 flex items-center gap-2"><i class="bi bi-check2-circle text-green-500"></i> <?= $row[0] ?></span>
        <span class="text-gray-400 line-through">₹<?= $row[1] ?></span>
      </div>
      <?php endforeach; ?>
    </div>
    <div class="bg-[#1e1e5c] text-white p-6 sm:p-8 text-center">
      <p class="text-white/60 text-sm">Total value <span class="line-through">₹<?= $VALUE ?>+</span> — aapke liye sirf</p>
      <div class="text-5xl font-extrabold my-1">₹<?= $PRICE ?></div>
      <p class="text-white/60 text-sm mb-5">one-time · all-inclusive · koi hidden charge nahi</p>
      <a href="<?= $WA_LINK ?>" target="_blank" rel="noopener" onclick="lead('wa_price')" class="inline-flex items-center justify-center gap-2 bg-green-500 hover:bg-green-600 text-white font-bold px-7 py-4 rounded-2xl transition w-full sm:w-auto"><i class="bi bi-whatsapp text-xl"></i> Free Consultation — WhatsApp Karein</a>
      <p class="text-white/50 text-xs mt-3"><i class="bi bi-shield-check"></i> Pehle free consultation · advance nahi · milestone payment</p>
    </div>
  </div>
</section>

<!-- ── WHY IT MATTERS ── -->
<section class="bg-gray-50 border-y border-gray-100">
  <div class="max-w-5xl mx-auto px-4 py-14">
    <h2 class="text-2xl sm:text-4xl font-extrabold text-[#1e1e5c] text-center mb-10">Iska Aapko Kya Fayda?</h2>
    <div class="grid sm:grid-cols-3 gap-6 text-center">
      <div class="p-6"><i class="bi bi-search text-3xl text-[#EE483D] mb-3 block"></i><h3 class="font-bold text-[#1e1e5c] mb-1">Google Pe Dikho</h3><p class="text-sm text-gray-500">Customer "near me" search kare to <b>aap</b> mile — competitor nahi.</p></div>
      <div class="p-6"><i class="bi bi-patch-check text-3xl text-[#49499A] mb-3 block"></i><h3 class="font-bold text-[#1e1e5c] mb-1">Bharosa Jeeto</h3><p class="text-sm text-gray-500">Sundar page + website dekhte hi lagta hai — yeh serious business hai.</p></div>
      <div class="p-6"><i class="bi bi-graph-up-arrow text-3xl text-green-600 mb-3 block"></i><h3 class="font-bold text-[#1e1e5c] mb-1">Zyada Enquiry</h3><p class="text-sm text-gray-500">Call, WhatsApp, message — sab seedha aapke paas, ek jagah.</p></div>
    </div>
  </div>
</section>

<!-- ── PROCESS ── -->
<section class="max-w-5xl mx-auto px-4 py-14">
  <h2 class="text-2xl sm:text-4xl font-extrabold text-[#1e1e5c] text-center mb-10">Bas 3 Step — Aapka Kaam Sirf Step 1</h2>
  <div class="grid sm:grid-cols-3 gap-6">
    <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100 text-center"><div class="w-10 h-10 rounded-full bg-[#EE483D] text-white font-bold flex items-center justify-center mx-auto mb-3">1</div><h3 class="font-bold text-[#1e1e5c] mb-1">WhatsApp / Form</h3><p class="text-sm text-gray-500">Business batao — free consultation me plan + quote dete hain.</p></div>
    <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100 text-center"><div class="w-10 h-10 rounded-full bg-[#49499A] text-white font-bold flex items-center justify-center mx-auto mb-3">2</div><h3 class="font-bold text-[#1e1e5c] mb-1">Hum Banate Hain</h3><p class="text-sm text-gray-500">GMB, social aur website — 7 din me sab ready.</p></div>
    <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100 text-center"><div class="w-10 h-10 rounded-full bg-green-500 text-white font-bold flex items-center justify-center mx-auto mb-3">3</div><h3 class="font-bold text-[#1e1e5c] mb-1">Live &amp; Customers</h3><p class="text-sm text-gray-500">Aap online ho — enquiries aana shuru.</p></div>
  </div>
</section>

<!-- ── RISK REVERSAL + BONUS ── -->
<section class="bg-[#1e1e5c] text-white">
  <div class="max-w-5xl mx-auto px-4 py-14 grid md:grid-cols-2 gap-8">
    <div>
      <h2 class="text-2xl font-extrabold mb-5"><i class="bi bi-shield-check text-green-400"></i> Aapka Risk Zero</h2>
      <ul class="space-y-3 text-white/85 text-sm">
        <li class="flex gap-2"><i class="bi bi-check2-circle text-green-400 text-lg"></i> <b>Free consultation</b> — plan + quote, pasand aaye tabhi aage</li>
        <li class="flex gap-2"><i class="bi bi-check2-circle text-green-400 text-lg"></i> <b>Advance nahi</b> — milestone-based payment</li>
        <li class="flex gap-2"><i class="bi bi-check2-circle text-green-400 text-lg"></i> <b>100% ownership aapka</b> — sab accounts aapke naam</li>
        <li class="flex gap-2"><i class="bi bi-check2-circle text-green-400 text-lg"></i> <b>No hidden charge</b> — jo price, wahi final</li>
        <li class="flex gap-2"><i class="bi bi-check2-circle text-green-400 text-lg"></i> <b>7-din delivery</b> commitment</li>
      </ul>
    </div>
    <div class="bg-white/5 border border-white/10 rounded-2xl p-6">
      <h3 class="text-lg font-extrabold mb-4"><i class="bi bi-gift-fill text-[#EE483D]"></i> Free Bonus (is month)</h3>
      <ul class="space-y-3 text-white/85 text-sm">
        <li class="flex gap-2"><i class="bi bi-plus-circle-fill text-[#EE483D] mt-0.5"></i> Google Review QR card (customers se reviews lene ke liye)</li>
        <li class="flex gap-2"><i class="bi bi-plus-circle-fill text-[#EE483D] mt-0.5"></i> 1 month free support — chhote changes free</li>
      </ul>
    </div>
  </div>
</section>

<!-- ── TRUST (real stats — REAL reviews add karna baad me) ── -->
<section class="max-w-5xl mx-auto px-4 py-14 text-center">
  <h2 class="text-2xl sm:text-4xl font-extrabold text-[#1e1e5c] mb-3">Gurugram &amp; Delhi-NCR Ka Bharosa</h2>
  <p class="text-gray-500 mb-8">Since 2013, 500+ businesses ke liye design &amp; development.</p>
  <div class="grid grid-cols-2 sm:grid-cols-4 gap-6">
    <div><div class="text-3xl font-extrabold text-[#EE483D]">500+</div><p class="text-sm text-gray-500">Projects Delivered</p></div>
    <div><div class="text-3xl font-extrabold text-[#49499A]">10+</div><p class="text-sm text-gray-500">Saal Experience</p></div>
    <div><div class="text-3xl font-extrabold text-green-600">5★</div><p class="text-sm text-gray-500">Google Rating</p></div>
    <div><div class="text-3xl font-extrabold text-[#1e1e5c]">7 Din</div><p class="text-sm text-gray-500">Delivery</p></div>
  </div>
  <!-- ⚠️ ASLI proof yahan add karo (sabse zyada conversion deta hai):
       - Google reviews ka screenshot  - banaye hue GMB/website ke screenshots
       - khush client ke WhatsApp chat screenshots  - ek 30-60s founder video -->
</section>

<!-- ── FAQ ── -->
<section id="faq" style="scroll-margin-top:80px" class="bg-gray-50 border-y border-gray-100">
  <div class="max-w-3xl mx-auto px-4 py-14">
    <h2 class="text-2xl sm:text-4xl font-extrabold text-[#1e1e5c] text-center mb-10">Common Sawaal</h2>
    <div class="space-y-3">
      <?php foreach ($faqs as $f): ?>
      <div class="bg-white border border-gray-100 rounded-xl overflow-hidden">
        <button type="button" class="faq-q w-full flex items-center justify-between gap-3 px-5 py-4 text-left font-semibold text-[#1e1e5c]"><span><?= $f[0] ?></span><i class="bi bi-chevron-down faq-ic transition-transform flex-shrink-0"></i></button>
        <div class="faq-a"><p class="px-5 pb-4 text-sm text-gray-600"><?= $f[1] ?></p></div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ── FINAL CTA + FORM ── -->
<section id="lead" style="scroll-margin-top:80px" class="hero-grad text-white">
  <div class="max-w-3xl mx-auto px-4 py-14 sm:py-16">
    <div class="text-center mb-8">
      <h2 class="text-2xl sm:text-4xl font-extrabold mb-3">Free Consultation Lo — Aaj Hi</h2>
      <p class="text-white/80">Form bharo ya WhatsApp karo. Hum aapke business ka <b>free plan + price quote</b> denge — koi payment ya obligation nahi. Pasand aaye to <b>₹<?= $PRICE ?></b> me kaam. 1 ghante me reply.</p>
    </div>
    <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-xl text-gray-800">
      <form id="leadForm" action="<?= $base ?>/lead-submit.php" method="POST" class="space-y-4">
        <input type="text" name="_hp" class="hidden" tabindex="-1" autocomplete="off" aria-hidden="true">
        <input type="hidden" name="source" value="business-starter">
        <input type="hidden" name="service" value="Business Starter Pack">
        <input type="hidden" name="page_url" value="<?= htmlspecialchars($canonical, ENT_QUOTES, 'UTF-8') ?>">
        <div><label class="block text-sm font-semibold text-gray-600 mb-1">Aapka Naam *</label><input type="text" name="name" required placeholder="Pura naam" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-[#49499A] focus:ring-2 focus:ring-[#49499A]/20 outline-none"></div>
        <div><label class="block text-sm font-semibold text-gray-600 mb-1">WhatsApp Number *</label><input type="tel" name="phone" required pattern="[0-9+ ]{8,15}" placeholder="10-digit mobile number" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-[#49499A] focus:ring-2 focus:ring-[#49499A]/20 outline-none"></div>
        <div><label class="block text-sm font-semibold text-gray-600 mb-1">Business / Kaam</label><input type="text" name="business" placeholder="e.g. Dental clinic, Restaurant, Boutique" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-[#49499A] focus:ring-2 focus:ring-[#49499A]/20 outline-none"></div>
        <button type="submit" id="leadBtn" class="w-full bg-[#EE483D] hover:bg-red-600 text-white font-bold py-4 rounded-xl transition flex items-center justify-center gap-2"><i class="bi bi-send-fill"></i> Free Consultation Chahiye</button>
        <p class="text-center text-xs text-gray-400"><i class="bi bi-shield-lock"></i> 1 ghante me reply · No spam · Ya seedha <a href="<?= $WA_LINK ?>" target="_blank" rel="noopener" onclick="lead('wa_form')" class="text-green-600 font-semibold">WhatsApp karein →</a></p>
        <div id="leadMsg" class="hidden text-center text-sm rounded-xl px-4 py-3"></div>
      </form>
    </div>
  </div>
</section>

<!-- ── Footer ── -->
<footer class="bg-white border-t border-gray-100">
  <div class="max-w-5xl mx-auto px-4 py-8 flex flex-col sm:flex-row items-center justify-between gap-3 text-sm text-gray-400">
    <img src="<?= $base ?>/assets/images/logo/chulbuldesign.svg" alt="Chulbul Design" class="h-6">
    <p>© 2013–2026 Chulbul Design LLP · Gurugram, Delhi-NCR</p>
    <div class="flex gap-4"><a href="tel:<?= $PHONE ?>" onclick="lead('call_footer')" class="hover:text-[#EE483D]"><i class="bi bi-telephone"></i> Call</a><a href="mailto:info@chulbuldesign.com" class="hover:text-[#EE483D]"><i class="bi bi-envelope"></i> Email</a></div>
  </div>
</footer>

<!-- ── Sticky mobile CTA ── -->
<div class="fixed bottom-0 inset-x-0 z-40 sm:hidden bg-white border-t border-gray-200 grid grid-cols-2 gap-2 p-2.5">
  <a href="tel:<?= $PHONE ?>" onclick="lead('call_sticky')" class="flex items-center justify-center gap-2 bg-[#1e1e5c] text-white font-bold py-3 rounded-xl text-sm"><i class="bi bi-telephone-fill"></i> Call</a>
  <a href="<?= $WA_LINK ?>" target="_blank" rel="noopener" onclick="lead('wa_sticky')" class="flex items-center justify-center gap-2 bg-green-500 text-white font-bold py-3 rounded-xl text-sm"><i class="bi bi-whatsapp"></i> Free Consult</a>
</div>
<div class="h-16 sm:hidden"></div>

<script src="<?= $base ?>/assets/js/site-analytics.js?v=<?= (int) (@filemtime(__DIR__ . '/assets/js/site-analytics.js') ?: 1) ?>"
        data-endpoint="<?= $base ?>/analytics-collect.php" defer></script>
<script>
function lead(where){
  try{ if(typeof window.fbq==='function') fbq('track','Lead',{content_name:'business-starter',cta:where}); }catch(e){}
  if(where==='form_submit'){
    // The lead handler records a saved enquiry server-side; avoid double counting here.
  }
}
document.querySelectorAll('.faq-q').forEach(function(b){ b.addEventListener('click',function(){ b.classList.toggle('open'); b.nextElementSibling.classList.toggle('open'); }); });
document.getElementById('leadForm').addEventListener('submit', async function(e){
  e.preventDefault();
  var btn=document.getElementById('leadBtn'), msg=document.getElementById('leadMsg');
  if(!this.checkValidity()){ this.reportValidity(); return; }
  btn.disabled=true; btn.innerHTML='<i class="bi bi-arrow-repeat"></i> Bhej rahe hain…';
  try{
    var res=await fetch(this.action,{method:'POST',body:new FormData(this),headers:{'Accept':'application/json'}});
    var data=await res.json().catch(function(){ return {}; });
    if(res.ok && data.success){ lead('form_submit');
      msg.className='text-center text-sm rounded-xl px-4 py-3 bg-green-50 text-green-700 border border-green-200';
      var whatsappUrl=data.whatsapp_url || '<?= $WA_LINK ?>';
      msg.innerHTML='✅ Mil gaya! Hum 1 ghante me aapka free plan + quote le ke call karenge. Ya abhi <a href="'+whatsappUrl+'" target="_blank" rel="noopener noreferrer" class="font-bold underline">WhatsApp karein</a>.';
      msg.classList.remove('hidden'); this.reset();
    } else throw new Error(data.error||'failed');
  }catch(err){
    msg.className='text-center text-sm rounded-xl px-4 py-3 bg-red-50 text-red-700 border border-red-200';
    msg.textContent='Kuch dikkat aayi — WhatsApp pe try karein: <?= $PHONE ?>';
    msg.classList.remove('hidden');
  }finally{ btn.disabled=false; btn.innerHTML='<i class="bi bi-send-fill"></i> Free Consultation Chahiye'; }
});
</script>
</body>
</html>
