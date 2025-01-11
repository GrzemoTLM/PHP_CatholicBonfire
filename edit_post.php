<?php
require_once 'Threads.php';
session_start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postId = isset($_POST['id']) ? intval($_POST['id']) : null;
    $title = isset($_POST['title']) ? trim($_POST['title']) : null;
    $content = isset($_POST['content']) ? trim($_POST['content']) : null;
    $userId = $_SESSION['user_id'] ?? null;

    if (!$postId || !$title || !$content || !$userId) {
        echo json_encode(['success' => false, 'message' => 'Invalid input.']);
        exit;
    }

    $threads = new Threads();
    $response = $threads->editPost($postId, $userId, $title, $content);

    echo json_encode($response);
}
?>
