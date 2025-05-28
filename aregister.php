<?php
session_start();

$empNameErr = $empEmailErr = $passwordErr = $phonenumErr = $cpasswordErr = "";
$empName = $empEmail = $userpassword = $usercpassword = $phonenum = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

   
    if (empty($_POST["txt_name"])) {
        $empNameErr = "Admin_Name is required";
    } else {
        $empName = $_POST["txt_name"];
    }

    
    if (empty($_POST["txt_email"])) {
        $empEmailErr = "Admin_Email is required";
    } else if (!filter_var($_POST["txt_email"], FILTER_VALIDATE_EMAIL)) {
        $empEmailErr = "Invalid email format";
    } else {
        $empEmail = $_POST["txt_email"];
    }

    
    if (empty($_POST["txt_password"])) {
        $passwordErr = "Password is required";
    } else if (!preg_match('/[0-9]/', $_POST["txt_password"])) {
        $passwordErr = "Password must contain at least one number";
    } else {
        $userpassword = $_POST["txt_password"];
    }

    
    if (empty($_POST["txt_cpassword"])) {
        $cpasswordErr = "Password needs to be confirmed";
    } else {
        $usercpassword = $_POST["txt_cpassword"];
    }

    
    if (empty($_POST["txt_phonenum"])) {
        $phonenumErr = "Phone number is required";
    } else if (!preg_match('/^[0-9]{8}$/', $_POST["txt_phonenum"])) {
        $phonenumErr = "Phone number must be 8 digits";
    } else {
        $phonenum = $_POST["txt_phonenum"];
    }

    
    if ($empNameErr == "" && $empEmailErr == "" && $passwordErr == "" && $phonenumErr == "" && $cpasswordErr == "") {

        require_once "db_connect.php";

        
        $stmt = $conn->prepare("SELECT * FROM employee WHERE emp_Email = :email");
        $stmt->bindParam(':email', $empEmail);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $empEmailErr = "Email already exists!";
        } else if ($userpassword != $usercpassword) {
            $cpasswordErr = 'Passwords do not match!';
        } else {
            
            $hashed_password = password_hash($userpassword, PASSWORD_DEFAULT);

            
            $sInsert = "INSERT INTO employee (emp_Name, emp_Email, emp_Password, emp_num) VALUES (:empName, :email, :password, :phonenum)";
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $conn->prepare($sInsert);
            $stmt->bindParam(':empName', $empName);
            $stmt->bindParam(':email', $empEmail);
            $stmt->bindParam(':password', $hashed_password);
            $stmt->bindParam(':phonenum', $phonenum);

            if ($stmt->execute()) {
                header('Location: alogin.php');
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

        
        <div class="inputbox">
            <input type="text" name="txt_name" required="required">
            <span>Name</span>
            <span class="error"><?php echo $empNameErr; ?></span>
        </div>

       
        <div class="inputbox">
            <input type="text" name="txt_email" required="required">
            <span>Email Address</span>
            <span class="error"><?php echo $empEmailErr; ?></span>
        </div>

       
        <div class="inputbox">
            <input type="password" name="txt_password" minlength="6" required="required">
            <span>Password</span>
            <span class="error"><?php echo $passwordErr; ?></span>
        </div>

        
        <div class="inputbox">
            <input type="password" name="txt_cpassword" required="required">
            <span>Confirm Password</span>
            <span class="error"><?php echo $cpasswordErr; ?></span>
        </div>

        
        <div class="inputbox">
            <input type="text" name="txt_phonenum" required="required">
            <span>Phone Number</span>
            <span class="error"><?php echo $phonenumErr; ?></span>
        </div>

        <input type="submit" value="Register Now">

        <div class="links">
            <a href>Already a member?</a>
            <a href="alogin.php">Login</a>
        </div>
    </form>
</div>

</body>
</html>
