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
    // Check if class exists
    $stmt = $pdo->prepare("SELECT id, name, trainer, schedule_time FROM classes WHERE id = ?");
    $stmt->execute([$classId]);
    $classData = $stmt->fetch();
    
    if (!$classData) {
        echo json_encode(['status' => 'error', 'message' => 'Class not found']);
        exit;
    }

    // Book it
    $stmt = $pdo->prepare("INSERT IGNORE INTO user_classes (user_id, class_id, class_name, trainer, schedule_time) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$userId, $classId, $classData['name'], $classData['trainer'], $classData['schedule_time']]);
    
    if ($stmt->rowCount() > 0) {
        echo json_encode(['status' => 'success', 'message' => 'Class booked successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Class already booked']);
    }
} catch(PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
