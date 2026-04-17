<?php
require_once 'config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require_once '../vendor/autoload.php';

function sendCommentNotification($restaurant_id, $restaurant_name, $commenter_name, $comment_text) {
    $mail = new PHPMailer(true);

    try {
        // Enable detailed SMTP debugging
        $mail->SMTPDebug = 2; // 2 = Show server conversation + errors
        $mail->Debugoutput = 'echo'; // Output to screen

        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USER;
        $mail->Password = SMTP_PASS;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = SMTP_PORT;

        $mail->setFrom(SMTP_USER, SITE_NAME);
        $mail->addAddress(ADMIN_EMAIL);

        $mail->isHTML(true);
        $mail->Subject = 'New Comment Posted - ' . SITE_NAME;
        $mail->Body = "New comment from $commenter_name on $restaurant_name";
        $mail->AltBody = "New comment from $commenter_name on $restaurant_name";

        $mail->send();
        return true;

    } catch (Exception $e) {
        // Show error on screen
        echo "<div style='background:#f8d7da; border:1px solid #f5c6cb; padding:20px; margin:20px; border-radius:5px;'>";
        echo "<h3>❌ SMTP Error Details:</h3>";
        echo "<p><strong>Error Code:</strong> " . $e->getCode() . "</p>";
        echo "<p><strong>Error Message:</strong> " . htmlspecialchars($mail->ErrorInfo) . "</p>";
        echo "<p><strong>SMTP Host:</strong> " . htmlspecialchars(SMTP_HOST) . "</p>";
        echo "<p><strong>SMTP User:</strong> " . htmlspecialchars(SMTP_USER) . "</p>";
        echo "<p><strong>SMTP Port:</strong> " . htmlspecialchars(SMTP_PORT) . "</p>";
        echo "<p><strong>App Password:</strong> [HIDDEN - Check your config.php]</p>";
        echo "</div>";
        return false;
    }
}
?>