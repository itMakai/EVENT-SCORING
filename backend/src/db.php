<?php
function getDBConnection() {
    $host = 'sql100.infinityfree.com';
    $dbname = 'if0_39077321_event_scoring';
    $username = 'if0_39077321'; 
    $password = 'Danmak6857'; 
    
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
}
?>