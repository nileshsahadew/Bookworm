<?php
session_start();
$activemenu = "review";
    $backgroundImage = "image/review-background.jpg";  
    include('includes/header.php');



include('db_connect.php');


$cust_name = '';
$prod_title = '';
$review_text = '';
$rating = 0;

// Check if data is received via GET request
if (isset($_GET['cust_name'], $_GET['prod_title'], $_GET['Comment'], $_GET['Rating'])) {
    // Capture data from the GET request
    $cust_name = htmlspecialchars($_GET['cust_name']);
    $prod_title = htmlspecialchars($_GET['prod_title']);
    $review_text = htmlspecialchars($_GET['Comment']);
    $rating = (int)$_GET['Rating'];
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bookworm - Review Submitted</title>
    <link rel="stylesheet" href="css/home.css"> 
    <style>
        @font-face {
            font-family: 'ShonenPunk';
            src: url('fonts/ShonenPunk.ttf') format('truetype');
        }
        body {
            font-family: "Comic Sans MS", cursive, sans-serif;  
            background-image: url('image/review.jpg');
            background-size: cover; 
            background-position: center; 
            background-repeat: no-repeat;            
            color: #fff;         
        }
    </style>
</head>
<body>


<div style="text-align: center; max-width: 600px; width: 90%; margin: auto;">
<h1>Your Review Submission</h1>
    <p>
        <strong>Name:</strong> <?php echo $cust_name; ?><br/>
        <strong>Product Title:</strong> <?php echo $prod_title; ?><br/>
        <strong>Rating:</strong> <?php echo $rating; ?>/5<br/>
        <strong>Review:</strong> <?php echo nl2br($review_text); ?><br/> 
    </p>
</div>

</body>
</html>
