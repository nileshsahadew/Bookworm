<?php
include 'db_connect.php';

if (isset($_GET['search'])) {
    $search_term = $_GET['search'];
    $min_budget = $_GET['min_budget'] ?? null;
    $max_budget = $_GET['max_budget'] ?? null;

    if (empty($min_budget) || $min_budget < 0) $min_budget = 0;
    if (empty($max_budget) || $max_budget < $min_budget) $max_budget = 10000;

    $stmt = $conn->prepare("SELECT * FROM item WHERE title LIKE :search_term AND UnitCost BETWEEN :min_budget AND :max_budget LIMIT 10");
    $stmt->execute([
        'search_term' => "%$search_term%",
        'min_budget' => $min_budget,
        'max_budget' => $max_budget
    ]);

    if ($stmt->rowCount() > 0) {
        while ($item = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo '<div class="autocomplete-suggestion" data-name="' . htmlspecialchars($item['Title']) . '">';
            echo '<img src="image/' . htmlspecialchars($item['image']) . '" alt="img"> ';
            echo htmlspecialchars($item['Title']) . ' - Rs. ' . $item['UnitCost'] . '/-';
            echo '</div>';
        }
    }
}
?>



