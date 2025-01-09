<?php
session_start();
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        header('Location: login.html?error=All fields are required.');
        exit;
    }

    $db = new Database();

    try {
        $sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $db->query($sql, ['email' => $email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['session_id'] = session_id();

            $insertSql = "REPLACE INTO logged_in_users (sessionId, userId, lastUpdate) 
                          VALUES (:sessionId, :userId, NOW())";
            $db->query($insertSql, [
                'sessionId' => session_id(),
                'userId' => $user['id']
            ]);

            header('Location: mainboard.php');
            exit;
        } else {
            error_log("Failed login attempt for email: $email"); // Logowanie błędu
            header('Location: login.html?error=Incorrect email or password.');
            exit;
        }
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage()); // Logowanie błędu
        header('Location: login.html?error=An error occurred. Please try again later.');
        exit;
    }
} else {
    header('Location: login.html');
    exit;
}
?>
