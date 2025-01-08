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

    // Pobierz komentarze dla każdego postu
    foreach ($posts as &$post) {
        $threadId = $post['thread_id'];

        $commentsSql = "SELECT 
                            comments.id AS comment_id, 
                            comments.content, 
                            comments.created_at, 
                            users.username 
                        FROM comments
                        JOIN users ON comments.user_id = users.id
                        WHERE comments.thread_id = :thread_id
                        ORDER BY comments.created_at ASC";

        $comments = $db->query($commentsSql, ['thread_id' => $threadId])->fetchAll();
        $post['comments'] = $comments;
    }

    echo json_encode(['success' => true, 'posts' => $posts]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
