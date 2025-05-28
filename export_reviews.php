<?php
include 'db_connect.php';

function updateReviewsJson($conn) {
    $stmt = $conn->query("SELECT * FROM review");
    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $jsonData = json_encode($reviews, JSON_PRETTY_PRINT);
    file_put_contents('reviews_data.json', $jsonData);
    
    return ['success' => true, 'message' => 'Reviews exported successfully'];
}

header('Content-Type: application/json');
echo json_encode(updateReviewsJson($conn));
?>