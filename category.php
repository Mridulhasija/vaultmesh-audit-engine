<?php
require_once __DIR__ . '/include/header_config.php';
require_once __DIR__ . '/include/location-template.php';

// If location is set, validate it first
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
    $location_data = json_decode($location_json ?? '', true);
    $location_data = is_array($location_data) ? $location_data : [];
}

$stmt = $conn->prepare("SELECT id, parent_id, title, url, icon, image, banner, short_description, description, meta_title, meta_keyword, meta_desc, price, discounted_price, status, created_at, updated_at, unit, heading, service_heading, service_meta_title, service_meta_keyword, service_meta_desc, service_description, faqs, service_faqs FROM `tbl_product_category` WHERE status = 1 AND url = ?");
$stmt->bind_param("s", $slug);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows <= 0) {
    http_response_code(404);
    header('location: ' . SITE_URL);
    exit();
}

$stmt->bind_result(
    $id,
    $parent_id,
    $title,
    $url,
    $icon,
    $image,
    $banner,
    $short_description,
    $description,
    $meta_title,
    $meta_keyword,
    $meta_desc,
    $price,
    $discounted_price,
    $status,
    $created_at,
    $updated_at,
    $unit,
    $heading,
    $service_heading,
    $service_meta_title,
    $service_meta_keyword,
    $service_meta_desc,
    $service_description,
    $faqs_json,
    $service_faqs_json
);

$stmt->fetch();

$stmt->close();

$category_name = $title;
$page_heading = $heading ?: $category_name;
$faq_json = $faqs_json;
if (isset($location)) {
    $replace = static fn($value, bool $escape = false) => replaceLocationTemplate((string) $value, $location_title, $location_data, $category_name, $escape);
    $page_heading = $replace($service_heading ?: $category_name);
    $meta_title = $replace($service_meta_title ?: $page_heading);
    $meta_keyword = $replace($service_meta_keyword);
    $meta_desc = $replace($service_meta_desc);
    $description = $replace($service_description ?: $description, true);
    $faq_json = $service_faqs_json;
}
$faqs = json_decode($faq_json ?: '[]', true);
$faqs = is_array($faqs) ? array_values(array_filter($faqs, static function ($item) {
    return is_array($item) && trim((string) ($item['question'] ?? '')) !== '' && trim((string) ($item['answer'] ?? '')) !== '';
})) : [];
if (isset($location)) {
    foreach ($faqs as &$faq) {
        $faq['question'] = $replace($faq['question']);
        $faq['answer'] = $replace($faq['answer']);
    }
    unset($faq);
}

$sub_cats = $getdata->fetch('tbl_product_category', "parent_id='$id' ORDER BY created_at");

$pageCanonical = SITE_URL . '/' . (isset($location) ? $location . '/' : '') . $url . (isset($location) ? '/' : '');
$offerPrice = '100';
$currencyCode = explode('-', CURRENCY, 2)[0];
$unitCodes = ['KGM', 'GRM', 'TNE', 'LBR', 'LTR', 'MLT', 'GLL', 'C62', 'BG', 'BO', 'CT'];
$unitCode = in_array((string) $unit, $unitCodes, true) ? (string) $unit : 'C62';
$unitLabels = ['KGM' => 'kg', 'GRM' => 'g', 'TNE' => 'tonne', 'LBR' => 'lb', 'LTR' => 'L', 'MLT' => 'ml', 'GLL' => 'US gal', 'C62' => 'unit', 'BG' => 'bag', 'BO' => 'bottle', 'CT' => 'carton'];

// Offers apply to product pages only. Use the discounted price when available,
// otherwise fall back to the regular price.
if (count($sub_cats) === 0) {
    $candidatePrice = (is_numeric($discounted_price) && (float) $discounted_price > 0)
        ? $discounted_price
        : $price;

    if (is_numeric($candidatePrice) && (float) $candidatePrice > 0) {
        $offerPrice = number_format((float) $candidatePrice, 2, '.', '');
    }
}
$hasPublishedPrice = count($sub_cats) === 0 && is_numeric($candidatePrice ?? null) && (float) ($candidatePrice ?? 0) > 0;
$displayPrice = $hasPublishedPrice ? $getdata->formatPrice((float) $candidatePrice) : null;
$displayOriginalPrice = (is_numeric($price) && (float) $price > 0 && $displayPrice !== null && (float) $price !== (float) $candidatePrice)
    ? $getdata->formatPrice((float) $price)
    : null;

$schema = [
    '@context' => 'https://schema.org',
    '@graph' => [
        [
            '@type' => count($sub_cats) === 0 ? 'Product' : 'CollectionPage',
            '@id' => $pageCanonical . (count($sub_cats) === 0 ? '#product' : '#collection'),
            'name' => $category_name,
            'url' => $pageCanonical,
            'description' => strip_tags(!empty($short_description)
                ? $short_description
                : implode(' ', array_slice(
                    preg_split('/(?<=[.!?])\s+/', strip_tags($description), -1, PREG_SPLIT_NO_EMPTY),
                    0,
                    2
                ))),
            'category' => $category_name,
        ],
        [
            '@type' => 'BreadcrumbList',
            '@id' => $pageCanonical . '#breadcrumb',
            'itemListElement' => [
                [
                    '@type' => 'ListItem',
                    'position' => 1,
                    'name' => 'Home',
                    'item' => SITE_URL . '/',
                ],
                [
                    '@type' => 'ListItem',
                    'position' => 2,
                    'name' => $title,
                    'item' => $pageCanonical,
                ],
            ],
        ],
    ],
];

if (!empty($image)) {
    $schema['@graph'][0]['image'] = [media_folder . $image];
}

if (count($sub_cats) === 0) {
    // Publish these customer-facing policy pages at the matching URLs before
    // enabling product offers in production.
    $shippingPolicyUrl = rtrim(SITE_URL, '/') . '/shipping-policy';
    $returnPolicyUrl = rtrim(SITE_URL, '/') . '/return-policy';

    $schema['@graph'][0]['offers'] = [
        '@type' => 'Offer',
        'url' => $pageCanonical,
        'seller' => [
            '@type' => 'Organization',
            'name' => $site_name,
            'url' => rtrim(SITE_URL, '/') . '/',
        ],
        'shippingDetails' => [
            '@type' => 'OfferShippingDetails',
            'shippingSettingsLink' => $shippingPolicyUrl,
            'shippingRate' => [
                '@type' => 'MonetaryAmount',
                // No additional shipping charge is shown in the offer;
                // destination-specific freight is confirmed by quotation.
                'value' => '0.00',
                'currency' => $currencyCode,
            ],
            'deliveryTime' => [
                '@type' => 'ShippingDeliveryTime',
                'handlingTime' => [
                    '@type' => 'QuantitativeValue',
                    'minValue' => 1,
                    'maxValue' => 3,
                    'unitCode' => 'DAY',
                ],
                'transitTime' => [
                    '@type' => 'QuantitativeValue',
                    'minValue' => 7,
                    'maxValue' => 30,
                    'unitCode' => 'DAY',
                ],
            ],
        ],
        'hasMerchantReturnPolicy' => [
            '@type' => 'MerchantReturnPolicy',
            'merchantReturnLink' => $returnPolicyUrl,
            'returnPolicyCategory' => 'https://schema.org/MerchantReturnNotPermitted',
        ],
    ];

    $schema['@graph'][0]['offers']['price'] = $offerPrice;
    $schema['@graph'][0]['offers']['priceCurrency'] = $currencyCode;
    $schema['@graph'][0]['offers']['availability'] = 'https://schema.org/InStock';
    $schema['@graph'][0]['offers']['itemCondition'] = 'https://schema.org/NewCondition';
    $schema['@graph'][0]['offers']['priceSpecification'] = [
        '@type' => 'UnitPriceSpecification',
        'price' => $offerPrice,
        'priceCurrency' => $currencyCode,
        'referenceQuantity' => [
            '@type' => 'QuantitativeValue',
            'value' => 1,
            'unitCode' => $unitCode,
        ],
    ];
}

if ($faqs) {
    $schema['@graph'][] = [
        '@type' => 'FAQPage',
        '@id' => $pageCanonical . '#faq',
        'mainEntity' => array_map(static fn($faq) => [
            '@type' => 'Question',
            'name' => strip_tags($faq['question']),
            'acceptedAnswer' => ['@type' => 'Answer', 'text' => $faq['answer']],
        ], $faqs),
    ];
}

include('components/header.php');
?>

<?php
$pageHeroTitle = $page_heading;
include __DIR__ . '/components/page-hero.php';
?>


<?php if (count($sub_cats) > 0): // category page blocks
?>

    <section class="py-12 bg-gray-100 px-6">
        <h2 class="text-center font-display text-4xl lg:text-5xl font-bold text-secondary-900 mb-12">
            Related Products
        </h2>

        <div class="max-w-6xl mx-auto grid grid-cols-1 sm:grid-cols-3 gap-10">
            <?php
            foreach ($sub_cats as $sub_cat):
                $url = SITE_URL . '/' . (isset($location) ? $location . '/' : '') . $sub_cat['url'] . (isset($location) ? '/' : '');
            ?>
                <div class="text-center">
                    <a href="<?= $url ?>">
                        <div class="rounded-lg overflow-hidden shadow-lg">
                            <img
                                src="<?= media_folder . $sub_cat['image'] ?>"
                                alt="<?= $sub_cat['title'] ?>"
                                class="w-full h-auto object-cover aspect-square" />
                            <div class="bg-primary-600">
                                <h3 class="text-lg font-medium text-white py-2"><?= $sub_cat['title'] ?></h3>
                            </div>
                            <?php
                            $subPrice = (is_numeric($sub_cat['discounted_price'] ?? null) && (float) $sub_cat['discounted_price'] > 0)
                                ? $sub_cat['discounted_price']
                                : ($sub_cat['price'] ?? '');
                            $subUnit = isset($unitLabels[$sub_cat['unit'] ?? '']) ? $sub_cat['unit'] : 'C62';
                            if (is_numeric($subPrice) && (float) $subPrice > 0):
                            ?>
                                <p class="bg-white px-3 py-3 text-sm font-semibold text-secondary-800">
                                    <?= htmlspecialchars($getdata->formatPrice((float) $subPrice)) ?>
                                    <span class="font-normal text-secondary-700">/ <?= htmlspecialchars($unitLabels[$subUnit]) ?></span>
                                </p>
                            <?php endif; ?>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>

        </div>
    </section>

<?php
endif;
?>

<!-- content -->
<section class="clearfix max-w-screen-xl mx-auto px-4 py-10 flex flex-col md:flex-row gap-8">

    <div class="md:w-2/3">

        <div class="text-secondary-600 text-lg space-y-4 content [&_a]:text-primary-600 [&_a]:underline [&_a]:font-medium [&_blockquote]:border-l-4 [&_blockquote]:border-l-primary-600 [&_blockquote]:text-secondary-600">
            <?= $description ?>
        </div>

        <?php if ($faqs): ?>
            <section class="mt-8" aria-labelledby="faq-heading">
                <h2 id="faq-heading" class="text-2xl font-semibold">Frequently Asked Questions</h2>
                <?php foreach ($faqs as $faq): ?>
                    <details class="mt-4 border rounded-lg p-4">
                        <summary class="cursor-pointer font-semibold"><?= htmlspecialchars($faq['question'], ENT_QUOTES, 'UTF-8') ?></summary>
                        <div class="mt-3"><?= nl2br(htmlspecialchars($faq['answer'], ENT_QUOTES, 'UTF-8')) ?></div>
                    </details>
                <?php endforeach; ?>
            </section>
        <?php endif; ?>

    </div>


    <!-- Sidebar Enquiry Form -->
    <aside class="md:w-1/3 bg-white p-6 rounded-2xl shadow-lg self-start">

        <img class="w-full rounded-lg mb-6 aspect-square" src="<?= media_folder . $image ?>" alt="<?= $getdata->fetchAltText($image) ?>" />
        <?php if ($displayPrice !== null): ?>
            <div class="mb-6 rounded-xl bg-primary-50 px-5 py-4">
                <div class="text-xs font-semibold uppercase tracking-wide text-secondary-700">From</div>
                <div class="mt-1 text-2xl font-semibold text-secondary-900">
                    <?= htmlspecialchars($displayPrice) ?>
                    <span class="text-sm font-normal text-secondary-700">/ <?= htmlspecialchars($unitLabels[$unitCode]) ?></span>
                    <?php if ($displayOriginalPrice !== null): ?><del class="ml-2 text-sm font-normal text-gray-500"><?= htmlspecialchars($displayOriginalPrice) ?></del><?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
        <h2 class="text-2xl font-semibold text-secondary-800 text-center mb-4">
            Send an Enquiry
        </h2>

        <?php $formId = uniqid('form_', true); ?>
        <form action="<?= SITE_URL ?>/contact.php" method="POST" class="contact-form space-y-4" data-form-id="<?= $formId ?>">
            <div class="form-response-message hidden rounded-xl px-4 py-3 text-sm font-medium" role="alert" aria-live="polite"></div>

            <input type="text" name="name" class="rounded-lg w-full p-3 border border-gray-300 text-secondary-700 focus:ring-2 focus:ring-primary-300 focus:border-primary-500 focus:outline-none" placeholder="Full Name" required="">

            <div>
                <input type="tel" id="phone" name="phone"
                    class="phone rounded-lg w-full p-3 border border-gray-300 text-secondary-700 focus:ring-2 focus:ring-primary-300 focus:border-primary-500 focus:outline-none">
                <span class="error-msg text-red-500 text-sm"></span>
                <span class="valid-msg text-green-500 text-sm"></span>
            </div>

            <input type="hidden" name="location">

            <input type="email" name="email" class="rounded-lg w-full p-3 border border-gray-300 text-secondary-700 focus:ring-2 focus:ring-primary-300 focus:border-primary-500 focus:outline-none" placeholder="Email Address" required="">

            <textarea name="message" rows="4" class="rounded-lg w-full p-3 border border-gray-300 text-secondary-700 focus:ring-2 focus:ring-primary-300 focus:border-primary-500 focus:outline-none" placeholder="Your Message" required=""></textarea>

            <!-- Custom CAPTCHA & Submit Button -->
            <div class="mt-4">
                <label class="block text-sm font-medium text-secondary-700 mb-2 font-sans">Verification Code</label>
                <div class="flex items-center gap-2 mb-4">
                    <div class="flex-1">
                        <input type="text"
                            name="captcha_code"
                            required
                            placeholder="Captcha"
                            class="captcha-input rounded-lg w-full p-3 border border-gray-300 text-secondary-700 focus:ring-2 focus:ring-primary-300 focus:border-primary-500 focus:outline-none">
                    </div>
                    <div class="captcha-image-container">
                        <img src="<?= SITE_URL ?>/captcha.php?generate=1&form_id=<?= $formId ?>"
                            alt="CAPTCHA"
                            class="captcha-image border border-gray-300 rounded-lg cursor-pointer"
                            style="height: 50px; width: auto;"
                            data-form-id="<?= $formId ?>">
                    </div>
                    <button type="button"
                        class="captcha-refresh-btn px-3 py-3 bg-secondary-200 hover:bg-secondary-300 text-secondary-700 rounded-lg transition-colors"
                        title="Refresh CAPTCHA">
                        ↻
                    </button>
                </div>
                <input type="hidden" name="captcha_form_id" class="captcha-form-id" value="<?= $formId ?>">

                <button type="submit" data-loading-text="Submitting..." class="w-full py-2 rounded-md bg-primary hover:scale-[1.05] text-white transition-all duration-300">
                    Submit Enquiry
                </button>
            </div>

        </form>
    </aside>

</section>

<?php
include('components/footer.php');
?>
