<?php

session_start();

$custNameErr = $custEmailErr = $passwordErr = $phonenumErr = $cpasswordErr = "";
$custName = $custEmail = $userpassword = $usercpassword = $phonenum = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validate Cust_Name
    if (empty($_POST["txt_name"])) {
        $custNameErr = "Cust_Name is required";
    } else {
        $custName = $_POST["txt_name"];
    }

    // Validate Cust_Email
    if (empty($_POST["txt_email"])) {
        $custEmailErr = "Cust_Email is required";
    } else if (!filter_var($_POST["txt_email"], FILTER_VALIDATE_EMAIL)) {
        $custEmailErr = "Invalid email format";
    } else {
        $custEmail = $_POST["txt_email"];
    }

    // Validate Password
    if (empty($_POST["txt_password"])) {
        $passwordErr = "Password is required";
    } else if (!preg_match('/[0-9]/', $_POST["txt_password"])) {
        $passwordErr = "Password must contain at least one number";
    } else {
        $userpassword = $_POST["txt_password"];
    }

    // Validate Confirm Password
    if (empty($_POST["txt_cpassword"])) {
        $cpasswordErr = "Password needs to be confirmed";
    } else {
        $usercpassword = $_POST["txt_cpassword"];
    }

    // Validate Phonenum
    if (empty($_POST["txt_phonenum"])) {
        $phonenumErr = "Phone number is required";
    } else if (!preg_match('/^[0-9]{8}$/', $_POST["txt_phonenum"])) {
        $phonenumErr = "Phone number must be 8 digits";
    } else {
        $phonenum = $_POST["txt_phonenum"];
    }

    // Check for errors before inserting into database
    if ($custNameErr == "" && $custEmailErr == "" && $passwordErr == "" && $phonenumErr == "" && $cpasswordErr == "") {

        require_once "db_connect.php";

        // Check if Cust_Email already exists
        $stmt = $conn->prepare("SELECT * FROM customer WHERE Cust_Email = :email");
        $stmt->bindParam(':email', $custEmail);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $custEmailErr = "Email already exists!";
        } else if ($userpassword != $usercpassword) {
            $cpasswordErr = 'Passwords do not match!';
        } else {
            // Hash password
            $hashed_password = password_hash($userpassword, PASSWORD_DEFAULT);

            // Insert data into database without Cust_ID
            $sInsert = "INSERT INTO customer (Cust_Name, Cust_Email, Password, Phonenum) VALUES (:custName, :email, :password, :phonenum)";
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $conn->prepare($sInsert);
            $stmt->bindParam(':custName', $custName);
            $stmt->bindParam(':email', $custEmail);
            $stmt->bindParam(':password', $hashed_password);
            $stmt->bindParam(':phonenum', $phonenum);

            if ($stmt->execute()) {
                header('Location: login.php');
                exit();
            } else {
                echo "ERROR: Your data could not be saved!";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>

<?php 
   $activemenu = "Register"; 
?>

<div class="box">
    <span class="borderline"></span>
    <form method="post" action="">
        <h2>Register</h2>

        <!-- Cust_Name -->
        <div class="inputbox">
            <input type="text" name="txt_name" required="required">
            <span>Name</span>
            <span class="error"><?php echo $custNameErr; ?></span>
        </div>

        <!-- Cust_Email -->
        <div class="inputbox">
            <input type="text" name="txt_email" required="required">
            <span>Email Address</span>
            <span class="error"><?php echo $custEmailErr; ?></span>
        </div>

        <!-- Password -->
        <div class="inputbox">
            <input type="password" name="txt_password" minlength="6" required="required">
            <span>Password</span>
            <span class="error"><?php echo $passwordErr; ?></span>
        </div>

        <!-- Confirm Password -->
        <div class="inputbox">
            <input type="password" name="txt_cpassword" required="required">
            <span>Confirm Password</span>
            <span class="error"><?php echo $cpasswordErr; ?></span>
        </div>

        <!-- Phonenum -->
        <div class="inputbox">
            <input type="text" name="txt_phonenum" required="required">
            <span>Phone Number</span>
            <span class="error"><?php echo $phonenumErr; ?></span>
        </div>

        <input type="submit" value="Register Now">

        <div class="links">
            <a href>Already a member?</a>
            <a href="login.php">Login</a>
        </div>
    </form>
</div>

</body>
</html>
