<?php
// Set headers for CORS and JSON response
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json');

// Include the database connection file
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and validate POST parameters
    $judge_id = $_POST['judge_id'] ?? '';
    $user_id = $_POST['user_id'] ?? '';
    $score = $_POST['score'] ?? '';

    // Validate the score to ensure it is numeric and within the range 1-100
    if (!is_numeric($score) || $score < 1 || $score > 100) {
        echo json_encode(['success' => false, 'message' => 'Score must be between 1 and 100']);
        exit;
    }

    // Establish a database connection
    $pdo = getDBConnection();

    try {
        // Insert the score into the database
        $stmt = $pdo->prepare("INSERT INTO scores (judge_id, user_id, score) VALUES (?, ?, ?)");
        $stmt->execute([$judge_id, $user_id, $score]);

        // Return a success response
        echo json_encode(['success' => true, 'message' => 'Score submitted successfully']);
    } catch (PDOException $e) {
        // Handle database errors
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
}
?>
