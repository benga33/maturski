
<?php
session_start();
require_once 'config.php';
require_once 'vendor/autoload.php';

use RobThree\Auth\TwoFactorAuth;
use RobThree\Auth\Providers\Qr\QRServerProvider;

if (!isset($_SESSION['2fa_user_id'])) {
    header('Location: index.php');
    exit();
}

$tfa = new TwoFactorAuth(new QRServerProvider());

$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $_SESSION['2fa_user_id']);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (isset($_POST['verify'])) {
    $code = trim($_POST['code']);
    if ($tfa->verifyCode($user['two_factor_secret'], $code)) {
        session_regenerate_id(true);
        $_SESSION['name']  = $user['name'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role']  = $user['role'];
        unset($_SESSION['2fa_user_id']);
        if ($user['role'] === 'admin') {
            header('Location: admin_page.php');
        } else {
            header('Location: user_page.php');
        }
        exit();
    } else {
        $error = "Invalid code. Try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Two-Factor Authentication</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container">
    <div class="form-box active">
        <h2>Two-Factor Authentication</h2>
        <p>Enter the code from your authenticator app:</p>
        <?php if (isset($error)) echo "<p class='error-message'>$error</p>"; ?>
        <form method="post">
            <input type="text" name="code" placeholder="Enter 6-digit code" required>
            <button type="submit" name="verify">Verify</button>
        </form>
    </div>
</div>
</body>
</html>