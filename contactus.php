<?php

include('db_connect.php');


$responseMessage = "";


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
   
    $name = $_POST['name'];
    $email = $_POST['email'];
    $message = $_POST['message'];

    try {
       
        $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, message) VALUES (:name, :email, :message)");
        
       
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':message', $message);

       
        if ($stmt->execute()) {
            $responseMessage = "Your message has been sent successfully!";
        } else {
            $responseMessage = "Error: Unable to send message.";
        }
    } catch (PDOException $e) {
        $responseMessage = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Contact Us</title>
    <link rel="stylesheet" href="contact-style.css">
</head>
<body>
    <div class="contact-container">
        <h1>Contact Us</h1>

       
        <?php if (!empty($responseMessage)): ?>
            <p style="color: white; font-weight: bold;"><?php echo $responseMessage; ?></p>
        <?php endif; ?>

        <form action="" method="post">
            <input type="text" name="name" placeholder="Your Name" required>
            <input type="email" name="email" placeholder="Your Email" required>
            <textarea name="message" placeholder="Your Message" rows="5" required></textarea>

           
            <div class="button-container">
                <button type="submit">Send Message</button>
                <a href="home.php"><button type="button">Go to Home</button></a>
            </div>
        </form>
    </div>
</body>
</html>



