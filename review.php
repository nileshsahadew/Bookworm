<?php
session_start();

// Redirect if user not logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !isset($_SESSION['cust_id'])) {
    header("Location: login.php");
    exit;
}

$activemenu = "review"; 
$backgroundImage = "image/review-background.jpg"; 
include('includes/header.php'); 
include('db_connect.php'); 

$cust_name = $_SESSION['cust_name'] ?? '';
$prod_title = '';
$review_text = '';
$rating = 0;

// Fetch products (optional for frontend usage)
try {
    $sql = "SELECT ItemID, Title FROM item";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error fetching products: " . $e->getMessage();
}

// Handle review submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cust_name   = htmlspecialchars($_POST['cust_name']);
    $prod_title  = htmlspecialchars($_POST['prod_title']);
    $review_text = htmlspecialchars($_POST['Comment']);
    $rating      = (int)$_POST['Rating'];
    
    $rev_date = date('Y-m-d H:i:s');
    $cust_id  = $_SESSION['cust_id'];

    try {
        $sql = "INSERT INTO review (Rev_Date, prod_title, Comment, Rating, Cust_ID, Cust_name) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$rev_date, $prod_title, $review_text, $rating, $cust_id, $cust_name]);

        // Save to JSON
        $reviewData = [
            'Rev_Date'   => $rev_date,
            'prod_title' => $prod_title,
            'Comment'    => $review_text,
            'Rating'     => $rating,
            'Cust_ID'    => $cust_id,
            'Cust_name'  => $cust_name
        ];

        $filename = 'review_data.json';
        $existingData = [];

        if (file_exists($filename)) {
            $existingData = json_decode(file_get_contents($filename), true);
            if (!is_array($existingData)) $existingData = [];
        }

        $existingData[] = $reviewData;
        file_put_contents($filename, json_encode($existingData, JSON_PRETTY_PRINT));

        header("Location: review_pro.php?cust_name=" . urlencode($cust_name) . 
               "&prod_title=" . urlencode($prod_title) . 
               "&Comment=" . urlencode($review_text) . 
               "&Rating=" . $rating);
        exit;

    } catch (PDOException $e) {
        echo "Error: Could not submit your review. " . $e->getMessage();
    }
}

$conn = null;
?>
<!-- HTML starts here -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bookworm - Submit Review</title>
  <link rel="stylesheet" href="css/home.css"> 
  <style>
    @font-face {
      font-family: 'ShonenPunk';
      src: url('fonts/ShonenPunk.ttf') format('truetype');
    }
    body {
      font-family: 'Comic Sans MS', cursive, sans-serif;
      background-image: url('image/review.jpg');
      background-size: cover;
      background-position: center;
      background-attachment: fixed;
      color: #fff;
    }
    form {
      background: rgba(0,0,0,0.8);
      padding: 15px;
      border-radius: 15px;
      max-width: 500px;
      margin: 0 auto;
    }
    h1 {
      font-family: 'ShonenPunk', sans-serif; 
      color: #f39c12;
      text-shadow: 2px 2px #000;
      text-align: center;
    }
    input, textarea, select {
      width: 90%;
      padding: 8px;
      margin: 10px auto;
      border: 2px solid #f39c12;
      border-radius: 10px;
      background: rgba(255,255,255,0.9);
      color: #000;
      display: block;
    }
    input[type="submit"] {
      background-color: #e74c3c;
      color: white;
      border: none;
      cursor: pointer;
      width: 50%;
      padding: 10px;
      border-radius: 20px;
      margin: 20px auto;
    }
    input[type="submit"]:hover {
      background-color: #c0392b;
    }
    #suggestions {
      position: absolute;
      background-color: rgba(0, 0, 0, 0.6);
      color: #fff;
      border-radius: 8px;
      margin-top: 2px;
      max-height: 150px;
      overflow-y: auto;
      width: 90%;
      left: 50%;
      transform: translateX(-50%);
      z-index: 999;
    }
    .autocomplete-suggestion {
      padding: 8px 12px;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }
    .autocomplete-suggestion:hover {
      background-color: rgba(42, 40, 40, 0.2);
    }
    .autocomplete-suggestion img {
  width: 40px;      /* Adjust as needed */
  height: auto;
  vertical-align: middle;
  margin-right: 8px;
  border-radius: 4px;
}

  </style>
</head>
<body>
<h1>Bookworm - Submit Your Review</h1>
<form method="post" action="review.php">
  <label for="cust_name">Your Name:</label>
  <input type="text" id="cust_name" name="cust_name" value="<?php echo htmlspecialchars($cust_name); ?>" readonly>

  <label for="prod_title">Product Title:</label>
  <div style="position: relative;">
    <input type="text" id="prod_title" name="prod_title" value="<?php echo htmlspecialchars($prod_title); ?>" autocomplete="off" required>
    <div id="suggestions"></div>
  </div>

  <label for="review_text">Your Review:</label>
  <textarea id="review_text" name="Comment" rows="4" required><?php echo htmlspecialchars($review_text); ?></textarea>

  <label for="rating">Rating (1 to 5):</label>
  <select id="rating" name="Rating" required>
    <?php
    for ($i = 1; $i <= 5; $i++) {
        echo "<option value='$i'" . ($rating == $i ? ' selected' : '') . ">$i</option>";
    }
    ?>
  </select>

  <input type="submit" value="Submit Review">
</form>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const input = document.getElementById('prod_title');
  const suggestionsBox = document.getElementById('suggestions');

  input.addEventListener('input', function () {
    const query = input.value.trim();
    if (query.length === 0) {
      suggestionsBox.innerHTML = '';
      return;
    }

    fetch(`fetch_suggestions.php?search=${encodeURIComponent(query)}`)
      .then(response => response.text())
      .then(data => {
        suggestionsBox.innerHTML = data;
        attachSuggestionClicks();
      })
      .catch(console.error);
  });

  function attachSuggestionClicks() {
    document.querySelectorAll('.autocomplete-suggestion').forEach(suggestion => {
      suggestion.addEventListener('click', () => {
        input.value = suggestion.getAttribute('data-name');
        suggestionsBox.innerHTML = '';
      });
    });
  }

  document.addEventListener('click', (e) => {
    if (!suggestionsBox.contains(e.target) && e.target !== input) {
      suggestionsBox.innerHTML = '';
    }
  });
});
</script>
</body>
</html>



