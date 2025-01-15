<?php
require_once 'check_session.php';
require_once '../classes/Comments.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $commentId = intval($_POST['commentId'] ?? 0);
    $userId = $_SESSION['user_id'] ?? null;
    $isAdmin = $_SESSION['role'] === 'admin';

    if (!$commentId) {
        echo json_encode(['success' => false, 'message' => 'Comment ID is required.']);
        exit;
    }

    $comments = new Comments();
    $response = $comments->deleteComment($commentId, $userId, $isAdmin);

    echo json_encode($response);
    exit;
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}
?>
