<?php
require_once '../auth.php';
requireAdmin();

// Get restaurant ID again
$id = (int)$_GET['id'];
if (!$id) {
    header('Location: index.php');
    exit;
}

// Delete restaurant
try {
    $stmt = $pdo->prepare("DELETE FROM restaurants WHERE id = ?");
    $stmt->execute([$id]);

    header('Location: index.php?success=deleted');
    exit;
} catch(PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>