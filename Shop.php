<?php
include 'db_connect.php';
session_start();

$cust_id = $_SESSION['cust_id'];

if (!isset($cust_id)) {
    header('Location: login.php');
    exit();
}

// Handle sorting and filtering
$sort_order = 'ASC';  
$order_by = 'Title';  

if (isset($_GET['sort_by'])) {
    if ($_GET['sort_by'] == 'price_asc') {
        $order_by = 'UnitCost';
        $sort_order = 'ASC';
    } elseif ($_GET['sort_by'] == 'price_desc') {
        $order_by = 'UnitCost';
        $sort_order = 'DESC';
    } elseif ($_GET['sort_by'] == 'alphabetical') {
        $order_by = 'Title';
        $sort_order = 'ASC';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Shop Page</title>
  <link rel="stylesheet" href="css/shop.css">
  <style>
     body {
      background-image: url('image/about1.jpg');
      background-size: cover;
      background-repeat: no-repeat;
      background-position: center;
      background-attachment: fixed;
      color: rgba(10, 1, 1, 0.6);
    }
  </style>
</head>
<body>

<?php
$activemenu = 'shop'; 
$backgroundImage = "image/about-background.jpg";
include 'includes/header.php'; 
?>

<!-- Filter Section -->
<section class="filter_section">
  <form method="get" action="shop.php">
    <select name="sort_by" onchange="this.form.submit()">
      <option value="alphabetical" <?php echo ($order_by == 'Title') ? 'selected' : ''; ?>>Sort by Alphabetical Order</option>
      <option value="price_asc" <?php echo ($order_by == 'UnitCost' && $sort_order == 'ASC') ? 'selected' : ''; ?>>Sort by Price (Low to High)</option>
      <option value="price_desc" <?php echo ($order_by == 'UnitCost' && $sort_order == 'DESC') ? 'selected' : ''; ?>>Sort by Price (High to Low)</option>
    </select>
  </form>
</section>

<section class="products_cont">
    <div class="pro_box_cont">
      <?php
      $select_item = $conn->prepare("SELECT * FROM item WHERE Availability = 1 ORDER BY $order_by $sort_order");
      $select_item->execute();

      if ($select_item->rowCount() > 0) {
          while ($fetch_item = $select_item->fetch(PDO::FETCH_ASSOC)) {
      ?>
          <form class="pro_box add-to-cart-form" data-product='<?php echo json_encode([
            "item_id" => $fetch_item['ItemID'],
            "item_name" => $fetch_item['Title'],
            "item_price" => $fetch_item['UnitCost'],
            "item_image" => $fetch_item['image']
          ]); ?>'>
            <img src="./image/<?php echo $fetch_item['image']; ?>" alt="">
            <h3><?php echo htmlspecialchars($fetch_item['Title']); ?></h3>
            <p>Rs. <?php echo number_format($fetch_item['UnitCost'], 2); ?>/-</p>
            <input type="number" name="item_quantity" min="1" value="1">
            <input type="submit" value="Add to Cart" class="product_btn">
            <button type="button" onclick="fetchReviews('<?php echo htmlspecialchars($fetch_item['Title']); ?>')" class="review_btn">Show Reviews</button>
          </form>
      <?php
          }
      } else {
          echo '<p class="empty">No Products Available!</p>';
      }
      ?>
    </div>
</section>

<!-- Review Popup Modal -->
<div id="reviewModal">
  <div id="reviewModalContent">
    <button id="closeBtn" onclick="closeModal()">×</button>
    <h2 style="text-align:center; margin-bottom:15px;">Product Reviews</h2>
    <div id="reviewContent">
     
    </div>
  </div>
</div>

<!-- Cart Message Modal -->
<div id="cartMessageModal" style="display:none;">
  <div id="reviewModalContent">
    <button id="closeBtn" onclick="closeCartModal()">×</button>
    <h2 style="text-align:center; margin-bottom:15px;">Cart Status</h2>
    <div id="reviewContent" style="text-align: center;">
      <p id="cartMessageText"></p>
    </div>
  </div>
</div>

<script>
// AJAX: Add to Cart
document.querySelectorAll('.add-to-cart-form').forEach(form => {
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        const data = JSON.parse(this.dataset.product);
        const quantity = this.querySelector('input[name="item_quantity"]').value;

        const formData = new FormData();
        formData.append('add_to_cart', 1);
        formData.append('item_id', data.item_id);
        formData.append('item_name', data.item_name);
        formData.append('item_price', data.item_price);
        formData.append('item_quantity', quantity);
        formData.append('item_image', data.item_image);

        fetch('add_to_cart_ajax.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(response => {
            showCartMessage(response.message);
        })
        .catch(() => {
            showCartMessage('Error adding product to cart.');
        });
    });
});

// Review Modal Functions
function fetchReviews(title) {
    const modal = document.getElementById('reviewModal');
    const content = document.getElementById('reviewContent');
    content.innerHTML = "<p>Loading reviews...</p>";
    modal.style.display = 'flex';

    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'fetch_reviews.php', true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xhr.onload = function () {
        if (this.status === 200) {
            const response = JSON.parse(this.responseText);
            if (response.success) {
                const reviews = response.reviews;
                if (reviews.length === 0) {
                    content.innerHTML = "<p>No reviews available for this product.</p>";
                } else {
                    content.innerHTML = reviews.map(review => `
                        <div class="review-box">
                            <p><strong>Reviewer:</strong> ${review.Reviewer}</p>
                            <p><strong>Rating:</strong> ${'⭐'.repeat(review.Rating)} (${review.Rating}/5)</p>
                            <p><strong>Comment:</strong> ${review.Comment.replace(/\n/g, "<br>")}</p>
                            <p style="font-size: 12px; color: gray;"><strong>Date:</strong> ${review.Date} | ID: ${review.ReviewID}</p>
                        </div>
                    `).join('');
                }
            } else {
                content.innerHTML = `<p>Error: ${response.message}</p>`;
            }
        } else {
            content.innerHTML = "<p>Failed to load reviews.</p>";
        }
    };
    xhr.send('title=' + encodeURIComponent(title));
}

function closeModal() {
    document.getElementById('reviewModal').style.display = 'none';
}

// Cart Message Modal Functions
function showCartMessage(message) {
    const modal = document.getElementById('cartMessageModal');
    const messageText = document.getElementById('cartMessageText');
    messageText.textContent = message;
    modal.style.display = 'flex';
}

function closeCartModal() {
    document.getElementById('cartMessageModal').style.display = 'none';
}
</script>

</body>
</html>
