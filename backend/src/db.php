<?php
function getDBConnection() {
    $host = '127.0.0.1';
    $dbname = 'event_scoring';
    $username = 'event_user'; 
    $password = 'secure_password'; 
    
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
}
?>