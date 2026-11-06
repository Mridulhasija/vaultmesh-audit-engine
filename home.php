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



<?php include('components/footer.php'); ?>
