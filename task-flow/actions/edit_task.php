<?php
require_once '../includes/auth.php';
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $task_id  = $_POST['task_id'];
    $title    = trim($_POST['title']);
    $priority = $_POST['priority'];
    $due_date = !empty($_POST['due_date']) ? $_POST['due_date'] : null;
    $user_id  = $_SESSION['user_id'];

    if (!empty($title)) {
        $stmt = $conn->prepare(
            "UPDATE tasks SET title = ?, priority = ?, due_date = ? WHERE id = ? AND user_id = ?"
        );
        $stmt->execute([$title, $priority, $due_date, $task_id, $user_id]);
    }
}

header("Location: /task-flow/dashboard.php");
exit();
?>