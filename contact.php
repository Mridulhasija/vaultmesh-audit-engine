<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/include/header_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json; charset=utf-8');

    $captchaValid = false;
    $formId = isset($_POST['captcha_form_id']) ? $_POST['captcha_form_id'] : 'default';
    $userInput = isset($_POST['captcha_code']) ? strtolower(trim($_POST['captcha_code'])) : '';
    $sessionKey = 'captcha_' . $formId;

    if (!empty($userInput) && isset($_SESSION[$sessionKey])) {
        $captchaPhrase = strtolower($_SESSION[$sessionKey]);
        $captchaValid = ($userInput === $captchaPhrase);
        // Only clear session after successful validation
        if ($captchaValid) {
            unset($_SESSION[$sessionKey]);
        }
    }

    if (!$captchaValid) {
        echo json_encode(['success' => false, 'message' => 'Invalid verification code. Please try again.']);
        exit;
    }

    $fields = [
        'name' => ['max' => 100, 'type' => 'text'],
        'phone' => ['max' => 20, 'type' => 'phone'],
        'email' => ['max' => 150, 'type' => 'email'],
        'location' => ['max' => 120, 'type' => 'text'],
        'message' => ['max' => 1000, 'type' => 'text'],
    ];

    $optionalFields = [];

    $sanitizedData = [];
    $errMsg = '';

    foreach ($fields as $fieldName => $fieldConfig) {
        if (!isset($_POST[$fieldName]) || empty(trim($_POST[$fieldName]))) {
            $errMsg = ucfirst($fieldName) . ' is required.';
            break;
        }

        $rawValue = $_POST[$fieldName];
        $sanitizedValue = false;

        switch ($fieldConfig['type']) {
            case 'email':
                $sanitizedValue = $getdata->sanitizeEmail($rawValue);
                if ($sanitizedValue === false) {
                    $errMsg = 'Invalid email format.';
                    break 2;
                }
                break;
            case 'phone':
                $sanitizedValue = $getdata->sanitizePhoneNumber($rawValue);
                if ($sanitizedValue === false) {
                    $errMsg = 'Invalid phone number format.';
                    break 2;
                }
                break;
            case 'text':
            default:
                if ($fieldName === 'message') {
                    $sanitizedValue = $getdata->cleanTextArea($rawValue);
                } else {
                    $sanitizedValue = $getdata->cleanTextLine($rawValue);
                }
                break;
        }

        $sanitizedData[$fieldName] = $sanitizedValue;
    }

    foreach ($optionalFields as $fieldName => $fieldConfig) {
        if (isset($_POST[$fieldName]) && !empty(trim($_POST[$fieldName]))) {
            $rawValue = $_POST[$fieldName];
            $sanitizedValue = $getdata->sanitizeInput($rawValue);
            if (mb_strlen($sanitizedValue) <= $fieldConfig['max']) {
                $sanitizedData[$fieldName] = $sanitizedValue;
            }
        }
    }

    if (empty($errMsg)) {
        $linkPattern = '/(https?:\/\/|www\.)/i';

        foreach ($fields as $fieldName => $fieldConfig) {
            $value = $sanitizedData[$fieldName];

            if (preg_match($linkPattern, $value)) {
                $errMsg = ucfirst($fieldName) . ' cannot contain links.';
                break;
            }

            if (mb_strlen($value) > $fieldConfig['max']) {
                $errMsg = ucfirst($fieldName) . ' exceeds the allowed length of ' . $fieldConfig['max'] . ' characters.';
                break;
            }

            if ($getdata->isSpamMessage($value)) {
                $errMsg = ucfirst($fieldName) . ' contains spam content.';
                break;
            }
        }
    }

    if (!empty($errMsg)) {
        echo json_encode(['success' => false, 'message' => $errMsg]);
        exit;
    }

    $name = $sanitizedData['name'];
    $phone = $sanitizedData['phone'];
    $email = $sanitizedData['email'];
    $location = $sanitizedData['location'];
    $message = $sanitizedData['message'];
    $state = isset($sanitizedData['state']) ? $sanitizedData['state'] : '';
    $product_title = isset($sanitizedData['product_title']) ? $sanitizedData['product_title'] : '';
    $textable = isset($sanitizedData['textable']) ? $sanitizedData['textable'] : '';

    $stmt = $conn->prepare("INSERT INTO tbl_enquiry (name, phone, email, location, message) VALUES (?, ?, ?, ?, ?)");
    if (!$stmt) {
        error_log("Database Error: " . $conn->error);
        echo json_encode(['success' => false, 'message' => 'An error occurred. Please try again later.']);
        exit;
    }

    $stmt->bind_param("sssss", $name, $phone, $email, $location, $message);
    if (!$stmt->execute()) {
        error_log("Database Execution Error: " . $stmt->error);
        $stmt->close();
        echo json_encode(['success' => false, 'message' => 'An error occurred. Please try again later.']);
        exit;
    }
    $stmt->close();

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = MAIL_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = MAIL_USER;
        $mail->Password = MAIL_PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = MAIL_PORT;

        $mail->setFrom(MAIL_USER, $site_name);
        $mail->addReplyTo($email, $name);
        if (!empty($configData['email_1'])) {
            $mail->addAddress($configData['email_1']);
        }
        $mail->addBCC('behoof.commercial@gmail.com');

        $safeName = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
        $safeEmail = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
        $safePhone = htmlspecialchars($phone, ENT_QUOTES, 'UTF-8');
        $safeLocation = htmlspecialchars($location, ENT_QUOTES, 'UTF-8');
        $safeTextable = htmlspecialchars((string) $textable, ENT_QUOTES, 'UTF-8');
        $safeState = htmlspecialchars((string) $state, ENT_QUOTES, 'UTF-8');
        $safeProductTitle = htmlspecialchars((string) $product_title, ENT_QUOTES, 'UTF-8');
        $safeMessage = nl2br(htmlspecialchars($message, ENT_QUOTES, 'UTF-8'), false);

        $mail->isHTML(true);
        $mail->Subject = "Enquiry for $site_name";
        $mail->Body = "
            <strong>Name:</strong> {$safeName} <br>
            <strong>Email:</strong> {$safeEmail} <br>
            <strong>Phone:</strong> {$safePhone} <br>" .
            (empty($textable) ? "" : "<strong>Is this number textable?:</strong> {$safeTextable} <br>") .
            "<strong>Country:</strong> {$safeLocation} <br>" .
            (empty($state) ? "" : "<strong>City/State:</strong> {$safeState} <br>") .
            (empty($product_title) ? "" : "<strong>Product Title:</strong> {$safeProductTitle} <br>") .
            "<strong>Message:</strong><br>{$safeMessage}<br>";

        if (!$mail->send()) {
            error_log("Mailer Error: " . $mail->ErrorInfo);
        }
    } catch (Exception $e) {
        error_log($e->getMessage());
    }

    echo json_encode(['success' => true, 'message' => 'Thank you for your enquiry. Our team will contact you soon.']);
    exit;
}

$pageCanonical = SITE_URL . '/contact';
include('components/header.php');
$pageHeroTitle = trim((string) ($respage->title ?? 'Contact Us')) ?: 'Contact Us';
include __DIR__ . '/components/page-hero.php';
?>


<?php include('components/footer.php'); ?>
