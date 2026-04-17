<?php
require_once '../auth.php';
requireAdmin();

//category ID
$id = (int)$_GET['id'];
if (!$id) {
    header('Location: index.php');
    exit;
}

// Delete category
try {
    $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->execute([$id]);

    header('Location: index.php?success=deleted');
    exit;
} catch(PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>