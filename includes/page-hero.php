<?php
// $hero = ['badge'=>'', 'breadcrumb'=>'', 'h1'=>'', 'desc'=>'', 'img'=>'', 'img_alt'=>'']
?>
<section style="background:linear-gradient(135deg,#1e1e5c 0%,#49499A 100%);position:relative;overflow:hidden">
    <div style="position:absolute;inset:0;background-image:radial-gradient(circle,rgba(255,255,255,.06) 1px,transparent 1px);background-size:24px 24px;pointer-events:none"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 relative">
        <div class="flex flex-col lg:flex-row items-center gap-10">

            <!-- Left: Text -->
            <div class="flex-1 text-center lg:text-left">
                <nav aria-label="Breadcrumb" class="flex items-center justify-center lg:justify-start mb-5">
                    <?php
                    $__bc_domain = cbd_site_base_url();
                    $__bc_current = $hero['canonical_url'] ?? ($__bc_domain . strtok($_SERVER['REQUEST_URI'], '?'));
                    ?>
                    <ol class="flex items-center gap-2 text-sm text-white/70" itemscope itemtype="https://schema.org/BreadcrumbList">
                        <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                            <a href="<?= $base ?>/" itemprop="item" class="hover:text-white transition-colors">
                                <span itemprop="name">Home</span>
                            </a>
                            <meta itemprop="position" content="1">
                        </li>
                        <?php if (!empty($hero['breadcrumb'])): ?>
                        <li aria-hidden="true"><i class="bi bi-chevron-right text-xs"></i></li>
                        <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                            <a href="<?= htmlspecialchars($__bc_current) ?>" itemprop="item" class="text-white/90 font-medium pointer-events-none">
                                <span itemprop="name"><?= htmlspecialchars($hero['breadcrumb']) ?></span>
                            </a>
                            <meta itemprop="position" content="2">
                        </li>
                        <?php endif; ?>
                    </ol>
                </nav>
                <span class="inline-block text-xs font-bold tracking-widest uppercase text-white/90 bg-white/10 border border-white/20 px-3 py-1 rounded-full mb-4">
                    <?= $hero['badge'] ?>
                </span>
                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-white leading-tight mb-4">
                    <?= $hero['h1'] ?>
                </h1>
                <p class="text-white/90 text-base lg:text-lg leading-relaxed max-w-xl mx-auto lg:mx-0 mb-8">
                    <?= $hero['desc'] ?>
                </p>
                <div class="flex flex-wrap gap-3 justify-center lg:justify-start">
                    <a href="https://wa.me/919990548795" target="_blank"
                       class="inline-flex items-center gap-2 bg-[#EE483D] hover:bg-red-600 text-white px-6 py-3 rounded-xl font-semibold transition-all text-sm">
                        <i class="bi bi-whatsapp"></i> Get Free Quote
                    </a>
                    <a href="tel:+919990548795"
                       class="inline-flex items-center gap-2 bg-white/10 hover:bg-white/20 border border-white/20 text-white px-6 py-3 rounded-xl font-semibold transition-all text-sm">
                        <i class="bi bi-telephone-fill"></i> +91 9990 548 795
                    </a>
                </div>
            </div>

            <!-- Right: Image -->
            <div class="flex-1 flex justify-center lg:justify-end">
                <img src="<?= $base . $hero['img'] ?>" alt="<?= $hero['img_alt'] ?>"
                     class="w-full max-w-md rounded-2xl shadow-2xl object-cover h-60 lg:h-80"
                     width="480" height="320" fetchpriority="high">
            </div>

        </div>
    </div>
</section>
