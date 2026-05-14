<?php
require_once '../includes/auth.php';
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $task_id        = $_POST['task_id'];
    $current_status = $_POST['current_status'];
    $user_id        = $_SESSION['user_id'];

    $new_status = ($current_status === 'pending') ? 'completed' : 'pending';

    $stmt = $conn->prepare("UPDATE tasks SET status = ? WHERE id = ? AND user_id = ?");
    $stmt->execute([$new_status, $task_id, $user_id]);
}

header("Location: /task-flow/dashboard.php");
exit();
?>