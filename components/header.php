<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title><?= htmlspecialchars((string) ($meta_title ?? $site_name ?? ''), ENT_QUOTES, 'UTF-8') ?></title>
    <meta name="keywords" content="<?= htmlspecialchars((string) ($meta_keyword ?? ''), ENT_QUOTES, 'UTF-8') ?>">
    <meta name="description" content="<?= htmlspecialchars((string) ($meta_desc ?? ''), ENT_QUOTES, 'UTF-8') ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Newsreader:ital,opsz,wght@0,6..72,400;0,6..72,500;0,6..72,600;1,6..72,400;1,6..72,500&family=Plus+Jakarta+Sans:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">

    <?php if (!empty($pageCanonical)): ?>
        <link rel="canonical" href="<?= htmlspecialchars($pageCanonical, ENT_QUOTES, 'UTF-8') ?>">
    <?php endif; ?>

    <?php if (!empty($schema)): ?>
        <script type="application/ld+json">
            <?= json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>
        </script>
    <?php endif; ?>

    <!-- Immediate Theme Initialization (prevents flash of unstyled theme) -->
    <script>
        (function() {
            try {
                const storedTheme = localStorage.getItem('theme');
                const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                if (storedTheme === 'dark' || (!storedTheme && systemPrefersDark)) {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            } catch (e) {}
        })();
    </script>

    <script src="<?= SITE_URL ?>/assets/js/tailwind.js"></script>
    <?php include __DIR__ . '/tailwind-config.php'; ?>
    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/style.css?v=<?= (int) filemtime(__DIR__ . '/../assets/css/style.css') ?>">

    <script src="<?= SITE_URL ?>/assets/js/navbar.js?v=<?= (int) filemtime(__DIR__ . '/../assets/js/navbar.js') ?>"></script>

    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/intlTelInput/style.css" />
    <script src="<?= SITE_URL ?>/assets/intlTelInput/main.js"></script>
    <script src="<?= SITE_URL ?>/assets/js/contact-form.js"></script>
</head>

<body class="bg-[#f6f8f5] dark:bg-[#000000] text-[#18392f] dark:text-[#f0fbf4] antialiased selection:bg-[#d6e8dd] dark:selection:bg-[#00ff88] selection:text-[#123328] dark:selection:text-[#000000] transition-colors duration-200">

    <!-- Top architectural micro-kicker bar (discreet trust signature) -->
    <div class="site-kicker-bar bg-[#eef3ef] dark:bg-[#020503] border-b border-[#e2eae4] dark:border-[#0e2116] text-[11px] text-[#556e62] dark:text-[#7d9b8b] tracking-wider py-1.5 px-4 hidden md:block transition-colors">
        <div class="max-w-7xl mx-auto flex items-center justify-between font-sans">
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center gap-1.5 font-semibold text-[#1f4236] dark:text-[#00ff88]">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#4fa572] dark:bg-[#00ff88] inline-block animate-pulse shadow-[0_0_8px_#00ff88]"></span>
                    SECURE PROTOCOL ACTIVE
                </span>
                <span class="text-[#b9c9be] dark:text-[#1d3d2c]">·</span>
                <span>FIDO2 / WebAuthn &amp; AES-256-GCM Enclave</span>
            </div>
            <div class="flex items-center gap-4 text-[10.5px]">
                <span class="text-[#647c70] dark:text-[#728f80]">Zero third-party trackers</span>
                <span class="text-[#b9c9be] dark:text-[#1d3d2c]">·</span>
                <a href="<?= SITE_URL ?>/about" class="text-[#2c4e40] dark:text-[#00ff88] hover:underline font-medium transition-colors">Why Behoof?</a>
            </div>
        </div>
    </div>

    <!-- Main Navigation Header -->
    <nav class="site-header top-0 sticky z-50 bg-[#f6f8f5]/95 dark:bg-[#000000]/95 backdrop-blur-md border-b border-[#e1e7e2] dark:border-[#102418] transition-colors">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between min-h-[80px] md:min-h-[86px] py-3 gap-3 lg:gap-6">

                <!-- Brand / Logo Section (Non-collapsing) -->
                <div class="flex items-center flex-shrink-0">
                    <a href="<?= SITE_URL ?>/" class="brand-emblem-wrap group flex items-center gap-3.5" aria-label="<?= htmlspecialchars($site_name, ENT_QUOTES, 'UTF-8') ?> Homepage">
                        <?php if (!empty($configData['logo'])): ?>
                            <img src="<?= $logo ?>" class="h-10 md:h-11 w-auto object-contain flex-shrink-0" alt="<?= htmlspecialchars($site_name, ENT_QUOTES, 'UTF-8') ?> Logo" />
                        <?php else: ?>
                            <span class="brand-emblem flex-shrink-0 w-10 h-10 md:w-11 md:h-11 rounded-xl bg-gradient-to-br from-[#1d4d40] to-[#12332a] dark:from-[#092418] dark:to-[#000000] text-[#f2f7f3] dark:text-[#00ff88] flex items-center justify-center shadow-md shadow-[#173f35]/25 dark:shadow-[0_0_20px_rgba(0,255,136,0.22)] border border-white/15 dark:border-[#00ff88]/30">
                                <svg class="w-5 h-5 text-[#d2e7d8] dark:text-[#00ff88]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                                    <path d="m9 12 2 2 4-4"/>
                                </svg>
                            </span>
                            <div class="flex flex-col flex-shrink-0">
                                <span class="brand-wordmark font-serif text-[23px] md:text-[25px] font-medium tracking-tight text-[#17372d] dark:text-white leading-none group-hover:text-[#0f2820] dark:group-hover:text-[#00ff88] transition-colors">
                                    <?= htmlspecialchars($site_name, ENT_QUOTES, 'UTF-8') ?>
                                </span>
                                <span class="brand-tagline text-[9px] font-bold tracking-[0.18em] uppercase text-[#667c70] dark:text-[#7f998b] mt-1 flex items-center gap-1.5 font-sans">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#5fa879] dark:bg-[#00ff88] inline-block dark:shadow-[0_0_6px_#00ff88]"></span>
                                    TRUSTED ACCESS
                                </span>
                            </div>
                        <?php endif; ?>
                    </a>
                </div>

                <!-- Desktop Navigation Menu (Flexible, comfortable spacing) -->
                <div class="site-header-links hidden xl:flex items-center justify-center flex-1 min-w-0 px-2 xl:px-4 overflow-hidden">
                    <ul class="nav-menu flex items-center gap-1 xl:gap-2 font-medium flex-nowrap">
                        <li>
                            <a href="<?= SITE_URL ?>/" class="nav-link inline-block whitespace-nowrap">
                                Home
                            </a>
                        </li>
                        <li>
                            <a href="<?= SITE_URL ?>/about" class="nav-link inline-block whitespace-nowrap">
                                About
                            </a>
                        </li>

                        <!-- Dynamic Dropdowns / Categories -->
                        <?php
                        $categories = $getdata->fetch('tbl_product_category', "parent_id='0' ORDER BY created_at");
                        if (!empty($categories)):
                            $totalCats = count($categories);
                            $catIdx = 0;
                            foreach ($categories as $category):
                                $catIdx++;
                                $isNearRight = ($catIdx >= $totalCats - 1);
                                $categoryUrl = SITE_URL . (isset($location) ? "/$location/{$category['url']}/" : "/{$category['url']}");
                                $subcategories = $getdata->fetch('tbl_product_category', "parent_id='{$category['id']}' ORDER BY created_at");

                                if (!empty($subcategories)): ?>
                                    <li class="dropdown relative">
                                        <div class="dropdown-link flex items-center gap-1.5 nav-link cursor-pointer select-none whitespace-nowrap" aria-haspopup="true" aria-expanded="false">
                                            <a href="<?= $categoryUrl ?>" class="hover:underline"><?= htmlspecialchars($category['title'], ENT_QUOTES, 'UTF-8') ?></a>
                                            <span class="dropdown-toggle inline-flex items-center p-0.5 rounded hover:bg-[#173f35]/10 dark:hover:bg-[#00ff88]/15">
                                                <svg class="w-3.5 h-3.5 text-[#5f786d] dark:text-[#00ff88] transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                                </svg>
                                            </span>
                                        </div>
                                        <div class="dropdown-menu nav-dropdown-menu hidden <?= $isNearRight ? 'dropdown-menu-right' : '' ?>">
                                            <ul class="py-1">
                                                <?php foreach ($subcategories as $subcategory):
                                                    $subcategoryUrl = SITE_URL . (isset($location) ? "/$location/{$subcategory['url']}/" : "/{$subcategory['url']}");
                                                ?>
                                                    <li>
                                                        <a href="<?= $subcategoryUrl ?>" class="nav-dropdown-item flex items-center gap-2.5">
                                                            <span class="w-1.5 h-1.5 rounded-full bg-[#52a16f]/60 dark:bg-[#00ff88] inline-block flex-shrink-0 dark:shadow-[0_0_6px_#00ff88]"></span>
                                                            <span class="truncate"><?= htmlspecialchars($subcategory['title'], ENT_QUOTES, 'UTF-8') ?></span>
                                                        </a>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                    </li>
                                <?php else: ?>
                                    <li>
                                        <a href="<?= $categoryUrl ?>" class="nav-link inline-block whitespace-nowrap">
                                            <?= htmlspecialchars($category['title'], ENT_QUOTES, 'UTF-8') ?>
                                        </a>
                                    </li>
                                <?php endif;
                            endforeach;
                        endif; ?>
                    </ul>
                </div>

                <!-- Right Side Actions & Mobile Toggle (Strictly Flex-Shrink-0 to prevent overlapping) -->
                <div class="site-header-actions flex items-center gap-2 sm:gap-2.5 justify-end flex-shrink-0">

                    <!-- Dark Mode Toggle Button (Desktop & Tablet) -->
                    <button type="button" class="dark-mode-toggle-btn flex-shrink-0" data-theme-toggle aria-label="Toggle dark mode" title="Toggle dark mode">
                        <!-- Moon Icon (Light mode default) -->
                        <svg class="w-4 h-4 theme-icon-moon" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                        </svg>
                        <!-- Sun Icon (Dark mode active) -->
                        <svg class="w-4 h-4 theme-icon-sun hidden text-[#00e575]" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </button>

                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="<?= SITE_URL ?>/account" class="header-btn-account flex-shrink-0 whitespace-nowrap" title="View your account dashboard">
                            <span class="avatar-dot" aria-hidden="true"><?= strtoupper(substr((string)($_SESSION['user_name'] ?? 'U'), 0, 1)) ?></span>
                            <span class="hidden sm:inline">My account</span>
                        </a>
                    <?php else: ?>
                        <!-- Refined Minimalist Executive Login Button -->
                        <a href="<?= SITE_URL ?>/login" class="header-btn-secondary hidden sm:inline-flex flex-shrink-0 whitespace-nowrap" aria-label="Log in">
                            <span>Log in</span>
                        </a>

                        <!-- Executive Register Button -->
                        <a href="<?= SITE_URL ?>/register" class="header-btn-primary group hidden sm:inline-flex flex-shrink-0 whitespace-nowrap" aria-label="Register an account">
                            <span>Register</span>
                            <svg class="w-3.5 h-3.5 transition-transform duration-200 group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </a>
                    <?php endif; ?>

                    <!-- Mobile Menu Hamburger Button -->
                    <button data-collapse-toggle="navbar-sticky" type="button"
                        class="inline-flex items-center justify-center p-2.5 w-10 h-10 text-[#204033] dark:text-[#e4e4e7] rounded-xl xl:hidden hover:bg-[#ebf2ec] dark:hover:bg-white/5 border border-[#dce5de] dark:border-white/10 focus:outline-none focus:ring-2 focus:ring-[#173f35]/25 dark:focus:ring-[#00e575]/40 transition-colors flex-shrink-0 ml-1"
                        aria-controls="navbar-sticky" aria-expanded="false" aria-label="Toggle navigation menu">
                        <span class="sr-only">Open main menu</span>
                        <svg class="w-5 h-5" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Collapsible Mobile Menu Drawer -->
            <div id="navbar-sticky" class="hidden xl:hidden pb-6">
                <div class="mobile-nav-panel">

                    <!-- Mobile Appearance & Dark Mode Toggle -->
                    <div class="flex items-center justify-between pb-3.5 mb-3.5 border-b border-[#edf2ee] dark:border-[#12261a]">
                        <span class="text-xs font-semibold uppercase tracking-wider text-[#526b5e] dark:text-[#7f998b]">Appearance</span>
                        <button type="button" class="mobile-dark-mode-toggle inline-flex items-center gap-2 py-1.5 px-3 rounded-lg text-xs font-semibold border border-[#dce5de] dark:border-[#153423] bg-[#f8faf8] dark:bg-[#070d0a] text-[#1c3f33] dark:text-[#00ff88] hover:border-[#173f35]/30 dark:hover:border-[#00ff88] transition-colors" data-theme-toggle aria-label="Toggle dark mode">
                            <svg class="w-3.5 h-3.5 theme-icon-moon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
                            <svg class="w-3.5 h-3.5 theme-icon-sun hidden text-[#00ff88]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                            <span class="theme-label-text">Dark Mode</span>
                        </button>
                    </div>

                    <ul class="nav-menu flex flex-col space-y-1 font-medium">
                        <li>
                            <a href="<?= SITE_URL ?>/" class="block py-2.5 px-3.5 rounded-lg text-[#1e3a2f] dark:text-[#d3ebdf] hover:bg-[#f3f7f4] dark:hover:bg-[#00ff88]/10 dark:hover:text-[#00ff88] transition-colors">
                                Home
                            </a>
                        </li>
                        <li>
                            <a href="<?= SITE_URL ?>/about" class="block py-2.5 px-3.5 rounded-lg text-[#1e3a2f] dark:text-[#d3ebdf] hover:bg-[#f3f7f4] dark:hover:bg-[#00ff88]/10 dark:hover:text-[#00ff88] transition-colors">
                                About
                            </a>
                        </li>
                        <?php if (!empty($categories)):
                            foreach ($categories as $category):
                                $categoryUrl = SITE_URL . (isset($location) ? "/$location/{$category['url']}/" : "/{$category['url']}");
                                $subcategories = $getdata->fetch('tbl_product_category', "parent_id='{$category['id']}' ORDER BY created_at");

                                if (!empty($subcategories)): ?>
                                    <li class="dropdown border-t border-[#edf2ee] dark:border-[#12261a] pt-1 mt-1">
                                        <div class="dropdown-link flex items-center justify-between w-full py-2.5 px-3.5 rounded-lg text-[#1e3a2f] dark:text-[#d3ebdf] hover:bg-[#f3f7f4] dark:hover:bg-[#00ff88]/10 dark:hover:text-[#00ff88] cursor-pointer transition-colors">
                                            <a href="<?= $categoryUrl ?>"><?= htmlspecialchars($category['title'], ENT_QUOTES, 'UTF-8') ?></a>
                                            <span class="dropdown-toggle p-1">
                                                <svg class="w-4 h-4 text-[#60796d] dark:text-[#00ff88] transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                                </svg>
                                            </span>
                                        </div>
                                        <div class="dropdown-menu hidden pl-4 pr-2 py-1 space-y-1 bg-[#f9fbf9] dark:bg-[#050907] border border-transparent dark:border-[#12261a] rounded-lg">
                                            <?php foreach ($subcategories as $subcategory):
                                                $subcategoryUrl = SITE_URL . (isset($location) ? "/$location/{$subcategory['url']}/" : "/{$subcategory['url']}");
                                            ?>
                                                <a href="<?= $subcategoryUrl ?>" class="block py-2 px-3 text-sm text-[#385246] dark:text-[#a0c5b2] rounded-md hover:bg-[#eef4f0] dark:hover:bg-[#00ff88]/15 dark:hover:text-[#00ff88] transition-colors">
                                                    <?= htmlspecialchars($subcategory['title'], ENT_QUOTES, 'UTF-8') ?>
                                                </a>
                                            <?php endforeach; ?>
                                        </div>
                                    </li>
                                <?php else: ?>
                                    <li>
                                        <a href="<?= $categoryUrl ?>" class="block py-2.5 px-3.5 rounded-lg text-[#1e3a2f] dark:text-[#d3ebdf] hover:bg-[#f3f7f4] dark:hover:bg-[#00ff88]/10 dark:hover:text-[#00ff88] transition-colors">
                                            <?= htmlspecialchars($category['title'], ENT_QUOTES, 'UTF-8') ?>
                                        </a>
                                    </li>
                                <?php endif;
                            endforeach;
                        endif; ?>
                    </ul>

                    <div class="mt-4 pt-4 border-t border-[#edf2ee] dark:border-[#12261a] flex flex-col gap-2.5">
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <a href="<?= SITE_URL ?>/account" class="block text-center py-3 px-4 bg-[#173f35] dark:bg-[#00ff88] text-white dark:text-black font-bold rounded-xl shadow-sm dark:shadow-[0_0_20px_rgba(0,255,136,0.4)] transition-all">
                                Go to My Account →
                            </a>
                        <?php else: ?>
                            <a href="<?= SITE_URL ?>/register" class="block text-center py-3.5 px-4 bg-[#173f35] dark:bg-[#00ff88] text-white dark:text-black font-bold rounded-xl shadow-md dark:shadow-[0_0_22px_rgba(0,255,136,0.45)] hover:opacity-95 transition-all">
                                Register / Create Account →
                            </a>
                            <a href="<?= SITE_URL ?>/login" class="block text-center py-2.5 px-4 text-[#204033] dark:text-[#e4f5ec] hover:bg-[#f3f7f4] dark:hover:bg-[#00ff88]/10 hover:text-[#00ff88] font-semibold rounded-xl border border-[#dce5de] dark:border-[rgba(0,255,136,0.3)] bg-white dark:bg-[#050806] transition-all">
                                Log in
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </nav>
