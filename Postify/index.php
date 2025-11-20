<?php
require_once "classes/Database.php";

$db = new Database();

if ($db->conn) {
    echo "✅ Database connected successfully!";
} else {
    echo "❌ Failed to connect!";
}
?>
