<?php
include 'db_connect.php';
session_start();

$cust_id = $_SESSION['cust_id'] ?? null;

if (!$cust_id) {
    header('Location: login.php');
    exit();
}

// Set background and active menu BEFORE including header
$backgroundImage = "image/about-background.jpg";
$activemenu = 'checkout';

include 'includes/header.php';

// Fetch customer name
$cust_query = $conn->prepare("SELECT Cust_Name FROM customer WHERE Cust_ID = :cust_id");
$cust_query->execute(['cust_id' => $cust_id]);
$cust_data = $cust_query->fetch(PDO::FETCH_ASSOC);

if (!$cust_data) {
    session_destroy();
    header('Location: login.php');
    exit();
}

$cust_name = $cust_data['Cust_Name'];
$popup_message = "";

if (isset($_POST['order_btn'])) {
    $method = htmlspecialchars($_POST['method']);
    $placed_on = date('Y-m-d');
    $payment_intent_id = $_POST['payment_intent_id'] ?? null;

    $cart_total = 0;
    $cart_products = [];

    $cart_query = $conn->prepare("SELECT * FROM cart WHERE CustID = :cust_id");
    $cart_query->execute(['cust_id' => $cust_id]);

    while ($cart_item = $cart_query->fetch(PDO::FETCH_ASSOC)) {
        $cart_products[] = $cart_item['Title'] . ' (' . $cart_item['Quantity'] . ')';
        $sub_total = $cart_item['UnitCost'] * $cart_item['Quantity'];
        $cart_total += $sub_total;
    }

    if ($method === 'card' && !$payment_intent_id) {
        $popup_message = "Card payment failed. Please try again.";
    } elseif ($cart_total > 0) {
        $order_check = $conn->prepare("SELECT * FROM payment WHERE PaymentType = :method AND TotalCost = :cart_total AND Cust_ID = :cust_id");
        $order_check->execute([
            'method' => $method,
            'cart_total' => $cart_total,
            'cust_id' => $cust_id
        ]);

        if ($order_check->rowCount() == 0) {
            $insert_order = $conn->prepare("INSERT INTO payment (PaymentType, PaymentDate, TotalCost, Cust_ID, Cust_Name, status, stripe_payment_id) VALUES (:method, :placed_on, :cart_total, :cust_id, :cust_name, 'pending', :stripe_id)");
            $insert_order->execute([
                'method' => $method,
                'placed_on' => $placed_on,
                'cart_total' => $cart_total,
                'cust_id' => $cust_id,
                'cust_name' => $cust_name,
                'stripe_id' => $payment_intent_id
            ]);

            $conn->prepare("DELETE FROM cart WHERE CustID = :cust_id")->execute(['cust_id' => $cust_id]);

            $popup_message = "Your order has been placed successfully!";
        }
    } else {
        $popup_message = "Your cart is empty.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout Page</title>
    <link rel="stylesheet" href="css/checkout.css">
    <script src="https://js.stripe.com/v3/"></script>
    <style>
            body {
            background-image: url('image/about1.jpg');
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            background-attachment: fixed;
        }

        /* Popup Styles */
        .popup {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.5);
        }

        .popup-content {
            background-color: #fff;
            margin: 15% auto;
            padding: 30px;
            border-radius: 10px;
            width: 300px;
            text-align: center;
            position: relative;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }

        .close {
            color: #aaa;
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 24px;
            font-weight: bold;
            cursor: pointer;
        }
        #card-section { display: none; margin: 20px 0; }
        #card-element { padding: 10px; border: 1px solid #ccc; border-radius: 6px; }
    </style>
</head>
<body>

<div id="popup" class="popup">
    <div class="popup-content">
        <span class="close">&times;</span>
        <p id="popup-text"></p>
    </div>
</div>

<section class="display_order">
    <div class="order-container">
        <h2>Ordered Products</h2>
        <?php
        $grand_total = 0;
        $select_cart = $conn->prepare("SELECT * FROM cart WHERE CustID = :cust_id");
        $select_cart->execute(['cust_id' => $cust_id]);

        while ($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)) {
            $total_price = $fetch_cart['UnitCost'] * $fetch_cart['Quantity'];
            $grand_total += $total_price;
            ?>
            <div class="order-item">
                <img src="./image/<?php echo htmlspecialchars($fetch_cart['image']); ?>" alt="Product" class="product-img">
                <div class="order-details">
                    <h3><?php echo htmlspecialchars($fetch_cart['Title']); ?></h3>
                    <p>Rs. <?php echo $fetch_cart['UnitCost']; ?> x <?php echo $fetch_cart['Quantity']; ?> = Rs. <?php echo number_format($total_price, 2); ?></p>
                </div>
            </div>
        <?php } ?>
        <h3 class="grand-total">Grand Total: Rs. <?php echo number_format($grand_total, 2); ?>/-</h3>
    </div>
</section>


<section class="checkout-section">
    <form id="checkout-form" method="post" class="checkout-form">
        <h2>Checkout</h2>
        <div class="form-row">
            <label><strong>Name:</strong></label>
            <p><?php echo htmlspecialchars($cust_name); ?></p>
        </div>

        <div class="form-row">
            <label for="payment-method">Payment Method</label>
            <select name="method" id="payment-method" required>
                <option value="" disabled selected>Select Payment Method</option>
                <option value="cash on delivery">Cash on Delivery</option>
                <option value="card">Card</option>
            </select>
        </div>

        <div id="card-section">
            <div class="form-row">
                <label for="card-element">Card Details</label>
                <div id="card-element"></div>
                <div id="card-errors" class="error-message"></div>
            </div>
        </div>

        <input type="hidden" name="payment_intent_id" id="payment-intent-id">
        <div class="form-row">
            <input type="submit" value="Place Your Order" name="order_btn" class="submit-btn">
        </div>
    </form>
</section>


<script>
    const popupMessage = <?php echo json_encode($popup_message); ?>;
    if (popupMessage) {
        document.getElementById('popup').style.display = 'block';
        document.getElementById('popup-text').textContent = popupMessage;
    }
    document.querySelector('.close').onclick = () => document.getElementById('popup').style.display = 'none';

    const stripe = Stripe('pk_test_51RH6HNPG7oE9x3QHdrPiM2ARkt6wmHc7vnCZOn1Gscru9KtbDK0r5HYYS162aolDQRFjSizs2a9OhYjG5NNUNZ6w00OArouKba');
    const elements = stripe.elements();
    const card = elements.create('card');
    card.mount('#card-element');

    

    document.getElementById('payment-method').addEventListener('change', function () {
        document.getElementById('card-section').style.display = this.value === 'card' ? 'block' : 'none';
    });

    document.getElementById('checkout-form').addEventListener('submit', async (e) => {
        if (document.getElementById('payment-method').value === 'card') {
            e.preventDefault();
            const res = await fetch('create_intent.php', { method: 'POST' });
            const { clientSecret } = await res.json();

            const result = await stripe.confirmCardPayment(clientSecret, {
                payment_method: { card }
            });

            if (result.error) {
                document.getElementById('card-errors').textContent = result.error.message;
            } else if (result.paymentIntent.status === 'succeeded') {

// Set hidden input with order_btn name and value
const hiddenSubmit = document.createElement('input');
hiddenSubmit.type = 'hidden';
hiddenSubmit.name = 'order_btn';
hiddenSubmit.value = 'Place Your Order';
document.getElementById('checkout-form').appendChild(hiddenSubmit);



                document.getElementById('payment-intent-id').value = result.paymentIntent.id;
                document.getElementById('checkout-form').submit();
            }
        }
    });
</script>

</body>
</html>