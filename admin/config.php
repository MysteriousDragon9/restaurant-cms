<?php
/**
 * Restaurant CMS Configuration
 * All system settings and constants
 */

// Database Settings
define('DB_HOST', 'localhost');
define('DB_NAME', 'restaurant_cms');
define('DB_USER', 'root');
define('DB_PASS', '');

// Site Settings
define('ADMIN_EMAIL','alandgoldroger@gmail.com'); //'admin@winnipegrestaurants.com');
define('SITE_NAME', 'Winnipeg Restaurants');
define('SITE_URL', 'http://localhost:31337/my-restaurant-cms');


// Pagination Settings
define('ITEMS_PER_PAGE', 6);

// Upload Settings
define('UPLOAD_PATH', 'uploads/images/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB

// Email Settings
define('EMAIL_ENABLED', true);
define('EMAIL_FROM_NAME', 'Winnipeg Restaurants CMS');
define('EMAIL_FROM_EMAIL', 'noreply@winnipegrestaurants.com');

define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_USER', 'alandgoldroger@gmail.com'); // Your Gmail address
define('SMTP_PASS', 'oussegyrebpfpdmx'); // Your Gmail App Password
define('SMTP_PORT', 587);
define('SMTP_SECURE', 'tls');

// Security Settings
define('HASH_ALGORITHM', PASSWORD_DEFAULT);
define('SESSION_LIFETIME', 3600); // 1 hour

// Error Reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Timezone
date_default_timezone_set('America/Winnipeg');

// Site Version
define('CMS_VERSION', '1.0.0');

// Maintenance Mode (set to true to disable the site)
define('MAINTENANCE_MODE', false);

// Debug Mode (set to false for production)
define('DEBUG_MODE', true);

// reCAPTCHA Settings (if you want to store them here too)
define('RECAPTCHA_SITE_KEY', '6Ld_your_site_key_here');
define('RECAPTCHA_SECRET_KEY', '6Ld_your_secret_key_here');
?>
if (function_exists('opcache_reset')) {
    opcache_reset();
}