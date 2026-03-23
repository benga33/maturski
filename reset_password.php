<?php
session_start();
require_once 'config.php';

$token = $_GET['token'] ?? '';
$now   = date('Y-m-d H:i:s');
$error = '';

if (empty($token)) {
    header('Location: index.php');
    exit();
}

$stmt = $conn->prepare("SELECT id FROM users WHERE reset_token = ? AND reset_token_expires > ?");
$stmt->bind_param("ss", $token, $now);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Invalid or expired reset link.");
}

$user = $result->fetch_assoc();

if (isset($_POST['submit'])) {
    $password = $_POST['password'];
    $confirm  = $_POST['confirm'];

    if ($password !== $confirm) {
        $error = "Passwords do not match.";
    } elseif (strlen($password) < 8) {
        $error = "Password must be at least 8 characters.";
    } else {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $update = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_token_expires = NULL WHERE id = ?");
        $update->bind_param("si", $hashed, $user['id']);
        $update->execute();
        header('Location: index.php');
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container">
    <div class="form-box active">
        <form method="post">
            <h2>Reset Password</h2>
            <?php if ($error) echo "<p class='error-message'>$error</p>"; ?>
            <input type="password" name="password" placeholder="New password" required minlength="8">
            <input type="password" name="confirm" placeholder="Confirm password" required minlength="8">
            <button type="submit" name="submit">Reset Password</button>
        </form>
    </div>
</div>
</body>
</html>