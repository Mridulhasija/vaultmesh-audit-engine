<?php
// error_reporting(0);
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', 'error_log.txt');

// Display errors on the page
// ini_set('display_errors', 1);

$isSecureRequest = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'secure' => $isSecureRequest,
    'httponly' => true,
    'samesite' => 'Strict',
]);
ini_set('session.use_strict_mode', '1');
ini_set('session.use_only_cookies', '1');
ini_set('session.gc_maxlifetime', '86400');
session_start();
require_once __DIR__ . '/connection.php';
$conn = db();

$config_result = mysqli_query($conn, "SELECT * FROM tbl_config LIMIT 1");
if ($config_result && mysqli_num_rows($config_result) > 0) {
    $configData = mysqli_fetch_assoc($config_result);
    $logo = !empty($configData['logo']) ? media_folder . $configData['logo'] : null;
    $site_name = 'VaultMesh';
    define("CURRENCY", $configData['currency']);
    define("THOUSAND_SEPERATOR", $configData['thousand_separator']);
    define("DECIMAL_SEPERATOR", $configData['decimal_separator']);
}

require_once __DIR__ . '/getdata.php';
$getdata = new Getdata();

$meta_title = $site_name ?? '';
$meta_keyword = '';
$meta_desc = '';

if (isset($router)) {
    // Check if $location is set and $slug is not set
    if (isset($location) && !isset($slug)) {
        $router = 'home-service';
    } else {
        $router = ($router == '') ? 'home' : $router;
    }

    // Fetch SEO metadata based on the router
    $selsql = $getdata->FetchSeoMeta($router);
    $respage = mysqli_fetch_object($selsql);

    // Set meta details
    $meta_title = $respage->meta_title ?? ($respage->title ?? $meta_title);
    $meta_keyword = $respage->meta_keyword ?? $meta_keyword;
    $meta_desc = $respage->meta_desc ?? $meta_desc;
}
