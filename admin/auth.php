<?php
require_once 'db.php';
session_start();

// Check if it is user logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}
// check it is admin
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

//if havent logged in
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}
//if not admin
function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        header('Location: ../public/index.php');
        exit;
    }
}
// Sanitize input
function sanitize($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}
function formatDate($date) {
    return date('F j, Y, g:i a', strtotime($date));
}
?>