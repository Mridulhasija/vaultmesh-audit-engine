<?php
require_once __DIR__ . '/components/config-header.php';

$conn = db();

$page_title = "Products";
$page_subtitle = "Management";
$table = "tbl_product";

$id_col = 'id';
$id_type = "s";

$tableContent = [
    "Title" => "title",
    "<center><i class='fa fa-picture-o' aria-hidden='true'></i></center>" => "image",
];

// Define the actions and their enable/disable status
$actions = [
    'approve' => false,
    'add' => true,
    'edit' => true,
    'delete' => true
];

$editor_url = "product-editor.php";
$currentUrl = $_SERVER['PHP_SELF'];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_GET['action'])) {
    $action = $_GET['action'] ?? null;
    $id = $_GET['id'] ?? null;
    try {
        include('components/quick-process.php');

        if ($action === "add" || $action === "edit") {
            $title = isset($_POST['title']) ? trim($_POST['title']) : null;
            if (!$title) {
                throw new Exception("Title is required.");
            }

            $url = isset($_POST['url']) ? trim($_POST['url']) : null;
            $url = empty($url) ? $title : $url;
            $url = $utils->stringToUrl($url);

            $meta_title = isset($_POST['meta_title']) ? trim($_POST['meta_title']) : null;
            $meta_title = empty($meta_title) ? $title : $meta_title;
            $meta_keyword = isset($_POST['meta_keyword']) ? trim($_POST['meta_keyword']) : null;
            $meta_desc = isset($_POST['meta_desc']) ? trim($_POST['meta_desc']) : null;
            $short_description = isset($_POST['short_description']) ? trim($_POST['short_description']) : null;
            $description = isset($_POST['description']) ? trim($_POST['description']) : null;
            $image = isset($_POST['image']) ? trim($_POST['image']) : null;
            $feature = isset($_POST['feature']) ? 1 : 0;
            $status = isset($_POST['status']) ? 1 : 0;
            $category = isset($_POST['category']) ? implode(",", $_POST['category']) : null;

            $price_raw = isset($_POST['price']) ? trim($_POST['price']) : null;
            if ($price_raw === null || $price_raw === '' || !is_numeric($price_raw) || floatval($price_raw) <= 0) {
                throw new Exception("Price must be greater than 0.");
            }
            $price = $price_raw;

            $discounted_price_raw = isset($_POST['discounted_price']) ? trim($_POST['discounted_price']) : null;
            $discounted_price = null;
            if ($discounted_price_raw !== null && $discounted_price_raw !== '') {
                if (!is_numeric($discounted_price_raw) || floatval($discounted_price_raw) <= 0) {
                    throw new Exception("Discounted price must be greater than 0.");
                }
                if (floatval($discounted_price_raw) >= floatval($price_raw)) {
                    throw new Exception("Discounted price must be less than the regular price.");
                }
                $discounted_price = $discounted_price_raw;
            }

            $stmt = null;

            if ($action === 'add') {
                $id = $getdata->uuidv4();
                $url = $utils->generateUniqueURL($conn, $table, $url);

                $stmt = $conn->prepare(
                    "INSERT INTO $table (id, title, meta_title, meta_keyword, meta_desc, category, url, short_description, description, image, feature, status, price, discounted_price) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)"
                );
                if (!$stmt) {
                    throw new Exception("Error preparing statement: " . $conn->error);
                }
                $stmt->bind_param(
                    "ssssssssssiiss",
                    $id,
                    $title,
                    $meta_title,
                    $meta_keyword,
                    $meta_desc,
                    $category,
                    $url,
                    $short_description,
                    $description,
                    $image,
                    $feature,
                    $status,
                    $price,
                    $discounted_price
                );
            } elseif ($action === 'edit') {
                if (!$id) {
                    throw new Exception("Missing product id for edit.");
                }

                $existing_url = $utils->getURLById($conn, $table, $id, $id_type);
                if ($url != $existing_url) {
                    $url = $utils->generateUniqueURL($conn, $table, $url);
                }

                $stmt = $conn->prepare(
                    "UPDATE $table SET title = ?, meta_title = ?, meta_keyword = ?, meta_desc = ?, category = ?, url = ?, short_description = ?, description = ?, image = ?, feature = ?, status = ?, price = ?, discounted_price = ? WHERE id = ?"
                );
                if (!$stmt) {
                    throw new Exception("Error preparing statement: " . $conn->error);
                }
                $stmt->bind_param(
                    "sssssssssiiiss",
                    $title,
                    $meta_title,
                    $meta_keyword,
                    $meta_desc,
                    $category,
                    $url,
                    $short_description,
                    $description,
                    $image,
                    $feature,
                    $status,
                    $price,
                    $discounted_price,
                    $id
                );
            }

            if (!$stmt->execute()) {
                throw new Exception("Error executing statement: " . $stmt->error);
            }

            $stmt->close();
            $_SESSION["success-msg"] = $page_title . " successfully " . ($action === 'add' ? 'added' : 'edited');

            header('Location: ' . $editor_url . "?action=edit&id=$id");
            exit();
        }
    } catch (Exception $e) {
        $_SESSION["error-msg"] = "Error: " . $e->getMessage();
        if ($action === 'edit' && $id) {
            header('Location: ' . $editor_url . "?action=edit&id=$id");
        } else {
            header('Location: ' . $editor_url . "?action=add");
        }
    }
    exit();
} else {
    //show page
    include("components/header.php");
    include("components/manage.php");
}
