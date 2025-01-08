<?php
require_once 'db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $error = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email format.';
    } else {
        $db = new Database();

        try {
            $sql = "SELECT * FROM users WHERE email = :email";
            $stmt = $db->query($sql, ['email' => $email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                session_start();
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                header('Location: home.php');
                exit;
            } else {
                $error = 'Incorrect email or password.';
            }
        } catch (PDOException $e) {
            $error = 'An error occurred: ' . $e->getMessage();
        }
    }
}
?>
