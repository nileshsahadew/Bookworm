<?php
$id = $_GET['id'];
$url = "http://localhost/books/list";
$response = file_get_contents($url);
$data = json_decode($response, true);

$book = null;
foreach($data['books'] as $b) {

    if($b['ItemID'] == $id) {
        $book = $b;
        break;
    }
}
?>

<section class="admin_add_products">
  <form action="/books/update/<?php echo $id; ?>" method="post">
    <h3>Update Item</h3>
    <input type="text" name="title" class="admin_input" value="<?php echo htmlspecialchars($book['Title']); ?>" required>
    <input type="number" name="price" class="admin_input" value="<?php echo $book['UnitCost']; ?>" required>
    <input type="text" name="image" class="admin_input" value="<?php echo htmlspecialchars($book['image']); ?>" required>
    <input type="submit" value="Update Book" class="admin_input btn_red">
    <a href="bookForm.php" class="admin_input btn_gray">Back to Admin Panel</a>
  </form>
</section>
<style>
.admin_add_products {
    margin-top: 8%;
    text-align: center;
    display: flex;
    justify-content: center;
}

.admin_input {
    margin: 1.2rem;
    padding: 1rem;
    border-radius: 10px;
    border: none;
    box-shadow: 2px 2px 5px gray;
    width: 300px;
}

.admin_input:focus {
    outline: none;
    box-shadow: 2px 2px 10px rgb(107, 107, 107);
}
.btn_red {
    background-color: #C1121F;
    color: white;
    font-weight: bold;
    cursor: pointer;
}

.btn_gray {
    background-color: #f1f1f1;
    color: #333;
    font-weight: bold;
    cursor: pointer;
    text-decoration: none; display: inline-block; text-align: center
}
</style>