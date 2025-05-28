<?php
session_start();

$custEmailErr = $passwordErr = "";
$custEmail = $userpassword = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

   
    if (empty($_POST["txt_email"])) {
        $custEmailErr = "User email is required";
    } else if (!filter_var($_POST["txt_email"], FILTER_VALIDATE_EMAIL)) {
        $custEmailErr = "Invalid email format";
    } else {
        $custEmail = $_POST["txt_email"];
    }

   
    if (empty($_POST["txt_password"])) {
        $passwordErr = "Password is required";
    } else {
        $userpassword = $_POST["txt_password"];
    }

   
    if ($custEmailErr == "" && $passwordErr == "") {

        require_once "db_connect.php";

        
        $stmt = $conn->prepare("SELECT * FROM customer WHERE Cust_Email = :email");
        $stmt->bindParam(':email', $custEmail);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

           
            if (password_verify($userpassword, $user['Password'])) {
                
                $_SESSION['cust_id'] = $user['Cust_ID'];
                $_SESSION['cust_name'] = $user['Cust_Name'];
                $_SESSION['loggedin'] = true; 
                
              
                header('Location: home.php');
                exit();
            }else {
                $passwordErr = "Incorrect password!";
            }
        } else {
            $custEmailErr = "No account found with that email!";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>

<?php 
   $activemenu = "Login"; 
?>

<div class="box">
    <span class="borderline"></span>
    <form method="post" action="">
        <h2>Login</h2>

        
        <div class="inputbox">
            <input type="text" name="txt_email" required="required">
            <span>Email Address</span>
            <span class="error"><?php echo $custEmailErr; ?></span>
        </div>

       
        <div class="inputbox">
            <input type="password" name="txt_password" required="required">
            <span>Password</span>
            <span class="error"><?php echo $passwordErr; ?></span>
        </div>

        
        <input type="submit" value="Login Now">

        
        <div class="links">
            <a href>Don't have an account?</a>
            <a href="register.php">Register</a>
        </div>
    </form>
</div>

</body>
</html>
