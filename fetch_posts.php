<?php
require_once 'db.php';

header('Content-Type: application/json');

try {
    $db = new Database();

    $sql = "
        SELECT 
            threads.id AS thread_id,
            threads.title,
            threads.content,
            threads.created_at AS thread_created_at,
            users.username AS thread_username,
            users.profile_image_id AS thread_profile_image,
            categories.name AS category_name,
            comments.content AS comment_content,
            comments.created_at AS comment_created_at,
            comment_users.username AS comment_username,
            comment_users.profile_image_id AS comment_profile_image
        FROM threads
        JOIN users ON threads.user_id = users.id
        JOIN categories ON threads.category_id = categories.id
        LEFT JOIN comments ON threads.id = comments.thread_id
        LEFT JOIN users AS comment_users ON comments.user_id = comment_users.id
        ORDER BY threads.created_at DESC, comments.created_at ASC
    ";

    $rows = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);

    $posts = [];
    foreach ($rows as $row) {
        $threadId = $row['thread_id'];

        if (!isset($posts[$threadId])) {
            $posts[$threadId] = [
                'thread_id' => $row['thread_id'],
                'title' => $row['title'],
                'content' => $row['content'],
                'created_at' => $row['thread_created_at'],
                'username' => $row['thread_username'],
                'profile_image_id' => $row['thread_profile_image'],
                'category_name' => $row['category_name'],
                'comments' => []
            ];
        }

        if (!empty($row['comment_content'])) {
            $posts[$threadId]['comments'][] = [
                'content' => $row['comment_content'],
                'created_at' => $row['comment_created_at'],
                'username' => $row['comment_username'],
                'profile_image_id' => $row['comment_profile_image']
            ];
        }
    }

    echo json_encode(['success' => true, 'posts' => array_values($posts)]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
