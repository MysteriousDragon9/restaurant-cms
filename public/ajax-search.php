<?php
require_once '../admin/db.php';

// Get search query from AJAX request
$query = isset($_GET['q']) ? trim($_GET['q']) : '';

if (empty($query)) {
    echo '<div class="alert alert-info">Please enter a search term.</div>';
    exit;
}

try {
    // Search restaurants by name, description, or address (using only columns that exist)
    $sql = "SELECT r.*, c.name as category_name
            FROM restaurants r
            LEFT JOIN categories c ON r.category_id = c.id
            WHERE r.name LIKE ?
               OR r.description LIKE ?
               OR r.address LIKE ?
            ORDER BY r.name
            LIMIT 10";

    $searchTerm = "%$query%";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$searchTerm, $searchTerm, $searchTerm]);
    $results = $stmt->fetchAll();

    if (count($results) > 0) {
        echo '<div class="list-group">';
        foreach ($results as $restaurant) {
            echo '<a href="restaurant.php?id=' . $restaurant['id'] . '" class="list-group-item list-group-item-action search-result-item">';
            echo '<div class="d-flex w-100 justify-content-between align-items-center">';
            echo '<div>';
            echo '<h6 class="mb-1">' . htmlspecialchars($restaurant['name']) . '</h6>';
            echo '<small class="text-muted">' . htmlspecialchars($restaurant['category_name'] ?? 'Uncategorized') . '</small><br>';
            echo '<small class="text-muted">' . htmlspecialchars(substr($restaurant['description'], 0, 80)) . '...</small>';
            echo '</div>';
            echo '<i class="fas fa-arrow-right text-muted"></i>';
            echo '</div>';
            echo '</a>';
        }
        echo '</div>';
    } else {
        echo '<div class="alert alert-warning">No restaurants found matching "' . htmlspecialchars($query) . '"</div>';
    }

} catch(PDOException $e) {
    echo '<div class="alert alert-danger">Search error: ' . htmlspecialchars($e->getMessage()) . '</div>';
}
?>