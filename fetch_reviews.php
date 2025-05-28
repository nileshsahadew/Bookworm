<?php
include 'db_connect.php';

header('Content-Type: application/json');

$response = [];

if (isset($_POST['title'])) {
    $title = $_POST['title'];

    $fetch_reviews = $conn->prepare("SELECT * FROM review WHERE prod_title = ?");
    $fetch_reviews->execute([$title]);

    if ($fetch_reviews->rowCount() > 0) {
        $reviews = [];

        while ($review = $fetch_reviews->fetch(PDO::FETCH_ASSOC)) {
            $reviews[] = [
                'Reviewer' => $review['Cust_name'],
                'Rating' => (int)$review['Rating'],
                'Comment' => $review['Comment'],
                'Date' => $review['Rev_Date'],
                'ReviewID' => $review['Rev_ID']
            ];
        }

        $response = [
            'success' => true,
            'reviews' => $reviews
        ];
    } else {
        $response = [
            'success' => true,
            'reviews' => []
        ];
    }
} else {
    $response = [
        'success' => false,
        'message' => 'Invalid request. Product title missing.'
    ];
}

echo json_encode($response);
?>
