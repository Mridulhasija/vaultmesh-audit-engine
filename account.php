<?php
require_once __DIR__ . '/include/header_config.php';

$page = trim((string) ($router ?? 'login'), '/');
$isRegister = $page === 'register';
$isAccount = $page === 'account';
$errors = [];
$notice = '';

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrfToken = $_SESSION['csrf_token'];

if ($page === 'logout' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (hash_equals($csrfToken, (string) ($_POST['csrf_token'] ?? ''))) {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();
    }
    header('Location: ' . SITE_URL . '/');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$isAccount) {
    if (!hash_equals($csrfToken, (string) ($_POST['csrf_token'] ?? ''))) {
        $errors[] = 'Your session expired. Please refresh the page and try again.';
    } elseif (isset($_SESSION['user_id'])) {
        header('Location: ' . SITE_URL . '/account');
        exit;
    } elseif ($isRegister) {
        $name = trim((string) ($_POST['name'] ?? ''));
        $email = strtolower(trim((string) ($_POST['email'] ?? '')));
        $password = (string) ($_POST['password'] ?? '');
        if ($name === '' || strlen($name) > 100) $errors[] = 'Enter your name (up to 100 characters).';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 100) $errors[] = 'Enter a valid email address.';
        if (strlen($password) < 10) $errors[] = 'Use a password with at least 10 characters.';
        if (!$errors) {
            $check = $conn->prepare('SELECT user_id FROM tbl_user WHERE email = ? LIMIT 1');
            $check->bind_param('s', $email);
            $check->execute();
            $exists = $check->get_result()->fetch_assoc();
            $check->close();
            if ($exists) {
                $errors[] = 'An account with that email already exists. Try logging in.';
            } else {
                $parts = preg_split('/\s+/', $name, 2);
                $firstName = substr($parts[0], 0, 50);
                $lastName = isset($parts[1]) ? substr($parts[1], 0, 50) : '';
                $username = substr(preg_replace('/[^a-z0-9_]/', '', strstr($email, '@', true) ?: 'member'), 0, 40) . '_' . bin2hex(random_bytes(4));
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare('INSERT INTO tbl_user (username, email, password_hash, first_name, last_name, is_email_verified, is_admin) VALUES (?, ?, ?, ?, ?, 1, 0)');
                $stmt->bind_param('sssss', $username, $email, $hash, $firstName, $lastName);
                if ($stmt->execute()) {
                    $lookup = $conn->prepare('SELECT user_id FROM tbl_user WHERE email = ? LIMIT 1');
                    $lookup->bind_param('s', $email);
                    $lookup->execute();
                    $createdUser = $lookup->get_result()->fetch_assoc();
                    $lookup->close();
                    session_regenerate_id(true);
                    $_SESSION['user_id'] = $createdUser['user_id'];
                    $_SESSION['user_name'] = $name;
                    header('Location: ' . SITE_URL . '/account');
                    exit;
                }
                $errors[] = 'We could not create your account. Please try again.';
                $stmt->close();
            }
        }
    } else {
        $email = strtolower(trim((string) ($_POST['email'] ?? '')));
        $password = (string) ($_POST['password'] ?? '');
        $stmt = $conn->prepare('SELECT user_id, password_hash, is_email_verified, first_name, last_name FROM tbl_user WHERE email = ? LIMIT 1');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if ($user && password_verify($password, $user['password_hash']) && (int) $user['is_email_verified'] === 1) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_name'] = trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) ?: $email;
            $update = $conn->prepare('UPDATE tbl_user SET last_login = CURRENT_TIMESTAMP WHERE user_id = ?');
            $update->bind_param('s', $user['user_id']);
            $update->execute();
            $update->close();
            header('Location: ' . SITE_URL . '/account');
            exit;
        }
        $errors[] = $user && (int) $user['is_email_verified'] !== 1
            ? 'Please verify your email before logging in.'
            : 'Email or password is incorrect.';
    }
}

if ($isAccount && !isset($_SESSION['user_id'])) {
    header('Location: ' . SITE_URL . '/login');
    exit;
}
$meta_title = $isRegister ? 'Create your account | ' . $site_name : ($isAccount ? 'Your account | ' . $site_name : 'Log in | ' . $site_name);
$meta_desc = 'Secure access to your ' . $site_name . ' account.';
include __DIR__ . '/components/header.php';
?>
<main class="auth-page">
  <div class="auth-wrap">
    <section class="auth-intro">
      <a class="auth-kicker" href="<?= SITE_URL ?>/"><?= htmlspecialchars($site_name, ENT_QUOTES, 'UTF-8') ?> <span>SECURE ACCESS</span></a>
      <h1><?= $isAccount ? 'You’re all set.' : ($isRegister ? 'A better way to sign in starts here.' : 'Good to have you back.') ?></h1>
      <p><?= $isAccount ? 'Your account is ready whenever you need it.' : 'One secure account for a smoother, more confident experience.' ?></p>
      <div class="security-note"><span class="security-icon">✓</span><div><strong>Security, built in</strong><small>Encrypted passwords. Protected sessions. Your account stays yours.</small></div></div>
    </section>
    <section class="auth-card">
      <?php if ($isAccount): ?>
        <span class="form-eyebrow">YOUR ACCOUNT</span><h2>Hello, <?= htmlspecialchars((string) ($_SESSION['user_name'] ?? 'there'), ENT_QUOTES, 'UTF-8') ?></h2>
        <p class="form-copy">You’re signed in securely.</p>
        <form method="post" action="<?= SITE_URL ?>/logout"><input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>"><button class="auth-submit" type="submit">Sign out <span>→</span></button></form>
        <a class="text-link account-home" href="<?= SITE_URL ?>/">Return to homepage</a>
      <?php else: ?>
        <span class="form-eyebrow"><?= $isRegister ? 'GET STARTED' : 'WELCOME BACK' ?></span>
        <h2><?= $isRegister ? 'Create your account' : 'Log in to your account' ?></h2>
        <p class="form-copy"><?= $isRegister ? 'It takes less than a minute.' : 'Enter your details to continue.' ?></p>
        <?php foreach ($errors as $error): ?><div class="form-alert" role="alert"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div><?php endforeach; ?>
        <form method="post" action="<?= SITE_URL ?>/<?= $isRegister ? 'register' : 'login' ?>" class="auth-form">
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
          <?php if ($isRegister): ?><label>Full name<input name="name" type="text" autocomplete="name" maxlength="100" placeholder="Jordan Lee" required value="<?= htmlspecialchars((string) ($_POST['name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"></label><?php endif; ?>
          <label>Email address<input name="email" type="email" autocomplete="email" maxlength="100" placeholder="you@example.com" required value="<?= htmlspecialchars((string) ($_POST['email'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"></label>
          <label>Password<input name="password" type="password" autocomplete="<?= $isRegister ? 'new-password' : 'current-password' ?>" <?= $isRegister ? 'minlength="10"' : '' ?> placeholder="<?= $isRegister ? 'At least 10 characters' : 'Enter your password' ?>" required></label>
          <button class="auth-submit" type="submit"><?= $isRegister ? 'Create account' : 'Log in' ?><span>→</span></button>
        </form>
        <p class="auth-switch"><?= $isRegister ? 'Already have an account?' : 'New here?' ?> <a class="text-link" href="<?= SITE_URL ?>/<?= $isRegister ? 'login' : 'register' ?>"><?= $isRegister ? 'Log in' : 'Create an account' ?></a></p>
        <p class="privacy-note">Your details are protected and never sold.</p>
      <?php endif; ?>
    </section>
  </div>
</main>
<style>
.auth-page{background:#f5f7f6;min-height:calc(100vh - 72px);padding:72px 24px;display:flex;align-items:center}.auth-wrap{width:min(1080px,100%);margin:auto;display:grid;grid-template-columns:1fr 440px;gap:90px;align-items:center}.auth-intro{max-width:500px}.auth-kicker{color:#173f35;text-decoration:none;font-size:14px;font-weight:700;letter-spacing:.04em}.auth-kicker span{display:block;color:#71847d;font-size:10px;letter-spacing:.18em;margin-top:9px}.auth-intro h1{font:500 clamp(40px,5.2vw,62px)/1.08 Georgia,serif;letter-spacing:-.045em;color:#172d27;margin:34px 0 18px}.auth-intro>p{font-size:17px;line-height:1.7;color:#687872;max-width:390px}.security-note{display:flex;gap:14px;margin-top:54px;padding-top:20px;border-top:1px solid #dfe6e2;color:#25463b}.security-icon{width:34px;height:34px;border:1px solid #aec6ba;border-radius:50%;display:grid;place-items:center}.security-note strong,.security-note small{display:block}.security-note strong{font-size:13px}.security-note small{font-size:12px;color:#708078;margin-top:4px;line-height:1.5}.auth-card{background:#fff;border:1px solid #e7ece9;border-radius:12px;padding:38px 36px;box-shadow:0 18px 55px #19382c0a}.form-eyebrow{font-size:10px;letter-spacing:.16em;font-weight:700;color:#72847b}.auth-card h2{font:500 28px/1.2 Georgia,serif;color:#172d27;margin:12px 0 8px}.form-copy{font-size:13px;color:#7a8781;margin:0 0 26px}.auth-form label{display:block;font-size:12px;color:#34473f;font-weight:600;margin:18px 0}.auth-form input{box-sizing:border-box;display:block;width:100%;margin-top:8px;padding:13px 14px;border:1px solid #dce4df;border-radius:6px;font:400 14px inherit;color:#26372f;outline:none}.auth-form input:focus{border-color:#4e7967;box-shadow:0 0 0 3px #4e796719}.auth-form input::placeholder{color:#a2ada7}.auth-submit{width:100%;border:0;border-radius:6px;background:#173f35;color:white;padding:14px 17px;margin-top:8px;font-size:13px;font-weight:600;cursor:pointer;display:flex;justify-content:space-between}.auth-submit:hover{background:#245746}.auth-switch{text-align:center;font-size:12px;color:#7a8781;margin:22px 0}.text-link{color:#245746;font-weight:600;text-decoration:none}.text-link:hover{text-decoration:underline}.privacy-note{text-align:center;border-top:1px solid #edf0ee;padding-top:17px;margin:0;color:#9aa49f;font-size:11px}.form-alert{background:#fff4f1;border:1px solid #f2d2c9;color:#914b3e;border-radius:5px;padding:10px 12px;font-size:12px;margin:10px 0}.account-home{display:block;text-align:center;margin-top:20px;font-size:13px}@media(max-width:760px){.auth-page{padding:46px 18px}.auth-wrap{grid-template-columns:1fr;gap:30px;max-width:460px}.auth-intro h1{font-size:42px;margin-top:22px}.auth-intro>p{font-size:15px}.security-note{margin-top:26px}.auth-card{padding:30px 24px}}
</style>
<?php include __DIR__ . '/components/footer.php'; ?>
