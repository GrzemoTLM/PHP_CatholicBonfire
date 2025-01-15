<?php
require_once 'db.php';

class Threads
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function createPost($userId, $categoryId, $title, $content)
    {
        if (empty($title) || empty($content)) {
            return ['success' => false, 'message' => 'Title and content are required.'];
        }

        $sql = "INSERT INTO threads (user_id, category_id, title, content, created_at) 
                VALUES (:userId, :categoryId, :title, :content, NOW())";
        $stmt = $this->db->prepare($sql);

        try {
            $stmt->execute([
                'userId' => $userId,
                'categoryId' => $categoryId,
                'title' => $title,
                'content' => $content
            ]);

            return ['success' => true, 'message' => 'Post created successfully.'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function fetchPosts()
    {
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

        try {
            $rows = $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);

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

            return ['success' => true, 'posts' => array_values($posts)];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function editPost($postId, $userId, $title, $content)
    {
        if (empty($title) || empty($content)) {
            return ['success' => false, 'message' => 'Title and content are required.'];
        }

        $sql = "SELECT id FROM threads WHERE id = :id AND user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'id' => $postId,
            'user_id' => $userId
        ]);

        if ($stmt->rowCount() === 0) {
            return ['success' => false, 'message' => 'Post not found or not authorized.'];
        }

        $updateSql = "UPDATE threads SET title = :title, content = :content WHERE id = :id";
        $updateStmt = $this->db->prepare($updateSql);

        try {
            $updateStmt->execute([
                'title' => $title,
                'content' => $content,
                'id' => $postId
            ]);

            return ['success' => true, 'message' => 'Post updated successfully.'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function deletePost($postId, $userId, $isAdmin = false)
    {
        $condition = $isAdmin ? '' : "AND user_id = :user_id";

        $sql = "DELETE FROM threads WHERE id = :id $condition";
        $stmt = $this->db->prepare($sql);

        try {
            $params = ['id' => $postId];
            if (!$isAdmin) {
                $params['user_id'] = $userId;
            }

            $stmt->execute($params);

            if ($stmt->rowCount() > 0) {
                return ['success' => true, 'message' => 'Post successfully deleted.'];
            } else {
                return ['success' => false, 'message' => 'Post not found or not authorized.'];
            }
        } catch (PDOException $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
?>
