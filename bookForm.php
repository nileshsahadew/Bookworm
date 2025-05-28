<?php
include 'db_connect.php';
session_start();

$emp_id = $_SESSION['emp_id']; 

if (!isset($emp_id)) {
    header('Location: alogin.php'); 
    exit();
}
?>
<style>
    .admin_add_products{
    margin-top: 15%;
    text-align: center;
    display: flex;
    justify-content: center;
}
    .admin_add_products form input[type="submit"]{
    background-color: #C1121F;
    color: white;
    letter-spacing: 2px;
    font-weight: 900;
}
.admin_add_products form h3{
    font-size: 2rem;
    letter-spacing: 1.5px;
}
.admin_input{
    margin: 1.2rem;
    padding: 1rem;
    border-radius: 10px;
    border: none;
    box-shadow: 2px 2px 5px gray;
}

.admin_input:focus{
    outline: none;
    box-shadow: 2px 2px 10px rgb(107, 107, 107);
}

.books-section {
    margin-top: 50px;
    text-align: center;
}

.books-table {
    margin: 20px auto;
    border-collapse: collapse;
    width: 80%;
}

.books-table th, .books-table td {
    padding: 12px 15px;
    border: 1px solid #444;
    text-align: center;
}

.books-table th {
    background-color: #f2f2f2;
    font-weight: bold;
}

.books-table img {
    width: 80px;
    height: auto;
    border-radius: 8px;
    box-shadow: 0 0 5px #aaa;
}

</style>

<head>
  <title>Items</title>
</head>
<body>
<?php
include 'aheader.php';
require 'db_connect.php';
?>




<head>
  <title>Items</title>
</head>
<body>
<?php
include 'aheader.php';

?>
 <section class="admin_add_products">
  <form action="/books/create" method="post">
    <h3>Add a New Book</h3>

    <input type="text" name="title" class="admin_input" placeholder="Enter Book Title" required>

    <input type="number" step="0.01" name="price" class="admin_input" placeholder="Enter Book Price" required>

    <input type="text" name="image" class="admin_input" placeholder="Enter Image Filename (e.g. book.jpg)" required>

    <input type="submit" value="Create Book" class="admin_input">
  </form>
  </body>
</html>

</section>
<?php if (isset($_GET['msg']) && $_GET['msg'] === 'updated'): ?>
  <div style="background-color: #d4edda; color: #155724; padding: 10px; margin: 20px auto; border: 1px solid #c3e6cb; width: 80%; text-align: center; border-radius: 8px;">
    ✅ Book updated successfully!
  </div>
<?php endif; ?>

<?php

?>
<section class="books-section">
  <h3>All Available Items</h3>
  <table class="books-table">
    <thead>
      <tr>
        <th>Title</th>
        <th>Price</th>
        <th>Image</th>
        <th>Action</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody id="book-table">
      <?php
      $url = "http://localhost/books/list";
      $response = file_get_contents($url);
      $data = json_decode($response, true);

      if (!empty($data['books'])) {
        foreach ($data['books'] as $book) {
          echo "<tr id='book-{$book['ItemID']}'>";
          echo "<td>" . htmlspecialchars($book['Title']) . "</td>";
          echo "<td>Rs. " . number_format($book['UnitCost'], 2) . "</td>";
          echo "<td><img src='./image/" . htmlspecialchars($book['image']) . "' width='80'></td>";
          echo "<td><a href='edit_book.php?id=" . $book['ItemID'] . "' class='admin_input'>Edit</a></td>";
          echo "<td>
            <button class='delete-btn' data-id='{$book['ItemID']}'>Delete</button>
          </td>";
          echo "</tr>";
        }
      } else {
        echo "<tr><td colspan='4'>No books available.</td></tr>";
      }
      ?>
    </tbody>
  </table>
</section>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
$(document).ready(function() {
  $('.delete-btn').click(function() {
    const bookId = $(this).data('id');
    if (confirm('Are you sure you want to delete this book?')) {
      $.ajax({
        url: '/books/delete/' + bookId,
        type: 'POST',
        success: function(response) {
          alert('Book deleted successfully!');
          $('#book-' + bookId).remove();
        },
        error: function() {
          alert('Failed to delete book.');
        }
      });
    }
  });
});
</script>
