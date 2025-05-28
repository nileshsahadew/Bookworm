<?php
session_start();

if (!isset($_SESSION['cust_id'])) {
    header('Location: login.php'); 
    exit();
}


require_once 'db_connect.php';


$user_id = $_SESSION['cust_id'];

if (isset($_POST['update_cart'])) {
    $cart_id = $_POST['cart_id'];
    $cart_quantity = $_POST['cart_quantity'];

   
    $update_query = $conn->prepare("UPDATE `cart` SET Quantity = :quantity WHERE CartID = :cart_id AND CustID = :user_id");
    $update_query->execute([
        ':quantity' => $cart_quantity,
        ':cart_id' => $cart_id,
        ':user_id' => $user_id
    ]);

    $message = 'Cart quantity updated successfully!';
}


if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];

  
    $delete_query = $conn->prepare("DELETE FROM `cart` WHERE CartID = :cart_id AND CustID = :user_id");
    $delete_query->execute([
        ':cart_id' => $delete_id,
        ':user_id' => $user_id
    ]);

    header('Location: cart.php'); 
    exit();
}


if (isset($_GET['delete_all'])) {
    
    $delete_all_query = $conn->prepare("DELETE FROM `cart` WHERE CustID = :user_id");
    $delete_all_query->execute([':user_id' => $user_id]);

    header('Location: cart.php'); 
    exit();
}


$cart_items_query = $conn->prepare("SELECT * FROM `cart` WHERE CustID = :user_id");
$cart_items_query->execute([':user_id' => $user_id]);
$cart_items = $cart_items_query->fetchAll(PDO::FETCH_ASSOC);

$grand_total = 0; 
?>

<!DOCTYPE html>
<html lang="en">
  <style>
    html, body {
      margin: 0;
      padding: 0;
      height: 100%;
      overflow-x: hidden; 
    }

    body {
      background-image: url('image/about1.jpg');
      background-size: cover; 
      background-position: center; 
      background-repeat: no-repeat; 
      height: 100%; 
    }

    .shopping_cart {
      padding: 50px;
      max-width: 1000px;
      margin: 0 auto;
      background-color: rgba(0, 0, 0, 0.7); 
      border-radius: 15px;
      color: white;
      min-height: calc(100vh - 100px); 
    }

    h1 {
      text-align: center;
      margin-bottom: 30px;
      font-size: 2.5em;
    }

    .cart_container {
      display: flex;
      flex-direction: column;
      gap: 20px;
      margin-bottom: 30px;
    }

    .cart_item {
      padding: 20px;
      background-color: rgba(255, 255, 255, 0.1); 
      border-radius: 10px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .cart_item h3, .cart_item p {
      margin: 0;
    }

    .cart_total {
      text-align: center;
      margin-top: 20px;
    }

    .cart_actions a {
      margin: 10px;
      padding: 10px 20px;
      background-color: #ff0000;
      color: white;
      text-decoration: none;
      border-radius: 5px;
      transition: background-color 0.3s ease;
    }

    .cart_actions a.disabled {
      background-color: grey;
      cursor: not-allowed;
    }

    .cart_actions a:hover {
      background-color: darkred;
    }

    input[type="submit"] {
      background-color: #00f;
      color: white;
      padding: 10px 20px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    input[type="submit"]:hover {
      background-color: darkblue;
    }

    .cart_actions {
      display: flex;
      justify-content: center;
    }
  </style>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    
</head>
<body>

<?php
$backgroundImage = "image/about-background.jpg"; 
$activemenu = 'cart'; 
include 'includes/header.php'; 
?> 

<section class="shopping_cart">
    <h1>Your Shopping Cart</h1>

    <div class="cart_container">
        <?php if (count($cart_items) > 0): ?>
            <?php foreach ($cart_items as $cart_item): ?>
                <?php 
                $sub_total = $cart_item['Quantity'] * $cart_item['UnitCost']; 
                $grand_total += $sub_total; 
                ?>
                <div class="cart_item">
                   
                    <a href="cart.php?delete=<?php echo $cart_item['CartID']; ?>" class="fas fa-times" onclick="return confirm('Are you sure you want to delete this item?');"></a>
                    
                    <h3><?php echo htmlspecialchars($cart_item['Title']); ?></h3>
                    <p>Price: Rs. <?php echo number_format($cart_item['UnitCost'], 2); ?>/-</p>

                  
                    <form method="post">
                        <input type="hidden" name="cart_id" value="<?php echo $cart_item['CartID']; ?>">
                        <input type="number" name="cart_quantity" value="<?php echo $cart_item['Quantity']; ?>" min="1">
                        <input type="submit" name="update_cart" value="Update">
                    </form>

    
                    <p>Subtotal: Rs. <?php echo number_format($sub_total, 2); ?>/-</p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="empty_cart">Your Cart is Empty!</p>
        <?php endif; ?>
    </div>

  
    <div class="cart_total">
        <h2>Grand Total: Rs. <?php echo number_format($grand_total, 2); ?>/-</h2>

        <div class="cart_actions">
          
            <a href="cart.php?delete_all" class="product_btn <?php echo ($grand_total > 0) ? '' : 'disabled'; ?>" onclick="return confirm('Are you sure you want to delete all items?');">Delete All</a>

            <a href="shop.php" class="product_btn">Continue Shopping</a>

       
            <a href="checkout.php" class="product_btn <?php echo ($grand_total > 0) ? '' : 'disabled'; ?>">Checkout</a>
        </div>
    </div>
</section>

</body>
</html>
