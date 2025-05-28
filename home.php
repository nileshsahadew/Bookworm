<?php
session_start();
$activemenu = "home"; 
$backgroundImage = "image/home-background.jpg"; 
include('includes/header.php'); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BOOKWORM</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background-image: url('image/image.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;           
        }

        /* Hero Section */
        .hero {
            height: 400px;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        .hero .content {
            background-color: rgba(0, 0, 0, 0.5); 
            padding: 20px;
            border-radius: 10px; 
        }

        .hero h1 {
            font-size: 50px;
            text-transform: uppercase;
            margin-bottom: 10px;
            color: white; 
        }

        .hero p {
            font-size: 20px;
            margin-bottom: 20px;
            color: white; 
        }

        .hero button {
            background-color: #00b4d8;
            color: white;
            border: black;
            padding: 10px 20px;
            cursor: pointer;
            font-size: 18px;
            border-radius: 5px;
        }

        .hero button:hover {
            background-color: #0077b6;
        }

        /* Products Section */
        .books-section {
            padding: 50px;
            display: flex;
            justify-content: space-around;
        }

        .book-card {
            background-color: lightgrey;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            transition: transform 0.3s ease;
        }

        .book-card img {
            width: 150px;
            height: 150px;
            border-radius: 10px;
        }

        .book-card h3 {
            margin-top: 15px;
            font-size: 18px;
            color: #04395e;
        }

        .book-card span {
            background-color: #ff4d4d;
            color: pink;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 10px;
        }

        .book-card:hover {
            transform: scale(1.05); 
        }

        
        a {
            text-decoration: none;
            color: inherit;
        }
    </style>
</head>
<body>

    <!-- Hero Section -->
    <div class="hero">
        <div class="content">
            <h1>BOOKWORM</h1>
            <p>Explore, Discover, and Buy Your Favorite Books</p>
            <button onclick="window.location.href='Shop.php'">Discover More</button>
        </div>
    </div>

    <!-- Products Section -->
    <div class="books-section">
        <a href="Shop.php?product=48laws" class="book-card">
            <span>Rs 650</span>
            <img src="image/The-48-Laws-of-Power.jpg" alt="The 48 Laws of Power">
            <h3>The 48 Laws of Power</h3>
        </a>

        <a href="Shop.php?product=darkpsychology" class="book-card">
            <span>Rs. 600/-</span>
            <img src="image/Dark Psychology.jpg" alt="Dark Psychology"> 
            <h3>Dark Psychology</h3>
        </a>
        
        <a href="Shop.php?product=manipulation" class="book-card">
            <span>Rs. 450/-</span>
            <img src="image/Manipulation.jpg" alt="Manipulation"> 
            <h3>Manipulation</h3>
        </a>
    </div>

    <?php include('includes/footer.php'); ?>
</body>
</html>
