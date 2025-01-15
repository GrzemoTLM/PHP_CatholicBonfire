<?php
require_once '../classes/User.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    $user = new User();
    $response = $user->register($username, $email, $password, $confirm_password);

    if ($response['success']) {
        header("Location: ../views/login.html?success=" . urlencode($response['message']));
    } else {
        header("Location: register.html?error=" . urlencode($response['message']));
    }
    exit();
} else {
    header("Location: login.html");
    exit();
}
