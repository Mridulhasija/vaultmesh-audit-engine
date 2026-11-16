<?php

function saveUserSession(mysqli $conn, string $userId): bool
{
    $sessionHash = hash('sha256', session_id());
    $stmt = $conn->prepare('INSERT INTO tbl_user_session (session_hash, user_id, expires_at) VALUES (?, ?, DATE_ADD(CURRENT_TIMESTAMP, INTERVAL 1 DAY))');
    if (!$stmt) return false;
    $stmt->bind_param('ss', $sessionHash, $userId);
    $saved = $stmt->execute();
    $stmt->close();
    return $saved;
}

function revokeUserSession(mysqli $conn): void
{
    $sessionHash = hash('sha256', session_id());
    $stmt = $conn->prepare('DELETE FROM tbl_user_session WHERE session_hash = ?');
    if (!$stmt) return;
    $stmt->bind_param('s', $sessionHash);
    $stmt->execute();
    $stmt->close();
}

function ensureCsrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}
