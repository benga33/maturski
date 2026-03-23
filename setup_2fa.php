<?php
session_start();
require_once 'config.php';
require_once 'vendor/autoload.php';

use RobThree\Auth\TwoFactorAuth;
use RobThree\Auth\Providers\Qr\QRServerProvider;

if (!isset($_SESSION['email'])) {
    header('Location: index.php');
    exit();
}

$tfa = new TwoFactorAuth(new QRServerProvider());
$email = $_SESSION['email'];

$stmt = $conn->prepare("SELECT two_factor_secret FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (empty($user['two_factor_secret'])) {
    $secret = (string) $tfa->createSecret();
    $update = $conn->prepare("UPDATE users SET two_factor_secret = ? WHERE email = ?");
    $update->bind_param("ss", $secret, $email);
    $update->execute();
} else {
    $secret = $user['two_factor_secret'];
}

$qrUrl = $tfa->getQRCodeImageAsDataUri('Maturski App', $secret);

if (isset($_POST['verify'])) {
    $code = trim($_POST['code']);
    if ($tfa->verifyCode($secret, $code)) {
        $update = $conn->prepare("UPDATE users SET two_factor_enabled = 1 WHERE email = ?");
        $update->bind_param("s", $email);
        $update->execute();
        $_SESSION['2fa_enabled'] = true;
        header('Location: user_page.php');
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
    <title>Setup 2FA</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container">
    <div class="form-box active">
        <h2>Setup 2FA</h2>
        <p>Scan this QR code with Google Authenticator:</p>
        <img src="<?= $qrUrl ?>" alt="QR Code">
        <?php if (isset($error)) echo "<p class='error-message'>$error</p>"; ?>
        <form method="post">
            <input type="text" name="code" placeholder="Enter 6-digit code" required>
            <button type="submit" name="verify">Verify</button>
        </form>
    </div>
</div>
</body>
</html>