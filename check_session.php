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

    $updateSql = "UPDATE logged_in_users SET lastUpdate = :lastUpdate WHERE sessionId = :sessionId";
    $db->query($updateSql, [
        'lastUpdate' => date('Y-m-d H:i:s'),
        'sessionId' => $_SESSION['session_id']
    ]);
} catch (PDOException $e) {
    die("Error verifying session: " . $e->getMessage());
}
$timeout = 30 * 60;
$expirationTime = date('Y-m-d H:i:s', time() - $timeout);

$sql = "DELETE FROM logged_in_users WHERE lastUpdate < :expirationTime";
$db->query($sql, ['expirationTime' => $expirationTime]);

?>
