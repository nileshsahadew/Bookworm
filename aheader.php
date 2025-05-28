<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<style>
.admin_header {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    z-index: 1000;
    box-shadow: 2px 2px 10px rgb(165, 165, 165);
    box-sizing: border-box;
}

.header_navigation {
    display: flex;
    align-items: center;
    padding: 2rem;
    justify-content: space-between;
    position: relative;
    flex-wrap: wrap;
}

.header_logo {
    font-size: 1.5rem;
    color: #000;
}

.header_logo span {
    color: #C1121F;
}

.header_navigation .header_navbar {
    margin: 0 1rem;
    font-size: 1.3rem;
    color: #000;
    transform: translate(1rem);
    transition: .2s;
}

.header_navigation .header_navbar a {
    background-color: white;
    box-shadow: 2px 2px 8px rgb(165, 165, 165);
    padding: 0.2rem 1rem;
    margin: 0 1rem;
    color: #000;
    border-radius: 10px;
}

.header_navigation .header_navbar a:hover {
    color: white;
    background-color: #C1121F;
    box-shadow: 2px 2px 11px rgb(165, 165, 165);
}

.header_navigation .header_icons div {
    margin-left: 1rem;
    font-size: 1.5rem;
    cursor: pointer;
    color: #000;
}

.header_navigation .header_icons div:hover {
    color: #C1121F;
}

.header_acc_box {
    position: absolute;
    top: 120%;
    right: 2rem;
    box-shadow: 2px 2px 10px grey;
    border-radius: .5rem;
    padding: 1rem;
    display: none;
    animation: fadeIn .2s linear;
    color: #000;
}

.header_acc_box p {
    margin-bottom: 1rem;
    color: #000;
}

.header_acc_box p span {
    color: #C1121F;
}

.header_acc_box .delete-btn {
    background-color: #C1121F;
    color: white;
    padding: 5px 10px;
    margin: 5px 0px;
    text-decoration: none;
}

.header_acc_box .delete-btn:hover {
    background-color: #000;
    color: white;
}

.header_acc_box.active {
    display: inline-block;
    background-color: white;
    z-index: 1000;
}
</style>

<?php
include 'db_connect.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?> 

<header class="admin_header">
    <div class="header_navigation">
        <a href="admin.php" class="header_logo">Admin <span>Dashboard</span></a>

        <nav class="header_navbar">
            <a href="admin.php">Home</a>
            <a href="bookForm.php">Items</a>
            <a href="aorders.php">Orders</a>
            <a href="areview.php">Reviews</a>
        </nav>

        <div class="header_icons">
            <div id="user_btn" class="fas fa-user" onclick="display_accbox()"></div>
        </div>

        <div id="div_accbox" class="header_acc_box">
            <p>Username : 
                <span>
                    <?php 
                    echo isset($_SESSION['emp_name']) ? htmlspecialchars($_SESSION['emp_name']) : 'Not logged in';
                    ?>
                </span>
            </p>
            <p>Email : 
                <span>
                    <?php 
                    echo isset($_SESSION['emp_email']) ? htmlspecialchars($_SESSION['emp_email']) : 'Not logged in';
                    ?>
                </span>
            </p>
            <a href="alogin.php" class="delete-btn">Logout</a>
        </div>
    </div>
</header>

<script>
function display_accbox() {
    document.getElementById('div_accbox').classList.toggle('active');
}
</script>
