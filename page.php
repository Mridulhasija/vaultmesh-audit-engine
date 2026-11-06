<?php
require_once __DIR__ . '/include/header_config.php';
$pageCanonical = SITE_URL . '/' . $router;

include('components/header.php');

$pageHeroTitle = trim((string) ($respage->title ?? 'Information')) ?: 'Information';
include __DIR__ . '/components/page-hero.php';
?>

<main class="bg-gray-50 px-4 py-12 sm:px-6 md:py-16 lg:px-8">
    <article class="content mx-auto max-w-5xl rounded-2xl border border-gray-100 bg-white px-6 py-8 text-secondary-700 shadow-lg shadow-primary-900/5 sm:px-10 sm:py-12 lg:px-14">
        <?= $respage->description ?? '' ?>
    </article>
</main>

<?php include('components/footer.php'); ?>
