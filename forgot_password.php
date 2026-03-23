<?php
session_start();
require_once 'config.php';
require_once 'mailer.php';

if (isset($_POST['submit'])) {
    $email = trim($_POST['email']);

    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $token   = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $update = $conn->prepare("UPDATE users SET reset_token = ?, reset_token_expires = ? WHERE email = ?");
        $update->bind_param("sss", $token, $expires, $email);
        $update->execute();

        $link = "http://localhost/maturski/reset_password.php?token=$token";
        sendResetEmail($email, $link);
    }

    $message = "If that email exists, a reset link has been sent.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container">
    <div class="form-box active">
        <form method="post">
            <h2>Forgot Password</h2>
            <?php if (isset($message)) echo "<p class='error-message'>$message</p>"; ?>
            <input type="email" name="email" placeholder="Enter your email" required>
            <button type="submit" name="submit">Send Reset Link</button>
            <p><a href="index.php">Back to Login</a></p>
        </form>
    </div>
</div>
</body>
</html>