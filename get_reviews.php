<?php
include 'db_connect.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // Optional: CORS

$response = [];

if (isset($_GET['title'])) {
    $title = trim($_GET['title']);

    if ($title === '') {
        echo json_encode(['success' => false, 'message' => 'Product title is empty.']);
        exit;
    }

    try {
        $stmt = $conn->prepare("SELECT * FROM review WHERE prod_title = ?");
        $stmt->execute([$title]);

        $reviews = [];

        while ($review = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $reviews[] = [
                'Reviewer' => $review['Cust_name'],
                'Rating'   => (int)$review['Rating'],
                'Comment'  => $review['Comment'],
                'Date'     => $review['Rev_Date'],
                'ReviewID' => $review['Rev_ID']
            ];
        }

        echo json_encode(['success' => true, 'reviews' => $reviews]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'DB Error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Missing product title.']);
}
?>
