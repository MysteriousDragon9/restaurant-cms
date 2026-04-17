<?php
require_once '../auth.php';
requireAdmin();

$id = (int)$_GET['id'];
if ($id) {
    try {
        $stmt = $pdo->prepare("DELETE FROM comments WHERE id = ?");
        $stmt->execute([$id]);
    } catch(PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}
header('Location: index.php');
exit;
?>