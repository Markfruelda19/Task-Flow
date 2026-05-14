<?php
require_once '../includes/auth.php';
require_once '../config/database.php';

header('Content-Type: application/json');

$data       = json_decode(file_get_contents('php://input'), true);
$task_id    = $data['task_id'] ?? null;
$new_status = $data['new_status'] ?? null;
$user_id    = $_SESSION['user_id'];

if (!$task_id || !$new_status) {
    echo json_encode(['success' => false]);
    exit();
}

$stmt = $conn->prepare("UPDATE tasks SET status = ? WHERE id = ? AND user_id = ?");
$stmt->execute([$new_status, $task_id, $user_id]);

echo json_encode(['success' => true, 'new_status' => $new_status]);
?>