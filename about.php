<?php
require_once __DIR__ . '/include/header_config.php';
$pageCanonical = SITE_URL . '/about';
include('components/header.php');
$pageHeroTitle = trim((string) ($respage->title ?? 'About Us')) ?: 'About Us';
include __DIR__ . '/components/page-hero.php';
?>



<?php include('components/footer.php');?>
