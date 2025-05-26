<?php
// This script retrieves a list of users from the database and returns it as a JSON response.
header("Access-Control-Allow-Origin: https://eventscoringboard.vercel.app");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json');

require_once 'db.php';

$pdo = getDBConnection();
$stmt = $pdo->query("SELECT id, username, display_name FROM users");
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
?>