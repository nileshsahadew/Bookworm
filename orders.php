<?php
include 'db_connect.php';
session_start();

$cust_id = $_SESSION['cust_id']; 

if (!isset($cust_id)) {
    header('location:login.php'); 
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Orders Page</title>
  <link rel="stylesheet" href="css/shop.css">

</head>
<body>
  
<?php
$backgroundImage = "image/about-background.jpg";
$activemenu = 'orders'; 
include 'includes/header.php'; 
?>
<style>
    body {
  background-image: url('image/about1.jpg');
  background-size: cover;
  background-repeat: no-repeat;
  color: white;
}

</style>
<section class="orders">
  <h2>Placed Orders</h2>
  <div class="orders_cont">
    <?php

    $order_query = $conn->prepare("SELECT * FROM payment WHERE Cust_ID = :cust_id");
    $order_query->execute([':cust_id' => $cust_id]);

    if ($order_query->rowCount() > 0) {
        while ($fetch_orders = $order_query->fetch(PDO::FETCH_ASSOC)) {
    ?>
    <div class="orders_box">
      <p> Placed on: <span><?php echo $fetch_orders['PaymentDate']; ?></span> </p>
      <p> Name: <span><?php echo $fetch_orders['Cust_Name']; ?></span> </p>
      <p> Payment Method: <span><?php echo $fetch_orders['PaymentType']; ?></span> </p>
      <p> Total Price: <span>Rs. <?php echo number_format($fetch_orders['TotalCost'], 2); ?>/-</span> </p>
      <p> Payment Status: 
        <span style="color:<?php echo ($fetch_orders['status'] == 'pending') ? 'red' : 'green'; ?>;">
          <?php echo ucfirst($fetch_orders['status']); ?>
        </span>
      </p>
    </div>
    <?php
        }
    } else {
        echo '<p class="empty">No orders placed yet!</p>';
    }
    ?>
  </div>
</section>

</body>
</html>