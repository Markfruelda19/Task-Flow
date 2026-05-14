<?php
require_once '../includes/auth.php';
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id  = $_SESSION['user_id'];
    $title    = trim($_POST['title']);
    $priority = $_POST['priority'];
    $due_date = !empty($_POST['due_date']) ? $_POST['due_date'] : null;

    if (!empty($title)) {
        $stmt = $conn->prepare(
            "INSERT INTO tasks (user_id, title, priority, due_date) VALUES (?, ?, ?, ?)"
        );
        $stmt->execute([$user_id, $title, $priority, $due_date]);
    }
}

header("Location: /task-flow/dashboard.php");
exit();
?>