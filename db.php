<?php
class Database
{
    private $host = 'localhost'; // Lokalny host
    private $dbname = 'forumkatolickie'; // Nazwa lokalnej bazy danych
    private $username = 'root'; // Nazwa użytkownika bazy danych
    private $password = ''; // Hasło do bazy danych
    private $port = 3306; // Port, domyślnie 3306
    private $pdo;

    public function __construct()
    {
        try {
            $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->dbname};charset=utf8mb4";
            $this->pdo = new PDO($dsn, $this->username, $this->password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
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
            die("Query failed: " . $e->getMessage());
        }
    }

    public function prepare($sql)
    {
        try {
            return $this->pdo->prepare($sql);
        } catch (PDOException $e) {
            die("Prepare failed: " . $e->getMessage());
        }
    }
}

// Test połączenia
try {
    $db = new Database();
    $connection = $db->getConnection();

} catch (Exception $e) {
}
?>
