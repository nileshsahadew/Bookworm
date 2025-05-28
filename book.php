<?php
require_once("DBController.php");

class Book {
    private $db;

    public function __construct() {
        $this->db = new DBController();
    }

    public function getAllBooks() {
        $query = "SELECT * FROM item WHERE Availability = 1";
        return $this->db->executeSelectQuery($query);
    }

    public function getBookById($id) {
        $query = "SELECT * FROM item WHERE ItemID = ?";
        return $this->db->executeSelectQuery($query, [$id]);
    }

    public function addBook($data) {
        $query = "INSERT INTO item (Title, UnitCost, image, Availability) VALUES (?, ?, ?, 1)";
        $params = [
            $data['title'],
            $data['price'],
            $data['image']
        ];
        $result = $this->db->executeQuery($query, $params);
        return $result ? ['success' => 1] : ['success' => 0];
    }

    public function updateBook($id, $data) {
        $query = "UPDATE item SET Title = ?, UnitCost = ?, image = ? WHERE ItemID = ?";
        $params = [
            $data['title'],
            $data['price'],
            $data['image'],
            $id
        ];
        $result = $this->db->executeQuery($query, $params);
        return $result ? ['success' => 1] : ['success' => 0];
    }

    public function deleteBook($id) {
        $query = "DELETE FROM item WHERE ItemID = ?";
        $result = $this->db->executeQuery($query, [$id]);
        return $result ? ['success' => 1] : ['success' => 0];
    }
    public function editBook() {
        if (isset($_GET['id']) && isset($_POST['title'])) {
            $id = $_GET['id'];
            $title = $_POST['title'];
            $price = $_POST['price'];
            $image = $_POST['image'];
    
            $query = "UPDATE item SET Title = ?, UnitCost = ?, image = ? WHERE ItemID = ?";
            $data = [$title, $price, $image, $id];
            $dbcontroller = new DBController();
            $result = $dbcontroller->executeQuery($query, $data);
    
            if ($result != 0) {
                return ['success' => 1];
            }
        }
    }
    
}
?>
