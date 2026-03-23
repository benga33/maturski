<?php
session_start();
require_once 'config.php';

if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die("Invalid CSRF token.");
}

if (isset($_POST['register'])) {
    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $token        = bin2hex(random_bytes(32));
    $token_expires = date('Y-m-d H:i:s', strtotime('+24 hours'));

    $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $_SESSION['register_error'] = 'Email is already registered!';
        $_SESSION['active_form']    = 'register';
    } else {
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, token, token_expires, is_verified) VALUES (?, ?, ?, ?, ?, 0)");
        $stmt->bind_param("sssss", $name, $email, $password, $token, $token_expires);
        $stmt->execute();

        // Pošalji verification email
        $link    = "http://yourdomain.com/verify.php?token=$token";
        $subject = "Verify your email";
        $message = "Click to verify: $link";
        require_once 'mailer.php';
        sendVerificationEmail($email, $token);

        $_SESSION['register_error'] = 'Check your email to verify your account.';
        $_SESSION['active_form']    = 'login';
    }

    header("Location: index.php");
    exit();
}

if (isset($_POST['login'])) {
    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            if (!$user['is_verified']) {
                $_SESSION['login_error'] = 'Please verify your email first.';
                header("Location: index.php");
                exit();
            }
           if ($user['two_factor_enabled']) {
    $_SESSION['2fa_user_id'] = $user['id'];
    header('Location: verify_2fa.php');
    exit();
}
session_regenerate_id(true);
$_SESSION['name']  = $user['name'];
$_SESSION['email'] = $user['email'];
$_SESSION['role']  = $user['role'];
header('Location: ' . ($user['role'] === 'admin' ? 'admin_page.php' : 'user_page.php'));
exit();
        }
    }

    $_SESSION['login_error'] = 'Incorrect email or password.';
    header("Location: index.php");
    exit();
}
?>