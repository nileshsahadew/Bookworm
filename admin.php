<?php
session_start();
include 'db_connect.php';
?>

<html>
 <head>
  <?php 
    require_once "metatags.php";
  ?>
   <title>Admin Home</title>
   <link rel="stylesheet" href="admin.css">
  
   </head>
 <body>
 <?php 
   $activemenu = "home"; 
   
  ?>

<?php
include 'aheader.php';
?>
<section class="admin_dashboard">
        <div class="admin_box_container">
            <div class="admin_box">
                <?php
                $total_pendings = 0;
                $stmt = $conn->prepare("SELECT TotalCost FROM payment WHERE status = 'pending'");
                $stmt->execute();
                $pending_orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($pending_orders as $order) {
                    $total_pendings += $order['TotalCost'];
                }
                ?>
                <h3>Rs. <?php echo $total_pendings; ?></h3>
                <p>Total Payments Pending</p>
            </div>

            <div class="admin_box">
                <?php
                $total_completed = 0;
                $stmt = $conn->prepare("SELECT TotalCost FROM payment WHERE status = 'completed'");
                $stmt->execute();
                $completed_orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                foreach ($completed_orders as $order) {
                    $total_completed += $order['TotalCost'];
                }
                ?>
                <h3>Rs. <?php echo $total_completed; ?></h3>
                <p>Completed Payments</p>
            </div>

            <div class="admin_box">
                <?php
                $stmt = $conn->prepare("SELECT COUNT(*) FROM payment");
                $stmt->execute();
                $number_of_orders = $stmt->fetchColumn();
                ?>
                <h3><?php echo $number_of_orders; ?></h3>
                <p>Orders Placed</p>
            </div>
            


            <div class="admin_box">
                <?php
                $stmt = $conn->prepare("SELECT COUNT(*) FROM review");
                $stmt->execute();
                $number_of_reviews = $stmt->fetchColumn();
                ?>
                <h3><?php echo $number_of_reviews; ?></h3>
                <p>New Reviews</p>
            </div>
            </div>
    </section>
</body>
</html>




