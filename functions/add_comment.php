<?php
require_once '../classes/Comments.php';
session_start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $threadId = intval($_POST['thread_id']);
    $userId = $_SESSION['user_id'] ?? null;
    $content = trim($_POST['content']);

    if (!$userId) {
        echo json_encode(['success' => false, 'message' => 'User not logged in.']);
        exit;
    }

    $comments = new Comments();
    $response = $comments->addComment($threadId, $userId, $content);

    echo json_encode($response);
}
?>
