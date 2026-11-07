<?php
require_once __DIR__ . '/include/header_config.php';
require_once __DIR__ . '/include/location-template.php';

// If location is set, validate it and do meta replacements
if (isset($location)) {
    // Checking if location is valid
    $stmt = $conn->prepare("SELECT title, data FROM tbl_location WHERE status = 1 AND url = ?");
    $stmt->bind_param("s", $location);
    $stmt->execute();

    // Bind the result to the $location_title variable
    $stmt->bind_result($location_title, $location_json);

    // Fetch the result
    $stmt->fetch();

    // Check if the result is valid
    if ($location_title === null) {
        $stmt->close();
        http_response_code(404);
        header("Location: " . SITE_URL);
        exit();
    }

    $stmt->close();

    $location_title = $location_title ?? '';
    $location_data = json_decode($location_json ?? '', true);
    $location_data = is_array($location_data) ? $location_data : [];

    $meta_title   = replaceLocationTemplate((string) ($meta_title ?? ''), $location_title, $location_data);
    $meta_keyword = replaceLocationTemplate((string) ($meta_keyword ?? ''), $location_title, $location_data);
    $meta_desc    = replaceLocationTemplate((string) ($meta_desc ?? ''), $location_title, $location_data);

    if (!empty($respage)) {
        $respage->title = replaceLocationTemplate((string) ($respage->title ?? ''), $location_title, $location_data);

        $respage->description = replaceLocationTemplate((string) ($respage->description ?? ''), $location_title, $location_data, null, true);
    }
}

$schemaSiteUrl = rtrim(SITE_URL, '/');
$pageCanonical = $schemaSiteUrl . (isset($location) ? '/' . $location . '/' : '/');
$schemaPageUrl = rtrim($pageCanonical, '/');
$organizationId = $schemaSiteUrl . '/#organization';
$websiteId = $schemaSiteUrl . '/#website';
$phone = trim((string) ($configData['phone_1'] ?? ''));
$phone = $phone !== '' ? $phone : trim((string) ($configData['phone_2'] ?? ''));
$address = trim((string) ($configData['address'] ?? ''));
$email = trim((string) ($configData['email_1'] ?? ''));
$serviceLocations = $getdata->fetch(
    'tbl_location',
    'status = 1 ORDER BY title'
) ?: [];
$productCategories = $getdata->fetch(
    'tbl_product_category',
    'status = 1 AND parent_id = 0 ORDER BY created_at'
) ?: [];

$schemaAreaServed = isset($location)
    ? [[
        '@type' => 'Place',
        'name' => $location_title,
    ]]
    : array_map(
        static fn (array $serviceLocation): array => [
            '@type' => 'Place',
            'name' => $serviceLocation['title'],
        ],
        $serviceLocations
    );

$schema = [
    '@context' => 'https://schema.org',
    '@graph' => [
        [
            '@type' => 'Organization',
            '@id' => $organizationId,
            'name' => $site_name,
            'url' => $schemaSiteUrl . '/',
            'description' => $meta_desc,
            'knowsAbout' => array_values(array_filter(array_map(
                static fn (array $category): string => trim((string) ($category['title'] ?? '')),
                $productCategories
            ))),
        ],
        [
            '@type' => 'WebSite',
            '@id' => $websiteId,
            'url' => $schemaSiteUrl . '/',
            'name' => $site_name,
            'description' => $meta_desc,
            'publisher' => ['@id' => $organizationId],
            'inLanguage' => 'en',
        ],
        [
            '@type' => 'WebPage',
            '@id' => $schemaPageUrl . '#webpage',
            'url' => $pageCanonical,
            'name' => $meta_title,
            'isPartOf' => ['@id' => $websiteId],
            'about' => ['@id' => $organizationId],
            'publisher' => ['@id' => $organizationId],
            'breadcrumb' => ['@id' => $schemaPageUrl . '#breadcrumb'],
            'description' => $meta_desc,
            'inLanguage' => 'en',
            'mainEntity' => ['@id' => $organizationId],
        ],
        [
            '@type' => 'LocalBusiness',
            '@id' => $schemaSiteUrl . '/#localbusiness',
            'name' => $site_name,
            'url' => $schemaSiteUrl . '/',
            'parentOrganization' => ['@id' => $organizationId],
            'areaServed' => $schemaAreaServed,
        ],
        [
            '@type' => 'BreadcrumbList',
            '@id' => $schemaPageUrl . '#breadcrumb',
            'itemListElement' => [[
                '@type' => 'ListItem',
                'position' => 1,
                'name' => 'Home',
                'item' => $schemaSiteUrl . '/',
            ]],
        ],
    ],
];

if (!empty($logo)) {
    $schema['@graph'][0]['logo'] = [
        '@type' => 'ImageObject',
        '@id' => $schemaSiteUrl . '/#logo',
        'url' => $logo,
        'contentUrl' => $logo,
    ];
    $schema['@graph'][3]['image'] = $logo;
    $schema['@graph'][3]['logo'] = $logo;
}

if ($address !== '') {
    $schema['@graph'][0]['address'] = [
        '@type' => 'PostalAddress',
        'streetAddress' => $address,
    ];
    $schema['@graph'][3]['address'] = [
        '@type' => 'PostalAddress',
        'streetAddress' => $address,
    ];
}

if ($phone !== '') {
    $schema['@graph'][0]['telephone'] = $phone;
    $schema['@graph'][0]['contactPoint'] = [
        '@type' => 'ContactPoint',
        'contactType' => 'sales',
        'telephone' => $phone,
        'url' => $schemaSiteUrl . '/contact',
    ];
    $schema['@graph'][3]['telephone'] = $phone;
}

if ($email !== '') {
    $schema['@graph'][0]['email'] = $email;
}

if (isset($location)) {
    $schema['@graph'][4]['itemListElement'][] = [
        '@type' => 'ListItem',
        'position' => 2,
        'name' => $location_title,
        'item' => $pageCanonical,
    ];
}
include('components/header.php');
?>



<main class="home-landing">
    <section class="home-hero">
        <div class="hero-copy">
            <span class="hero-label"><i></i> TRUSTED ACCESS, THOUGHTFULLY BUILT</span>
            <h1>Security that feels<br><em>simple.</em></h1>
            <p>A calmer, more considered way to access the services you rely on. Your account is protected from the first sign in.</p>
            <div class="hero-actions"><a class="hero-primary" href="<?= SITE_URL ?>/register">Create your account <span>→</span></a><a class="hero-secondary" href="<?= SITE_URL ?>/login">Already a member? Log in</a></div>
            <div class="hero-proof"><span class="proof-check">✓</span> Secure sessions <b>·</b> Encrypted passwords <b>·</b> Privacy first</div>
        </div>
        <div class="hero-art" aria-label="Abstract illustration representing secure account access">
            <div class="art-orbit orbit-one"></div><div class="art-orbit orbit-two"></div>
            <div class="art-card"><div class="art-card-head"><span>ACCOUNT SECURITY</span><span class="live-dot">● ACTIVE</span></div><div class="art-lock"><svg viewBox="0 0 64 64" fill="none" aria-hidden="true"><rect x="13" y="28" width="38" height="29" rx="7" stroke="currentColor" stroke-width="2.5"/><path d="M21 28v-8a11 11 0 0 1 22 0v8" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/><circle cx="32" cy="41" r="3" fill="currentColor"/><path d="M32 44v5" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/></svg></div><strong>Your account, protected.</strong><small>Security is part of every step.</small><div class="art-divider"></div><div class="art-status"><span><i></i> Password secured</span><span>ENCRYPTED</span></div></div>
            <div class="floating-tag tag-top"><span>✓</span> Private by design</div><div class="floating-tag tag-bottom"><span class="tag-shield">◆</span> Protected session</div>
        </div>
        <div class="hero-index">01 <span></span> ACCESS, REFINED</div>
    </section>
    <section class="home-details"><div><span class="details-label">BUILT AROUND YOUR TRUST</span><h2>Good security should<br>feel effortless.</h2></div><p>From account creation to every return visit, the essentials are handled with care. Straightforward access, protected credentials, and a little more peace of mind.</p><a href="<?= SITE_URL ?>/register">Get started <span>↗</span></a></section>
</main>
<style>
.home-landing{color:#17352c}.home-hero{position:relative;overflow:hidden;min-height:610px;background:#f5f7f4;display:flex;align-items:center;padding:72px max(7.5vw,24px) 96px;gap:7%;}.hero-copy{position:relative;z-index:1;max-width:550px;flex:1}.hero-label,.details-label{font-size:10px;font-weight:700;letter-spacing:.17em;color:#62776d}.hero-label i{display:inline-block;width:7px;height:7px;border-radius:50%;background:#638c74;margin-right:9px}.hero-copy h1{font:500 clamp(56px,7vw,88px)/.99 Georgia,serif;letter-spacing:-.055em;color:#18392f;margin:31px 0 22px}.hero-copy h1 em{font-weight:400;color:#678774}.hero-copy>p{color:#687870;font-size:15px;line-height:1.8;max-width:440px;margin:0}.hero-actions{display:flex;align-items:center;gap:24px;margin-top:31px;flex-wrap:wrap}.hero-primary{display:flex;justify-content:space-between;gap:32px;background:#1d493b;color:#fff;padding:15px 17px;border-radius:5px;text-decoration:none;font-size:12px;font-weight:600;min-width:190px}.hero-primary:hover{background:#28624f}.hero-primary span{font-size:16px}.hero-secondary{font-size:12px;color:#355b4d;text-decoration:none;font-weight:600}.hero-secondary:hover{text-decoration:underline}.hero-proof{display:flex;align-items:center;gap:9px;color:#819087;font-size:10px;margin-top:45px}.hero-proof b{font-weight:400;color:#bdc7c0}.proof-check{display:grid;place-items:center;border:1px solid #a7bcae;color:#48705b;border-radius:50%;width:19px;height:19px;font-size:11px}.hero-art{position:relative;flex:0 0 min(40vw,470px);height:430px;display:grid;place-items:center}.art-orbit{position:absolute;width:380px;height:380px;border:1px solid #dce5dd;border-radius:50%}.orbit-two{width:310px;height:310px;border-color:#e5ebe6}.art-card{position:relative;background:#fff;border:1px solid #e8ede8;border-radius:10px;width:285px;padding:21px 23px 19px;box-shadow:0 24px 70px #17352c15;transform:rotate(-2deg)}.art-card-head{display:flex;justify-content:space-between;font-size:8px;letter-spacing:.12em;color:#85928a;font-weight:700}.live-dot{color:#6e9a7d;font-size:8px}.art-lock{width:75px;height:75px;background:#edf4ee;color:#547d63;border-radius:50%;display:grid;place-items:center;margin:30px auto 19px}.art-lock svg{width:45px}.art-card>strong,.art-card>small{display:block;text-align:center}.art-card>strong{font:500 18px Georgia,serif;color:#254337}.art-card>small{font-size:10px;color:#8a9790;margin-top:7px}.art-divider{height:1px;background:#eef1ee;margin:23px 0 14px}.art-status{display:flex;justify-content:space-between;font-size:8px;color:#78877e;letter-spacing:.04em}.art-status i{display:inline-block;width:6px;height:6px;background:#76a185;border-radius:50%;margin-right:5px}.art-status span:last-child{font-size:7px;letter-spacing:.12em;color:#718d78}.floating-tag{position:absolute;background:#fff;border:1px solid #ebefeb;border-radius:5px;padding:11px 13px;box-shadow:0 8px 25px #17352c0d;font-size:10px;color:#425c4f}.floating-tag span{color:#6b9978;margin-right:6px}.tag-top{top:58px;right:-5px}.tag-bottom{bottom:48px;left:2px}.tag-shield{font-size:10px}.hero-index{position:absolute;bottom:30px;right:max(7.5vw,24px);display:flex;align-items:center;gap:12px;color:#9aa69f;font-size:9px;letter-spacing:.16em}.hero-index span{width:40px;height:1px;background:#c8d2ca}.home-details{max-width:1120px;margin:auto;padding:72px 24px 84px;display:grid;grid-template-columns:1fr 1fr auto;align-items:center;gap:36px}.details-label{font-size:9px}.home-details h2{font:500 32px/1.15 Georgia,serif;letter-spacing:-.03em;color:#244538;margin:15px 0 0}.home-details>p{font-size:12px;line-height:1.9;color:#77857d;margin:0;max-width:430px}.home-details>a{white-space:nowrap;color:#315e49;font-size:11px;font-weight:600;text-decoration:none;border-bottom:1px solid #a8bdae;padding-bottom:7px}.home-details>a span{margin-left:12px}@media(max-width:850px){.home-hero{gap:0;padding-top:62px;min-height:550px}.hero-art{flex-basis:42vw;transform:scale(.82);transform-origin:center right;margin-left:-30px}.art-orbit{width:320px;height:320px}.orbit-two{width:260px;height:260px}.home-details{grid-template-columns:1fr 1fr}.home-details>a{grid-column:2}}@media(max-width:620px){.home-hero{display:block;padding:57px 24px 82px}.hero-copy h1{font-size:64px;margin-top:25px}.hero-proof{font-size:9px;gap:6px;margin-top:30px}.hero-art{height:345px;transform:scale(.82);transform-origin:top center;margin:40px 0 -35px}.hero-index{bottom:22px}.home-details{padding:52px 24px;display:block}.home-details>p{margin-top:20px}.home-details>a{display:inline-block;margin-top:20px}}
</style>
<?php include('components/footer.php'); ?>
