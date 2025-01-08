<?php
session_start();
require_once 'db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $categoryId = intval($_POST['category_id']);
    $userId = $_SESSION['user_id'] ?? null;

    if (!$userId) {
        echo json_encode(['success' => false, 'message' => 'User not logged in.']);
        exit;
    }

    if (empty($title)) {
        echo json_encode(['success' => false, 'message' => 'Post title cannot be empty.']);
        exit;
    }

    if (empty($content)) {
        echo json_encode(['success' => false, 'message' => 'Post content cannot be empty.']);
        exit;
    }

    $db = new Database();

    try {
        $sql = "INSERT INTO threads (user_id, category_id, title, content, created_at) 
                VALUES (:userId, :categoryId, :title, :content, NOW())";
        $db->query($sql, [
            'userId' => $userId,
            'categoryId' => $categoryId,
            'title' => $title,
            'content' => $content
        ]);

        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
