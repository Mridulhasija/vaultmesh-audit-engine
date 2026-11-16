<?php
$heroTitle = trim((string) ($pageHeroTitle ?? ''));
?>
<section class="relative overflow-hidden bg-[#153a30] dark:bg-[#000000] text-[#f4f7f4] px-4 py-16 sm:py-20 md:py-24 border-b border-[#0e2720] dark:border-[#0e2116] transition-colors">
    <!-- Ambient architectural craft linework -->
    <div class="pointer-events-none absolute inset-0 opacity-10">
        <svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <pattern id="hero-grid" width="40" height="40" patternUnits="userSpaceOnUse">
                    <path d="M 40 0 L 0 0 0 40" fill="none" stroke="currentColor" stroke-width="0.75" />
                </pattern>
            </defs>
            <rect width="100%" height="100%" fill="url(#hero-grid)" />
        </svg>
    </div>
    <div class="pointer-events-none absolute -top-24 -right-24 w-96 h-96 rounded-full bg-[#276652]/30 dark:bg-[#00ff88]/10 blur-3xl"></div>
    <div class="pointer-events-none absolute -bottom-24 -left-24 w-96 h-96 rounded-full bg-[#0d221c]/40 dark:bg-[#003319]/30 blur-3xl"></div>

    <div class="relative max-w-4xl mx-auto text-center">
        <span class="inline-flex items-center gap-2 text-[10.5px] font-bold tracking-[0.2em] uppercase text-[#a5c6b4] dark:text-[#00ff88] mb-4 bg-white/5 dark:bg-[#00ff88]/10 border border-white/10 dark:border-[#00ff88]/30 px-3.5 py-1.5 rounded-full">
            <i class="w-1.5 h-1.5 rounded-full bg-[#67b785] dark:bg-[#00ff88] inline-block dark:shadow-[0_0_8px_#00ff88]"></i>
            AUTHENTIC ACCESS · ARCHITECTURE
        </span>
        <h1 class="font-serif text-3xl sm:text-5xl md:text-6xl font-medium tracking-tight text-white mb-4">
            <?= htmlspecialchars($heroTitle, ENT_QUOTES, 'UTF-8') ?>
        </h1>
        <div class="flex items-center justify-center gap-2 text-xs text-[#a0b8ab] dark:text-[#8baea0] font-sans tracking-wide">
            <a href="<?= SITE_URL ?>/" class="hover:text-white dark:hover:text-[#00ff88] transition-colors">Home</a>
            <span class="text-[#456b5b] dark:text-[#00ff88]/60">/</span>
            <span class="text-[#e2ece5] dark:text-[#ffffff] font-medium"><?= htmlspecialchars($heroTitle, ENT_QUOTES, 'UTF-8') ?></span>
        </div>
    </div>
</section>
