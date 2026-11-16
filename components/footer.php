    <!-- Site Editorial Footer -->
    <footer class="mt-auto bg-[#122e25] dark:bg-[#000000] text-[#d6e3da] dark:text-[#d0e5db] border-t border-[#0d221c] dark:border-[#0e2116] font-sans transition-colors">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 lg:py-16">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-10 lg:gap-14 pb-12 border-b border-white/10 dark:border-[#102418]">
                
                <!-- Brand statement -->
                <div class="md:col-span-5">
                    <div class="flex items-center gap-3.5 mb-4">
                        <span class="w-10 h-10 rounded-xl bg-white/10 dark:bg-[#00ff88]/10 border border-white/15 dark:border-[#00ff88]/30 text-[#e0efe5] dark:text-[#00ff88] flex items-center justify-center shadow-inner">
                            <svg class="w-5 h-5 text-[#8acba2] dark:text-[#00ff88]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                                <path d="m9 12 2 2 4-4"/>
                            </svg>
                        </span>
                        <div>
                            <span class="font-serif text-2xl font-medium text-white tracking-tight leading-none block">
                                <?= htmlspecialchars($site_name, ENT_QUOTES, 'UTF-8') ?>
                            </span>
                            <span class="text-[9.5px] font-bold tracking-[0.16em] uppercase text-[#7ba58c] dark:text-[#00ff88] mt-1 block">
                                TRUSTED SECURITY
                            </span>
                        </div>
                    </div>
                    <p class="text-sm text-[#9db2a5] dark:text-[#8ea99b] leading-relaxed max-w-sm mt-3">
                        Thoughtfully crafted account access with strict privacy and cryptographic integrity. Designed for human clarity, free from artificial fluff.
                    </p>
                </div>

                <!-- Navigation Links -->
                <div class="md:col-span-3">
                    <h3 class="text-xs font-bold tracking-[0.16em] uppercase text-[#7da38d] dark:text-[#00ff88] mb-4">Navigation</h3>
                    <ul class="space-y-2.5 text-sm">
                        <li><a href="<?= SITE_URL ?>/" class="text-[#c7d9ce] hover:text-white dark:hover:text-[#00ff88] transition-colors">Home</a></li>
                        <li><a href="<?= SITE_URL ?>/about" class="text-[#c7d9ce] hover:text-white dark:hover:text-[#00ff88] transition-colors">About</a></li>
                        <li><a href="<?= SITE_URL ?>/service-areas" class="text-[#c7d9ce] hover:text-white dark:hover:text-[#00ff88] transition-colors">Service Locations</a></li>
                        <li><a href="<?= SITE_URL ?>/contact" class="text-[#c7d9ce] hover:text-white dark:hover:text-[#00ff88] transition-colors">Contact Support</a></li>
                    </ul>
                </div>

                <!-- Security & Access -->
                <div class="md:col-span-4">
                    <h3 class="text-xs font-bold tracking-[0.16em] uppercase text-[#7da38d] dark:text-[#00ff88] mb-4">Security Standards</h3>
                    <div class="space-y-3 text-xs text-[#9eb5a7] dark:text-[#8ea99b]">
                        <div class="flex items-start gap-2.5">
                            <span class="text-[#5fa879] dark:text-[#00ff88] mt-0.5">✓</span>
                            <span>Bcrypt-hashed credentials & strict session isolation</span>
                        </div>
                        <div class="flex items-start gap-2.5">
                            <span class="text-[#5fa879] dark:text-[#00ff88] mt-0.5">✓</span>
                            <span>Anti-CSRF protection across all transactional forms</span>
                        </div>
                        <div class="flex items-start gap-2.5">
                            <span class="text-[#5fa879] dark:text-[#00ff88] mt-0.5">✓</span>
                            <span>Zero trackers, no data reselling, human-built interfaces</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom legal bar -->
            <div class="pt-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-[#7f998b] dark:text-[#6a8b7a]">
                <p>© <?= date('Y') ?> <?= htmlspecialchars($site_name, ENT_QUOTES, 'UTF-8') ?>. All rights reserved.</p>
                <div class="flex items-center gap-6">
                    <span class="inline-flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-[#52a16f] dark:bg-[#00ff88] dark:shadow-[0_0_8px_#00ff88]"></span>
                        Systems operational
                    </span>
                    <a href="<?= SITE_URL ?>/shipping-policy" class="hover:text-white dark:hover:text-[#00ff88] transition-colors">Policy</a>
                </div>
            </div>
        </div>
    </footer>
</body>

</html>
