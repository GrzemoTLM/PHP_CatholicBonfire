<?php
require_once 'check_session.php';
require_once 'db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postId = $_POST['postId'] ?? null;

    if (!$postId) {
        echo json_encode(['success' => false, 'message' => 'Post ID is required.']);
        exit;
    }

    try {
        $db = new Database();
        $sql = "DELETE FROM threads WHERE id = :id";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $postId, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true, 'message' => 'Post successfully deleted.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Post not found or already deleted.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
