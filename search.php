<?php
include 'db_connect.php';
require_once 'vendor/autoload.php';

use Opis\JsonSchema\Validator;

session_start();

$cust_id = $_SESSION['cust_id'];

if (!isset($cust_id)) {
    header('location:login.php');
    exit;
}

$message_text = '';
if (isset($_POST['add_to_cart'])) {
    $data = [
        'item_id' => $_POST['item_id'],
        'item_name' => $_POST['item_name'],
        'item_price' => floatval($_POST['item_price']),
        'item_image' => $_POST['item_image'],
        'item_quantity' => intval($_POST['item_quantity']),
    ];

    // Load and decode schema from local JSON file
    $schemaPath = __DIR__ . '/add_to_cart.schema.json';
    $schemaData = json_decode(file_get_contents($schemaPath));

    $validator = new Validator();
    $result = $validator->validate((object)$data, $schemaData);

    if (!$result->isValid()) {
        $message_text = '<div class="popup-message error" id="popupMessage">
                            Invalid product data!
                            <button class="close-btn" onclick="closePopup()">×</button>
                         </div>';
    } else {
        // Check for duplicate in cart
        $stmt = $conn->prepare("SELECT * FROM cart WHERE Title = :item_name AND CustID = :cust_id");
        $stmt->execute(['item_name' => $data['item_name'], 'cust_id' => $cust_id]);

        if ($stmt->rowCount() > 0) {
            $message_text = '<div class="popup-message error" id="popupMessage">
                                Item already added to cart!
                                <button class="close-btn" onclick="closePopup()">×</button>
                             </div>';
        } else {
            $stmt = $conn->prepare("INSERT INTO cart(CustId, title, unitcost, quantity, image) 
                                    VALUES(:cust_id, :name, :price, :quantity, :image)");
            $stmt->execute([
                'cust_id' => $cust_id,
                'name' => $data['item_name'],
                'price' => $data['item_price'],
                'quantity' => $data['item_quantity'],
                'image' => $data['item_image']
            ]);

            $message_text = '<div class="popup-message" id="popupMessage">
                                Item added to cart!
                                <button class="close-btn" onclick="closePopup()">×</button>
                             </div>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Search Products</title>
    <link rel="stylesheet" href="search.css">
</head>

<?php 
$activemenu = 'search'; 
$backgroundImage = "image/search.jpg"; 
include 'includes/header.php';
?>

<body>
<section class="search_cont">
    <form action="" method="post">
        <input type="text" name="search" id="search" placeholder="Search products..." onkeyup="fetchSuggestions()">

        <label for="min_budget">Min Budget:</label>
        <select name="min_budget">
            <option value="0">0</option>
            <option value="100">100</option>
            <option value="500">500</option>
            <option value="1000">1000</option>
            <option value="2000">2000</option>
        </select>

        <label for="max_budget">Max Budget:</label>
        <select name="max_budget">
            <option value="500">500</option>
            <option value="1000">1000</option>
            <option value="2000">2000</option>
            <option value="5000">5000</option>
            <option value="10000">10000</option>
        </select>

        <input type="submit" name="submit" value="Search">
    </form>

    <?php echo $message_text; ?>

    <div id="suggestions" class="autocomplete-suggestions"></div>
</section>

<section class="products_cont">
    <div class="pro_box_cont">
        <?php
        if (isset($_POST['submit'])) {
            $search_term = $_POST['search'];
            $min_budget = $_POST['min_budget'];
            $max_budget = $_POST['max_budget'];

            if (empty($min_budget) || $min_budget < 0) $min_budget = 0;
            if (empty($max_budget) || $max_budget < $min_budget) $max_budget = 10000;

            $stmt = $conn->prepare("SELECT * FROM item WHERE title LIKE :search_term AND UnitCost BETWEEN :min_budget AND :max_budget");
            $stmt->execute([
                'search_term' => "%$search_term%",
                'min_budget' => $min_budget,
                'max_budget' => $max_budget
            ]);

            if ($stmt->rowCount() > 0) {
                while ($item = $stmt->fetch(PDO::FETCH_ASSOC)) {
        ?>
                    <form action="" method="post" class="pro_box">
                        <img src="./image/<?php echo $item['image']; ?>" alt="Product Image">
                        <h3><?php echo $item['Title']; ?></h3>
                        <p>Price: Rs. <?php echo $item['UnitCost']; ?>/-</p>
                        <input type="hidden" name="item_id" value="<?php echo $item['ItemID']; ?>">
                        <input type="hidden" name="item_name" value="<?php echo $item['Title']; ?>">
                        <input type="hidden" name="item_price" value="<?php echo $item['UnitCost']; ?>">
                        <input type="hidden" name="item_image" value="<?php echo $item['image']; ?>">
                        <input type="number" name="item_quantity" min="1" value="1">
                        <input type="submit" name="add_to_cart" value="Add to Cart" class="product_btn">
                    </form>
        <?php
                }
            } else {
                echo '<p>No products found within your budget!</p>';
            }
        }
        ?>
    </div>
</section>

<script>
    function fetchSuggestions() {
        let searchTerm = document.getElementById('search').value;
        let minBudget = document.querySelector('select[name="min_budget"]').value;
        let maxBudget = document.querySelector('select[name="max_budget"]').value;

        if (searchTerm.length < 1) {
            document.getElementById('suggestions').style.display = 'none';
            return;
        }

        let xhr = new XMLHttpRequest();
        xhr.open("GET", "fetch_suggestions.php?search=" + encodeURIComponent(searchTerm) + "&min_budget=" + minBudget + "&max_budget=" + maxBudget, true);
        xhr.onload = function() {
            let suggestionsDiv = document.getElementById('suggestions');
            if (xhr.status === 200) {
                suggestionsDiv.innerHTML = xhr.responseText;
                suggestionsDiv.style.display = xhr.responseText.trim() !== '' ? 'block' : 'none';

                let suggestions = suggestionsDiv.querySelectorAll('.autocomplete-suggestion');
                suggestions.forEach(function(suggestion) {
                    suggestion.addEventListener('click', function() {
                        let itemName = this.getAttribute('data-name');
                        selectItem(itemName);
                    });
                });
            }
        };
        xhr.send();
    }

    function selectItem(itemName) {
        document.getElementById('search').value = itemName;
        document.getElementById('suggestions').style.display = 'none';
    }

    document.addEventListener('DOMContentLoaded', function () {
        const closeButtons = document.querySelectorAll('.popup-message .close-btn');
        closeButtons.forEach(function(button) {
            button.addEventListener('click', function() {
                const popup = this.closest('.popup-message');
                popup.style.display = 'none';
            });
        });
    });
</script>

</body>
</html>













