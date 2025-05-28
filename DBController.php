<?php
class DBController {
    private $conn = "";
    private $host = "localhost";
    private $user = "root"; 
    private $password = "";
    private $database = "bookworm"; 

    public function __construct() {
        $this->conn = $this->connectDB();
    }

    public function connectDB() {
        try {
            $conn = new PDO("mysql:host={$this->host};dbname={$this->database}", $this->user, $this->password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conn;
        } catch (PDOException $e) {
            die("Database Connection Failed: " . $e->getMessage());
        }
    }

    public function executeQuery($query, $data = []) {
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute($data);
            return $stmt->rowCount();
        } catch (PDOException $e) {
            die("Query Failed: " . $e->getMessage());
        }
    }

    public function executeSelectQuery($query, $data = []) {
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute($data);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Select Query Failed: " . $e->getMessage());
        }
    }
}
?>
