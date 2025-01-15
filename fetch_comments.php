<?php
require_once 'Comments.php';

header('Content-Type: application/json');

$threadId = intval($_GET['thread_id'] ?? 0);

if ($threadId === 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid thread ID.']);
    exit;
}

$comments = new Comments();
$response = $comments->fetchComments($threadId);

echo json_encode($response);
?>
