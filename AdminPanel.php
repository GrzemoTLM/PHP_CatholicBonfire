<?php
require_once 'db.php';
require_once 'Threads.php';
require_once 'Comments.php';

class AdminPanel
{
    private $db;
    private $threads;
    private $comments;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
        $this->threads = new Threads();
        $this->comments = new Comments();
    }

    public function getLoggedUsers()
    {
        $sql = "
            SELECT users.id AS user_id, users.username, users.email, users.role, 
                   logged_in_users.lastUpdate, logged_in_users.sessionId
            FROM logged_in_users
            JOIN users ON logged_in_users.userId = users.id
            ORDER BY logged_in_users.lastUpdate DESC
        ";
        try {
            return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function getAllPosts()
    {
        $postsResponse = $this->threads->fetchPosts();
        return $postsResponse['success'] ? $postsResponse['posts'] : [];
    }

    public function getAllComments()
    {
        $comments = [];
        $posts = $this->getAllPosts();

        foreach ($posts as $post) {
            $response = $this->comments->fetchComments($post['thread_id']);
            if ($response['success']) {
                $comments = array_merge($comments, $response['comments']);
            }
        }

        return $comments;
    }

    public function changeUserRole($userId, $newRole)
    {
        $validRoles = ['user', 'admin'];

        if (!in_array($newRole, $validRoles)) {
            return ['success' => false, 'message' => 'Invalid role.'];
        }

        $sql = "UPDATE users SET role = :role WHERE id = :id";
        $stmt = $this->db->prepare($sql);

        try {
            $stmt->execute(['role' => $newRole, 'id' => $userId]);
            return ['success' => true, 'message' => 'User role updated successfully.'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Failed to update user role: ' . $e->getMessage()];
        }
    }
}
?>
