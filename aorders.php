<?php
include 'db_connect.php';
session_start();

$admin_id = $_SESSION['emp_id'];

if (!isset($admin_id)) {
    header('location:alogin.php');
    exit();
}

if (isset($_POST['update_order'])) {
    $order_update_id = $_POST['order_id'];
    $update_payment = $_POST['update_payment'];

    $stmt = $conn->prepare("UPDATE payment SET status = ? WHERE PaymentID = ?");
    $stmt->execute([$update_payment, $order_update_id]);

 
}

if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];

    $stmt = $conn->prepare("DELETE FROM payment WHERE PaymentID = ?");
    $stmt->execute([$delete_id]);

    
}
?>
<style>
    .admin_orders{
    width: 100%;
    margin-top: 10%;
    display: flex;
    flex-direction: column;
    justify-content: center;
}
.admin_orders h1{
    margin: 0;
    display: flex;
    justify-content: center;
    font-size: 2rem;
    letter-spacing: 1px;
}

.admin_orders .admin_box_container .admin_box form input,
.admin_orders .admin_box_container .admin_box form select,
.admin_orders .admin_box_container .admin_box form a{
    width: 100%;
    margin-top: 1rem;
    text-align: center;
    display: flex;
    font-size: 1rem;
    justify-content: center;
    padding: 0.5rem 1rem;
}

.delete-btn{
    background-color: #C1121F;
    text-decoration: none;
    border: none;
    cursor: pointer;
    color: white;
    padding: 0.5rem 1rem;
}

.option-btn{
    background-color: greenyellow;
    text-decoration: none;
    border: none;
    cursor: pointer;
    padding: 0.5rem 1rem;
}

.admin_box_container .admin_box p{
    font-weight: 900;
}

 .admin_box_container .admin_box span{
    color: #C1121F;
}

.admin_orders .admin_box_container .admin_box,
.admin_messages .admin_box_container .admin_box{
    width: 40%;
}

.admin_orders .admin_box_container,
.admin_messages .admin_box_container{
    align-items: center;
    justify-content: center;
    
 }

</style>

<head>
  
  <title>Orders</title>
  
</head>
<body>

<?php
include 'aheader.php';
?>

<section class="admin_orders">
  <h1 class="title">Placed Orders</h1>

  <div class="admin_box_container">
    <?php
    $stmt = $conn->prepare("SELECT * FROM payment");
    $stmt->execute();
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);


    if (count($orders) > 0) {
        foreach ($orders as $fetch_orders) {
    ?>
    <div class="admin_box">
      <p>User Id : <span><?php echo htmlspecialchars($fetch_orders['Cust_ID']); ?></span></p>
      <p>Placed On : <span><?php echo htmlspecialchars($fetch_orders['PaymentDate']); ?></span></p>
      <p>Name : <span><?php echo htmlspecialchars($fetch_orders['Cust_Name']); ?></span></p>
      <p>Total Price : <span><?php echo htmlspecialchars($fetch_orders['TotalCost']); ?></span></p>
      <p>Payment Method : <span><?php echo htmlspecialchars($fetch_orders['PaymentType']); ?></span></p>

      <form action="" method="post">
        <input type="hidden" name="order_id" value="<?php echo $fetch_orders['PaymentID']; ?>">
        <select name="update_payment">
          <option value=""><?php echo htmlspecialchars($fetch_orders['status']); ?></option>
          <option value="pending">pending</option>
          <option value="completed">completed</option>
        </select>
        <input type="submit" value="update" name="update_order" class="option-btn">
        <a href="aorders.php?delete=<?php echo $fetch_orders['PaymentID']; ?>" onclick="return confirm('Are you sure you want to delete this order?');" class="delete-btn">delete</a>
      </form>
    </div>
    <?php
        }
    } else {
        echo '<p class="empty">No orders placed yet!</p>';
    }
    ?>
  </div>
</section>