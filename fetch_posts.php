<?php
require_once 'db.php';

header('Content-Type: application/json');

try {
    $db = new Database();

    $sql = "SELECT 
                threads.id AS thread_id, 
                threads.title, 
                threads.content, 
                threads.created_at, 
                users.username, 
                users.profile_image_id, 
                categories.name AS category_name
            FROM threads
            JOIN users ON threads.user_id = users.id
            JOIN categories ON threads.category_id = categories.id
            ORDER BY threads.created_at DESC";

    $posts = $db->query($sql)->fetchAll();

    echo json_encode(['success' => true, 'posts' => $posts]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
