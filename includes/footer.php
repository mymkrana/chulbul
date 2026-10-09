</main>

<!-- ============================================================
     FOOTER — Light & Clean
============================================================ -->
<footer aria-label="Site footer" class="bg-gray-50 border-t border-gray-100 mt-20">

    <!-- Main footer grid -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10">

            <!-- Col 1: Brand -->
            <div class="lg:col-span-1">
                <a href="<?= $base ?>/">
                    <img src="<?= $base ?>/assets/images/logo/chulbuldesign.svg" alt="Chulbul Design" class="h-7 mb-4" width="160" height="28">
                </a>
                <p class="text-gray-500 text-sm leading-relaxed mb-5">
                    Web design &amp; development company based in <strong class="text-gray-700">Gurugram, Delhi NCR</strong>. Serving businesses across India, USA, UK &amp; UAE since 2013.
                </p>
                <!-- Social icons -->
                <div class="flex gap-3">
                    <a href="https://wa.me/919990548795" target="_blank" rel="noopener noreferrer" aria-label="WhatsApp"
                       class="w-10 h-10 rounded-full bg-white border border-gray-200 flex items-center justify-center text-gray-500 hover:text-green-500 hover:border-green-300 hover:bg-green-50 transition">
                        <i class="bi bi-whatsapp text-base"></i>
                    </a>
                    <a href="https://www.facebook.com/chulbuldesign/" target="_blank" rel="noopener noreferrer" aria-label="Facebook"
                       class="w-10 h-10 rounded-full bg-white border border-gray-200 flex items-center justify-center text-gray-500 hover:text-blue-600 hover:border-blue-300 hover:bg-blue-50 transition">
                        <i class="bi bi-facebook text-base"></i>
                    </a>
                    <a href="https://www.instagram.com/chulbuldesign/" target="_blank" rel="noopener noreferrer" aria-label="Instagram"
                       class="w-10 h-10 rounded-full bg-white border border-gray-200 flex items-center justify-center text-gray-500 hover:text-pink-500 hover:border-pink-300 hover:bg-pink-50 transition">
                        <i class="bi bi-instagram text-base"></i>
                    </a>
                    <a href="https://twitter.com/ChulbulDesign/" target="_blank" rel="noopener noreferrer" aria-label="Twitter/X"
                       class="w-10 h-10 rounded-full bg-white border border-gray-200 flex items-center justify-center text-gray-500 hover:text-gray-900 hover:border-gray-400 hover:bg-gray-50 transition">
                        <i class="bi bi-twitter-x text-base"></i>
                    </a>
                    <a href="https://www.linkedin.com/company/chulbuldesign/" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn"
                       class="w-10 h-10 rounded-full bg-white border border-gray-200 flex items-center justify-center text-gray-500 hover:text-blue-700 hover:border-blue-300 hover:bg-blue-50 transition">
                        <i class="bi bi-linkedin text-base"></i>
                    </a>
                </div>
            </div>

            <!-- Col 2: Services -->
            <div>
                <h3 class="text-xs font-extrabold uppercase tracking-widest text-gray-400 mb-4">Our Services</h3>
                <ul class="space-y-2.5">
                    <li><a href="<?= $base ?>/web-design"                   class="text-gray-600 hover:text-[#EE483D] text-sm transition">Web Design</a></li>
                    <li><a href="<?= $base ?>/web-development"              class="text-gray-600 hover:text-[#EE483D] text-sm transition">Web Development</a></li>
                    <li><a href="<?= $base ?>/ecommerce-website-development"     class="text-gray-600 hover:text-[#EE483D] text-sm transition">E-Commerce Website</a></li>
                    <li><a href="<?= $base ?>/wordpress-development"        class="text-gray-600 hover:text-[#EE483D] text-sm transition">WordPress Development</a></li>
                    <li><a href="<?= $base ?>/android-app-development"      class="text-gray-600 hover:text-[#EE483D] text-sm transition">Mobile App Development</a></li>
                    <li><a href="<?= $base ?>/local-seo-services"           class="text-gray-600 hover:text-[#EE483D] text-sm transition">Online Marketing</a></li>
                </ul>
            </div>

            <!-- Col 3: Cities -->
            <div>
                <h3 class="text-xs font-extrabold uppercase tracking-widest text-gray-400 mb-4">Cities We Serve</h3>
                <div class="grid grid-cols-2 gap-x-2 gap-y-2">
                    <a href="<?= $base ?>/city/gurgaon"   class="text-gray-600 hover:text-[#EE483D] text-sm transition">Gurgaon</a>
                    <a href="<?= $base ?>/city/delhi"      class="text-gray-600 hover:text-[#EE483D] text-sm transition">Delhi</a>
                    <a href="<?= $base ?>/city/noida"      class="text-gray-600 hover:text-[#EE483D] text-sm transition">Noida</a>
                    <a href="<?= $base ?>/city/mumbai"     class="text-gray-600 hover:text-[#EE483D] text-sm transition">Mumbai</a>
                    <a href="<?= $base ?>/city/bangalore"  class="text-gray-600 hover:text-[#EE483D] text-sm transition">Bangalore</a>
                    <a href="<?= $base ?>/city/hyderabad"  class="text-gray-600 hover:text-[#EE483D] text-sm transition">Hyderabad</a>
                    <a href="<?= $base ?>/city/pune"       class="text-gray-600 hover:text-[#EE483D] text-sm transition">Pune</a>
                    <a href="<?= $base ?>/city/chennai"    class="text-gray-600 hover:text-[#EE483D] text-sm transition">Chennai</a>
                    <a href="<?= $base ?>/city/new-york"   class="text-gray-600 hover:text-[#EE483D] text-sm transition">New York</a>
                    <a href="<?= $base ?>/city/london"     class="text-gray-600 hover:text-[#EE483D] text-sm transition">London</a>
                    <a href="<?= $base ?>/city/dubai"      class="text-gray-600 hover:text-[#EE483D] text-sm transition">Dubai</a>
                    <a href="<?= $base ?>/city/toronto"    class="text-gray-600 hover:text-[#EE483D] text-sm transition">Toronto</a>
                </div>
                <a href="<?= $base ?>/cities" class="inline-block mt-3 text-xs text-[#EE483D] hover:underline font-semibold">View all 52 cities →</a>
            </div>

            <!-- Col 4: Contact -->
            <div>
                <h3 class="text-xs font-extrabold uppercase tracking-widest text-gray-400 mb-4">Get In Touch</h3>
                <ul class="space-y-3">
                    <li class="flex items-start gap-2.5">
                        <i class="bi bi-geo-alt text-[#EE483D] text-base mt-0.5 flex-shrink-0"></i>
                        <span class="text-gray-600 text-sm">Sector 15, Flat No. 1277,<br>Gurugram, Haryana — 122001</span>
                    </li>
                    <li>
                        <a href="tel:+919990548795" class="flex items-center gap-2.5 text-gray-600 hover:text-[#EE483D] transition text-sm">
                            <i class="bi bi-telephone text-[#EE483D] text-base flex-shrink-0"></i>
                            +91 9990 548 795
                        </a>
                    </li>
                    <li>
                        <a href="mailto:info@chulbuldesign.com" class="flex items-center gap-2.5 text-gray-600 hover:text-[#EE483D] transition text-sm">
                            <i class="bi bi-envelope text-[#EE483D] text-base flex-shrink-0"></i>
                            info@chulbuldesign.com
                        </a>
                    </li>
                </ul>
                <!-- CTA -->
                <a href="<?= $base ?>/contact-us"
                   class="inline-flex items-center gap-2 mt-5 bg-[#EE483D] hover:bg-[#d43c31] text-white text-sm font-bold px-5 py-2.5 rounded-xl transition">
                    <i class="bi bi-chat-dots-fill text-xs"></i> Free Consultation
                </a>
            </div>

        </div>
    </div>

    <!-- Bottom bar -->
    <div class="border-t border-gray-200 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex flex-col sm:flex-row items-center justify-between gap-3">
            <p class="text-gray-400 text-xs">
                &copy; 2013&ndash;<?php echo date('Y'); ?> Chulbul Design LLP. All rights reserved.
            </p>
            <div class="flex gap-4">
                <a href="<?= $base ?>/about"      class="text-gray-400 hover:text-[#EE483D] text-xs transition">About</a>
                <a href="<?= $base ?>/blog/"       class="text-gray-400 hover:text-[#EE483D] text-xs transition">Blog</a>
                <a href="<?= $base ?>/contact-us" class="text-gray-400 hover:text-[#EE483D] text-xs transition">Contact</a>
                <a href="<?= $base ?>/cities"     class="text-gray-400 hover:text-[#EE483D] text-xs transition">Cities</a>
            </div>
        </div>
    </div>

</footer>

<script src="<?= $base ?>/assets/js/main.js" defer></script>
<script src="<?= $base ?>/assets/js/site-analytics.js?v=<?= (int) (@filemtime(__DIR__ . '/../assets/js/site-analytics.js') ?: 1) ?>"
        data-endpoint="<?= $base ?>/analytics-collect.php" defer></script>

<!-- Floating WhatsApp consultation CTA -->
<a href="https://wa.me/919990548795?text=Hi%20Chulbul%20Design%2C%20I%20would%20like%20a%20free%20website%20consultation."
   class="cbd-whatsapp-float"
   target="_blank"
   rel="noopener noreferrer"
   aria-label="Chat with Chulbul Design on WhatsApp for a free consultation">
    <span class="cbd-whatsapp-icon" aria-hidden="true">
        <i class="bi bi-whatsapp"></i>
    </span>
    <span class="cbd-whatsapp-copy">
        <span class="cbd-whatsapp-kicker">Chat with an expert</span>
        <strong>Free Consultation</strong>
    </span>
    <span class="cbd-whatsapp-status" aria-hidden="true"></span>
</a>

<!-- Back to Top -->
<button id="back-to-top" onclick="window.scrollTo({top:0,behavior:'smooth'})" aria-label="Back to top">
    <i class="bi bi-arrow-up"></i>
</button>
<script>
(function(){
    var btn = document.getElementById('back-to-top');
    window.addEventListener('scroll', function(){
        if (window.scrollY > 300) {
            btn.style.opacity = '1';
            btn.style.transform = 'translateY(0)';
            btn.style.pointerEvents = 'auto';
        } else {
            btn.style.opacity = '0';
            btn.style.transform = 'translateY(12px)';
            btn.style.pointerEvents = 'none';
        }
    }, {passive:true});
})();
</script>

</body>
</html>
