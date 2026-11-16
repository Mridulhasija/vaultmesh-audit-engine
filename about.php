<?php
require_once __DIR__ . '/include/header_config.php';
$pageCanonical = SITE_URL . '/about';
$meta_title = 'VaultMesh — Secure Authentication & Audit Portal';
$meta_desc = 'Learn about ' . $site_name . '\'s hardware-bound passkeys, zero-knowledge verification, and cryptographic access architecture built for absolute trust.';
include('components/header.php');
$pageHeroTitle = 'Architected for Absolute Cryptographic Trust';
?>

<!-- Custom Editorial Hero for About Us -->
<section class="relative overflow-hidden bg-gradient-to-b from-[#13372c] via-[#173f35] to-[#112d24] text-[#f4f7f4] px-4 pt-20 pb-24 md:pt-28 md:pb-32 border-b border-[#0d231c]">
    <!-- Ambient architectural linework & radiant glows -->
    <div class="pointer-events-none absolute inset-0 opacity-15 enclave-grid-bg"></div>
    <div class="pointer-events-none absolute -top-36 right-1/4 w-[500px] h-[500px] rounded-full bg-[#3b8c66]/20 blur-3xl"></div>
    <div class="pointer-events-none absolute -bottom-36 left-1/4 w-[450px] h-[450px] rounded-full bg-[#0a1e17]/50 blur-3xl"></div>

    <div class="relative max-w-5xl mx-auto text-center">
        <!-- Trust badge -->
        <div class="inline-flex items-center gap-2.5 px-4 py-1.5 rounded-full bg-white/10 border border-white/15 text-[11px] font-semibold tracking-wider text-[#a7f3d0] shadow-sm mb-6 backdrop-blur-sm">
            <span class="w-2 h-2 rounded-full bg-[#4ade80] inline-block animate-pulse"></span>
            <span class="uppercase tracking-widest font-mono text-[10.5px]">THE VAULTMESH ARCHITECTURAL MANIFESTO</span>
        </div>

        <h1 class="font-serif text-3xl sm:text-5xl md:text-6xl lg:text-[64px] font-medium tracking-tight text-white leading-[1.12] mb-6">
            Authentication built on mathematical proof, <br class="hidden sm:inline" />
            <span class="italic text-[#86efac]">not misplaced trust.</span>
        </h1>

        <p class="text-base sm:text-xl text-[#c5ded0] leading-relaxed max-w-3xl mx-auto font-normal mb-10">
            For decades, digital identity relied on an unfixable flaw: asking humans to remember secret strings and handing them across the wire. <?= htmlspecialchars($site_name, ENT_QUOTES, 'UTF-8') ?> replaces vulnerable legacy credentials with client-isolated hardware enclaves and provable zero-knowledge attestation.
        </p>

        <!-- Breadcrumb & Status Ribbon -->
        <div class="flex flex-wrap items-center justify-center gap-3 text-xs text-[#9ebaa9] font-sans">
            <a href="<?= SITE_URL ?>/" class="hover:text-white transition-colors">Home</a>
            <span class="text-[#456b5b]">/</span>
            <span class="text-white font-medium">About</span>
            <span class="text-[#456b5b]">·</span>
            <span class="inline-flex items-center gap-1.5 text-[#86efac] font-mono text-[11px]">
                <span class="w-1.5 h-1.5 rounded-full bg-[#4ade80]"></span>
                RFC 8812 &amp; FIDO2 COMPLIANT
            </span>
        </div>
    </div>
</section>

<!-- ========================================================
     SECTION 1: Core Metrics of Cryptographic Assurance
     ======================================================== -->
<section class="relative -mt-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 z-20">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <div class="about-metric-card bg-white rounded-2xl p-6 border border-[#d8e3dc] shadow-lg shadow-[#173f35]/06 hover:border-[#2f6f55] transition-all">
            <div class="text-[11px] font-mono uppercase tracking-wider text-[#637d70] mb-2">Stored Plaintext</div>
            <div class="font-serif text-4xl font-semibold text-[#143329] mb-1">0 Bits</div>
            <p class="text-xs text-[#526c5f] leading-relaxed">No raw passwords, PINs, or biometrics ever touch our database servers.</p>
        </div>

        <div class="about-metric-card bg-white rounded-2xl p-6 border border-[#d8e3dc] shadow-lg shadow-[#173f35]/06 hover:border-[#2f6f55] transition-all">
            <div class="text-[11px] font-mono uppercase tracking-wider text-[#637d70] mb-2">Passkey Security</div>
            <div class="font-serif text-4xl font-semibold text-[#143329] mb-1">100%</div>
            <p class="text-xs text-[#526c5f] leading-relaxed">Hardware-backed FIDO2 / WebAuthn keys bound to Apple T2/TPM 2.0 chips.</p>
        </div>

        <div class="about-metric-card bg-white rounded-2xl p-6 border border-[#d8e3dc] shadow-lg shadow-[#173f35]/06 hover:border-[#2f6f55] transition-all">
            <div class="text-[11px] font-mono uppercase tracking-wider text-[#637d70] mb-2">Attestation Speed</div>
            <div class="font-serif text-4xl font-semibold text-[#143329] mb-1">4.2 ms</div>
            <p class="text-xs text-[#526c5f] leading-relaxed">Constant-time cryptographic challenge computation and session verification.</p>
        </div>

        <div class="about-metric-card bg-white rounded-2xl p-6 border border-[#d8e3dc] shadow-lg shadow-[#173f35]/06 hover:border-[#2f6f55] transition-all">
            <div class="text-[11px] font-mono uppercase tracking-wider text-[#637d70] mb-2">External Trackers</div>
            <div class="font-serif text-4xl font-semibold text-[#143329] mb-1">Zero</div>
            <p class="text-xs text-[#526c5f] leading-relaxed">No tracking scripts, surveillance cookies, or third-party behavioral analytics.</p>
        </div>

    </div>
</section>

<!-- ========================================================
     SECTION 2: The Foundational Thesis (Editorial Two-Column)
     ======================================================== -->
<section class="py-20 lg:py-28 bg-[#f6f8f5] border-b border-[#e1e7e2]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16 items-start">
            
            <div class="lg:col-span-5">
                <span class="inline-flex items-center gap-2 text-[10.5px] font-mono font-bold tracking-[0.2em] uppercase text-[#276652] mb-3 bg-[#e4ede6] px-3 py-1 rounded-md">
                    OUR CORE ORIGIN
                </span>
                <h2 class="font-serif text-3xl sm:text-4xl lg:text-5xl font-medium tracking-tight text-[#143329] leading-[1.15] mb-6">
                    Why the conventional login box failed every user.
                </h2>
                <div class="space-y-4 text-base text-[#4a6356] leading-relaxed">
                    <p>
                        The traditional authentication model was invented in the 1960s for time-shared mainframes: a shared secret entered by a human operator. In the modern hyper-connected web, that paradigm collapsed under the weight of credential stuffing, phishing proxies, SIM swaps, and corporate database breaches.
                    </p>
                    <p>
                        Legacy identity brokers stepped in to offer &ldquo;Sign in with Tech Giant&rdquo;—turning user authentication into an advertising surveillance pipeline. Every sign-in became an opportunity to track who you are, what device you use, and which services you access.
                    </p>
                    <p class="font-medium text-[#18392f]">
                        We engineered <?= htmlspecialchars($site_name, ENT_QUOTES, 'UTF-8') ?> to permanently abolish this compromise. Security is not an afterthought or an enterprise upsell; it is an intrinsic mathematical property of our architecture.
                    </p>
                </div>
            </div>

            <div class="lg:col-span-7">
                <div class="bg-white rounded-3xl p-7 sm:p-9 border border-[#dce5df] shadow-sm relative overflow-hidden">
                    <div class="flex items-center justify-between pb-6 border-b border-[#e9efe9] mb-6">
                        <span class="font-serif text-xl sm:text-2xl font-medium text-[#153a2f]">The Dual Paradigm Shift</span>
                        <span class="text-xs font-mono font-semibold text-[#276652] bg-[#f0f6f2] px-3 py-1 rounded-full border border-[#d6e4db]">PARADIGM AUDIT</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Legacy Column -->
                        <div class="rounded-2xl bg-[#faf5f4] p-5 border border-[#eedad6]">
                            <div class="flex items-center gap-2 text-xs font-bold font-mono tracking-wider text-[#9e3b2b] uppercase mb-3">
                                <span class="w-2 h-2 rounded-full bg-[#dc4437]"></span>
                                Legacy Web Authentication
                            </div>
                            <ul class="space-y-2.5 text-xs text-[#6e4640] leading-relaxed">
                                <li class="flex items-start gap-2">
                                    <span class="text-[#b54a3a] font-bold">✕</span>
                                    <span>Passwords sent across network and easily phished via mimic domains.</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="text-[#b54a3a] font-bold">✕</span>
                                    <span>Centralized password hashes susceptible to offline GPU cracking.</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="text-[#b54a3a] font-bold">✕</span>
                                    <span>Long-lived session cookies vulnerable to browser info-stealer malware.</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="text-[#b54a3a] font-bold">✕</span>
                                    <span>User metadata harvested by ad networks and third-party trackers.</span>
                                </li>
                            </ul>
                        </div>

                        <!-- VaultMesh Enclave Column -->
                        <div class="rounded-2xl bg-[#f2f7f4] p-5 border border-[#cbe0d3]">
                            <div class="flex items-center gap-2 text-xs font-bold font-mono tracking-wider text-[#1e523f] uppercase mb-3">
                                <span class="w-2 h-2 rounded-full bg-[#3fa86f]"></span>
                                VaultMesh Enclave Protocol
                            </div>
                            <ul class="space-y-2.5 text-xs text-[#2b5949] leading-relaxed">
                                <li class="flex items-start gap-2">
                                    <span class="text-[#2b8857] font-bold">✓</span>
                                    <span>Private keys permanently sealed inside hardware; mathematically immune to phishing.</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="text-[#2b8857] font-bold">✓</span>
                                    <span>Zero-knowledge public key cryptography; no shared secret stored on the server.</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="text-[#2b8857] font-bold">✓</span>
                                    <span>15-minute sliding session window with strict SameSite and anti-CSRF token rotation.</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="text-[#2b8857] font-bold">✓</span>
                                    <span>Zero surveillance, zero analytics scripts, and 100% sovereign user privacy.</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Enclave Seal Quote -->
                    <div class="mt-8 pt-6 border-t border-[#e9efe9] flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                        <div class="text-xs text-[#526b5e]">
                            <strong>Cryptographic Principle:</strong> &ldquo;Never authenticate by revealing the secret. Authenticate by proving ownership of the secret.&rdquo;
                        </div>
                        <a href="<?= SITE_URL ?>/register" class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#183f34] hover:text-[#286b55] hover:underline flex-shrink-0">
                            <span>Experience the difference</span>
                            <span>→</span>
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- ========================================================
     SECTION 3: The Four Pillars of Our Cryptographic Enclave
     ======================================================== -->
<section class="py-20 lg:py-28 bg-white border-b border-[#e1e7e2]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="text-center max-w-3xl mx-auto mb-16">
            <span class="inline-flex items-center gap-2 text-[10.5px] font-mono font-bold tracking-[0.2em] uppercase text-[#276652] mb-3 bg-[#e4ede6] px-3.5 py-1.5 rounded-full">
                ARCHITECTURE BLUEPRINT
            </span>
            <h2 class="font-serif text-3xl sm:text-5xl font-medium tracking-tight text-[#143329] leading-tight mb-4">
                The Four Pillars of Sovereign Security
            </h2>
            <p class="text-base sm:text-lg text-[#526b5e]">
                Every component of <?= htmlspecialchars($site_name, ENT_QUOTES, 'UTF-8') ?> is audited against formal cryptographic proofs to withstand nation-state side-channel attacks and brute-force vectors.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            
            <!-- Pillar 1 -->
            <div class="pillar-card group bg-[#fbfdfb] rounded-3xl p-8 border border-[#e1eae3] hover:border-[#173f35] hover:shadow-xl hover:shadow-[#173f35]/08 transition-all">
                <div class="flex items-center justify-between mb-6">
                    <span class="w-12 h-12 rounded-2xl bg-[#173f35] text-[#86efac] flex items-center justify-center shadow-md">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </span>
                    <span class="font-mono text-xs font-bold text-[#2d6f56] bg-[#eef5f0] px-3 py-1 rounded-full">PILLAR 01 // FIDO2</span>
                </div>
                <h3 class="font-serif text-2xl font-medium text-[#143329] mb-3 group-hover:text-[#173f35] transition-colors">
                    Hardware Secure Enclave Isolation
                </h3>
                <p class="text-sm text-[#4e675b] leading-relaxed mb-6">
                    Credentials exist exclusively inside device hardware chips—such as Apple&rsquo;s Secure Enclave, Android StrongBox, or Windows TPM 2.0. Keys are non-extractable, meaning even physical device root access cannot leak your private keys.
                </p>
                <div class="space-y-2 text-xs font-mono text-[#5f7a6d] pt-4 border-t border-[#e6eee8]">
                    <div class="flex justify-between">
                        <span>Curve Standard:</span>
                        <span class="text-[#1a4436] font-semibold">NIST P-256 / Ed25519</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Biometric Relay:</span>
                        <span class="text-[#1a4436] font-semibold">Local-Only (Touch ID / Face ID)</span>
                    </div>
                </div>
            </div>

            <!-- Pillar 2 -->
            <div class="pillar-card group bg-[#fbfdfb] rounded-3xl p-8 border border-[#e1eae3] hover:border-[#173f35] hover:shadow-xl hover:shadow-[#173f35]/08 transition-all">
                <div class="flex items-center justify-between mb-6">
                    <span class="w-12 h-12 rounded-2xl bg-[#173f35] text-[#86efac] flex items-center justify-center shadow-md">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </span>
                    <span class="font-mono text-xs font-bold text-[#2d6f56] bg-[#eef5f0] px-3 py-1 rounded-full">PILLAR 02 // ARGON2ID</span>
                </div>
                <h3 class="font-serif text-2xl font-medium text-[#143329] mb-3 group-hover:text-[#173f35] transition-colors">
                    Zero-Knowledge Password Vaulting
                </h3>
                <p class="text-sm text-[#4e675b] leading-relaxed mb-6">
                    When password authentication is selected, our vault implements memory-hard Argon2id key derivation combined with CSPRNG salting. This makes offline dictionary and custom GPU/ASIC rainbow attacks economically unfeasible.
                </p>
                <div class="space-y-2 text-xs font-mono text-[#5f7a6d] pt-4 border-t border-[#e6eee8]">
                    <div class="flex justify-between">
                        <span>Hash Derivation:</span>
                        <span class="text-[#1a4436] font-semibold">Argon2id (m=64MB, t=4, p=2)</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Side-Channel Defense:</span>
                        <span class="text-[#1a4436] font-semibold">Constant-Time Byte Verification</span>
                    </div>
                </div>
            </div>

            <!-- Pillar 3 -->
            <div class="pillar-card group bg-[#fbfdfb] rounded-3xl p-8 border border-[#e1eae3] hover:border-[#173f35] hover:shadow-xl hover:shadow-[#173f35]/08 transition-all">
                <div class="flex items-center justify-between mb-6">
                    <span class="w-12 h-12 rounded-2xl bg-[#173f35] text-[#86efac] flex items-center justify-center shadow-md">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                    </span>
                    <span class="font-mono text-xs font-bold text-[#2d6f56] bg-[#eef5f0] px-3 py-1 rounded-full">PILLAR 03 // SLIDING TTL</span>
                </div>
                <h3 class="font-serif text-2xl font-medium text-[#143329] mb-3 group-hover:text-[#173f35] transition-colors">
                    Sliding-Window Ephemeral Sessions
                </h3>
                <p class="text-sm text-[#4e675b] leading-relaxed mb-6">
                    Rather than risky long-lived authorization tokens, sessions are cryptographically bound to a 15-minute sliding window with strict cookie policies. Session identifiers automatically rotate on high-risk interactions and terminate upon anomaly detection.
                </p>
                <div class="space-y-2 text-xs font-mono text-[#5f7a6d] pt-4 border-t border-[#e6eee8]">
                    <div class="flex justify-between">
                        <span>Cookie Envelope:</span>
                        <span class="text-[#1a4436] font-semibold">HttpOnly; SameSite=Strict; Secure</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Anti-CSRF Entropy:</span>
                        <span class="text-[#1a4436] font-semibold">256-bit Cryptographic Nonce</span>
                    </div>
                </div>
            </div>

            <!-- Pillar 4 -->
            <div class="pillar-card group bg-[#fbfdfb] rounded-3xl p-8 border border-[#e1eae3] hover:border-[#173f35] hover:shadow-xl hover:shadow-[#173f35]/08 transition-all">
                <div class="flex items-center justify-between mb-6">
                    <span class="w-12 h-12 rounded-2xl bg-[#173f35] text-[#86efac] flex items-center justify-center shadow-md">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                        </svg>
                    </span>
                    <span class="font-mono text-xs font-bold text-[#2d6f56] bg-[#eef5f0] px-3 py-1 rounded-full">PILLAR 04 // ZERO TELEMETRY</span>
                </div>
                <h3 class="font-serif text-2xl font-medium text-[#143329] mb-3 group-hover:text-[#173f35] transition-colors">
                    Sovereign Privacy &amp; Zero Trackers
                </h3>
                <p class="text-sm text-[#4e675b] leading-relaxed mb-6">
                    We deliberately reject corporate analytics, pixel trackers, and cross-site telemetry. Your logins, device footprints, and session metadata belong solely to you. We do not sell, broker, or monetize user interaction graphs.
                </p>
                <div class="space-y-2 text-xs font-mono text-[#5f7a6d] pt-4 border-t border-[#e6eee8]">
                    <div class="flex justify-between">
                        <span>Third-Party Scripts:</span>
                        <span class="text-[#1a4436] font-semibold">Strict Zero-Inclusion Policy</span>
                    </div>
                    <div class="flex justify-between">
                        <span>GDPR / CCPA Status:</span>
                        <span class="text-[#1a4436] font-semibold">Privacy-by-Design Verified</span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- ========================================================
     SECTION 4: The Interactive Attestation Pipeline Visualizer
     ======================================================== -->
<section class="py-20 lg:py-28 bg-[#122e25] text-white overflow-hidden relative">
    <div class="absolute inset-0 opacity-10 enclave-grid-bg pointer-events-none"></div>
    <div class="absolute top-0 right-1/3 w-96 h-96 bg-[#3b8c66]/20 rounded-full blur-3xl pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        
        <div class="text-center max-w-3xl mx-auto mb-16">
            <span class="inline-flex items-center gap-2 text-[10.5px] font-mono font-bold tracking-[0.2em] uppercase text-[#86efac] mb-3 bg-white/10 px-3.5 py-1.5 rounded-full border border-white/10">
                FORMAL PROTOCOL PIPELINE
            </span>
            <h2 class="font-serif text-3xl sm:text-5xl font-medium tracking-tight text-white leading-tight mb-4">
                How an Attestation is Proven in Real Time
            </h2>
            <p class="text-base text-[#a5c6b4]">
                See the exact lifecycle of an authentication challenge from the initial client request to zero-knowledge server verification.
            </p>
        </div>

        <!-- 4 Step Attestation Flow Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-12">
            
            <div class="bg-[#0b1f18] p-6 rounded-2xl border border-white/10 relative hover:border-[#4ade80]/50 transition-all">
                <div class="w-8 h-8 rounded-lg bg-[#1a4738] text-[#86efac] font-mono font-bold text-xs flex items-center justify-center mb-4">
                    01
                </div>
                <h4 class="font-serif text-lg font-medium text-white mb-2">Entropy Nonce Mint</h4>
                <p class="text-xs text-[#9ebaa9] leading-relaxed mb-4">
                    Client requests authentication. The server generates a single-use 32-byte CSPRNG nonce to prevent replay attacks.
                </p>
                <div class="font-mono text-[10px] text-[#4ade80] bg-black/40 p-2 rounded border border-white/5 truncate">
                    nonce: 0x4f7b...e912
                </div>
            </div>

            <div class="bg-[#0b1f18] p-6 rounded-2xl border border-white/10 relative hover:border-[#4ade80]/50 transition-all">
                <div class="w-8 h-8 rounded-lg bg-[#1a4738] text-[#86efac] font-mono font-bold text-xs flex items-center justify-center mb-4">
                    02
                </div>
                <h4 class="font-serif text-lg font-medium text-white mb-2">Hardware Signature</h4>
                <p class="text-xs text-[#9ebaa9] leading-relaxed mb-4">
                    The user approves Touch ID or Face ID. The private key signs the nonce inside the secure hardware chip.
                </p>
                <div class="font-mono text-[10px] text-[#4ade80] bg-black/40 p-2 rounded border border-white/5 truncate">
                    sign(P-256, nonce)
                </div>
            </div>

            <div class="bg-[#0b1f18] p-6 rounded-2xl border border-white/10 relative hover:border-[#4ade80]/50 transition-all">
                <div class="w-8 h-8 rounded-lg bg-[#1a4738] text-[#86efac] font-mono font-bold text-xs flex items-center justify-center mb-4">
                    03
                </div>
                <h4 class="font-serif text-lg font-medium text-white mb-2">Constant-Time Verify</h4>
                <p class="text-xs text-[#9ebaa9] leading-relaxed mb-4">
                    The server verifies the digital signature using the public key. Execution time is mathematically constant (0 side-channel leak).
                </p>
                <div class="font-mono text-[10px] text-[#4ade80] bg-black/40 p-2 rounded border border-white/5 truncate">
                    constant_time_cmp() = 0x0
                </div>
            </div>

            <div class="bg-[#0b1f18] p-6 rounded-2xl border border-white/10 relative hover:border-[#4ade80]/50 transition-all">
                <div class="w-8 h-8 rounded-lg bg-[#1a4738] text-[#86efac] font-mono font-bold text-xs flex items-center justify-center mb-4">
                    04
                </div>
                <h4 class="font-serif text-lg font-medium text-white mb-2">Sliding Session Seal</h4>
                <p class="text-xs text-[#9ebaa9] leading-relaxed mb-4">
                    An isolated HttpOnly, SameSite=Strict cookie envelope is issued with an auto-rotating 15-minute sliding session window.
                </p>
                <div class="font-mono text-[10px] text-[#4ade80] bg-black/40 p-2 rounded border border-white/5 truncate">
                    Set-Cookie: SameSite=Strict
                </div>
            </div>

        </div>

        <!-- Terminal Console Preview -->
        <div class="max-w-3xl mx-auto bg-[#071610] rounded-2xl border border-white/15 p-5 font-mono text-xs shadow-2xl">
            <div class="flex items-center justify-between pb-3 border-b border-white/10 mb-4 text-[11px] text-[#719582]">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-[#ef4444] inline-block opacity-75"></span>
                    <span class="w-2.5 h-2.5 rounded-full bg-[#eab308] inline-block opacity-75"></span>
                    <span class="w-2.5 h-2.5 rounded-full bg-[#22c55e] inline-block opacity-75"></span>
                    <span class="ml-2 font-medium text-[#c5ded0]">vaultmesh-protocol-attestation.log</span>
                </div>
                <span class="text-[#86efac] text-[10px] tracking-wider uppercase font-semibold">LIVE ATTESTATION VERIFIED</span>
            </div>
            <pre class="text-[#8da597] leading-relaxed overflow-x-auto whitespace-pre font-mono text-[11.5px]">
<span class="text-[#517564]">[15:06:32.401]</span> <span class="text-[#86efac]">INFO</span>  attestation_init: origin="https://auth.vaultmesh.security" client_ip="::ffff:127.0.0.1"
<span class="text-[#517564]">[15:06:32.403]</span> <span class="text-[#86efac]">INFO</span>  csprng_nonce_mint: entropy_bits=256 nonce=0x83e29f10a7cb540e1189...
<span class="text-[#517564]">[15:06:32.405]</span> <span class="text-[#86efac]">INFO</span>  fido2_assertion_received: alg=ES256 user_verification=required
<span class="text-[#517564]">[15:06:32.407]</span> <span class="text-[#86efac]">INFO</span>  crypto_verify_signature: matched_public_key=true timing=4.2ms
<span class="text-[#517564]">[15:06:32.408]</span> <span class="text-[#86efac]">OK</span>    session_envelope_sealed: flags=[HttpOnly, Secure, SameSite=Strict]
            </pre>
        </div>

    </div>
</section>

<!-- ========================================================
     SECTION 5: Formal Standards & Verification Compliance
     ======================================================== -->
<section class="py-20 lg:py-24 bg-white border-b border-[#e1e7e2]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="text-center max-w-2xl mx-auto mb-14">
            <span class="inline-flex items-center gap-2 text-[10.5px] font-mono font-bold tracking-[0.2em] uppercase text-[#276652] mb-3 bg-[#e4ede6] px-3.5 py-1.5 rounded-full">
                FORMAL CERTIFICATION MATRIX
            </span>
            <h2 class="font-serif text-3xl sm:text-4xl font-medium tracking-tight text-[#143329] leading-tight mb-3">
                Built to Worldwide Security Standards
            </h2>
            <p class="text-sm text-[#526b5e]">
                We do not invent proprietary obscure cryptography. We rigorously implement open standards audited by global academic and security bodies.
            </p>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
            
            <div class="p-5 rounded-2xl bg-[#f7faf7] border border-[#dce5df] text-center hover:border-[#173f35] transition-colors">
                <div class="font-mono text-xs font-bold text-[#173f35] mb-1">FIDO2 / WebAuthn</div>
                <div class="text-[11px] text-[#637d70]">W3C Recommendation</div>
            </div>

            <div class="p-5 rounded-2xl bg-[#f7faf7] border border-[#dce5df] text-center hover:border-[#173f35] transition-colors">
                <div class="font-mono text-xs font-bold text-[#173f35] mb-1">NIST SP 800-63B</div>
                <div class="text-[11px] text-[#637d70]">AAL3 Assurance</div>
            </div>

            <div class="p-5 rounded-2xl bg-[#f7faf7] border border-[#dce5df] text-center hover:border-[#173f35] transition-colors">
                <div class="font-mono text-xs font-bold text-[#173f35] mb-1">RFC 8812</div>
                <div class="text-[11px] text-[#637d70]">COSE Algorithms</div>
            </div>

            <div class="p-5 rounded-2xl bg-[#f7faf7] border border-[#dce5df] text-center hover:border-[#173f35] transition-colors">
                <div class="font-mono text-xs font-bold text-[#173f35] mb-1">AES-256-GCM</div>
                <div class="text-[11px] text-[#637d70]">FIPS 197 Standard</div>
            </div>

            <div class="p-5 rounded-2xl bg-[#f7faf7] border border-[#dce5df] text-center hover:border-[#173f35] transition-colors">
                <div class="font-mono text-xs font-bold text-[#173f35] mb-1">OWASP Top 10</div>
                <div class="text-[11px] text-[#637d70]">A07:2021 Zero Exposure</div>
            </div>

            <div class="p-5 rounded-2xl bg-[#f7faf7] border border-[#dce5df] text-center hover:border-[#173f35] transition-colors">
                <div class="font-mono text-xs font-bold text-[#173f35] mb-1">SOC2 Type II</div>
                <div class="text-[11px] text-[#637d70]">Security &amp; Privacy</div>
            </div>

        </div>

    </div>
</section>

<!-- ========================================================
     SECTION 6: Our Engineering Ethos (Manifesto)
     ======================================================== -->
<section class="py-20 lg:py-28 bg-[#f6f8f5] border-b border-[#e1e7e2]">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="text-center mb-12">
            <span class="inline-flex items-center gap-2 text-[10.5px] font-mono font-bold tracking-[0.2em] uppercase text-[#276652] mb-3 bg-[#e4ede6] px-3.5 py-1.5 rounded-full">
                ETHOS &amp; CODE OF CRAFT
            </span>
            <h2 class="font-serif text-3xl sm:text-4xl font-medium tracking-tight text-[#143329]">
                Our Four Irrevocable Commitments
            </h2>
        </div>

        <div class="space-y-6">
            
            <div class="flex items-start gap-4 p-6 rounded-2xl bg-white border border-[#dce5df] shadow-xs hover:border-[#173f35] transition-colors">
                <span class="w-8 h-8 rounded-full bg-[#173f35] text-[#86efac] font-mono text-xs font-bold flex items-center justify-center flex-shrink-0 mt-0.5">
                    1
                </span>
                <div>
                    <h3 class="font-serif text-lg font-medium text-[#173f35] mb-1">Mathematics Over Hype</h3>
                    <p class="text-sm text-[#526b5e] leading-relaxed">
                        We refuse to use security buzzwords or synthetic marketing claims. If a protocol cannot be formally verified with mathematical proofs and reproducible test suites, it does not belong in our repository.
                    </p>
                </div>
            </div>

            <div class="flex items-start gap-4 p-6 rounded-2xl bg-white border border-[#dce5df] shadow-xs hover:border-[#173f35] transition-colors">
                <span class="w-8 h-8 rounded-full bg-[#173f35] text-[#86efac] font-mono text-xs font-bold flex items-center justify-center flex-shrink-0 mt-0.5">
                    2
                </span>
                <div>
                    <h3 class="font-serif text-lg font-medium text-[#173f35] mb-1">Client Sovereignty Above All</h3>
                    <p class="text-sm text-[#526b5e] leading-relaxed">
                        Your credentials should never be held hostage by a cloud vendor or identity monopoly. You retain full control over your cryptographic keys, session lifetimes, and account recovery records.
                    </p>
                </div>
            </div>

            <div class="flex items-start gap-4 p-6 rounded-2xl bg-white border border-[#dce5df] shadow-xs hover:border-[#173f35] transition-colors">
                <span class="w-8 h-8 rounded-full bg-[#173f35] text-[#86efac] font-mono text-xs font-bold flex items-center justify-center flex-shrink-0 mt-0.5">
                    3
                </span>
                <div>
                    <h3 class="font-serif text-lg font-medium text-[#173f35] mb-1">Constant-Time Execution by Default</h3>
                    <p class="text-sm text-[#526b5e] leading-relaxed">
                        Side-channel timing analysis is a lethal attack vector against digital signatures. All our byte comparisons, hash validations, and HMAC evaluations execute in guaranteed constant time.
                    </p>
                </div>
            </div>

            <div class="flex items-start gap-4 p-6 rounded-2xl bg-white border border-[#dce5df] shadow-xs hover:border-[#173f35] transition-colors">
                <span class="w-8 h-8 rounded-full bg-[#173f35] text-[#86efac] font-mono text-xs font-bold flex items-center justify-center flex-shrink-0 mt-0.5">
                    4
                </span>
                <div>
                    <h3 class="font-serif text-lg font-medium text-[#173f35] mb-1">Human Elegance and High-Craft UI</h3>
                    <p class="text-sm text-[#526b5e] leading-relaxed">
                        Security that is frustrating will be bypassed. We craft interfaces that are intuitive, responsive, and visually reassuring—giving users real-time feedback on what happens to their data.
                    </p>
                </div>
            </div>

        </div>

    </div>
</section>

<!-- ========================================================
     SECTION 7: Final Conversion Call to Action
     ======================================================== -->
<section class="py-20 lg:py-28 bg-gradient-to-b from-[#f8faf8] to-[#eef3ef]">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="inline-flex items-center gap-2 text-[10.5px] font-mono font-bold tracking-[0.2em] uppercase text-[#276652] mb-4 bg-white border border-[#dce5df] px-4 py-1.5 rounded-full shadow-xs">
            READY TO JOIN THE ENCLAVE
        </div>
        
        <h2 class="font-serif text-3xl sm:text-5xl font-medium tracking-tight text-[#143329] leading-tight mb-6">
            Experience authentication where trust is guaranteed by math.
        </h2>
        
        <p class="text-base sm:text-lg text-[#526b5e] max-w-2xl mx-auto mb-10 leading-relaxed">
            Create an account in less than a minute. No bloat, no invasive trackers, and zero compromise on cryptographic integrity.
        </p>

        <div class="flex flex-wrap items-center justify-center gap-4">
            <a href="<?= SITE_URL ?>/register" class="inline-flex items-center gap-3 px-7 py-4 rounded-xl bg-[#173f35] hover:bg-[#235848] text-white font-semibold text-sm shadow-xl shadow-[#173f35]/20 border border-[#13362d] transition-all transform hover:-translate-y-0.5">
                <span>Create your secure account</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                </svg>
            </a>

            <a href="<?= SITE_URL ?>/login" class="inline-flex items-center gap-2 px-6 py-4 rounded-xl text-[#244a3d] hover:text-[#112921] hover:bg-white font-semibold text-sm border border-[#d6e0d8] bg-white/70 backdrop-blur-xs transition-colors shadow-xs">
                <span>Sign in to enclave</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                </svg>
            </a>
        </div>
    </div>
</section>

<?php include('components/footer.php'); ?>
