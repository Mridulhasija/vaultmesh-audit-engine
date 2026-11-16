<?php
require_once __DIR__ . '/include/header_config.php';
require_once __DIR__ . '/include/auth-session.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . SITE_URL . '/login');
    exit;
}

$sessionHash = hash('sha256', session_id());
$sessionCheck = $conn->prepare('SELECT session_hash FROM tbl_user_session WHERE session_hash = ? AND user_id = ? AND expires_at > CURRENT_TIMESTAMP LIMIT 1');
if (!$sessionCheck) {
    http_response_code(503);
    exit('Account sessions are unavailable. Please apply the auth session database migration.');
}
$sessionCheck->bind_param('ss', $sessionHash, $_SESSION['user_id']);
$sessionCheck->execute();
$validSession = (bool) $sessionCheck->get_result()->fetch_assoc();
$sessionCheck->close();

if (!$validSession) {
    $_SESSION = [];
    session_destroy();
    header('Location: ' . SITE_URL . '/login');
    exit;
}

$activity = $conn->prepare('UPDATE tbl_user_session SET last_activity = CURRENT_TIMESTAMP WHERE session_hash = ?');
if ($activity) {
    $activity->bind_param('s', $sessionHash);
    $activity->execute();
    $activity->close();
}

$profile = $conn->prepare('SELECT username, email, first_name, last_name, created_at, last_login FROM tbl_user WHERE user_id = ? LIMIT 1');
$profile->bind_param('s', $_SESSION['user_id']);
$profile->execute();
$accountUser = $profile->get_result()->fetch_assoc();
$profile->close();
if (!$accountUser) {
    $_SESSION = [];
    session_destroy();
    header('Location: ' . SITE_URL . '/login');
    exit;
}

$csrfToken = ensureCsrfToken();
$meta_title = 'VaultMesh — Secure Authentication & Audit Portal';
$meta_desc = 'Manage your ' . $site_name . ' account.';
include __DIR__ . '/components/header.php';
?>

<main class="account-dashboard min-h-[calc(100vh-80px)] bg-[#f6f8f5] dark:bg-black py-10 sm:py-14">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <header class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-5 mb-9">
            <div>
                <p class="text-xs font-semibold tracking-[0.16em] uppercase text-[#527363] dark:text-[#00e676] mb-2">Your account</p>
                <h1 class="font-serif text-4xl sm:text-5xl text-[#17372d] dark:text-white">Welcome back, <?= htmlspecialchars((string) ($accountUser['first_name'] ?: $_SESSION['user_name']), ENT_QUOTES, 'UTF-8') ?>.</h1>
                <p class="text-sm text-[#64786d] dark:text-[#9aaa9f] mt-3">Manage your profile and review your current sign-in.</p>
            </div>
            <form method="post" action="<?= SITE_URL ?>/logout">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-white dark:bg-[#090d0a] border border-[#dce5de] dark:border-[#26332b] text-[#294538] dark:text-[#d7e2da] text-sm font-semibold hover:border-[#00a957] transition-colors">Sign out</button>
            </form>
        </header>

        <section class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6" aria-label="Account summary">
            <article class="account-summary-card rounded-2xl border border-[#e1e7e2] dark:border-[#202a23] bg-white dark:bg-[#070908] p-5">
                <p class="text-xs uppercase tracking-wider text-[#64786d] dark:text-[#8b9b91]">Account status</p>
                <p class="mt-3 flex items-center gap-2 text-lg font-semibold text-[#17372d] dark:text-white"><span class="w-2 h-2 rounded-full bg-[#00c96b]"></span>Active</p>
            </article>
            <article class="account-summary-card rounded-2xl border border-[#e1e7e2] dark:border-[#202a23] bg-white dark:bg-[#070908] p-5">
                <p class="text-xs uppercase tracking-wider text-[#64786d] dark:text-[#8b9b91]">Member since</p>
                <p class="mt-3 text-lg font-semibold text-[#17372d] dark:text-white"><?= htmlspecialchars(date('M Y', strtotime((string) $accountUser['created_at'])), ENT_QUOTES, 'UTF-8') ?></p>
            </article>
            <article class="account-summary-card rounded-2xl border border-[#e1e7e2] dark:border-[#202a23] bg-white dark:bg-[#070908] p-5">
                <p class="text-xs uppercase tracking-wider text-[#64786d] dark:text-[#8b9b91]">Latest sign-in</p>
                <p class="mt-3 text-lg font-semibold text-[#17372d] dark:text-white"><?= htmlspecialchars(date('M j, Y', strtotime((string) $accountUser['last_login'])), ENT_QUOTES, 'UTF-8') ?></p>
            </article>
        </section>

        <section class="rounded-2xl border border-[#e1e7e2] dark:border-[#202a23] bg-white dark:bg-[#070908] overflow-hidden">
            <div class="px-5 sm:px-7 py-5 border-b border-[#edf2ee] dark:border-[#202a23]">
                <h2 class="font-serif text-2xl text-[#17372d] dark:text-white">Profile details</h2>
                <p class="text-sm text-[#64786d] dark:text-[#8b9b91] mt-1">Information associated with your account.</p>
            </div>
            <dl class="grid sm:grid-cols-2 gap-x-8 px-5 sm:px-7 py-2">
                <div class="py-4 border-b border-[#edf2ee] dark:border-[#202a23]"><dt class="text-xs text-[#64786d] dark:text-[#8b9b91]">Name</dt><dd class="mt-1 text-sm font-medium text-[#17372d] dark:text-[#e8eee9]"><?= htmlspecialchars(trim((string) $accountUser['first_name'] . ' ' . (string) $accountUser['last_name']), ENT_QUOTES, 'UTF-8') ?></dd></div>
                <div class="py-4 border-b border-[#edf2ee] dark:border-[#202a23]"><dt class="text-xs text-[#64786d] dark:text-[#8b9b91]">Email</dt><dd class="mt-1 text-sm font-medium text-[#17372d] dark:text-[#e8eee9]"><?= htmlspecialchars((string) $accountUser['email'], ENT_QUOTES, 'UTF-8') ?></dd></div>
                <div class="py-4"><dt class="text-xs text-[#64786d] dark:text-[#8b9b91]">Username</dt><dd class="mt-1 text-sm font-medium text-[#17372d] dark:text-[#e8eee9]"><?= htmlspecialchars((string) $accountUser['username'], ENT_QUOTES, 'UTF-8') ?></dd></div>
                <div class="py-4"><dt class="text-xs text-[#64786d] dark:text-[#8b9b91]">Session security</dt><dd class="mt-1 text-sm font-medium text-[#087a43] dark:text-[#00e676]">Protected · HttpOnly cookie</dd></div>
            </dl>
        </section>
    </div>
</main>

<?php include __DIR__ . '/components/footer.php'; ?>
