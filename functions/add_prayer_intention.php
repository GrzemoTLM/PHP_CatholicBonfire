<?php
require_once '../functions/check_session.php';
require_once '../classes/Intentions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['user_id'] ?? null;
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if (!$userId) {
        echo json_encode(['success' => false, 'message' => 'User not logged in.']);
        exit;
    }

    if (empty($title) || empty($description)) {
        echo json_encode(['success' => false, 'message' => 'Both title and description are required.']);
        exit;
    }

    $intentions = new Intentions();

    $response = $intentions->addIntention($userId, $title, $description);

    echo json_encode($response);
    exit;
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}
?>
