<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../db.php';

$type = isset($_GET['type']) ? $_GET['type'] : '';

try {
    if (!empty($type) && $type != 'All') {
        $stmt = $pdo->prepare("SELECT * FROM classes WHERE type = ?");
        $stmt->execute([$type]);
    } else {
        $stmt = $pdo->query("SELECT * FROM classes");
    }
    
    $classes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'status' => 'success',
        'data' => $classes
    ]);
} catch(PDOException $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>
