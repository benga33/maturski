<?php
session_start();
require_once 'vendor/autoload.php';
require_once 'config.php';

$client = new Google\Client();
$client->setClientId(GOOGLE_CLIENT_ID);
$client->setClientSecret(GOOGLE_CLIENT_SECRET);
$client->setRedirectUri(GOOGLE_REDIRECT_URI);

if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $client->setAccessToken($token);

    $google_oauth = new Google\Service\Oauth2($client);
    $google_account_info = $google_oauth->userinfo->get();

    $name  = $google_account_info->name;
    $email = $google_account_info->email;

    require_once 'config.php';

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, role, is_verified) VALUES (?, ?, '', 'user', 1)");
        $stmt->bind_param("ss", $name, $email);
        $stmt->execute();
    }

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    session_regenerate_id(true);
    $_SESSION['name']  = $user['name'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['role']  = $user['role'];

    header('Location: user_page.php');
    exit();
}

header('Location: index.php');
exit();