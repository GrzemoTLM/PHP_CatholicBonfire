<?php
require_once 'check_session.php';
require_once '../classes/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sessionId = $_POST['sessionId'] ?? null;

    if (!$sessionId) {
        echo json_encode(['success' => false, 'message' => 'Session ID is required.']);
        exit;
    }

    try {
        $db = Database::getInstance()->getConnection();
        $sql = "DELETE FROM logged_in_users WHERE sessionId = :sessionId";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':sessionId', $sessionId, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true, 'message' => 'User successfully logged out.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Session ID not found or already logged out.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
