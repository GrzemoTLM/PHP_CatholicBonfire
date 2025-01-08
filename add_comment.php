<?php
session_start();
require_once 'db.php';

header('Content-Type: application/json');

if (!isset($_POST['thread_id'], $_POST['content']) || empty($_POST['thread_id']) || empty(trim($_POST['content']))) {
    echo json_encode(['success' => false, 'message' => 'Post ID and content are required.']);
    exit;
}

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in.']);
    exit;
}

$threadId = $_POST['thread_id'];
$content = trim($_POST['content']);
$userId = $_SESSION['user_id'];

try {
    $db = new Database();
    $sql = "INSERT INTO comments (content, thread_id, user_id, created_at) VALUES (:content, :thread_id, :user_id, NOW())";
    $db->query($sql, [
        'content' => $content,
        'thread_id' => $threadId,
        'user_id' => $userId,
    ]);

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
