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

    public function getAllIntentions()
    {
        $sql = "
            SELECT intentions.id, intentions.title, intentions.description, intentions.created_at,
                   users.username
            FROM intentions
            JOIN users ON intentions.user_id = users.id
            ORDER BY intentions.created_at DESC
        ";
        try {
            return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
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

    public function deleteIntention($intentionId)
    {
        try {
            $sql = "DELETE FROM intentions WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['id' => $intentionId]);

            $sqlLikes = "DELETE FROM intention_likes WHERE intention_id = :id";
            $stmt = $this->db->prepare($sqlLikes);
            $stmt->execute(['id' => $intentionId]);

            return ['success' => true, 'message' => 'Intention deleted successfully.'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Failed to delete intention: ' . $e->getMessage()];
        }
    }
}
?>
