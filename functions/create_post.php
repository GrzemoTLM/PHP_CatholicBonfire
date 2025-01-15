<?php
session_start();
require_once '../classes/Threads.php';

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

    $threads = new Threads();
    $response = $threads->createPost($userId, $categoryId, $title, $content);

    echo json_encode($response);
}
?>
