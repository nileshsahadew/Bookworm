<?php
session_start();

$empEmailErr = $passwordErr = "";
$empEmail = $userpassword = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST["txt_email"])) {
        $empEmailErr = "User email is required";
    } else if (!filter_var($_POST["txt_email"], FILTER_VALIDATE_EMAIL)) {
        $empEmailErr = "Invalid email format";
    } else {
        $empEmail = $_POST["txt_email"];
    }

    if (empty($_POST["txt_password"])) {
        $passwordErr = "Password is required";
    } else {
        $userpassword = $_POST["txt_password"];
    }

    if ($empEmailErr == "" && $passwordErr == "") {
        require_once "db_connect.php";

        $stmt = $conn->prepare("SELECT * FROM employee WHERE emp_email = :email");
        $stmt->bindParam(':email', $empEmail);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (password_verify($userpassword, $user['Emp_Password'])) {
                $_SESSION['emp_id'] = $user['Emp_ID'];
                $_SESSION['emp_name'] = $user['Emp_Name'];
                $_SESSION['emp_email'] = $user['Emp_Email'];
                $_SESSION['loggedin'] = true;

                header('Location: admin.php');
                exit();
            } else {
                $passwordErr = "Incorrect password!";
            }
        } else {
            $empEmailErr = "No account found with that email!";
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

<div class="box">
    <span class="borderline"></span>
    <form method="post" action="">
        <h2>Login</h2>

        <div class="inputbox">
            <input type="text" name="txt_email" required="required">
            <span>Email Address</span>
            <span class="error"><?php echo $empEmailErr; ?></span>
        </div>

        <div class="inputbox">
            <input type="password" name="txt_password" required="required">
            <span>Password</span>
            <span class="error"><?php echo $passwordErr; ?></span>
        </div>

        <input type="submit" value="Login Now">

        <div class="links">
            <a href="#">Don't have an account?</a>
            <a href="aregister.php">Register</a>
        </div>
    </form>
</div>

</body>
</html>
