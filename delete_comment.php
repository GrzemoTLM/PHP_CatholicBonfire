<?php
require_once 'check_session.php';
require_once 'db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $commentId = $_POST['commentId'] ?? null;

    if (!$commentId) {
        echo json_encode(['success' => false, 'message' => 'Comment ID is required.']);
        exit;
    }

    try {
        $db = new Database();
        $sql = "DELETE FROM comments WHERE id = :id";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $commentId, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true, 'message' => 'Comment successfully deleted.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Comment not found or already deleted.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
    }
}
?>
