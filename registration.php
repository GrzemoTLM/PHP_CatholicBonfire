<?php
require_once 'db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirmPassword = trim($_POST['confirm_password']);

    if (empty($username) || empty($email) || empty($password) || empty($confirmPassword)) {
        $error = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email format.';
    } elseif ($password !== $confirmPassword) {
        $error = 'Passwords do not match.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long.';
    } else {

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $db = new Database();

        try {
            $sql = "INSERT INTO users (username, email, password) VALUES (:username, :email, :password)";
            $db->query($sql, [
                'username' => $username,
                'email' => $email,
                'password' => $hashedPassword
            ]);
            $success = 'Registration successful! You can now <a href="login.html">log in</a>.';
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                $error = 'This email is already registered.';
            } else {
                $error = 'An error occurred: ' . $e->getMessage();
            }
        }
    }
}
?>
