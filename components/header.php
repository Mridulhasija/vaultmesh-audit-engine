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
    <link href="https://fonts.googleapis.com/css2?family=Newsreader:ital,opsz,wght@0,6..72,400;0,6..72,500;0,6..72,600;1,6..72,400;1,6..72,500&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <?php if (!empty($pageCanonical)): ?>
        <link rel="canonical" href="<?= htmlspecialchars($pageCanonical, ENT_QUOTES, 'UTF-8') ?>">
    <?php endif; ?>

    <?php if (!empty($schema)): ?>
        <script type="application/ld+json">
            <?= json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>
        </script>
    <?php endif; ?>

    <script src="<?= SITE_URL ?>/assets/js/tailwind.js"></script>
    <?php include __DIR__ . '/tailwind-config.php'; ?>
    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/style.css">

    <script src="<?= SITE_URL ?>/assets/js/navbar.js"></script>

    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/intlTelInput/style.css" />
    <script src="<?= SITE_URL ?>/assets/intlTelInput/main.js"></script>
    <script src="<?= SITE_URL ?>/assets/js/contact-form.js"></script>
</head>

<body class="bg-[#f6f8f5] text-[#18392f] antialiased">

    <!-- Top architectural micro-kicker bar (discreet trust signature) -->
    <div class="bg-[#eef3ef] border-b border-[#e2eae4] text-[11px] text-[#556e62] tracking-wider py-1.5 px-4 hidden md:block">
        <div class="max-w-7xl mx-auto flex items-center justify-between font-sans">
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center gap-1.5 font-semibold text-[#1f4236]">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#4fa572] inline-block"></span>
                    SECURE PROTOCOL ACTIVE
                </span>
                <span class="text-[#b9c9be]">·</span>
                <span>End-to-end encrypted authentication</span>
            </div>
            <div class="flex items-center gap-4 text-[10.5px]">
                <span class="text-[#647c70]">Privacy by design</span>
                <span class="text-[#b9c9be]">·</span>
                <a href="<?= SITE_URL ?>/about" class="text-[#2c4e40] hover:underline font-medium">Why Behoof?</a>
            </div>
        </div>
    </div>

    <!-- Main Navigation Header -->
    <nav class="site-header top-0 sticky z-50 bg-[#f6f8f5]/95 backdrop-blur-md border-b border-[#e1e7e2]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between min-h-[82px] md:min-h-[90px] py-3.5 md:py-4 gap-4">
                
                <!-- Brand / Logo Section -->
                <div class="flex items-center">
                    <a href="<?= SITE_URL ?>/" class="brand-emblem-wrap group flex items-center gap-3.5" aria-label="<?= htmlspecialchars($site_name, ENT_QUOTES, 'UTF-8') ?> Homepage">
                        <?php if (!empty($configData['logo'])): ?>
                            <img src="<?= $logo ?>" class="h-11 md:h-12 w-auto object-contain" alt="<?= htmlspecialchars($site_name, ENT_QUOTES, 'UTF-8') ?> Logo" />
                        <?php else: ?>
                            <span class="brand-emblem flex-shrink-0 w-11 h-11 md:w-12 md:h-12 rounded-xl bg-gradient-to-br from-[#1d4d40] to-[#12332a] text-[#f2f7f3] flex items-center justify-center shadow-md shadow-[#173f35]/25 border border-white/15">
                                <svg class="w-6 h-6 text-[#d2e7d8]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                                    <path d="m9 12 2 2 4-4"/>
                                </svg>
                            </span>
                            <div class="flex flex-col">
                                <span class="brand-wordmark font-serif text-[24px] md:text-[27px] font-medium tracking-tight text-[#17372d] leading-none group-hover:text-[#0f2820] transition-colors">
                                    <?= htmlspecialchars($site_name, ENT_QUOTES, 'UTF-8') ?>
                                </span>
                                <span class="brand-tagline text-[9.5px] font-bold tracking-[0.18em] uppercase text-[#667c70] mt-1.5 flex items-center gap-1.5 font-sans">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#5fa879] inline-block"></span>
                                    TRUSTED ACCESS
                                </span>
                            </div>
                        <?php endif; ?>
                    </a>
                </div>

                <!-- Desktop Navigation Menu -->
                <div class="hidden md:flex items-center justify-center flex-1 px-4 lg:px-8">
                    <ul class="nav-menu flex items-center gap-1.5 lg:gap-2 font-medium">
                        <li>
                            <a href="<?= SITE_URL ?>/" class="nav-link inline-block">
                                Home
                            </a>
                        </li>
                        <li>
                            <a href="<?= SITE_URL ?>/about" class="nav-link inline-block">
                                About
                            </a>
                        </li>

                        <!-- Dynamic Dropdowns / Categories -->
                        <?php
                        $categories = $getdata->fetch('tbl_product_category', "parent_id='0' ORDER BY created_at");
                        if (!empty($categories)):
                            foreach ($categories as $category):
                                $categoryUrl = SITE_URL . (isset($location) ? "/$location/{$category['url']}/" : "/{$category['url']}");
                                $subcategories = $getdata->fetch('tbl_product_category', "parent_id='{$category['id']}' ORDER BY created_at");

                                if (!empty($subcategories)): ?>
                                    <li class="dropdown relative">
                                        <div class="dropdown-link flex items-center gap-1.5 nav-link cursor-pointer" aria-haspopup="true" aria-expanded="false">
                                            <a href="<?= $categoryUrl ?>" class="hover:underline"><?= htmlspecialchars($category['title'], ENT_QUOTES, 'UTF-8') ?></a>
                                            <span class="dropdown-toggle inline-flex items-center">
                                                <svg class="w-3.5 h-3.5 text-[#5f786d] transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                                </svg>
                                            </span>
                                        </div>
                                        <div class="dropdown-menu nav-dropdown-menu hidden">
                                            <ul class="py-1">
                                                <?php foreach ($subcategories as $subcategory):
                                                    $subcategoryUrl = SITE_URL . (isset($location) ? "/$location/{$subcategory['url']}/" : "/{$subcategory['url']}");
                                                ?>
                                                    <li>
                                                        <a href="<?= $subcategoryUrl ?>" class="nav-dropdown-item">
                                                            <?= htmlspecialchars($subcategory['title'], ENT_QUOTES, 'UTF-8') ?>
                                                        </a>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                    </li>
                                <?php else: ?>
                                    <li>
                                        <a href="<?= $categoryUrl ?>" class="nav-link inline-block">
                                            <?= htmlspecialchars($category['title'], ENT_QUOTES, 'UTF-8') ?>
                                        </a>
                                    </li>
                                <?php endif;
                            endforeach;
                        endif; ?>
                    </ul>
                </div>

                <!-- Right Side Actions & Mobile Toggle -->
                <div class="flex items-center gap-2 sm:gap-3 justify-end">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="<?= SITE_URL ?>/account" class="header-btn-account" title="View your account dashboard">
                            <span class="avatar-dot" aria-hidden="true"><?= strtoupper(substr((string)($_SESSION['user_name'] ?? 'U'), 0, 1)) ?></span>
                            <span class="hidden sm:inline">My account</span>
                        </a>
                    <?php else: ?>
                        <a href="<?= SITE_URL ?>/login" class="header-btn-secondary">
                            Log in
                        </a>
                        <a href="<?= SITE_URL ?>/register" class="header-btn-primary group">
                            <span>Get started</span>
                            <svg class="w-4 h-4 transition-transform duration-200 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </a>
                    <?php endif; ?>

                    <!-- Mobile Menu Hamburger Button -->
                    <button data-collapse-toggle="navbar-sticky" type="button"
                        class="inline-flex items-center justify-center p-2.5 w-11 h-11 text-[#204033] rounded-xl md:hidden hover:bg-[#ebf2ec] border border-[#dce5de] focus:outline-none focus:ring-2 focus:ring-[#173f35]/25 transition-colors"
                        aria-controls="navbar-sticky" aria-expanded="false" aria-label="Toggle navigation menu">
                        <span class="sr-only">Open main menu</span>
                        <svg class="w-6 h-6" aria-hidden="true" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Collapsible Mobile Menu Drawer -->
            <div id="navbar-sticky" class="hidden md:hidden pb-6">
                <div class="mobile-nav-panel">
                    <ul class="nav-menu flex flex-col space-y-1 font-medium">
                        <li>
                            <a href="<?= SITE_URL ?>/" class="block py-2.5 px-3.5 rounded-lg text-[#1e3a2f] hover:bg-[#f3f7f4]">
                                Home
                            </a>
                        </li>
                        <li>
                            <a href="<?= SITE_URL ?>/about" class="block py-2.5 px-3.5 rounded-lg text-[#1e3a2f] hover:bg-[#f3f7f4]">
                                About
                            </a>
                        </li>
                        <?php if (!empty($categories)):
                            foreach ($categories as $category):
                                $categoryUrl = SITE_URL . (isset($location) ? "/$location/{$category['url']}/" : "/{$category['url']}");
                                $subcategories = $getdata->fetch('tbl_product_category', "parent_id='{$category['id']}' ORDER BY created_at");

                                if (!empty($subcategories)): ?>
                                    <li class="dropdown border-t border-[#edf2ee] pt-1 mt-1">
                                        <div class="dropdown-link flex items-center justify-between w-full py-2.5 px-3.5 rounded-lg text-[#1e3a2f] hover:bg-[#f3f7f4] cursor-pointer">
                                            <a href="<?= $categoryUrl ?>"><?= htmlspecialchars($category['title'], ENT_QUOTES, 'UTF-8') ?></a>
                                            <svg class="w-4 h-4 text-[#60796d]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                            </svg>
                                        </div>
                                        <div class="dropdown-menu hidden pl-4 pr-2 py-1 space-y-1 bg-[#f9fbf9] rounded-lg">
                                            <?php foreach ($subcategories as $subcategory):
                                                $subcategoryUrl = SITE_URL . (isset($location) ? "/$location/{$subcategory['url']}/" : "/{$subcategory['url']}");
                                            ?>
                                                <a href="<?= $subcategoryUrl ?>" class="block py-2 px-3 text-sm text-[#385246] rounded-md hover:bg-[#eef4f0]">
                                                    <?= htmlspecialchars($subcategory['title'], ENT_QUOTES, 'UTF-8') ?>
                                                </a>
                                            <?php endforeach; ?>
                                        </div>
                                    </li>
                                <?php else: ?>
                                    <li>
                                        <a href="<?= $categoryUrl ?>" class="block py-2.5 px-3.5 rounded-lg text-[#1e3a2f] hover:bg-[#f3f7f4]">
                                            <?= htmlspecialchars($category['title'], ENT_QUOTES, 'UTF-8') ?>
                                        </a>
                                    </li>
                                <?php endif;
                            endforeach;
                        endif; ?>
                    </ul>

                    <div class="mt-4 pt-4 border-t border-[#edf2ee] flex flex-col gap-2">
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <a href="<?= SITE_URL ?>/account" class="block text-center py-3 px-4 bg-[#173f35] text-white font-semibold rounded-lg">
                                Go to My Account
                            </a>
                        <?php else: ?>
                            <a href="<?= SITE_URL ?>/register" class="block text-center py-3 px-4 bg-[#173f35] text-white font-semibold rounded-lg shadow-sm">
                                Create an account →
                            </a>
                            <a href="<?= SITE_URL ?>/login" class="block text-center py-2.5 px-4 text-[#204033] hover:bg-[#f3f7f4] font-semibold rounded-lg">
                                Log in
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </nav>
