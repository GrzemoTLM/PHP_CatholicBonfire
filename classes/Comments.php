<?php
require_once 'db.php';

class Comments
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function addComment($threadId, $userId, $content)
    {
        if (empty($content)) {
            return ['success' => false, 'message' => 'Comment content cannot be empty.'];
        }

        $sql = "INSERT INTO comments (thread_id, user_id, content, created_at) VALUES (:threadId, :userId, :content, NOW())";
        $stmt = $this->db->prepare($sql);

        try {
            $stmt->execute([
                'threadId' => $threadId,
                'userId' => $userId,
                'content' => $content
            ]);

            return ['success' => true, 'message' => 'Comment added successfully.'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function fetchComments($threadId)
    {
        $sql = "
            SELECT 
                comments.id AS comment_id,
                comments.content,
                comments.created_at,
                users.username,
                users.profile_image_id
            FROM comments
            JOIN users ON comments.user_id = users.id
            WHERE comments.thread_id = :threadId
            ORDER BY comments.created_at ASC
        ";

        $stmt = $this->db->prepare($sql);

        try {
            $stmt->execute(['threadId' => $threadId]);
            return ['success' => true, 'comments' => $stmt->fetchAll(PDO::FETCH_ASSOC)];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function deleteComment($commentId, $userId, $isAdmin = false)
    {
        $condition = $isAdmin ? '' : "AND user_id = :user_id";

        $sql = "DELETE FROM comments WHERE id = :commentId $condition";
        $stmt = $this->db->prepare($sql);

        try {
            $params = ['commentId' => $commentId];
            if (!$isAdmin) {
                $params['user_id'] = $userId;
            }

            $stmt->execute($params);

            if ($stmt->rowCount() > 0) {
                return ['success' => true, 'message' => 'Comment deleted successfully.'];
            } else {
                return ['success' => false, 'message' => 'Comment not found or not authorized.'];
            }
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Failed to delete comment: ' . $e->getMessage()];
        }
    }

}
?>
