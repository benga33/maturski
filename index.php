<?php
session_start();
$errors = [
    'login_error'    => $_SESSION['login_error'] ?? '',
    'register_error' => $_SESSION['register_error'] ?? '',
];
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$activeForm = $_SESSION['active_form'] ?? 'login';
unset($_SESSION['login_error'], $_SESSION['register_error'], $_SESSION['active_form']);

function showError($error) {
    return !empty($error)
        ? "<p class='error-message'>" . htmlspecialchars($error) . "</p>"
        : '';
}
function isActiveForm($form_name, $activeForm) {
    return $form_name === $activeForm ? 'active' : '';
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Full-Stack Login & Register Form With User & Admin Page</title>
    <link rel="stylesheet" href="styles.css">
</head>


<body>
<div class="container">
    <div class="form-box <?= isActiveForm('login', $activeForm); ?>" id="login-form">
        <form action="login_register.php" method="post">
            <h2>Login</h2>
            <?= showError($errors['login_error']); ?>
            <input type="email" name="email" placeholder="Email" required 
            value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            <input type="password" name="password" placeholder="Password" required minlength="8" style="margin-bottom: 2px;">           
            <p style="text-align:left; margin: 2px 0;"><a href="forgot_password.php">Forgot your password?</a></p>
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">
            <button type="submit" name="login">login</button>
            <a href="google_login.php" class="google-btn" style="text-decoration: none; display: block; text-align: center;">Login with Google</a>           
            <p>Don't have an account?<a href="#" onclick="showForm('register-form')">Register</a></p>
        </form>
    </div>

     <div class="form-box <?= isActiveForm('register', $activeForm); ?>" id="register-form">
    <form action="login_register.php" method="post">
        <h2>Register</h2>
        <?= showError($errors['register_error']); ?>
        <input type="text" name="name" placeholder="Name" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" id="register-password" placeholder="Password" required>
        <div id="strength-bar" style="height:6px; background:#eee; border-radius:4px; margin:4px 0;">
            <div id="strength-fill" style="height:100%; width:0%; border-radius:4px; transition:0.3s;"></div>
        </div>
        <p id="strength-text" style="font-size:0.8rem;"></p>
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">
        <button type="submit" name="register">Register</button>
        <p>Already have an account? <a href="#" onclick="showForm('login-form')">Login</a></p>
    </form>
</div>

        <script src="script.js" defer></script>
    </body>


</html>