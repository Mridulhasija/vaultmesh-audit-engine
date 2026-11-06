<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title><?= htmlspecialchars((string) ($meta_title ?? $site_name ?? ''), ENT_QUOTES, 'UTF-8') ?></title>
    <meta name="keywords" content="<?= htmlspecialchars((string) ($meta_keyword ?? ''), ENT_QUOTES, 'UTF-8') ?>">
    <meta name="description" content="<?= htmlspecialchars((string) ($meta_desc ?? ''), ENT_QUOTES, 'UTF-8') ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

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

<body>

    <!-- navbar -->
    <nav class="top-0 sticky bg-white border-gray-200 z-50 shadow-md">
        <div class="flex flex-wrap items-center justify-between max-w-screen-xl mx-auto px-2 py-3">
            <a href="<?= SITE_URL ?>/" class="flex items-center space-x-3 rtl:space-x-reverse md:w-1/4">
                <?php if (!empty($configData['logo'])): ?>
                    <img src="<?= $logo ?>" class="h-10" alt="<?= $site_name ?> Logo" />
                <?php else: ?>
                    <span class="text-xl font-bold text-gray-800"><?= $site_name ?></span>
                <?php endif; ?>
            </a>

            <div class="flex items-center md:order-2 space-x-1 md:space-x-2 rtl:space-x-reverse md:w-1/4 justify-end">
                <a href="<?= SITE_URL ?>/contact"
                    class="text-white bg-primary-500 hover:bg-primary-700 focus:ring-4 focus:ring-primary-300 font-medium rounded-lg text-sm px-4 py-2 md:px-5 md:py-2.5">
                    Contact
                </a>
                <button data-collapse-toggle="navbar-sticky" type="button"
                    class="inline-flex items-center p-2 w-10 h-10 justify-center text-sm text-gray-500 rounded-lg md:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200"
                    aria-controls="navbar-sticky" aria-expanded="false">
                    <span class="sr-only">Open main menu</span>
                    <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 17 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M1 1h15M1 7h15M1 13h15" />
                    </svg>
                </button>
            </div>
            <div id="navbar-sticky" class="md:w-2/4 items-center justify-center text-secondary-700 hidden w-full md:flex md:order-1">
                <ul
                    class="nav-menu flex flex-col md:items-center mt-4 font-medium md:flex-row md:mt-0 md:space-x-8 rtl:space-x-reverse">
                    <li>
                        <a href="<?= SITE_URL ?>/"
                            class="block py-2 px-3 border-b border-gray-100 hover:bg-gray-50 md:hover:bg-transparent md:border-0 md:hover:text-primary-600 md:p-0">
                            Home
                        </a>
                    </li>
                    <li>
                        <a href="<?= SITE_URL ?>/about"
                            class="block py-2 px-3 border-b border-gray-100 hover:bg-gray-50 md:hover:bg-transparent md:border-0 md:hover:text-primary-600 md:p-0">
                            About
                        </a>
                    </li>
                    <!-- Dropdowns -->
                    <?php
                    $categories = $getdata->fetch('tbl_product_category', "parent_id='0' ORDER BY created_at");
                    foreach ($categories as $category):
                        $categoryUrl = SITE_URL . (isset($location) ? "/$location/{$category['url']}/" : "/{$category['url']}");
                        $subcategories = $getdata->fetch('tbl_product_category', "parent_id='{$category['id']}' ORDER BY created_at");

                        if (!empty($subcategories)): ?>
                            <li class="dropdown relative">
                                <div class="flex items-center justify-between w-full px-3 py-2 rounded hover:text-primary-700 dropdown-link">
                                    <a href="<?= $categoryUrl ?>"><?= $category['title'] ?></a>
                                    <span class="dropdown-toggle cursor-pointer">
                                        <svg class="w-2 h-2 ml-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4" />
                                        </svg>
                                    </span>
                                </div>
                                <div class="dropdown-menu absolute left-0 z-10 hidden font-normal bg-white rounded-lg shadow w-44 divide-y divide-gray-100">
                                    <ul class="py-2 text-sm">
                                        <?php
                                        foreach ($subcategories as $subcategory):
                                            $subcategoryUrl = SITE_URL . (isset($location) ? "/$location/{$subcategory['url']}/" : "/{$subcategory['url']}");
                                        ?>
                                            <li>
                                                <a href="<?= $subcategoryUrl ?>" class="block px-4 py-2 text-black hover:bg-gray-100">
                                                    <?= $subcategory['title'] ?>
                                                </a>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </li>
                        <?php else: ?>
                            <li>
                                <a href="<?= $categoryUrl ?>"
                                    class="block py-2 px-3 border-b border-gray-100 hover:bg-gray-50 md:hover:bg-transparent md:border-0 md:hover:text-primary-600 md:p-0">
                                    <?= $category['title'] ?>
                                </a>
                            </li>
                    <?php endif;
                    endforeach; ?>
                </ul>
            </div>
        </div>
    </nav>