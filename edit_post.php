<?php
require_once 'check_session.php';
require_once 'db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postId = isset($_POST['id']) ? intval($_POST['id']) : null;
    $title = isset($_POST['title']) ? trim($_POST['title']) : null;
    $content = isset($_POST['content']) ? trim($_POST['content']) : null;

    if (!$postId || !$title || !$content) {
        echo json_encode(['success' => false, 'message' => 'Invalid input. All fields are required.']);
        exit;
    }

    try {
        $db = new Database();

        $query = "SELECT id FROM threads WHERE id = :id AND user_id = :user_id";
        $stmt = $db->prepare($query);
        $stmt->bindValue(':id', $postId, PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() === 0) {
            echo json_encode(['success' => false, 'message' => 'Post not found or not authorized.']);
            exit;
        }

        $updateQuery = "UPDATE threads SET title = :title, content = :content WHERE id = :id";
        $updateStmt = $db->prepare($updateQuery);
        $updateStmt->bindValue(':title', $title, PDO::PARAM_STR);
        $updateStmt->bindValue(':content', $content, PDO::PARAM_STR);
        $updateStmt->bindValue(':id', $postId, PDO::PARAM_INT);

        if ($updateStmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Post updated successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update post.']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
