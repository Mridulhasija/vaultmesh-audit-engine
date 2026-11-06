<?php
$heroTitle = trim((string) ($pageHeroTitle ?? ''));
?>
<section class="bg-gray-400 bg-center bg-cover bg-no-repeat bg-blend-multiply px-4 py-20" style="background-image: url('<?= htmlspecialchars(SITE_URL, ENT_QUOTES, 'UTF-8') ?>/assets/img/background.png');">
    <h1 class="text-center text-4xl font-bold text-white sm:text-5xl">
        <?= htmlspecialchars($heroTitle, ENT_QUOTES, 'UTF-8') ?>
    </h1>
</section>
