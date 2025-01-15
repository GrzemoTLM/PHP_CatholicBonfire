<?php
require_once 'User.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $user = new User();
    $response = $user->login($email, $password);

    if ($response['success']) {
        header('Location: mainboard.php');
        exit();
    } else {
        header('Location: login.html?error=' . urlencode($response['message']));
        exit();
    }
} else {
    header('Location: login.html');
    exit();
}
?>
