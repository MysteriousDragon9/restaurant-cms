<?php
require_once '../admin/db.php';
require_once '../admin/captcha.php';

// Check if reCAPTCHA was submitted
if (!isset($_POST['g-recaptcha-response']) || empty($_POST['g-recaptcha-response'])) {
    die("Please complete the reCAPTCHA verification.");
}

// Verify reCAPTCHA response
if (!verifyRecaptcha($_POST['g-recaptcha-response'])) {
    die("reCAPTCHA verification failed. Please try again.");
}

// Get form data
$restaurant_id = (int)$_POST['restaurant_id'];
$user_name = sanitize($_POST['user_name']);
$comment_text = sanitize($_POST['comment_text']);

try {
    // Insert the comment
    $sql = "INSERT INTO comments (restaurant_id, user_name, comment_text, created_at) VALUES (?, ?, ?, NOW())";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$restaurant_id, $user_name, $comment_text]);

    // Redirect back to the restaurant page
    header("Location: restaurant.php?id=" . $restaurant_id);
    exit;
} catch(PDOException $e) {
    die("Error adding comment: " . $e->getMessage());
}
?>