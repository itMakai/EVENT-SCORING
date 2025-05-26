<?php
// Enable error reporting (optional for development)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Add CORS headers
header("Access-Control-Allow-Origin: https://eventscoringboard.vercel.app");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
    
}

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
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $pdo = getDBConnection();
    $stmt = $pdo->query("SELECT id, username, display_name FROM judges");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['error' => 'Method not allowed']);
}
?>
