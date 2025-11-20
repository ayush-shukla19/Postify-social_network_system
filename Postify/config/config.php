<?php
// Database configuration
define("DB_HOST", "localhost");
define("DB_USER", "root");  // default XAMPP username
define("DB_PASS", "");      // default XAMPP password is empty
define("DB_NAME", "postify_db");
define("DB_PORT", 3307);

// Enable error reporting for debugging
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
?>
