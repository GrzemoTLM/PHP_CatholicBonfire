<?php
session_start();
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $imageId = intval($data['image_id']);
    $userId = $_SESSION['user_id'];

    if ($imageId < 1 || $imageId > 10) {
        echo json_encode(['success' => false, 'message' => 'Invalid image ID.']);
        exit;
    }

    $db = new Database();

    try {
        $sql = "UPDATE users SET profile_image_id = :imageId WHERE id = :userId";
        $db->query($sql, [
            'imageId' => $imageId,
            'userId' => $userId
        ]);

        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
?>
