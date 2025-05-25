<?php
require_once 'db.php';
$pdo = getDBConnection();
if ($pdo) {
    echo "Database connection successful!";
} else {
    echo "Database connection failed!";
}
?>