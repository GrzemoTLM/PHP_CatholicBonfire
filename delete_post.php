<?php
require_once 'Threads.php';
session_start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postId = intval($_POST['postId']);
    $userId = $_SESSION['user_id'] ?? null;
    $isAdmin = $_SESSION['role'] === 'admin';

    if (!$postId) {
        echo json_encode(['success' => false, 'message' => 'Post ID is required.']);
        exit;
    }

    $threads = new Threads();
    $response = $threads->deletePost($postId, $userId, $isAdmin);

    echo json_encode($response);
}
?>
