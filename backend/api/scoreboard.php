<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json');

require_once __DIR__ . '/../src/db.php';

// Submits a score for a user by a judge
function submitScore($judge_id, $user_id, $score) {
    if (empty($judge_id) || empty($user_id) || empty($score)) {
        return ['success' => false, 'message' => 'All fields are required'];
    }

    if (!is_numeric($score) || $score < 1 || $score > 100) {
        return ['success' => false, 'message' => 'Score must be between 1 and 100'];
    }

    $pdo = getDBConnection();
    if (!$pdo) {
        return ['success' => false, 'message' => 'Database connection failed'];
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO scores (judge_id, user_id, score) VALUES (?, ?, ?)");
        $stmt->execute([$judge_id, $user_id, $score]);
        return ['success' => true, 'message' => 'Score submitted successfully'];
    } catch (PDOException $e) {
        error_log('Database error: ' . $e->getMessage());
        return ['success' => false, 'message' => 'Error submitting score'];
    }
}

// Retrieves the scoreboard with total scores
function getScoreboard() {
    $pdo = getDBConnection();
    if (!$pdo) {
        return ['error' => 'Database connection failed'];
    }

    try {
        $stmt = $pdo->prepare("
            SELECT u.id, u.display_name, COALESCE(SUM(s.score), 0) as total_score
            FROM users u
            LEFT JOIN scores s ON u.id = s.user_id
            GROUP BY u.id, u.display_name
            ORDER BY total_score DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log('Database error: ' . $e->getMessage());
        return ['error' => 'Error fetching scoreboard'];
    }
}

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $judge_id = $_POST['judge_id'] ?? '';
        $user_id = $_POST['user_id'] ?? '';
        $score = $_POST['score'] ?? '';
        echo json_encode(submitScore($judge_id, $user_id, $score));
    } elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
        echo json_encode(getScoreboard());
    } else {
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
    }
} catch (Exception $e) {
    error_log('Unexpected error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'An unexpected error occurred']);
}
?>
