<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../db.php';

$type = isset($_GET['type']) ? $_GET['type'] : '';
$isLoggedIn = isset($_SESSION['user_id']);
$userId = $isLoggedIn ? $_SESSION['user_id'] : null;

try {
    if (!empty($type) && $type != 'All') {
        $stmt = $pdo->prepare("SELECT * FROM classes WHERE type = ?");
        $stmt->execute([$type]);
    } else {
        $stmt = $pdo->query("SELECT * FROM classes");
    }
    
    $classes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $bookedClassIds = [];
    if ($isLoggedIn) {
        $bookStmt = $pdo->prepare("SELECT class_id FROM user_classes WHERE user_id = ?");
        $bookStmt->execute([$userId]);
        $bookedClassIds = $bookStmt->fetchAll(PDO::FETCH_COLUMN);
    }

    foreach ($classes as &$class) {
        $class['is_booked'] = in_array($class['id'], $bookedClassIds);
    }
    
    echo json_encode([
        'status' => 'success',
        'is_logged_in' => $isLoggedIn,
        'data' => $classes
    ]);
} catch(PDOException $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>
