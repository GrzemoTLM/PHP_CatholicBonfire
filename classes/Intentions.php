<?php

class Intentions
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAllIntentions($status = null)
    {
        try {
            $sql = "
            SELECT intentions.id, intentions.title, intentions.description, intentions.created_at,
                   IFNULL(intentions.likes_count, 0) AS likes_count, users.username
            FROM intentions
            JOIN users ON intentions.user_id = users.id
        ";

            if ($status) {
                $sql .= " WHERE intentions.status = :status";
            }

            $sql .= " ORDER BY intentions.created_at DESC";

            $stmt = $this->db->prepare($sql);

            if ($status) {
                $stmt->execute(['status' => $status]);
            } else {
                $stmt->execute();
            }

            return ['success' => true, 'intentions' => $stmt->fetchAll()];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }


    public function addIntention($userId, $title, $description)
    {
        try {
            $sql = "INSERT INTO intentions (user_id, title, description, status) VALUES (:user_id, :title, :description, 'pending')";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'user_id' => $userId,
                'title' => $title,
                'description' => $description
            ]);
            return ['success' => true, 'message' => 'Prayer intention added successfully.'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }

    public function offerPrayer($userId, $intentionId)
    {
        try {
            $checkSql = "
            SELECT COUNT(*) 
            FROM intention_likes 
            WHERE user_id = :user_id AND intention_id = :intention_id
        ";
            $stmt = $this->db->prepare($checkSql);
            $stmt->execute([
                'user_id' => $userId,
                'intention_id' => $intentionId
            ]);
            $alreadyLiked = $stmt->fetchColumn() > 0;

            if ($alreadyLiked) {
                return ['success' => false, 'message' => 'You have already offered a prayer for this intention.'];
            }

            $insertSql = "
            INSERT INTO intention_likes (user_id, intention_id, created_at) 
            VALUES (:user_id, :intention_id, NOW())
        ";
            $stmt = $this->db->prepare($insertSql);
            $stmt->execute([
                'user_id' => $userId,
                'intention_id' => $intentionId
            ]);

            $updateSql = "
            UPDATE intentions 
            SET likes_count = IFNULL(likes_count, 0) + 1 
            WHERE id = :intention_id
        ";
            $stmt = $this->db->prepare($updateSql);
            $stmt->execute(['intention_id' => $intentionId]);

            return ['success' => true, 'message' => 'Prayer offered successfully.'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }


    public function deleteIntention($intentionId, $isAdmin)
    {
        if (!$isAdmin) {
            return ['success' => false, 'message' => 'Unauthorized access.'];
        }

        try {
            $sql = "DELETE FROM intentions WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['id' => $intentionId]);

            if ($stmt->rowCount() > 0) {
                return ['success' => true, 'message' => 'Prayer intention deleted successfully.'];
            } else {
                return ['success' => false, 'message' => 'Prayer intention not found.'];
            }
        } catch (PDOException $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
