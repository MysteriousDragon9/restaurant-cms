<?php
require_once '../admin/db.php';
require_once '../admin/captcha.php';
require_once '../admin/config.php';
require_once '../admin/email.php';

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
    // Get restaurant name for email
    $stmt = $pdo->prepare("SELECT name FROM restaurants WHERE id = ?");
    $stmt->execute([$restaurant_id]);
    $restaurant = $stmt->fetch();
    $restaurant_name = $restaurant['name'];

    // Insert the comment
    $sql = "INSERT INTO comments (restaurant_id, user_name, comment_text, created_at) VALUES (?, ?, ?, NOW())";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$restaurant_id, $user_name, $comment_text]);

    // === EMAIL TESTING SECTION ===
    echo "<div style='background:#fff3cd; border:1px solid #ffc107; padding:20px; margin:20px; border-radius:5px;'>";
    echo "<h3>📧 Testing Email Notification...</h3>";

    $email_result = sendCommentNotification($restaurant_id, $restaurant_name, $user_name, $comment_text);

    if ($email_result === true) {
        echo "<p style='color:green; font-weight:bold;'>✅ SUCCESS: Email sent successfully!</p>";
        echo "<p><strong>Note:</strong> Check your inbox at " . htmlspecialchars(ADMIN_EMAIL) . "</p>";
    } else {
        echo "<p style='color:red; font-weight:bold;'>❌ FAILED: Email could not be sent.</p>";
        echo "<p><strong>Debug:</strong> The email function returned false. Check error logs.</p>";
    }
    echo "</div>";

    // === END EMAIL TESTING ===

    // Redirect after showing result
    header("Location: restaurant.php?id=" . $restaurant_id);
    exit;

} catch(PDOException $e) {
    die("Error adding comment: " . $e->getMessage());
}
?>