<?php
require_once 'db.php';

class Profile
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getUserData($userId)
    {
        $sql = "SELECT username, email, profile_image_id FROM users WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getPostCount($userId)
    {
        $sql = "SELECT COUNT(*) as post_count FROM threads WHERE user_id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC)['post_count'];
    }

    public function getLikesGiven($userId)
    {
        $sql = "SELECT COUNT(*) as likes_given FROM intention_likes WHERE user_id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC)['likes_given'];
    }

    public function updateProfileImage($userId, $imageId)
    {
        $sql = "UPDATE users SET profile_image_id = :image_id WHERE id = :id";
        $stmt = $this->db->prepare($sql);

        try {
            $stmt->execute(['image_id' => $imageId, 'id' => $userId]);
            return ['success' => true];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
?>
