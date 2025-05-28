<?php
require_once("BookRestHandler.php");

$method = $_SERVER['REQUEST_METHOD'];
$resource = isset($_GET['resource']) ? $_GET['resource'] : "";
$page_key = isset($_GET['page_key']) ? $_GET['page_key'] : "";
$id = isset($_GET['id']) ? $_GET['id'] : null;

switch($resource) {
    case "book":
        $handler = new BookRestHandler();

        switch($page_key) {
            case "list":
                $handler->getAllBooks();
                break;
            case "create":
                $handler->addBook();
                break;
            case "update":
                if ($id) {
                    $handler->updateBook($id);
                }
                break;
            case "delete":
                if ($id) {
                    $handler->deleteBook($id);
                }
                break;
            default:
                header("HTTP/1.1 400 Bad Request");
                echo json_encode(["error" => "Invalid page_key"]);
                break;
        }
        break;
    default:
        header("HTTP/1.1 404 Not Found");
        echo json_encode(["error" => "Resource not found"]);
        break;
}
?>
