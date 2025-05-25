<?php
// This script handles adding new judges via POST requests and fetching the list of judges via GET requests.

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json');

require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $display_name = $_POST['display_name'] ?? '';
    if (empty($username) || empty($display_name)) {
        echo json_encode(['success' => false, 'message' => 'Username and display name are required']);
        exit;
    }
    $pdo = getDBConnection();
    try {
        $stmt = $pdo->prepare("INSERT INTO judges (username, display_name) VALUES (?, ?)");
        $stmt->execute([$username, $display_name]);
        echo json_encode(['success' => true, 'message' => 'Judge added successfully']);
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            echo json_encode(['success' => false, 'message' => 'Username already exists']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }
} else {
    $pdo = getDBConnection();
    $stmt = $pdo->query("SELECT id, username, display_name FROM judges");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}
?>