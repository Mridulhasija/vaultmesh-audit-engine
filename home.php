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

<main class="home-page-root">

    <!-- ========================================================
         HERO SECTION: High-Craft Editorial & Animated Enclave
         ======================================================== -->
    <section class="relative overflow-hidden bg-gradient-to-b from-[#f4f7f4] via-[#f7faf7] to-[#eef3ef] border-b border-[#e1e7e2] pt-12 md:pt-16 pb-20 lg:pb-28">
        
        <!-- Architectural ambient grid background -->
        <div class="absolute inset-0 opacity-[0.035] pointer-events-none enclave-grid-bg"></div>
        <div class="absolute -top-40 right-1/4 w-96 h-96 bg-[#3b8c66]/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute top-1/2 -left-20 w-80 h-80 bg-[#173f35]/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="auth-marquee mb-9 rounded-2xl bg-gradient-to-r from-[#0d231b] via-[#12362b] to-[#0d231b] border border-[#235848]/40 shadow-lg shadow-[#173f35]/10 overflow-hidden py-3 text-[10px] sm:text-[11px] font-mono tracking-wider text-[#a7f3d0]" aria-label="Security standards">
                <div class="auth-marquee-track flex w-max items-center gap-6">
                    <div class="flex shrink-0 items-center gap-5 px-3">
                        <span class="inline-flex items-center gap-1.5 font-semibold text-white"><span class="w-2 h-2 rounded-full bg-[#4ade80] inline-block animate-pulse"></span>FIDO2 / WEBAUTHN CERTIFIED ENCLAVE</span><span class="text-[#456b5b]">✦</span>
                        <span class="text-[#c7e5d4]">AES-256-GCM ZERO-PLAINTEXT SEAL</span><span class="text-[#456b5b]">✦</span>
                        <span class="text-[#c7e5d4]">ARGON2ID MEMORY-HARD DERIVATION</span><span class="text-[#456b5b]">✦</span>
                        <span class="text-[#c7e5d4]">SAME-SITE STRICT SLIDING SESSION</span><span class="text-[#456b5b]">✦</span>
                        <span class="text-[#86efac] font-medium">ANTI-CSRF CSPRNG 256-BIT TOKENS ARMED</span><span class="text-[#456b5b]">✦</span>
                        <span class="text-[#c7e5d4]">SUB-5MS CRYPTOGRAPHIC ATTESTATION</span><span class="text-[#456b5b]">✦</span>
                        <span class="text-[#c7e5d4]">ZERO THIRD-PARTY TRACKERS OR PIXELS</span>
                    </div>
                    <div class="flex shrink-0 items-center gap-5 px-3" aria-hidden="true">
                        <span class="inline-flex items-center gap-1.5 font-semibold text-white"><span class="w-2 h-2 rounded-full bg-[#4ade80] inline-block animate-pulse"></span>FIDO2 / WEBAUTHN CERTIFIED ENCLAVE</span><span class="text-[#456b5b]">✦</span>
                        <span class="text-[#c7e5d4]">AES-256-GCM ZERO-PLAINTEXT SEAL</span><span class="text-[#456b5b]">✦</span>
                        <span class="text-[#c7e5d4]">ARGON2ID MEMORY-HARD DERIVATION</span><span class="text-[#456b5b]">✦</span>
                        <span class="text-[#c7e5d4]">SAME-SITE STRICT SLIDING SESSION</span><span class="text-[#456b5b]">✦</span>
                        <span class="text-[#86efac] font-medium">ANTI-CSRF CSPRNG 256-BIT TOKENS ARMED</span><span class="text-[#456b5b]">✦</span>
                        <span class="text-[#c7e5d4]">SUB-5MS CRYPTOGRAPHIC ATTESTATION</span><span class="text-[#456b5b]">✦</span>
                        <span class="text-[#c7e5d4]">ZERO THIRD-PARTY TRACKERS OR PIXELS</span>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-8 items-center">
                
                <!-- Hero Left: Typography & Authoritative Copy -->
                <div class="lg:col-span-7 xl:col-span-6 flex flex-col items-start">
                    
                    <!-- Top trust micro-badge -->
                    <div class="inline-flex items-center gap-2.5 px-3 py-1.5 rounded-full bg-[#e7eee9] border border-[#d2ded5] text-[11px] font-semibold tracking-wider text-[#244b3c] shadow-xs mb-6">
                        <span class="w-2 h-2 rounded-full bg-[#4fa572] inline-block animate-pulse"></span>
                        <span class="uppercase tracking-widest font-mono-code text-[10px]">FIDO2 / WEBAUTHN CERTIFIED ENCLAVE</span>
                    </div>

                    <!-- Main Editorial Headline -->
                    <h1 class="font-serif text-4xl sm:text-5xl lg:text-[56px] xl:text-[62px] font-medium tracking-tight text-[#143229] leading-[1.08] mb-6">
                        Authentication built for <br class="hidden sm:inline" />
                        <span class="italic text-[#276652]">absolute trust.</span>
                    </h1>

                    <!-- Concrete technical description (Zero AI Slop) -->
                    <p class="text-base sm:text-lg text-[#4a6356] leading-relaxed max-w-xl mb-8 font-normal">
                        Move beyond fragile passwords, leaky session cookies, and bloated third-party trackers. <?= htmlspecialchars($site_name, ENT_QUOTES, 'UTF-8') ?> delivers hardware-bound passkeys, sliding-window session isolation, and tamper-evident audit logs designed for cryptographic certainty.
                    </p>

                    <!-- Primary Actions -->
                    <div class="flex flex-wrap items-center gap-4 w-full sm:w-auto mb-10">
                        <a href="<?= SITE_URL ?>/register" class="w-full sm:w-auto inline-flex items-center justify-between sm:justify-center gap-3 px-6 py-3.5 rounded-xl bg-[#173f35] hover:bg-[#235848] text-white font-semibold text-[15px] shadow-lg shadow-[#173f35]/20 border border-[#13362d] transition-all transform hover:-translate-y-0.5 active:translate-y-0">
                            <span>Create your secure account</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </a>
                        <a href="<?= SITE_URL ?>/login" class="w-full sm:w-auto inline-flex items-center justify-center px-5 py-3.5 rounded-xl text-[#244a3d] hover:text-[#112921] hover:bg-[#173f35]/06 font-semibold text-[14.5px] border border-[#d6e0d8] bg-white/70 backdrop-blur-xs transition-colors">
                            Already a member? Log in
                        </a>
                    </div>

                    <!-- Hardened Security Proof Ribbon -->
                    <div class="pt-6 border-t border-[#dce5df] w-full max-w-xl">
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 text-xs text-[#526a5e]">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-[#449163] flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                <span class="font-medium">Hardware Passkeys</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-[#449163] flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                <span class="font-medium">AES-256-GCM Vault</span>
                            </div>
                            <div class="flex items-center gap-2 col-span-2 sm:col-span-1">
                                <svg class="w-4 h-4 text-[#449163] flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                <span class="font-medium">Zero Plaintext Data</span>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Hero Right: Animated & Interactive Cryptographic Enclave Poster -->
                <div class="lg:col-span-5 xl:col-span-6 relative flex justify-center items-center py-6">
                    
                    <!-- Ambient Glow Behind Poster -->
                    <div class="absolute -inset-2 bg-gradient-to-r from-[#215a49]/30 to-[#10372b]/25 rounded-3xl blur-2xl opacity-70"></div>

                    <!-- Master Poster Terminal Container -->
                    <div class="relative w-full max-w-[500px] bg-[#0d221b] border border-white/15 rounded-2xl p-5 sm:p-6 shadow-[0_28px_70px_-15px_rgba(14,40,32,0.45)] text-[#d5e5db] font-sans overflow-hidden">
                        
                        <!-- Terminal Sub-grid Background -->
                        <div class="absolute inset-0 enclave-grid-bg opacity-15 pointer-events-none"></div>

                        <!-- Terminal Titlebar -->
                        <div class="relative flex items-center justify-between pb-4 border-b border-white/10 mb-5">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-[#4fa572] inline-block"></span>
                                <span class="w-3 h-3 rounded-full bg-[#d4af37] inline-block opacity-75"></span>
                                <span class="w-3 h-3 rounded-full bg-[#64c2a7] inline-block opacity-60"></span>
                                <span class="font-mono-code text-[11px] font-medium text-[#8cb29c] ml-2 tracking-wide hidden sm:inline">vaultmesh-enclave-daemon // v3.4.1</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-[#4ade80] inline-block beacon-dot"></span>
                                <span class="font-mono-code text-[10px] font-semibold text-[#86efac] tracking-widest uppercase">ENCLAVE SEALED</span>
                            </div>
                        </div>

                        <!-- Enclave Interactive Mode Switcher Tabs -->
                        <div class="relative flex items-center bg-[#071711] p-1 rounded-xl border border-white/10 mb-5 text-xs font-mono-code">
                            <button type="button" id="tab-btn-handshake" class="enclave-tab-btn flex-1 py-1.5 px-2 rounded-lg text-center transition-all bg-[#173f35] text-white font-medium shadow-xs" data-target="panel-handshake">
                                Handshake
                            </button>
                            <button type="button" id="tab-btn-token" class="enclave-tab-btn flex-1 py-1.5 px-2 rounded-lg text-center transition-all text-[#8da597] hover:text-white" data-target="panel-token">
                                Token Payload
                            </button>
                            <button type="button" id="tab-btn-audit" class="enclave-tab-btn flex-1 py-1.5 px-2 rounded-lg text-center transition-all text-[#8da597] hover:text-white" data-target="panel-audit">
                                Audit Log
                            </button>
                        </div>

                        <!-- TAB PANEL 1: Animated Biometric & Cipher Scanner (Default) -->
                        <div id="panel-handshake" class="enclave-panel relative">
                            
                            <!-- Scanner Core Stage -->
                            <div class="relative h-56 sm:h-60 rounded-xl bg-gradient-to-b from-[#112d24] to-[#0a1c16] border border-white/10 flex items-center justify-center overflow-hidden mb-5">
                                
                                <!-- Laser scanning beam that sweeps back and forth -->
                                <div class="scanner-laser"></div>

                                <!-- Dual Concentric Rotating Cipher Rings -->
                                <svg class="absolute w-52 h-52 text-[#2f6f55]/40 cipher-ring-outer pointer-events-none" viewBox="0 0 200 200" fill="none">
                                    <circle cx="100" cy="100" r="92" stroke="currentColor" stroke-width="1.5" stroke-dasharray="6 8 18 4" />
                                    <circle cx="100" cy="100" r="76" stroke="currentColor" stroke-width="1" stroke-dasharray="3 5" />
                                </svg>
                                <svg class="absolute w-36 h-36 text-[#4fa572]/45 cipher-ring-inner pointer-events-none" viewBox="0 0 150 150" fill="none">
                                    <circle cx="75" cy="75" r="66" stroke="currentColor" stroke-width="1.8" stroke-dasharray="4 6 12 4" />
                                </svg>

                                <!-- Pulsing Sonar Waves from the Shield Core -->
                                <div class="absolute w-24 h-24 rounded-full border border-[#4ade80]/40 radar-pulse-wave-1 pointer-events-none"></div>
                                <div class="absolute w-24 h-24 rounded-full border border-[#4ade80]/30 radar-pulse-wave-2 pointer-events-none"></div>

                                <!-- Centerpiece: Glowing Biometric Lock Shield Emblem -->
                                <div class="relative z-10 w-20 h-20 rounded-2xl bg-gradient-to-br from-[#1d4d3f] to-[#0f2820] border border-[#52a175]/60 flex items-center justify-center shadow-lg shadow-[#061811]/80">
                                    <svg class="w-10 h-10 text-[#86efac]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                                        <path d="M12 8v4"/>
                                        <circle cx="12" cy="15" r="1" fill="currentColor"/>
                                    </svg>
                                </div>

                                <!-- Nonce Hash Watermark -->
                                <div class="absolute bottom-2.5 left-3 font-mono-code text-[9.5px] text-[#719582] tracking-wider">
                                    NONCE: <span id="dyn-nonce" class="text-[#a7f3d0]">0x7F4A...B902</span>
                                </div>
                                <div class="absolute bottom-2.5 right-3 font-mono-code text-[9.5px] text-[#719582] tracking-wider">
                                    LATENCY: <span class="text-[#a7f3d0]">4.2ms</span>
                                </div>
                            </div>

                            <!-- Real-time Verification Protocol Pipeline Track -->
                            <div class="relative bg-[#071711] rounded-xl p-3.5 border border-white/10 mb-4">
                                <div class="flex items-center justify-between text-[11px] font-mono-code mb-2.5">
                                    <span class="text-[#7da38f]">AUTHENTICATION PIPELINE</span>
                                    <span class="text-[#86efac] font-semibold">STAGE 03 / COMPLETE</span>
                                </div>

                                <!-- Progress line with animated packet dot -->
                                <div class="relative h-1.5 bg-white/10 rounded-full overflow-hidden mb-3">
                                    <div class="h-full bg-gradient-to-r from-[#22c55e] via-[#4ade80] to-[#86efac] w-full rounded-full"></div>
                                    <div class="packet-pulse-dot"></div>
                                </div>

                                <!-- Protocol Stage Badges -->
                                <div class="grid grid-cols-3 gap-2 text-center font-mono-code text-[10px]">
                                    <div class="p-1.5 rounded-lg bg-white/5 border border-white/5">
                                        <div class="text-[#4ade80] font-bold">01. SYN-ACK</div>
                                        <div class="text-[#7fa391] text-[9px]">P-256 Nonce</div>
                                    </div>
                                    <div class="p-1.5 rounded-lg bg-white/5 border border-white/5">
                                        <div class="text-[#4ade80] font-bold">02. WebAuthn</div>
                                        <div class="text-[#7fa391] text-[9px]">Passkey Valid</div>
                                    </div>
                                    <div class="p-1.5 rounded-lg bg-[#143d31] border border-[#2b7256]">
                                        <div class="text-[#86efac] font-bold">03. Token Mint</div>
                                        <div class="text-[#a7f3d0] text-[9px]">Ed25519 Verified</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Interactive Simulator Trigger Button -->
                            <div class="flex items-center justify-between gap-3 pt-1">
                                <button type="button" id="btn-reverify" class="w-full inline-flex items-center justify-center gap-2 py-2 px-3.5 rounded-lg bg-white/10 hover:bg-white/15 border border-white/15 text-xs font-mono-code text-[#c7decfa] font-medium transition-colors">
                                    <svg class="w-3.5 h-3.5 text-[#4ade80] animate-spin-reverse" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M21.5 2v6h-6M2.5 22v-6h6M2 11.5a10 10 0 0 1 18.8-4.3M22 12.5a10 10 0 0 1-18.8 4.2"/>
                                    </svg>
                                    <span>Simulate Cryptographic Challenge</span>
                                </button>
                            </div>

                        </div>

                        <!-- TAB PANEL 2: Cryptographic Token Payload Inspector (JSON) -->
                        <div id="panel-token" class="enclave-panel hidden relative">
                            <div class="h-80 rounded-xl bg-[#06140e] border border-white/10 p-3.5 font-mono-code text-[11px] leading-relaxed overflow-y-auto terminal-scroll text-[#a7c5b4]">
                                <div class="text-[#5b8770] mb-2">// RFC 7519 Verified Ephemeral Session Assertion</div>
                                <pre class="text-[#e2eae4] whitespace-pre-wrap">{
  <span class="text-[#86efac]">"iss"</span>: <span class="text-[#fde68a]">"https://auth.vaultmesh.security"</span>,
  <span class="text-[#86efac]">"sub"</span>: <span class="text-[#fde68a]">"usr_9f40c18da2"</span>,
  <span class="text-[#86efac]">"aud"</span>: <span class="text-[#fde68a]">"vaultmesh:enclave:v3"</span>,
  <span class="text-[#86efac]">"auth_time"</span>: <span class="text-[#93c5fd]">1731676800</span>,
  <span class="text-[#86efac]">"amr"</span>: [
    <span class="text-[#fde68a]">"passkey"</span>,
    <span class="text-[#fde68a]">"fido2_user_verified"</span>,
    <span class="text-[#fde68a]">"hardware_enclave"</span>
  ],
  <span class="text-[#86efac]">"cnf"</span>: {
    <span class="text-[#86efac]">"jwk"</span>: {
      <span class="text-[#86efac]">"kty"</span>: <span class="text-[#fde68a]">"OKP"</span>,
      <span class="text-[#86efac]">"crv"</span>: <span class="text-[#fde68a]">"Ed25519"</span>,
      <span class="text-[#86efac]">"x"</span>: <span class="text-[#fde68a]">"O2A9...8vQ1mP4"</span>
    }
  },
  <span class="text-[#86efac]">"session_fingerprint"</span>: <span class="text-[#fde68a]">"sha384:e3b0c44298fc1c149afbf4c8996fb924"</span>,
  <span class="text-[#86efac]">"cookie_flags"</span>: <span class="text-[#fde68a]">"SameSite=Strict; Secure; HttpOnly"</span>,
  <span class="text-[#86efac]">"sliding_ttl_sec"</span>: <span class="text-[#93c5fd]">900</span>
}</pre>
                            </div>
                            <div class="mt-3 flex items-center justify-between text-[11px] font-mono-code text-[#7ea590]">
                                <span>Signature: <b class="text-[#86efac]">Ed25519 Verified ✓</b></span>
                                <span>Tamper Check: <b class="text-[#86efac]">0x00 Clean</b></span>
                            </div>
                        </div>

                        <!-- TAB PANEL 3: Live Real-Time Audit Log Stream -->
                        <div id="panel-audit" class="enclave-panel hidden relative">
                            <div class="h-80 rounded-xl bg-[#06140e] border border-white/10 p-3 font-mono-code text-[10.5px] leading-relaxed overflow-y-auto terminal-scroll text-[#94b8a4] space-y-2">
                                <div class="flex items-start gap-2 text-[#799f8c]">
                                    <span class="text-[#57816f]">[18:38:02]</span>
                                    <span>ENCLAVE_INIT: Hardware entropy seeded from /dev/urandom</span>
                                </div>
                                <div class="flex items-start gap-2">
                                    <span class="text-[#57816f]">[18:38:09]</span>
                                    <span>RATE_LIMIT: Subnet 192.168.1.0/24 verified (0 throttled events)</span>
                                </div>
                                <div class="flex items-start gap-2 text-[#a7f3d0]">
                                    <span class="text-[#57816f]">[18:38:12]</span>
                                    <span>WEBAUTHN_ASSERTION: Public key credential ID matched (FIDO2)</span>
                                </div>
                                <div class="flex items-start gap-2 text-[#4ade80]">
                                    <span class="text-[#57816f]">[18:38:13]</span>
                                    <span>SIGNATURE_CHECK: P-256 signature verified in 2.8ms (OK)</span>
                                </div>
                                <div class="flex items-start gap-2">
                                    <span class="text-[#57816f]">[18:38:13]</span>
                                    <span>SESSION_MINTED: Token usr_9f40c18da2 bound to TLS session</span>
                                </div>
                                <div class="flex items-start gap-2 text-[#86efac]">
                                    <span class="text-[#57816f]">[18:38:14]</span>
                                    <span>CSRF_GUARD: Synchronizer token validated · 200 SUCCESS</span>
                                </div>
                                <div class="flex items-start gap-2 text-[#6ee7b7] pt-1 border-t border-white/5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#4ade80] inline-block mt-1 animate-pulse"></span>
                                    <span>LISTENING: Zero anomalies detected in active memory enclave</span>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Floating 3D Telemetry Badges (Smooth keyframe float) -->
                    <!-- Badge 1: Top Right -->
                    <div class="hidden sm:flex floating-badge-top absolute -top-3 -right-4 z-20 items-center gap-2.5 px-3.5 py-2 rounded-xl bg-white/95 backdrop-blur-md border border-[#d2ded5] shadow-lg shadow-[#143228]/10 text-xs font-semibold text-[#183a2f]">
                        <span class="w-2 h-2 rounded-full bg-[#22c55e] inline-block"></span>
                        <span>Passkey Verified (FIDO2)</span>
                    </div>

                    <!-- Badge 2: Bottom Left -->
                    <div class="hidden sm:flex floating-badge-left absolute -bottom-4 -left-4 z-20 items-center gap-2.5 px-3.5 py-2 rounded-xl bg-white/95 backdrop-blur-md border border-[#d2ded5] shadow-lg shadow-[#143228]/10 text-xs font-semibold text-[#183a2f]">
                        <span class="text-[#3b8c66]">⚡</span>
                        <span>AES-256 Session Vault</span>
                    </div>

                </div>

            </div>
        </div>
    </section>

    <!-- ========================================================
         SECTION 2: Interactive Cryptographic Protocol Inspector
         ======================================================== -->
    <section class="py-16 lg:py-24 bg-[#ffffff] border-b border-[#e1e7e2]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="text-center max-w-3xl mx-auto mb-14">
                <span class="text-[11px] font-bold tracking-[0.2em] uppercase text-[#4f8664] font-mono-code block mb-3">
                    HOW AUTHENTICATION ACTUALLY WORKS
                </span>
                <h2 class="font-serif text-3xl sm:text-4xl lg:text-[42px] font-medium text-[#143229] tracking-tight">
                    Engineered for transparency, <br class="hidden sm:inline" />
                    <span class="italic text-[#296853]">not automated shortcuts.</span>
                </h2>
                <p class="text-sm sm:text-base text-[#5c7367] mt-4 leading-relaxed">
                    Most web apps rely on brittle third-party social buttons or plain cookie storage. Examine how VaultMesh’s four-stage cryptographic handshake delivers zero-leakage security.
                </p>
            </div>

            <!-- 4-Stage Interactive Pipeline Selector -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                
                <button type="button" class="proto-step-btn text-left p-5 rounded-xl border border-[#173f35] bg-[#f4f8f5] shadow-xs transition-all cursor-pointer" data-step="1">
                    <div class="flex items-center justify-between mb-2">
                        <span class="font-mono-code text-xs font-bold text-[#173f35]">STEP 01</span>
                        <span class="w-2 h-2 rounded-full bg-[#173f35] step-indicator"></span>
                    </div>
                    <div class="font-semibold text-sm text-[#18392f] mb-1">Challenge Nonce</div>
                    <p class="text-xs text-[#637a6e] leading-snug">Client requests login; enclave generates an unreplayable entropy nonce.</p>
                </button>

                <button type="button" class="proto-step-btn text-left p-5 rounded-xl border border-[#e1e7e2] bg-white hover:bg-[#fafcfa] transition-all cursor-pointer" data-step="2">
                    <div class="flex items-center justify-between mb-2">
                        <span class="font-mono-code text-xs font-bold text-[#647c70]">STEP 02</span>
                        <span class="w-2 h-2 rounded-full bg-transparent border border-[#96a99e] step-indicator"></span>
                    </div>
                    <div class="font-semibold text-sm text-[#18392f] mb-1">Hardware Signature</div>
                    <p class="text-xs text-[#637a6e] leading-snug">Device Secure Enclave signs challenge using user biometric or hardware key.</p>
                </button>

                <button type="button" class="proto-step-btn text-left p-5 rounded-xl border border-[#e1e7e2] bg-white hover:bg-[#fafcfa] transition-all cursor-pointer" data-step="3">
                    <div class="flex items-center justify-between mb-2">
                        <span class="font-mono-code text-xs font-bold text-[#647c70]">STEP 03</span>
                        <span class="w-2 h-2 rounded-full bg-transparent border border-[#96a99e] step-indicator"></span>
                    </div>
                    <div class="font-semibold text-sm text-[#18392f] mb-1">Constant-Time Verify</div>
                    <p class="text-xs text-[#637a6e] leading-snug">Public key validates proof without passwords ever crossing the network.</p>
                </button>

                <button type="button" class="proto-step-btn text-left p-5 rounded-xl border border-[#e1e7e2] bg-white hover:bg-[#fafcfa] transition-all cursor-pointer" data-step="4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="font-mono-code text-xs font-bold text-[#647c70]">STEP 04</span>
                        <span class="w-2 h-2 rounded-full bg-transparent border border-[#96a99e] step-indicator"></span>
                    </div>
                    <div class="font-semibold text-sm text-[#18392f] mb-1">Ephemeral Session</div>
                    <p class="text-xs text-[#637a6e] leading-snug">Rotating, HttpOnly, SameSite=Strict session token locked to your device.</p>
                </button>

            </div>

            <!-- Dynamic Protocol Step Detail Display Card -->
            <div class="rounded-2xl bg-[#0f271f] border border-white/10 p-6 sm:p-8 text-[#dce7e1] font-sans shadow-md">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                    
                    <div class="lg:col-span-7">
                        <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded bg-[#1e4a3c] text-[10.5px] font-mono-code text-[#a7f3d0] mb-3">
                            <span id="proto-step-badge">PROTOCOL SPECIFICATION // RFC 8812</span>
                        </div>
                        <h3 id="proto-step-title" class="font-serif text-2xl sm:text-3xl font-medium text-white mb-3">
                            Cryptographic Challenge &amp; Replay Prevention
                        </h3>
                        <p id="proto-step-desc" class="text-sm sm:text-[15px] text-[#9dbcb0] leading-relaxed mb-6">
                            When an authentication attempt begins, the VaultMesh server mints a single-use 32-byte cryptographic nonce mixed with client hardware identifiers. This ensures that even if an attacker intercepts network packets, they cannot replay the request.
                        </p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs font-mono-code text-[#94b8a4]">
                            <div class="flex items-center gap-2.5 p-3 rounded-lg bg-black/25 border border-white/5">
                                <span class="text-[#4ade80]">✓</span>
                                <span id="proto-spec-1">Entropy: 256-bit CSPRNG</span>
                            </div>
                            <div class="flex items-center gap-2.5 p-3 rounded-lg bg-black/25 border border-white/5">
                                <span class="text-[#4ade80]">✓</span>
                                <span id="proto-spec-2">Time-to-Live: 60 Seconds</span>
                            </div>
                        </div>
                    </div>

                    <!-- Code / State Preview Window -->
                    <div class="lg:col-span-5 bg-[#06140f] rounded-xl border border-white/10 p-4 font-mono-code text-[11px] leading-relaxed text-[#c3dacd] overflow-x-auto">
                        <div class="flex items-center justify-between pb-2 mb-2 border-b border-white/10 text-[10px] text-[#6b917d]">
                            <span>STATE // ENCLAVE EXECUTION</span>
                            <span class="text-[#4ade80]">STRICT_OK</span>
                        </div>
                        <pre id="proto-code-view" class="text-[#e0eee5] whitespace-pre-wrap">{
  "action": "challenge.create",
  "nonce": "0x4b78a992e10c...",
  "origin": "https://auth.vaultmesh.security",
  "rp_id": "vaultmesh.security",
  "user_verification": "required"
}</pre>
                    </div>

                </div>
            </div>

        </div>
    </section>

    <!-- ========================================================
         SECTION 3: Core Security Architecture Pillars
         ======================================================== -->
    <section class="py-18 lg:py-24 bg-[#f6f8f5] border-b border-[#e1e7e2]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="text-center max-w-2xl mx-auto mb-16">
                <span class="text-[11px] font-bold tracking-[0.2em] uppercase text-[#4f8664] font-mono-code block mb-3">
                    DEFENSE IN DEPTH
                </span>
                <h2 class="font-serif text-3xl sm:text-4xl lg:text-[40px] font-medium text-[#143229] tracking-tight">
                    Four pillars of uncompromising security.
                </h2>
                <p class="text-sm sm:text-base text-[#5c7367] mt-3">
                    Every layer of <?= htmlspecialchars($site_name, ENT_QUOTES, 'UTF-8') ?> is built to withstand modern credential leaks, automated credential stuffing, and session hijacking.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 lg:gap-7">
                
                <!-- Pillar 1 -->
                <div class="bg-white rounded-2xl p-6 sm:p-7 border border-[#e1e8e3] shadow-xs hover:shadow-md transition-shadow flex flex-col justify-between">
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-[#eef5f0] text-[#1f4a3b] border border-[#d2ded5] flex items-center justify-center mb-5">
                            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                            </svg>
                        </div>
                        <h3 class="font-serif text-xl font-medium text-[#143229] mb-2.5">Passkeys &amp; FIDO2</h3>
                        <p class="text-xs sm:text-sm text-[#5c7367] leading-relaxed">
                            Cryptographic key pairs stored in device hardware enclaves. Phishing becomes mathematically impossible because credentials cannot be lured onto fake login forms.
                        </p>
                    </div>
                    <div class="mt-6 pt-4 border-t border-[#edf2ee] flex items-center justify-between text-[11px] font-mono-code text-[#406856]">
                        <span>Standard: W3C WebAuthn</span>
                        <span class="font-bold text-[#1f4a3b]">Immune to Phishing</span>
                    </div>
                </div>

                <!-- Pillar 2 -->
                <div class="bg-white rounded-2xl p-6 sm:p-7 border border-[#e1e8e3] shadow-xs hover:shadow-md transition-shadow flex flex-col justify-between">
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-[#eef5f0] text-[#1f4a3b] border border-[#d2ded5] flex items-center justify-center mb-5">
                            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"/>
                                <polyline points="12 6 12 12 16 14"/>
                            </svg>
                        </div>
                        <h3 class="font-serif text-xl font-medium text-[#143229] mb-2.5">Sliding Session Isolation</h3>
                        <p class="text-xs sm:text-sm text-[#5c7367] leading-relaxed">
                            Ephemeral session tokens rotated automatically with each sensitive action. Bound to client IP subnets and protected by SameSite=Strict HTTP-only cookies.
                        </p>
                    </div>
                    <div class="mt-6 pt-4 border-t border-[#edf2ee] flex items-center justify-between text-[11px] font-mono-code text-[#406856]">
                        <span>Rotation: 15-Min TTL</span>
                        <span class="font-bold text-[#1f4a3b]">Anti-Hijack</span>
                    </div>
                </div>

                <!-- Pillar 3 -->
                <div class="bg-white rounded-2xl p-6 sm:p-7 border border-[#e1e8e3] shadow-xs hover:shadow-md transition-shadow flex flex-col justify-between">
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-[#eef5f0] text-[#1f4a3b] border border-[#d2ded5] flex items-center justify-center mb-5">
                            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                            </svg>
                        </div>
                        <h3 class="font-serif text-xl font-medium text-[#143229] mb-2.5">Zero-Knowledge Hashing</h3>
                        <p class="text-xs sm:text-sm text-[#5c7367] leading-relaxed">
                            Passwords are processed using adaptive Bcrypt work factors with isolated salt generation. Plaintext credentials are never written to disk or transmitted unhashed.
                        </p>
                    </div>
                    <div class="mt-6 pt-4 border-t border-[#edf2ee] flex items-center justify-between text-[11px] font-mono-code text-[#406856]">
                        <span>Cost Factor: 12 Iterations</span>
                        <span class="font-bold text-[#1f4a3b]">Zero Plaintext</span>
                    </div>
                </div>

                <!-- Pillar 4 -->
                <div class="bg-white rounded-2xl p-6 sm:p-7 border border-[#e1e8e3] shadow-xs hover:shadow-md transition-shadow flex flex-col justify-between">
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-[#eef5f0] text-[#1f4a3b] border border-[#d2ded5] flex items-center justify-center mb-5">
                            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                <polyline points="14 2 14 8 20 8"/>
                                <line x1="16" y1="13" x2="8" y2="13"/>
                                <line x1="16" y1="17" x2="8" y2="17"/>
                            </svg>
                        </div>
                        <h3 class="font-serif text-xl font-medium text-[#143229] mb-2.5">Immutable Audit Trail</h3>
                        <p class="text-xs sm:text-sm text-[#5c7367] leading-relaxed">
                            Complete visibility over every sign-in event, IP location change, and session lifecycle. Instant one-click global session revocation whenever you choose.
                        </p>
                    </div>
                    <div class="mt-6 pt-4 border-t border-[#edf2ee] flex items-center justify-between text-[11px] font-mono-code text-[#406856]">
                        <span>Audit: Append-Only</span>
                        <span class="font-bold text-[#1f4a3b]">Global Kill-Switch</span>
                    </div>
                </div>

            </div>

        </div>
    </section>

    <!-- ========================================================
         SECTION 4: Comparison Matrix (Bespoke vs Generic AI Slop)
         ======================================================== -->
    <section class="py-16 lg:py-24 bg-white border-b border-[#e1e7e2]">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="text-center max-w-2xl mx-auto mb-14">
                <span class="text-[11px] font-bold tracking-[0.2em] uppercase text-[#4f8664] font-mono-code block mb-3">
                    HONEST ENGINEERING STANDARDS
                </span>
                <h2 class="font-serif text-3xl sm:text-4xl font-medium text-[#143229] tracking-tight">
                    Why custom craft matters in authentication.
                </h2>
                <p class="text-sm sm:text-base text-[#5c7367] mt-3">
                    Generic templates and automated AI-generated sites compromise on session hygiene, load trackers, and store fragile tokens. See the difference:
                </p>
            </div>

            <div class="overflow-x-auto rounded-2xl border border-[#dce5de] shadow-xs">
                <table class="w-full text-left text-sm border-collapse">
                    <thead>
                        <tr class="bg-[#f2f6f3] border-b border-[#dce5de]">
                            <th class="py-4 px-6 text-xs font-bold uppercase tracking-wider text-[#406856]">Security Vector</th>
                            <th class="py-4 px-6 text-xs font-bold uppercase tracking-wider text-[#143229] bg-[#e6eee8]">
                                <?= htmlspecialchars($site_name, ENT_QUOTES, 'UTF-8') ?> Architecture
                            </th>
                            <th class="py-4 px-6 text-xs font-bold uppercase tracking-wider text-[#798e82]">
                                Generic AI Template / Plugin
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#e8efe9] text-xs sm:text-sm">
                        <tr>
                            <td class="py-4 px-6 font-semibold text-[#1c382e]">Third-Party Trackers &amp; Bloat</td>
                            <td class="py-4 px-6 bg-[#f7faf7] text-[#1f503d] font-medium flex items-center gap-2">
                                <span class="text-[#3ca166] text-base font-bold">✓</span>
                                <span>Zero trackers. 100% self-contained code.</span>
                            </td>
                            <td class="py-4 px-6 text-[#6c8075]">
                                12+ external analytics scripts &amp; advertising pixels
                            </td>
                        </tr>
                        <tr>
                            <td class="py-4 px-6 font-semibold text-[#1c382e]">Session Token Storage</td>
                            <td class="py-4 px-6 bg-[#f7faf7] text-[#1f503d] font-medium flex items-center gap-2">
                                <span class="text-[#3ca166] text-base font-bold">✓</span>
                                <span>HttpOnly, SameSite=Strict cookies with dynamic rotation</span>
                            </td>
                            <td class="py-4 px-6 text-[#6c8075]">
                                localStorage / sessionStorage (vulnerable to XSS leaks)
                            </td>
                        </tr>
                        <tr>
                            <td class="py-4 px-6 font-semibold text-[#1c382e]">Phishing &amp; Man-in-the-Middle</td>
                            <td class="py-4 px-6 bg-[#f7faf7] text-[#1f503d] font-medium flex items-center gap-2">
                                <span class="text-[#3ca166] text-base font-bold">✓</span>
                                <span>Origin-bound hardware credentials (FIDO2)</span>
                            </td>
                            <td class="py-4 px-6 text-[#6c8075]">
                                Easily spoofed standard password forms &amp; fake redirects
                            </td>
                        </tr>
                        <tr>
                            <td class="py-4 px-6 font-semibold text-[#1c382e]">Brute Force Throttling</td>
                            <td class="py-4 px-6 bg-[#f7faf7] text-[#1f503d] font-medium flex items-center gap-2">
                                <span class="text-[#3ca166] text-base font-bold">✓</span>
                                <span>Sliding-window IP &amp; subnet rate limiter (<span class="font-mono-code text-xs">tbl_rate_limit</span>)</span>
                            </td>
                            <td class="py-4 px-6 text-[#6c8075]">
                                None or annoying third-party captcha puzzles
                            </td>
                        </tr>
                        <tr>
                            <td class="py-4 px-6 font-semibold text-[#1c382e]">Handshake Latency</td>
                            <td class="py-4 px-6 bg-[#f7faf7] text-[#1f503d] font-medium flex items-center gap-2">
                                <span class="text-[#3ca166] text-base font-bold">✓</span>
                                <span>Under 12ms localized cryptographic response</span>
                            </td>
                            <td class="py-4 px-6 text-[#6c8075]">
                                400ms - 800ms slow multi-hop OAuth loops
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </section>

    <!-- ========================================================
         SECTION 5: Cryptographic Benchmarks & Standards Strip
         ======================================================== -->
    <section class="py-12 bg-[#122e25] text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                <div>
                    <div class="font-serif text-3xl sm:text-4xl lg:text-5xl font-medium text-[#86efac] mb-1">256-bit</div>
                    <div class="text-xs sm:text-sm text-[#9ec1af] font-mono-code uppercase tracking-wider">AES-GCM &amp; Ed25519</div>
                </div>
                <div>
                    <div class="font-serif text-3xl sm:text-4xl lg:text-5xl font-medium text-[#86efac] mb-1">&lt; 12ms</div>
                    <div class="text-xs sm:text-sm text-[#9ec1af] font-mono-code uppercase tracking-wider">Handshake Latency</div>
                </div>
                <div>
                    <div class="font-serif text-3xl sm:text-4xl lg:text-5xl font-medium text-[#86efac] mb-1">Zero</div>
                    <div class="text-xs sm:text-sm text-[#9ec1af] font-mono-code uppercase tracking-wider">Trackers or Ads</div>
                </div>
                <div>
                    <div class="font-serif text-3xl sm:text-4xl lg:text-5xl font-medium text-[#86efac] mb-1">100%</div>
                    <div class="text-xs sm:text-sm text-[#9ec1af] font-mono-code uppercase tracking-wider">Session Sovereignty</div>
                </div>
            </div>
        </div>
    </section>

    <!-- ========================================================
         SECTION 6: Technical Frequently Asked Questions
         ======================================================== -->
    <section class="py-16 lg:py-24 bg-[#f8faf8] border-b border-[#e1e7e2]">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="text-center mb-14">
                <span class="text-[11px] font-bold tracking-[0.2em] uppercase text-[#4f8664] font-mono-code block mb-3">
                    CLARITY &amp; PROTOCOL SPECS
                </span>
                <h2 class="font-serif text-3xl sm:text-4xl font-medium text-[#143229] tracking-tight">
                    Frequently asked questions.
                </h2>
            </div>

            <div class="space-y-4">
                
                <!-- FAQ Item 1 -->
                <div class="faq-item bg-white rounded-xl border border-[#dce5df] p-5 sm:p-6 transition-all">
                    <button type="button" class="faq-toggle w-full flex items-center justify-between text-left cursor-pointer">
                        <span class="font-serif text-lg sm:text-xl font-medium text-[#17372d]">
                            How does Passkey authentication protect my account from phishing?
                        </span>
                        <span class="faq-chevron ml-4 p-1 rounded-full bg-[#f1f6f2] text-[#4f8664] transition-transform duration-200">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </span>
                    </button>
                    <div class="faq-answer hidden pt-3 text-xs sm:text-sm text-[#536d60] leading-relaxed border-t border-[#edf2ee] mt-3">
                        Passkeys use public-key cryptography bound strictly to the domain (web origin). Your private key never leaves your secure device enclave (such as Apple Touch ID, Windows Hello, or Android Biometrics). Even if an attacker crafts an identical-looking fraudulent website, the browser will refuse to produce a signature because the origin domain does not match.
                    </div>
                </div>

                <!-- FAQ Item 2 -->
                <div class="faq-item bg-white rounded-xl border border-[#dce5df] p-5 sm:p-6 transition-all">
                    <button type="button" class="faq-toggle w-full flex items-center justify-between text-left cursor-pointer">
                        <span class="font-serif text-lg sm:text-xl font-medium text-[#17372d]">
                            How are passwords protected if I choose not to use a Passkey?
                        </span>
                        <span class="faq-chevron ml-4 p-1 rounded-full bg-[#f1f6f2] text-[#4f8664] transition-transform duration-200">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </span>
                    </button>
                    <div class="faq-answer hidden pt-3 text-xs sm:text-sm text-[#536d60] leading-relaxed border-t border-[#edf2ee] mt-3">
                        Passwords are authenticated via industry-standard adaptive Bcrypt hashes with unique, high-entropy cryptographic salts. Password verification is performed in constant time to thwart timing analysis attacks, and plaintext passwords never enter persistent database tables or log files.
                    </div>
                </div>

                <!-- FAQ Item 3 -->
                <div class="faq-item bg-white rounded-xl border border-[#dce5df] p-5 sm:p-6 transition-all">
                    <button type="button" class="faq-toggle w-full flex items-center justify-between text-left cursor-pointer">
                        <span class="font-serif text-lg sm:text-xl font-medium text-[#17372d]">
                            What happens if I lose my device or need to sign out everywhere?
                        </span>
                        <span class="faq-chevron ml-4 p-1 rounded-full bg-[#f1f6f2] text-[#4f8664] transition-transform duration-200">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </span>
                    </button>
                    <div class="faq-answer hidden pt-3 text-xs sm:text-sm text-[#536d60] leading-relaxed border-t border-[#edf2ee] mt-3">
                        Our session management architecture maintains an active device ledger. You can trigger an instantaneous global session revocation from your account dashboard, which invalidates all ephemeral session cookies across every desktop, tablet, and mobile device immediately.
                    </div>
                </div>

                <!-- FAQ Item 4 -->
                <div class="faq-item bg-white rounded-xl border border-[#dce5df] p-5 sm:p-6 transition-all">
                    <button type="button" class="faq-toggle w-full flex items-center justify-between text-left cursor-pointer">
                        <span class="font-serif text-lg sm:text-xl font-medium text-[#17372d]">
                            Does <?= htmlspecialchars($site_name, ENT_QUOTES, 'UTF-8') ?> sell or track authentication activity?
                        </span>
                        <span class="faq-chevron ml-4 p-1 rounded-full bg-[#f1f6f2] text-[#4f8664] transition-transform duration-200">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </span>
                    </button>
                    <div class="faq-answer hidden pt-3 text-xs sm:text-sm text-[#536d60] leading-relaxed border-t border-[#edf2ee] mt-3">
                        Never. We strictly reject marketing pixels, cross-site trackers, and advertising telemetry. Your authentication sessions exist solely to verify your identity and protect your data.
                    </div>
                </div>

            </div>

        </div>
    </section>

    <!-- ========================================================
         SECTION 7: Final Conversion Call-to-Action
         ======================================================== -->
    <section class="py-16 lg:py-24 bg-gradient-to-b from-[#f8faf8] to-[#eef3ef]">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="rounded-3xl bg-gradient-to-br from-[#173f35] to-[#0e2720] border border-[#2b6855]/60 p-8 sm:p-12 lg:p-16 text-center text-white shadow-2xl relative overflow-hidden">
                
                <!-- Ambient lighting decoration -->
                <div class="absolute -top-24 -right-24 w-80 h-80 bg-[#4fa572]/20 rounded-full blur-3xl pointer-events-none"></div>
                <div class="absolute -bottom-24 -left-24 w-80 h-80 bg-[#122e25] rounded-full blur-3xl pointer-events-none"></div>

                <div class="relative z-10 max-w-2xl mx-auto">
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/10 text-xs font-mono-code text-[#a7f3d0] border border-white/15 mb-6">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#4ade80] inline-block animate-pulse"></span>
                        READY FOR MODERN WORKFLOWS
                    </div>
                    <h2 class="font-serif text-3xl sm:text-4xl lg:text-5xl font-medium tracking-tight mb-4">
                        Experience authentication without compromise.
                    </h2>
                    <p class="text-sm sm:text-base text-[#c7decfa] leading-relaxed mb-8">
                        Set up your protected account in less than a minute. Straightforward access, protected credentials, and complete peace of mind.
                    </p>
                    <div class="flex flex-wrap items-center justify-center gap-4">
                        <a href="<?= SITE_URL ?>/register" class="inline-flex items-center gap-2 px-7 py-3.5 rounded-xl bg-white hover:bg-[#f1f6f2] text-[#123328] font-bold text-sm shadow-md transition-all transform hover:-translate-y-0.5">
                            <span>Get started in 30 seconds</span>
                            <span class="text-base">→</span>
                        </a>
                        <a href="<?= SITE_URL ?>/login" class="inline-flex items-center gap-2 px-6 py-3.5 rounded-xl bg-white/10 hover:bg-white/15 text-white font-semibold text-sm border border-white/20 transition-colors">
                            <span>Log in to your account</span>
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </section>

</main>

<!-- Interactive Script for Terminal Simulator & Protocol Tabs -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    // 1. Enclave Terminal Tabs
    const tabButtons = document.querySelectorAll(".enclave-tab-btn");
    const panels = document.querySelectorAll(".enclave-panel");

    tabButtons.forEach(btn => {
        btn.addEventListener("click", () => {
            tabButtons.forEach(b => {
                b.classList.remove("bg-[#173f35]", "text-white");
                b.classList.add("text-[#8da597]");
            });
            btn.classList.add("bg-[#173f35]", "text-white");
            btn.classList.remove("text-[#8da597]");

            const targetId = btn.getAttribute("data-target");
            panels.forEach(p => {
                if (p.id === targetId) {
                    p.classList.remove("hidden");
                } else {
                    p.classList.add("hidden");
                }
            });
        });
    });

    // 2. Simulate Cryptographic Challenge Button
    const reverifyBtn = document.getElementById("btn-reverify");
    const dynNonce = document.getElementById("dyn-nonce");
    if (reverifyBtn && dynNonce) {
        reverifyBtn.addEventListener("click", () => {
            const hexChars = "0123456789ABCDEF";
            let randHex = "0x";
            for (let i = 0; i < 4; i++) randHex += hexChars[Math.floor(Math.random() * 16)];
            randHex += "...";
            for (let i = 0; i < 4; i++) randHex += hexChars[Math.floor(Math.random() * 16)];
            dynNonce.textContent = randHex;
            dynNonce.classList.add("text-white");
            setTimeout(() => {
                dynNonce.classList.remove("text-white");
            }, 600);
        });
    }

    // 3. Protocol Steps Interactive Selector
    const stepButtons = document.querySelectorAll(".proto-step-btn");
    const stepBadge = document.getElementById("proto-step-badge");
    const stepTitle = document.getElementById("proto-step-title");
    const stepDesc = document.getElementById("proto-step-desc");
    const spec1 = document.getElementById("proto-spec-1");
    const spec2 = document.getElementById("proto-spec-2");
    const codeView = document.getElementById("proto-code-view");

    const stepData = {
        "1": {
            badge: "PROTOCOL SPECIFICATION // RFC 8812 - STEP 01",
            title: "Cryptographic Challenge & Replay Prevention",
            desc: "When an authentication attempt begins, the VaultMesh server mints a single-use 32-byte cryptographic nonce mixed with client hardware identifiers. This ensures that even if an attacker intercepts network packets, they cannot replay the request.",
            spec1: "Entropy: 256-bit CSPRNG",
            spec2: "Time-to-Live: 60 Seconds",
            code: `{\n  "action": "challenge.create",\n  "nonce": "0x4b78a992e10c...",\n  "origin": "https://auth.vaultmesh.security",\n  "rp_id": "vaultmesh.security",\n  "user_verification": "required"\n}`
        },
        "2": {
            badge: "CLIENT-SIDE SECURITY ENCLAVE // FIDO2 - STEP 02",
            title: "Local Hardware Signature via Biometrics or Key",
            desc: "The client operating system prompts the user for Touch ID, Face ID, or a physical YubiKey. The private key remains forever locked inside the device's hardware chip; only an Ed25519 or P-256 digital signature of the challenge is computed.",
            spec1: "Algorithm: ES256 / EdDSA",
            spec2: "Key Storage: Apple T2 / TPM 2.0",
            code: `{\n  "authenticatorData": "SZYN5YgOjQ...",\n  "clientDataJSON": "eyJ0eXBlIjoi...",\n  "signature": "MEQCIF6y8d1Z...",\n  "userHandle": "usr_9f40c18da2"\n}`
        },
        "3": {
            badge: "ZERO-KNOWLEDGE VERIFICATION // ED25519 - STEP 03",
            title: "Constant-Time Server Assertion Verification",
            desc: "The server checks the cryptographic signature against the user's registered public key in constant time to eliminate side-channel timing analysis. No passwords, PINs, or raw biometrics are ever received by the server.",
            spec1: "Comparison: Constant-Time 0x00",
            spec2: "Timing Attack Resistant: Yes",
            code: `{\n  "verification": "SUCCESS",\n  "signature_valid": true,\n  "public_key_match": true,\n  "uv_flag": 1,\n  "risk_score": 0.001\n}`
        },
        "4": {
            badge: "SESSION LIFECYCLE // SAME-SITE STRICT - STEP 04",
            title: "Ephemeral Rotating Session Minting",
            desc: "Once validated, an isolated session cookie is issued with HttpOnly, Secure, and SameSite=Strict flags. Session tokens automatically rotate on a 15-minute sliding window and are bound to the client's network subnet.",
            spec1: "Flags: HttpOnly; SameSite=Strict",
            spec2: "Sliding TTL: 15-Minute Window",
            code: `{\n  "session_token": "sess_8a09f3e...",\n  "issued_at": 1731676800,\n  "expires_in": 900,\n  "device_bound": true,\n  "auto_rotate": true\n}`
        }
    };

    stepButtons.forEach(btn => {
        btn.addEventListener("click", () => {
            const step = btn.getAttribute("data-step");
            stepButtons.forEach(b => {
                b.classList.remove("border-[#173f35]", "bg-[#f4f8f5]", "shadow-xs");
                b.classList.add("border-[#e1e7e2]", "bg-white");
                const ind = b.querySelector(".step-indicator");
                if (ind) {
                    ind.className = "w-2 h-2 rounded-full bg-transparent border border-[#96a99e] step-indicator";
                }
            });
            btn.classList.add("border-[#173f35]", "bg-[#f4f8f5]", "shadow-xs");
            btn.classList.remove("border-[#e1e7e2]", "bg-white");
            const activeInd = btn.querySelector(".step-indicator");
            if (activeInd) {
                activeInd.className = "w-2 h-2 rounded-full bg-[#173f35] step-indicator";
            }

            if (stepData[step]) {
                stepBadge.textContent = stepData[step].badge;
                stepTitle.textContent = stepData[step].title;
                stepDesc.textContent = stepData[step].desc;
                spec1.textContent = stepData[step].spec1;
                spec2.textContent = stepData[step].spec2;
                codeView.textContent = stepData[step].code;
            }
        });
    });

    // 4. FAQ Accordion
    const faqToggles = document.querySelectorAll(".faq-toggle");
    faqToggles.forEach(toggle => {
        toggle.addEventListener("click", () => {
            const parent = toggle.closest(".faq-item");
            const answer = parent.querySelector(".faq-answer");
            const chevron = toggle.querySelector(".faq-chevron svg");
            const isOpen = !answer.classList.contains("hidden");

            // Close all
            document.querySelectorAll(".faq-answer").forEach(a => a.classList.add("hidden"));
            document.querySelectorAll(".faq-chevron svg").forEach(c => c.style.transform = "rotate(0deg)");

            if (!isOpen) {
                answer.classList.remove("hidden");
                if (chevron) chevron.style.transform = "rotate(180deg)";
            }
        });
    });
});
</script>

<?php include('components/footer.php'); ?>
