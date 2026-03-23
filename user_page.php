<?php
session_start();
if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'user') {
    header('Location: index.php');
    exit();
}
require_once 'config.php';
$stmt = $conn->prepare("SELECT two_factor_enabled FROM users WHERE email = ?");
$stmt->bind_param("s", $_SESSION['email']);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User Page</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body style="background: #fff;">
    <div class="box">
        <h1>Welcome, <span><?php echo htmlspecialchars($_SESSION['name']); ?></span></h1>
        <p>This is an <span>user</span> page</p>
        <?php if (!$user['two_factor_enabled']): ?>
            <a href="setup_2fa.php">Setup Two-Factor Authentication</a>
        <?php endif; ?>
        <button onclick="window.location.href='logout.php'">Logout</button>
    </div>
</body>
</html>