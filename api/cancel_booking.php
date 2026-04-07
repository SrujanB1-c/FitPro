<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit;
}

require_once '../db.php';

$userId = $_SESSION['user_id'];
$classId = isset($_POST['class_id']) ? intval($_POST['class_id']) : 0;

if ($classId <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid class ID']);
    exit;
}

try {
    // Delete the booking
    $stmt = $pdo->prepare("DELETE FROM user_classes WHERE user_id = ? AND class_id = ?");
    $stmt->execute([$userId, $classId]);
    
    if ($stmt->rowCount() > 0) {
        echo json_encode(['status' => 'success', 'message' => 'Booking cancelled successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Booking not found or already cancelled']);
    }
} catch(PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
