<?php
require_once '../includes/auth.php';
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $task_id = $_POST['task_id'];
    $user_id = $_SESSION['user_id'];

    // Make sure user owns this task before deleting
    $stmt = $conn->prepare("DELETE FROM tasks WHERE id = ? AND user_id = ?");
    $stmt->execute([$task_id, $user_id]);
}

header("Location: /task-flow/dashboard.php");
exit();
?>