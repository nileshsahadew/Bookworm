<?php
include 'db_connect.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['cust_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please log in to add to cart.']);
    exit;
}

$cust_id = $_SESSION['cust_id'];

if (isset($_POST['item_id'], $_POST['item_name'], $_POST['item_price'], $_POST['item_quantity'], $_POST['item_image'])) {
    $i_name = $_POST['item_name'];

    $check_cart = $conn->prepare("SELECT * FROM cart WHERE Title = ? AND CustID = ?");
    $check_cart->execute([$i_name, $cust_id]);

    if ($check_cart->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Already added to cart!']);
    } else {
        $insert_cart = $conn->prepare("INSERT INTO cart (CustID, Title, UnitCost, Quantity, image) VALUES (?, ?, ?, ?, ?)");
        $insert_cart->execute([$cust_id, $i_name, $_POST['item_price'], $_POST['item_quantity'], $_POST['item_image']]);
        echo json_encode(['success' => true, 'message' => 'Product added to cart!']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid data received.']);
}
?>
