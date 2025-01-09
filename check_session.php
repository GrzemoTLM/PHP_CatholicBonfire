<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['session_id'])) {
    header('Location: login.html?error=Please log in.');
    exit;
}

$db = new Database();

try {

    $sql = "SELECT * FROM logged_in_users WHERE sessionId = :sessionId";
    $stmt = $db->query($sql, ['sessionId' => $_SESSION['session_id']]);
    $session = $stmt->fetch();

    if (!$session) {
        session_unset();
        session_destroy();
        header('Location: login.html?error=Session expired.');
        exit;
    }

    $updateSql = "UPDATE logged_in_users SET lastUpdate = NOW() WHERE sessionId = :sessionId";
    $db->query($updateSql, ['sessionId' => $_SESSION['session_id']]);

    $timeout = 30 * 60;
    $expirationTime = date('Y-m-d H:i:s', time() - $timeout);

    $deleteSql = "DELETE FROM logged_in_users WHERE lastUpdate < :expirationTime";
    $db->query($deleteSql, ['expirationTime' => $expirationTime]);
} catch (PDOException $e) {
    die("Error verifying session: " . $e->getMessage());
}
?>
