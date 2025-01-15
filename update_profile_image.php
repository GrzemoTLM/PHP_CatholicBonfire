<?php
session_start();
require_once 'Profile.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $imageId = intval($data['image_id']);
    $userId = $_SESSION['user_id'];

    if ($imageId < 1 || $imageId > 10) {
        echo json_encode(['success' => false, 'message' => 'Invalid image ID.']);
        exit;
    }

    $profile = new Profile();

    try {
        $response = $profile->updateProfileImage($userId, $imageId);

        if ($response['success']) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => $response['message']]);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
