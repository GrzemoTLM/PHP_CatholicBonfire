<?php
require_once 'db.php';

class User
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function register($username, $email, $password, $confirmPassword)
    {
        if (empty($username) || empty($email) || empty($password) || empty($confirmPassword)) {
            return ['success' => false, 'message' => 'All fields are required.'];
        }

        if ($password !== $confirmPassword) {
            return ['success' => false, 'message' => 'Passwords do not match.'];
        }

        $checkSql = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->db->prepare($checkSql);
        $stmt->execute(['email' => $email]);

        if ($stmt->fetch()) {
            return ['success' => false, 'message' => 'Email is already registered.'];
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $insertSql = "INSERT INTO users (username, email, password, created_at) VALUES (:username, :email, :password, NOW())";
        $stmt = $this->db->prepare($insertSql);
        $stmt->execute([
            'username' => $username,
            'email' => $email,
            'password' => $hashedPassword
        ]);

        return ['success' => true, 'message' => 'Registration successful!'];
    }

    public function login($email, $password)
    {
        if (empty($email) || empty($password)) {
            return ['success' => false, 'message' => 'All fields are required.'];
        }

        $sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['session_id'] = session_id();

            $insertSql = "REPLACE INTO logged_in_users (sessionId, userId, lastUpdate) VALUES (:sessionId, :userId, NOW())";
            $stmt = $this->db->prepare($insertSql);
            $stmt->execute([
                'sessionId' => session_id(),
                'userId' => $user['id']
            ]);

            return ['success' => true, 'message' => 'Login successful!'];
        } else {
            return ['success' => false, 'message' => 'Incorrect email or password.'];
        }
    }
}
?>
