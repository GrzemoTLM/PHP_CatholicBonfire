<?php
require_once '../functions/check_session.php';
require_once '../classes/Intentions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

$userId = $_SESSION['user_id'] ?? null;
$intentionId = intval($_POST['intention_id'] ?? 0);

if (!$userId) {
    echo json_encode(['success' => false, 'message' => 'User not logged in.']);
    exit;
}

if ($intentionId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid intention ID.']);
    exit;
}

$intentions = new Intentions();

$response = $intentions->offerPrayer($userId, $intentionId);

echo json_encode($response);
