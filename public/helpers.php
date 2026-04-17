<?php
// Helper functions for public pages

function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function formatDate($date) {
    return date('F j, Y, g:i a', strtotime($date));
}
?>