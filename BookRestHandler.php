<?php
require_once("core/SimpleRest.php");
require_once("Book.php");

class BookRestHandler extends SimpleRest {

    function getAllBooks() {
        $book = new Book();
        $rawData = $book->getAllBooks();

        $statusCode = empty($rawData) ? 404 : 200;
        $this->setHttpHeaders("application/json", $statusCode);
        echo $this->encodeJson(['books' => $rawData]);
    }

    function addBook() {
        $data = [
            'title' => $_POST['title'],
            'price' => $_POST['price'],
            'image' => $_POST['image']
        ];

        $book = new Book();
        $rawData = $book->addBook($data);

        $this->setHttpHeaders("application/json", 200);
        echo $this->encodeJson($rawData);
    }

    function updateBook($id) {
        $data = [
            'title' => $_POST['title'],
            'price' => $_POST['price'],
            'image' => $_POST['image']
        ];

        $book = new Book();
        $rawData = $book->updateBook($id, $data);

        if ($rawData['success'] == 1) {
            header("Location: /bookForm.php?msg=updated");
            exit();
        } else {
            $this->setHttpHeaders("application/json", 400);
            echo $this->encodeJson($rawData);
        }
        
    }

    function deleteBook($id) {
        $book = new Book();
        $rawData = $book->deleteBook($id);

        $this->setHttpHeaders("application/json", 200);
        echo $this->encodeJson($rawData);
    }
    function editBookById() {
        $book = new Book();
        $rawData = $book->editBook(); 
        
    }
    
    

    private function encodeJson($responseData) {
        return json_encode($responseData);
    }
}
?>
