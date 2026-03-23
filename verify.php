<?php
require_once 'config.php';

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    $now   = date('Y-m-d H:i:s');

    $stmt = $conn->prepare("SELECT id, token_expires FROM users WHERE token = ? AND is_verified = 0");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo "Invalid or already used token.";
    } else {
        $user = $result->fetch_assoc();
        if ($now > $user['token_expires']) {
            echo "Token has expired. Please register again.";
        } else {
            $update = $conn->prepare("UPDATE users SET is_verified = 1, token = NULL, token_expires = NULL WHERE id = ?");
            $update->bind_param("i", $user['id']);
            $update->execute();
            echo "Email verified. You can now log in.";
        }
    }
}
?>