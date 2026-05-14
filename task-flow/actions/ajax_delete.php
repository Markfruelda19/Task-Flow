<?php
require_once '../includes/auth.php';
require_once '../config/database.php';

header('Content-Type: application/json');

$data    = json_decode(file_get_contents('php://input'), true);
$task_id = $data['task_id'] ?? null;
$user_id = $_SESSION['user_id'];

if (!$task_id) {
    echo json_encode(['success' => false]);
    exit();
}

$stmt = $conn->prepare("DELETE FROM tasks WHERE id = ? AND user_id = ?");
$stmt->execute([$task_id, $user_id]);

echo json_encode(['success' => true]);
?>