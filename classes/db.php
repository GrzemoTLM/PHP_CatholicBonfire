<?php
class Database
{
    private $host;
    private $dbname;
    private $username;
    private $password;
    private $port;
    private $pdo;
    private static $instance = null;

    private function __construct()
    {
        $this->host = getenv('DB_HOST') ?: 'localhost';
        $this->dbname = getenv('DB_NAME') ?: 'forumkatolickie';
        $this->username = getenv('DB_USER') ?: 'root';
        $this->password = getenv('DB_PASSWORD') ?: '';
        $this->port = getenv('DB_PORT') ?: 3306;

        try {
            $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->dbname};charset=utf8mb4";
            $this->pdo = new PDO($dsn, $this->username, $this->password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage(), 3, __DIR__ . '/error.log');
            die("Something went wrong. Please try again later.");
        }
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection()
    {
        return $this->pdo;
    }

    public function query($sql, $params = [])
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            error_log("Query failed: " . $e->getMessage(), 3, __DIR__ . '/error.log');
            return false;
        }
    }

    public function prepare($sql)
    {
        try {
            return $this->pdo->prepare($sql);
        } catch (PDOException $e) {
            error_log("Prepare failed: " . $e->getMessage(), 3, __DIR__ . '/error.log');
            return false;
        }
    }
}

try {
    $db = Database::getInstance();
    $connection = $db->getConnection();
} catch (Exception $e) {
    error_log("Error initializing database: " . $e->getMessage(), 3, __DIR__ . '/error.log');
    die("Initialization failed. Please check logs.");
}
?>
