<?php
require_once 'db.php';

class Session
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function checkSession()
    {
        session_start();

        if (!isset($_SESSION['session_id'])) {
            header('Location: login.html?error=Please log in.');
            exit();
        }

        try {
            $sql = "SELECT * FROM logged_in_users WHERE sessionId = :sessionId";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['sessionId' => $_SESSION['session_id']]);
            $session = $stmt->fetch();

            if (!$session) {
                $this->destroySession();
                header('Location: login.html?error=Session expired.');
                exit();
            }

            $this->updateSession($_SESSION['session_id']);
        } catch (PDOException $e) {
            die("Error verifying session: " . $e->getMessage());
        }
    }

    public function updateSession($sessionId)
    {
        $updateSql = "UPDATE logged_in_users SET lastUpdate = NOW() WHERE sessionId = :sessionId";
        $stmt = $this->db->prepare($updateSql);
        $stmt->execute(['sessionId' => $sessionId]);

        $timeout = 30 * 60;
        $expirationTime = date('Y-m-d H:i:s', time() - $timeout);

        $deleteSql = "DELETE FROM logged_in_users WHERE lastUpdate < :expirationTime";
        $stmt = $this->db->prepare($deleteSql);
        $stmt->execute(['expirationTime' => $expirationTime]);
    }

    public function destroySession()
    {
        session_unset();
        session_destroy();
    }
}
?>
