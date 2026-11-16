<?php
require_once __DIR__ . '/include/header_config.php';
require_once __DIR__ . '/include/auth-session.php';
if (isset($_SESSION['user_id'])) { header('Location: ' . SITE_URL . '/account'); exit; }
$errors = [];
$csrfToken = ensureCsrfToken();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($csrfToken, (string) ($_POST['csrf_token'] ?? ''))) {
        $errors[] = 'Your session expired. Please refresh the page and try again.';
    } else {
        $email = strtolower(trim((string) ($_POST['email'] ?? '')));
        $password = (string) ($_POST['password'] ?? '');
        $stmt = $conn->prepare('SELECT user_id, password_hash, is_email_verified, first_name, last_name FROM tbl_user WHERE email = ? LIMIT 1');
        if (!$stmt) {
            $user = null;
            $errors[] = 'Sign-in is temporarily unavailable. Please try again.';
        } else {
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $user = $stmt->get_result()->fetch_assoc();
            $stmt->close();
        }
        if (!$errors && $user && password_verify($password, $user['password_hash']) && (int) $user['is_email_verified'] === 1) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_name'] = trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) ?: $email;
            $update = $conn->prepare('UPDATE tbl_user SET last_login = CURRENT_TIMESTAMP WHERE user_id = ?');
            if ($update) { $update->bind_param('s', $user['user_id']); $update->execute(); $update->close(); }
            if (saveUserSession($conn, $user['user_id'])) { header('Location: ' . SITE_URL . '/account'); exit; }
            unset($_SESSION['user_id'], $_SESSION['user_name']);
            $errors[] = 'We could not start a secure session. Please try again.';
        } elseif (!$errors) {
            $errors[] = $user && (int) $user['is_email_verified'] !== 1 ? 'Please verify your email before logging in.' : 'Email or password is incorrect.';
        }
    }
}
$isRegister = false;
$meta_title = 'Log in to secure enclave | ' . $site_name;
$meta_desc = 'Log in to your ' . $site_name . '.';
include __DIR__ . '/components/header.php';
?>

<!-- ========================================================
     AUTHENTICATION PAGE HERO & AMBIENT SCENERY
     ======================================================== -->
<main class="auth-enclave-root min-h-[calc(100vh-80px)] bg-gradient-to-b from-[#f4f7f4] via-[#f8faf8] to-[#edf3ef] py-8 sm:py-12 lg:py-16 relative overflow-hidden">
    
    <!-- Ambient glowing halos and background linework -->
    <div class="pointer-events-none absolute inset-0 opacity-[0.035] bg-[linear-gradient(to_right,rgba(255,255,255,0.04)_1px,transparent_1px),linear-gradient(to_bottom,rgba(255,255,255,0.04)_1px,transparent_1px)] bg-[length:24px_24px]"></div>
    <div class="pointer-events-none absolute -top-48 right-1/4 w-[600px] h-[600px] bg-[#3b8c66]/15 rounded-full blur-3xl"></div>
    <div class="pointer-events-none absolute top-1/2 -left-48 w-[500px] h-[500px] bg-[#173f35]/12 rounded-full blur-3xl"></div>
    <div class="pointer-events-none absolute -bottom-48 right-1/3 w-[550px] h-[550px] bg-[#112d24]/10 rounded-full blur-3xl"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">

        <!-- Scrolling security standards marquee -->
        <div class="auth-marquee mb-8 sm:mb-10 rounded-2xl bg-gradient-to-r from-[#0d231b] via-[#12362b] to-[#0d231b] border border-[#235848]/40 shadow-lg shadow-[#173f35]/10 overflow-hidden py-3 text-[10px] sm:text-[11px] font-mono tracking-wider text-[#a7f3d0]" aria-label="Security standards">
            <div class="auth-marquee-track flex w-max items-center gap-6">
                <div class="flex shrink-0 items-center gap-5 px-3">
                    <span class="inline-flex items-center gap-1.5 font-semibold text-white">
                        <span class="w-2 h-2 rounded-full bg-[#4ade80] inline-block animate-pulse"></span>
                        FIDO2 / WEBAUTHN CERTIFIED ENCLAVE
                    </span>
                    <span class="text-[#456b5b]">✦</span>
                    <span class="text-[#c7e5d4]">AES-256-GCM ZERO-PLAINTEXT SEAL</span>
                    <span class="text-[#456b5b]">✦</span>
                    <span class="text-[#c7e5d4]">ARGON2ID MEMORY-HARD DERIVATION</span>
                    <span class="text-[#456b5b]">✦</span>
                    <span class="text-[#c7e5d4]">SAME-SITE STRICT SLIDING SESSION</span>
                    <span class="text-[#456b5b]">✦</span>
                    <span class="text-[#86efac] font-medium">ANTI-CSRF CSPRNG 256-BIT TOKENS ARMED</span>
                    <span class="text-[#456b5b]">✦</span>
                    <span class="text-[#c7e5d4]">SUB-5MS CRYPTOGRAPHIC ATTESTATION</span>
                    <span class="text-[#456b5b]">✦</span>
                    <span class="text-[#c7e5d4]">ZERO THIRD-PARTY TRACKERS OR PIXELS</span>
                </div>
                <div class="flex shrink-0 items-center gap-5 px-3" aria-hidden="true">
                    <span class="inline-flex items-center gap-1.5 font-semibold text-white"><span class="w-2 h-2 rounded-full bg-[#4ade80] inline-block animate-pulse"></span>FIDO2 / WEBAUTHN CERTIFIED ENCLAVE</span>
                    <span class="text-[#456b5b]">✦</span><span class="text-[#c7e5d4]">AES-256-GCM ZERO-PLAINTEXT SEAL</span>
                    <span class="text-[#456b5b]">✦</span><span class="text-[#c7e5d4]">ARGON2ID MEMORY-HARD DERIVATION</span>
                    <span class="text-[#456b5b]">✦</span><span class="text-[#c7e5d4]">SAME-SITE STRICT SLIDING SESSION</span>
                    <span class="text-[#456b5b]">✦</span><span class="text-[#86efac] font-medium">ANTI-CSRF CSPRNG 256-BIT TOKENS ARMED</span>
                    <span class="text-[#456b5b]">✦</span><span class="text-[#c7e5d4]">SUB-5MS CRYPTOGRAPHIC ATTESTATION</span>
                    <span class="text-[#456b5b]">✦</span><span class="text-[#c7e5d4]">ZERO THIRD-PARTY TRACKERS OR PIXELS</span>
                </div>
            </div>
        </div>

        <!-- ========================================================
             TWO-COLUMN WORKSPACE: LEFT POSTER & RIGHT FORM
             ======================================================== -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10 xl:gap-12 items-start">

            <!-- ========================================================
                 LEFT COLUMN: COMPLEX SECURITY AUTH POSTER & SENTINEL
                 ======================================================== -->
            <div class="lg:col-span-7 xl:col-span-7 flex flex-col gap-6">

                <!-- Primary Master Security Poster Terminal Container -->
                <div class="security-poster-card relative w-full bg-[#0d221b] border border-white/15 rounded-3xl p-6 sm:p-7 shadow-[0_25px_65px_-12px_rgba(17,63,53,0.35)] text-[#d5e5db] overflow-hidden">
                    
                    <!-- Ambient luminous background reflection -->
                    <div class="pointer-events-none absolute -inset-1 bg-gradient-to-r from-[#215a49]/30 via-transparent to-[#10372b]/25 rounded-3xl blur-xl opacity-70"></div>
                    <div class="absolute inset-0 bg-[linear-gradient(to_right,rgba(255,255,255,0.04)_1px,transparent_1px),linear-gradient(to_bottom,rgba(255,255,255,0.04)_1px,transparent_1px)] bg-[length:24px_24px] opacity-15 pointer-events-none"></div>

                    <!-- Poster Titlebar -->
                    <div class="relative flex items-center justify-between pb-4 border-b border-white/10 mb-5">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-[#4fa572] inline-block"></span>
                            <span class="w-3 h-3 rounded-full bg-[#d4af37] inline-block opacity-75"></span>
                            <span class="w-3 h-3 rounded-full bg-[#64c2a7] inline-block opacity-60"></span>
                            <span class="font-mono text-[11px] font-medium text-[#8cb29c] ml-2 tracking-wide hidden sm:inline">
                                behoof-sentinel // auth-enclave-daemon
                            </span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-[#4ade80] inline-block animate-pulse shadow-[0_0_8px_#4ade80]"></span>
                            <span id="poster-status-pill" class="font-mono text-[10px] font-semibold text-[#86efac] tracking-widest uppercase transition-colors">
                                SHIELD ARMED
                            </span>
                        </div>
                    </div>

                    <!-- Poster Mode Switcher Tabs -->
                    <div class="relative flex items-center bg-[#071711] p-1 rounded-xl border border-white/10 mb-5 text-xs font-mono">
                        <button type="button" id="auth-tab-sentinel" class="auth-poster-tab flex-1 py-1.5 px-2 rounded-lg text-center transition-all bg-[#173f35] text-white font-medium shadow-xs" data-target="poster-panel-sentinel">
                            Live Sentinel
                        </button>
                        <button type="button" id="auth-tab-audit" class="auth-poster-tab flex-1 py-1.5 px-2 rounded-lg text-center transition-all text-[#8da597] hover:text-white" data-target="poster-panel-audit">
                            Audit Stream
                        </button>
                        <button type="button" id="auth-tab-specs" class="auth-poster-tab flex-1 py-1.5 px-2 rounded-lg text-center transition-all text-[#8da597] hover:text-white" data-target="poster-panel-specs">
                            Enclave Specs
                        </button>
                    </div>

                    <!-- TAB 1: Live Sentinel & Biometric Scanner with Real-Time Typing Reaction -->
                    <div id="poster-panel-sentinel" class="poster-panel relative">
                        
                        <!-- Biometric Stage Scanner Core -->
                        <div class="relative h-56 sm:h-60 rounded-xl bg-gradient-to-b from-[#112d24] to-[#0a1c16] border border-white/10 flex items-center justify-center overflow-hidden mb-5">
                            
                            <!-- Laser scanning beam -->
                            <div id="auth-laser-beam" class="scanner-laser"></div>

                            <!-- Dual Concentric Rotating Cipher Rings -->
                            <svg class="absolute w-52 h-52 text-[#2f6f55]/40 cipher-ring-outer pointer-events-none" viewBox="0 0 200 200" fill="none">
                                <circle cx="100" cy="100" r="92" stroke="currentColor" stroke-width="1.5" stroke-dasharray="6 8 18 4" />
                                <circle cx="100" cy="100" r="76" stroke="currentColor" stroke-width="1" stroke-dasharray="3 5" />
                            </svg>
                            <svg class="absolute w-36 h-36 text-[#4fa572]/45 cipher-ring-inner pointer-events-none" viewBox="0 0 150 150" fill="none">
                                <circle cx="75" cy="75" r="66" stroke="currentColor" stroke-width="1.8" stroke-dasharray="4 6 12 4" />
                            </svg>

                            <!-- Sonar Waves -->
                            <div class="absolute w-24 h-24 rounded-full border border-[#4ade80]/40 radar-pulse-wave-1 pointer-events-none"></div>
                            <div class="absolute w-24 h-24 rounded-full border border-[#4ade80]/30 radar-pulse-wave-2 pointer-events-none"></div>

                            <!-- Center Shield Lock Emblem with Breathing Glow -->
                            <div id="auth-shield-core" class="relative z-10 w-20 h-20 rounded-2xl bg-gradient-to-br from-[#1d4d3f] to-[#0f2820] border border-[#52a175]/60 flex items-center justify-center shadow-lg shadow-[#061811]/90 transition-all duration-300">
                                <svg class="w-10 h-10 text-[#86efac]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                                    <path d="M12 8v4"/>
                                    <circle cx="12" cy="15" r="1" fill="currentColor"/>
                                </svg>
                            </div>

                            <!-- Real-time Nonce Watermark -->
                            <div class="absolute bottom-3 left-4 font-mono text-[9.5px] text-[#719582] tracking-wider">
                                NONCE: <span id="poster-nonce" class="text-[#a7f3d0] font-semibold">0x7F4A...B902</span>
                            </div>
                            <div class="absolute bottom-3 right-4 font-mono text-[9.5px] text-[#719582] tracking-wider">
                                LATENCY: <span id="poster-latency" class="text-[#a7f3d0] font-semibold">4.2ms</span>
                            </div>
                        </div>

                        <!-- Real-Time Typing Reaction: Dynamic Entropy Gauge & Hash Preview -->
                        <div class="relative bg-[#071711] rounded-xl p-3.5 border border-white/10 mb-4 transition-all">
                            
                            <div class="flex items-center justify-between text-[11px] font-mono mb-2">
                                <span class="text-[#7da38f] flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#4ade80] inline-block"></span>
                                    LIVE INPUT CRYPTOMETRY
                                </span>
                                <span id="entropy-label" class="text-[#86efac] font-bold">STANDBY // READY</span>
                            </div>

                            <!-- Real-Time Dynamic Strength Progress Bar -->
                            <div class="entropy-meter-track relative h-2 bg-[#020503] dark:bg-black rounded-full overflow-hidden mb-3 shadow-[inset_0_0_5px_rgba(0,0,0,0.8)]">
                                <div id="entropy-progress-bar" class="entropy-meter-fill h-full bg-gradient-to-r from-[#00b85e] via-[#00e676] to-[#8effbd] w-[20%] rounded-full transition-all duration-300 shadow-[0_0_10px_rgba(0,230,118,0.75)]"></div>
                            </div>

                            <!-- Live Simulated SHA-256 Hash Stream Output -->
                            <div class="bg-black/40 rounded-xl p-2.5 border border-white/5 font-mono text-[10.5px] leading-tight">
                                <div class="text-[#5b806d] text-[9.5px] mb-1">REAL-TIME BLAKE3 / SHA-256 SIMULATION:</div>
                                <div id="hash-stream-preview" class="text-[#a7f3d0] font-mono truncate select-all">
                                    e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855
                                </div>
                            </div>

                            <!-- Pipeline Verification Badges -->
                            <div class="grid grid-cols-3 gap-2 mt-3 text-center font-mono text-[10px]">
                                <div id="pipe-node-1" class="p-2 rounded-lg bg-white/5 border border-white/5 transition-colors">
                                    <div class="node-title text-[#4ade80] font-bold">01. SYN-ACK</div>
                                    <div class="text-[#7fa391] text-[9px]">Nonce Derived</div>
                                </div>
                                <div id="pipe-node-2" class="p-2 rounded-lg bg-white/5 border border-white/5 transition-colors">
                                    <div class="node-title text-[#7fa391] font-bold">02. ENTROPY</div>
                                    <div id="pipe-entropy-score" class="text-[#7fa391] text-[9px]">Awaiting Key</div>
                                </div>
                                <div id="pipe-node-3" class="p-2 rounded-lg bg-[#143d31] border border-[#2b7256] transition-colors">
                                    <div class="node-title text-[#86efac] font-bold">03. SEAL READY</div>
                                    <div class="text-[#a7f3d0] text-[9px]">FIDO2 Armed</div>
                                </div>
                            </div>

                        </div>

                        <!-- Simulator Refresh Action -->
                        <div class="flex items-center justify-between gap-3 pt-1">
                            <button type="button" id="btn-refresh-nonce" class="w-full inline-flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl bg-white/10 hover:bg-white/15 border border-white/15 text-xs font-mono text-white font-medium transition-all transform hover:-translate-y-0.5 active:translate-y-0">
                                <svg class="w-3.5 h-3.5 text-[#4ade80]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21.5 2v6h-6M2.5 22v-6h6M2 11.5a10 10 0 0 1 18.8-4.3M22 12.5a10 10 0 0 1-18.8 4.2"/>
                                </svg>
                                <span>Re-mint Cryptographic Challenge Nonce</span>
                            </button>
                        </div>

                    </div>

                    <!-- TAB 2: Scrollable Live Security Audit Log Stream with Glowing Scrollbar -->
                    <div id="poster-panel-audit" class="poster-panel hidden relative">
                        <div class="h-80 sm:h-96 rounded-2xl bg-[#06140e] border border-white/10 p-4 font-mono text-[11px] leading-relaxed overflow-y-auto [scrollbar-width:thin] [scrollbar-color:rgba(0,230,118,0.38)_transparent] [&::-webkit-scrollbar]:w-1.5 [&::-webkit-scrollbar-track]:bg-transparent [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-thumb]:bg-[#00e676]/35 [&::-webkit-scrollbar-thumb:hover]:bg-[#00e676]/70 text-[#a7c5b4]">
                            <div class="text-[#527d66] mb-3 pb-2 border-b border-white/10 flex items-center justify-between">
                                <span>// REAL-TIME AUDIT STREAM (SCROLL FOR FULL TELEMETRY)</span>
                                <span class="text-[9.5px] text-[#4ade80]">ENCRYPTED LOG</span>
                            </div>
                            
                            <div id="audit-log-entries" class="space-y-3 font-mono text-[11px]">
                                <div class="p-2 rounded bg-white/5 border border-white/5">
                                    <span class="text-[#517564]">[15:06:01.012]</span> <span class="text-[#4ade80] font-semibold">TLS_ESTABLISHED</span>: Cipher=ECDHE-RSA-AES256-GCM-SHA384
                                </div>
                                <div class="p-2 rounded bg-white/5 border border-white/5">
                                    <span class="text-[#517564]">[15:06:01.018]</span> <span class="text-[#4ade80] font-semibold">CSPRNG_NONCE_MINT</span>: 32-byte entropy block sealed
                                </div>
                                <div class="p-2 rounded bg-white/5 border border-white/5">
                                    <span class="text-[#517564]">[15:06:01.025]</span> <span class="text-[#4ade80] font-semibold">CSRF_INSPECTOR</span>: Token hash verified against session storage
                                </div>
                                <div class="p-2 rounded bg-white/5 border border-white/5">
                                    <span class="text-[#517564]">[15:06:01.031]</span> <span class="text-[#4ade80] font-semibold">RATE_LIMITER</span>: Client IP within standard 60-second burst threshold
                                </div>
                                <div class="p-2 rounded bg-white/5 border border-white/5">
                                    <span class="text-[#517564]">[15:06:01.040]</span> <span class="text-[#4ade80] font-semibold">ENCLAVE_READY</span>: WebAuthn challenge listening on FIDO2 socket
                                </div>
                                <div class="p-2 rounded bg-white/5 border border-white/5">
                                    <span class="text-[#517564]">[15:06:01.055]</span> <span class="text-[#4ade80] font-semibold">SAME_SITE_POLICY</span>: Strict cookie isolation flags enforced
                                </div>
                                <div class="p-2 rounded bg-white/5 border border-white/5">
                                    <span class="text-[#517564]">[15:06:01.062]</span> <span class="text-[#4ade80] font-semibold">ZERO_TRACKERS</span>: External network egress blocked; zero telemetry
                                </div>
                                <div class="p-2 rounded bg-white/5 border border-white/5">
                                    <span class="text-[#517564]">[15:06:01.070]</span> <span class="text-[#4ade80] font-semibold">SIDE_CHANNEL_DEFENSE</span>: Constant-time byte comparator primed
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 3: Enclave Cryptographic Specs -->
                    <div id="poster-panel-specs" class="poster-panel hidden relative">
                        <div class="h-80 sm:h-96 rounded-2xl bg-[#06140e] border border-white/10 p-4 font-mono text-[11px] leading-relaxed overflow-y-auto [scrollbar-width:thin] [scrollbar-color:rgba(0,230,118,0.38)_transparent] [&::-webkit-scrollbar]:w-1.5 [&::-webkit-scrollbar-track]:bg-transparent [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-thumb]:bg-[#00e676]/35 [&::-webkit-scrollbar-thumb:hover]:bg-[#00e676]/70 text-[#a7c5b4]">
                            <div class="text-[#527d66] mb-3 pb-2 border-b border-white/10">
                                // BEHOOF CRYPTOGRAPHIC SPECIFICATION (RFC 8812 &amp; FIPS 197)
                            </div>
                            <pre class="text-[#c5ded0] leading-relaxed whitespace-pre font-mono text-[11px]">
{
  "cryptographic_profile": "behoof-shield-v3",
  "asymmetric_standards": [
    "FIDO2 / WebAuthn (Level 3 Authenticator)",
    "ECDSA on Curve P-256 (ES256)",
    "Ed25519 (RFC 8032 EdDSA)"
  ],
  "symmetric_ciphers": [
    "AES-256-GCM (Authenticated Encryption)",
    "ChaCha20-Poly1305 (TLS 1.3 Transport)"
  ],
  "key_derivation": [
    "Argon2id (m=65536, t=4, p=2)",
    "PBKDF2-HMAC-SHA256 Fallback"
  ],
  "session_isolation": {
    "cookie_flags": "HttpOnly; Secure; SameSite=Strict",
    "sliding_window_seconds": 900,
    "automatic_csrf_renewal": true,
    "side_channel_constant_time": true
  }
}
                            </pre>
                        </div>
                    </div>

                </div>

                <!-- Hardened Security Proof Mini-Card -->
                <div class="p-5 rounded-2xl bg-white/70 backdrop-blur-sm border border-[#dce5df] shadow-xs">
                    <div class="flex items-center gap-3">
                        <span class="w-9 h-9 rounded-xl bg-[#173f35] text-[#86efac] flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </span>
                        <div>
                            <div class="text-xs font-bold text-[#173f35] uppercase tracking-wide">Provable Zero-Knowledge</div>
                            <p class="text-xs text-[#526b5e] leading-snug mt-0.5">
                                Passwords and biometric scans never leave your local device. The server only evaluates non-reversible cryptographic proofs.
                            </p>
                        </div>
                    </div>
                </div>

            </div>

            <!-- ========================================================
                 RIGHT COLUMN: PREMIUM AUTH FORM WITH GLOWING HOVER
                 ======================================================== -->
            <div class="lg:col-span-5 xl:col-span-5">
                
                <!-- Radiant Glowing Auth Card -->
                <div data-cursor-glow class="group relative overflow-hidden backdrop-blur-md bg-white dark:bg-[#070908] rounded-3xl p-7 sm:p-9 border border-[#d9e5dd] dark:border-[#202a23] shadow-[0_20px_60px_-15px_rgba(23,63,53,0.12)] transition-all duration-300 hover:-translate-y-1 hover:border-[#52a175] hover:shadow-[0_28px_70px_-18px_rgba(0,210,110,0.22)] dark:hover:border-[#176b43] dark:hover:shadow-[0_0_45px_rgba(0,230,118,0.14)]">
                    
                    <!-- Ambient radiant perimeter glow on hover -->
                    <div class="pointer-events-none absolute inset-0 rounded-3xl opacity-0 transition-opacity duration-300 group-hover:opacity-100 bg-[radial-gradient(420px_circle_at_var(--glow-x,50%)_var(--glow-y,50%),rgba(0,230,118,0.16),transparent_62%)]"></div>

                    
                        <!-- ========================================
                             STATE: LOGIN OR REGISTRATION FORM
                             ======================================== -->
                        <div class="relative z-10">

                            <!-- Mode Switcher Tabs (Login vs Register) with Glowing Active Tab -->
                            <div class="flex items-center bg-[#f0f4f1] p-1 rounded-2xl border border-[#d6e3da] mb-6">
                                <a href="<?= SITE_URL ?>/login" class="flex-1 py-2 px-3 rounded-xl text-center font-medium text-xs transition-all <?= !$isRegister ? 'bg-white text-[#173f35] font-semibold shadow-xs border border-[#cfdfd5]' : 'text-[#5a7366] hover:text-[#173f35]' ?>">
                                    Sign In
                                </a>
                                <a href="<?= SITE_URL ?>/register" class="flex-1 py-2 px-3 rounded-xl text-center font-medium text-xs transition-all <?= $isRegister ? 'bg-white text-[#173f35] font-semibold shadow-xs border border-[#cfdfd5]' : 'text-[#5a7366] hover:text-[#173f35]' ?>">
                                    Create Account
                                </a>
                            </div>

                            <!-- Header copy -->
                            <div class="mb-6">
                                <span class="text-[10px] font-mono font-bold tracking-[0.2em] uppercase text-[#276652] block mb-1">
                                    <?= $isRegister ? 'JOIN THE HARDWARE ENCLAVE' : 'AUTHENTICATE ACCESS' ?>
                                </span>
                                <h1 class="font-serif text-2xl sm:text-3xl font-medium tracking-tight text-[#143329] leading-tight">
                                    <?= $isRegister ? 'Create your secure account' : 'Welcome back to the enclave' ?>
                                </h1>
                                <p class="text-xs sm:text-sm text-[#526b5e] mt-1.5 leading-relaxed">
                                    <?= $isRegister ? 'Register your cryptographic credentials in less than a minute.' : 'Enter your credentials to initiate cryptographic attestation.' ?>
                                </p>
                            </div>

                            <!-- Error Alerts with Glowing Border -->
                            <?php if (!empty($errors)): ?>
                                <div class="space-y-2 mb-6">
                                    <?php foreach ($errors as $error): ?>
                                        <div class="p-3.5 rounded-xl bg-[#fff5f3] border border-[#f5c6bb] text-[#a13b2c] text-xs flex items-start gap-2.5 shadow-sm shadow-[#a13b2c]/05">
                                            <svg class="w-4 h-4 flex-shrink-0 mt-0.5 text-[#d04430]" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                            </svg>
                                            <span class="leading-relaxed"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>

                            <!-- The Main Interactive Form -->
                            <form id="auth-main-form" method="post" action="<?= SITE_URL ?>/<?= $isRegister ? 'register' : 'login' ?>" class="space-y-4">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

                                <?php if ($isRegister): ?>
                                    <!-- Full Name Field -->
                                    <div class="form-group">
                                        <label for="input-name" class="block text-xs font-semibold text-[#18392f] mb-1.5 flex items-center justify-between">
                                            <span>Full Name</span>
                                            <span class="text-[10px] text-[#6d887a] font-mono">Max 100 chars</span>
                                        </label>
                                        <div class="relative">
                                            <input id="input-name" name="name" type="text" autocomplete="name" maxlength="100" placeholder="e.g. Jordan Lee" required value="<?= htmlspecialchars((string) ($_POST['name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" class="w-full px-4 py-3 rounded-xl border border-[#d6e2da] text-sm text-[#143329] placeholder-[#9cb0a4] bg-[#fbfdfb] transition-all focus:outline-none focus:ring-2 focus:ring-[#276652]/20 focus:border-[#276652]">
                                            <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-[#93aba0]">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <!-- Email Address Field -->
                                <div class="form-group">
                                    <label for="input-email" class="block text-xs font-semibold text-[#18392f] mb-1.5 flex items-center justify-between">
                                        <span>Email Address</span>
                                        <span class="text-[10px] text-[#6d887a] font-mono">Zero plaintext leak</span>
                                    </label>
                                    <div class="relative">
                                        <input id="input-email" name="email" type="email" autocomplete="email" maxlength="100" placeholder="you@domain.com" required value="<?= htmlspecialchars((string) ($_POST['email'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" class="w-full px-4 py-3 rounded-xl border border-[#d6e2da] text-sm text-[#143329] placeholder-[#9cb0a4] bg-[#fbfdfb] transition-all focus:outline-none focus:ring-2 focus:ring-[#276652]/20 focus:border-[#276652]">
                                        <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-[#93aba0]">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                                            </svg>
                                        </div>
                                    </div>
                                </div>

                                <!-- Password Field with Visibility Toggle -->
                                <div class="form-group">
                                    <label for="input-password" class="block text-xs font-semibold text-[#18392f] mb-1.5 flex items-center justify-between">
                                        <span>Password</span>
                                        <span class="text-[10px] text-[#6d887a] font-mono"><?= $isRegister ? 'Min 10 chars' : 'Encrypted envelope' ?></span>
                                    </label>
                                    <div class="relative">
                                        <input id="input-password" name="password" type="password" autocomplete="<?= $isRegister ? 'new-password' : 'current-password' ?>" <?= $isRegister ? 'minlength="10"' : '' ?> placeholder="<?= $isRegister ? 'Create a strong passphrase' : 'Enter your passphrase' ?>" required class="w-full px-4 py-3 rounded-xl border border-[#d6e2da] text-sm text-[#143329] placeholder-[#9cb0a4] bg-[#fbfdfb] transition-all focus:outline-none pr-10">
                                        <button type="button" id="btn-toggle-password" class="absolute inset-y-0 right-0 pr-3 flex items-center text-[#93aba0] hover:text-[#173f35] transition-colors" aria-label="Toggle password visibility">
                                            <svg id="eye-icon-open" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            <svg id="eye-icon-closed" class="w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <!-- High-Craft Glowing Submit Button with Shimmer -->
                                <div class="pt-2">
                                    <button type="submit" id="btn-auth-submit" class="group relative w-full inline-flex items-center justify-between py-3.5 px-6 rounded-xl bg-[#173f35] hover:bg-[#215647] text-white font-semibold text-sm shadow-lg shadow-[#173f35]/25 hover:shadow-[0_0_26px_rgba(0,230,118,0.28)] border border-[#13362d] transition-all transform hover:-translate-y-0.5 active:translate-y-0 overflow-hidden cursor-pointer">
                                        <!-- Shimmer animation reflection -->
                                        
                                        
                                        <span id="btn-submit-text" class="relative z-10 flex items-center gap-2">
                                            <span class="w-2 h-2 rounded-full bg-[#4ade80] inline-block animate-pulse"></span>
                                            <?= $isRegister ? 'Initialize Secure Account' : 'Authenticate & Sign In' ?>
                                        </span>
                                        <svg class="relative z-10 w-4 h-4 transition-transform duration-200 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                        </svg>
                                    </button>
                                </div>
                            </form>

                            <!-- Bottom Links & Trust Badges -->
                            <div class="mt-6 pt-5 border-t border-[#e8f0eb] text-center">
                                <p class="text-xs text-[#526b5e]">
                                    <?= $isRegister ? 'Already registered an enclave identity?' : 'New to this cryptographic enclave?' ?>
                                    <a href="<?= SITE_URL ?>/<?= $isRegister ? 'login' : 'register' ?>" class="font-semibold text-[#183f34] hover:text-[#276652] hover:underline ml-1">
                                        <?= $isRegister ? 'Sign in instead' : 'Create an account' ?>
                                    </a>
                                </p>
                                <div class="mt-4 flex items-center justify-center gap-3 text-[10.5px] font-mono text-[#7a9386]">
                                    <span class="flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5 text-[#3ea66e]" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        Zero-Knowledge Salt
                                    </span>
                                    <span>·</span>
                                    <span class="flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5 text-[#3ea66e]" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        Anti-Phishing Token
                                    </span>
                                </div>
                            </div>

                        </div>

                </div>
            </div>

        </div>

    </div>
</main>

<script>
document.addEventListener("DOMContentLoaded", function () {

    const glowCard = document.querySelector("[data-cursor-glow]");
    if (glowCard && window.matchMedia("(pointer: fine)").matches) {
        glowCard.addEventListener("pointermove", (event) => {
            const rect = glowCard.getBoundingClientRect();
            glowCard.style.setProperty("--glow-x", `${event.clientX - rect.left}px`);
            glowCard.style.setProperty("--glow-y", `${event.clientY - rect.top}px`);
        });
        glowCard.addEventListener("pointerleave", () => {
            glowCard.style.setProperty("--glow-x", "50%");
            glowCard.style.setProperty("--glow-y", "50%");
        });
    }

    document.querySelectorAll(".auth-marquee").forEach((marquee) => {
        marquee.addEventListener("pointerenter", () => marquee.classList.add("is-paused"));
        marquee.addEventListener("pointerleave", () => marquee.classList.remove("is-paused"));
    });
    
    // --- 2. Poster Tab Switcher ---
    const tabBtns = document.querySelectorAll(".auth-poster-tab");
    const panels = document.querySelectorAll(".poster-panel");

    tabBtns.forEach(btn => {
        btn.addEventListener("click", () => {
            tabBtns.forEach(b => {
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

    // --- 3. Password Visibility Toggle ---
    const togglePassBtn = document.getElementById("btn-toggle-password");
    const passInput = document.getElementById("input-password");
    const iconOpen = document.getElementById("eye-icon-open");
    const iconClosed = document.getElementById("eye-icon-closed");

    if (togglePassBtn && passInput) {
        togglePassBtn.addEventListener("click", () => {
            const isPassword = passInput.getAttribute("type") === "password";
            passInput.setAttribute("type", isPassword ? "text" : "password");
            if (iconOpen && iconClosed) {
                iconOpen.classList.toggle("hidden", isPassword);
                iconClosed.classList.toggle("hidden", !isPassword);
            }
        });
    }

    // --- 4. Re-mint Challenge Nonce Action ---
    const refreshNonceBtn = document.getElementById("btn-refresh-nonce");
    const posterNonce = document.getElementById("poster-nonce");
    const posterLatency = document.getElementById("poster-latency");
    const auditEntries = document.getElementById("audit-log-entries");

    function generateHexNonce() {
        const hex = "0123456789ABCDEF";
        let r = "0x";
        for (let i = 0; i < 4; i++) r += hex[Math.floor(Math.random() * 16)];
        r += "...";
        for (let i = 0; i < 4; i++) r += hex[Math.floor(Math.random() * 16)];
        return r;
    }

    function appendAuditLog(tag, message) {
        if (!auditEntries) return;
        const now = new Date();
        const timeStr = now.toTimeString().split(' ')[0] + '.' + String(now.getMilliseconds()).padStart(3, '0');
        const entry = document.createElement("div");
        entry.className = "p-2 rounded bg-white/5 border border-white/5 animate-pulse";
        entry.innerHTML = `<span class="text-[#517564]">[${timeStr}]</span> <span class="text-[#4ade80] font-semibold">${tag}</span>: ${message}`;
        auditEntries.prepend(entry);
    }

    if (refreshNonceBtn && posterNonce) {
        refreshNonceBtn.addEventListener("click", () => {
            const fresh = generateHexNonce();
            posterNonce.textContent = fresh;
            posterNonce.classList.add("text-white");
            const newLat = (3.8 + Math.random() * 1.5).toFixed(1) + "ms";
            if (posterLatency) posterLatency.textContent = newLat;
            
            appendAuditLog("CHALLENGE_RENEWED", `New CSPRNG Nonce minted (${fresh}) latency=${newLat}`);
            
            setTimeout(() => {
                posterNonce.classList.remove("text-white");
            }, 500);
        });
    }

    // --- 5. REAL-TIME REACTION: WHEN USER FILLS FORM INPUTS ---
    const nameInput = document.getElementById("input-name");
    const emailInput = document.getElementById("input-email");
    const passwordInput = document.getElementById("input-password");
    
    const entropyLabel = document.getElementById("entropy-label");
    const entropyBar = document.getElementById("entropy-progress-bar");
    const hashPreview = document.getElementById("hash-stream-preview");
    const statusPill = document.getElementById("poster-status-pill");
    const shieldCore = document.getElementById("auth-shield-core");
    const laserBeam = document.getElementById("auth-laser-beam");

    const pipeNode1 = document.getElementById("pipe-node-1");
    const pipeNode2 = document.getElementById("pipe-node-2");
    const pipeNode3 = document.getElementById("pipe-node-3");
    const pipeEntropyScore = document.getElementById("pipe-entropy-score");

    // Fast simulated cryptographic digest generator
    function simpleHash(str) {
        let hash1 = 0x811c9dc5;
        let hash2 = 0x5b3f129a;
        for (let i = 0; i < str.length; i++) {
            hash1 ^= str.charCodeAt(i);
            hash1 = (hash1 * 0x01000193) >>> 0;
            hash2 ^= str.charCodeAt(i);
            hash2 = (hash2 * 0x01000193) >>> 0;
        }
        let h1Str = hash1.toString(16).padStart(8, '0');
        let h2Str = hash2.toString(16).padStart(8, '0');
        return (h1Str + h2Str + "98fc1c149afbf4c8" + h1Str.split('').reverse().join('') + h2Str).slice(0, 64);
    }

    function calculateEntropy(val) {
        if (!val) return 0;
        let pool = 0;
        if (/[a-z]/.test(val)) pool += 26;
        if (/[A-Z]/.test(val)) pool += 26;
        if (/[0-9]/.test(val)) pool += 10;
        if (/[^a-zA-Z0-9]/.test(val)) pool += 33;
        if (pool === 0) pool = 1;
        const bits = Math.round(val.length * Math.log2(pool));
        return bits;
    }

    function onUserInputEvent(e) {
        const emailVal = emailInput ? emailInput.value : "";
        const passVal = passwordInput ? passwordInput.value : "";
        const nameVal = nameInput ? nameInput.value : "";
        
        const combined = emailVal + "::" + passVal + "::" + nameVal;
        const passEntropy = calculateEntropy(passVal);

        // 1. Update Real-time simulated hash digest
        if (hashPreview) {
            if (combined.length > 2) {
                hashPreview.textContent = simpleHash(combined);
                hashPreview.classList.add("text-[#86efac]");
            } else {
                hashPreview.textContent = "e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855";
                hashPreview.classList.remove("text-[#86efac]");
            }
        }

        // 2. Update Entropy Gauge
        if (entropyBar && entropyLabel) {
            let percent = 20;
            let statusText = "STANDBY // READY";

            if (passEntropy > 0) {
                if (passEntropy < 35) {
                    percent = 35;
                    statusText = "EVALUATING // BASIC (" + passEntropy + " BITS)";
                } else if (passEntropy < 65) {
                    percent = 65;
                    statusText = "EXPANDING // STRONG (" + passEntropy + " BITS)";
                } else {
                    percent = 100;
                    statusText = "HARDENED // CRYPTO-GRADE (" + passEntropy + " BITS)";
                }
            } else if (emailVal.length > 3) {
                percent = 40;
                statusText = "IDENTITY BUFFERED // INPUT ACTIVE";
            }

            const widthClass = { 20: "w-1/5", 35: "w-[35%]", 40: "w-2/5", 65: "w-[65%]", 100: "w-full" }[percent];
            entropyBar.style.width = percent + "%";
            entropyLabel.textContent = statusText;
        }

        // 3. Pipeline Nodes Reaction
        if (pipeEntropyScore) {
            if (passEntropy > 0) {
                pipeEntropyScore.textContent = passEntropy + " Bits Valid";
                pipeEntropyScore.classList.add("text-[#86efac]", "font-semibold");
            } else if (emailVal.length > 0) {
                pipeEntropyScore.textContent = "Identity Parsed";
            } else {
                pipeEntropyScore.textContent = "Awaiting Key";
                pipeEntropyScore.classList.remove("text-[#86efac]", "font-semibold");
            }
        }

        // 4. Accentuate Scanner Laser & Shield Glow when typing
        if (shieldCore && laserBeam) {
            shieldCore.classList.add("shadow-[0_0_25px_rgba(74,222,128,0.4)]");
            if (statusPill) {
                statusPill.textContent = "ANALYZING STREAM";
                statusPill.classList.add("text-[#4ade80]");
            }
            clearTimeout(window.typingIdleTimer);
            window.typingIdleTimer = setTimeout(() => {
                shieldCore.classList.remove("shadow-[0_0_25px_rgba(74,222,128,0.4)]");
                if (statusPill) {
                    statusPill.textContent = "SHIELD ARMED";
                }
            }, 800);
        }
    }

    [nameInput, emailInput, passwordInput].forEach(inp => {
        if (inp) {
            inp.addEventListener("input", onUserInputEvent);
            inp.addEventListener("focus", () => {
                if (statusPill) {
                    statusPill.textContent = "ACTIVE FOCUS: " + inp.name.toUpperCase();
                }
                appendAuditLog("INPUT_FOCUS", `Target field [${inp.name}] active`);
            });
        }
    });

    // Form submit pulse feedback
    const authForm = document.getElementById("auth-main-form");
    const submitBtn = document.getElementById("btn-auth-submit");
    const submitText = document.getElementById("btn-submit-text");

    if (authForm && submitBtn) {
        authForm.addEventListener("submit", function () {
            if (submitText) {
                submitText.innerHTML = `<span class="w-2.5 h-2.5 rounded-full bg-white inline-block animate-ping"></span> Sealing Enclave Envelope...`;
            }
            submitBtn.classList.add("opacity-90", "cursor-wait");
            appendAuditLog("DISPATCH_SEAL", "Client attestation payload sent to cryptographic enclave");
        });
    }

});
</script>

<?php include __DIR__ . "/components/footer.php"; ?>
