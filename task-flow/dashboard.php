<?php
require_once 'includes/auth.php';
require_once 'includes/header.php';
require_once 'config/database.php';

$user_id = $_SESSION['user_id'];
$filter  = $_GET['filter'] ?? 'all';
$search  = trim($_GET['search'] ?? '');

// Build query based on filter and search
$where = "WHERE user_id = :user_id";
$params = ['user_id' => $user_id];

if ($filter === 'pending' || $filter === 'completed') {
    $where .= " AND status = :status";
    $params['status'] = $filter;
}

if (!empty($search)) {
    $where .= " AND title LIKE :search";
    $params['search'] = '%' . $search . '%';
}

$stmt = $conn->prepare("SELECT * FROM tasks $where ORDER BY created_at DESC");
$stmt->execute($params);
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="dashboard">

    <!-- Sidebar -->
    <aside class="sidebar">
        <h1 class="logo">✅ TaskFlow</h1>
        <nav>
          <a href="dashboard.php" class="<?= $filter === 'all' ? 'active' : '' ?>">
              All Tasks
          </a>
          <a href="dashboard.php?filter=pending" class="<?= $filter === 'pending' ? 'active' : '' ?>">
              Pending
          </a>
          <a href="dashboard.php?filter=completed" class="<?= $filter === 'completed' ? 'active' : '' ?>">
              Completed
          </a>
      </nav>
        <div class="sidebar-footer">
            <span>👤 <?= htmlspecialchars($_SESSION['username']) ?></span>
            <a href="logout.php">Logout</a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main">
        <!-- Search Bar -->
        <div class="search-bar">
            <input type="text" id="searchInput"
                  placeholder="🔍 Search tasks..."
                  value="<?= htmlspecialchars($search) ?>">
        </div>
        <!-- Add Task Form -->
        <div class="card">
            <h2>Add New Task</h2>
            <form action="actions/add_task.php" method="POST" class="task-form">
                <input type="text" name="title" placeholder="Task title..." required>
                <select name="priority">
                    <option value="low">Low</option>
                    <option value="medium" selected>Medium</option>
                    <option value="high">High</option>
                </select>
                <input type="date" name="due_date">
                <button type="submit">Add Task</button>
            </form>
        </div>

        <!-- Task List -->
        <div class="card">
            <h2>My Tasks <span class="task-count"><?= count($tasks) ?></span></h2>

            <?php if (empty($tasks)): ?>
                <p class="empty">No tasks yet. Add one above!</p>
            <?php else: ?>
                <ul class="task-list">
                    <?php foreach ($tasks as $task): ?>
                        <li class="task-item <?= $task['status'] === 'completed' ? 'done' : '' ?>"
                        id="task-<?= $task['id'] ?>"
                        data-title="<?= htmlspecialchars(strtolower($task['title'])) ?>">

                            <div class="task-left">
                                <!-- Toggle complete (AJAX) -->
                                <button type="button"
                                    class="btn-check"
                                    data-id="<?= $task['id'] ?>"
                                    data-status="<?= $task['status'] ?>"
                                    onclick="toggleTask(this)">
                                    <?= $task['status'] === 'completed' ? '✅' : '⬜' ?>
                                </button>

                                <div class="task-info">
                                    <span class="task-title"><?= htmlspecialchars($task['title']) ?></span>
                                    <div class="task-meta">
                                        <span class="badge <?= $task['priority'] ?>"><?= ucfirst($task['priority']) ?></span>
                                        <?php if ($task['due_date']): ?>
                                            <span class="due">📅 <?= $task['due_date'] ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="task-actions">
                                <!-- Edit button -->
                                <button class="btn-edit"
                                    onclick="openEdit(
                                        '<?= $task['id'] ?>',
                                        '<?= htmlspecialchars($task['title'], ENT_QUOTES) ?>',
                                        '<?= $task['priority'] ?>',
                                        '<?= $task['due_date'] ?>'
                                    )">✏️ Edit</button>

                               <!-- Delete (AJAX) -->
                                <button type="button"
                                    class="btn-delete"
                                    data-id="<?= $task['id'] ?>"
                                    onclick="deleteTask(this)">
                                    🗑️ Delete
                                </button>
                            </div>

                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>

    </main>
</div>

<!-- Edit Modal -->
<div class="modal-overlay" id="editModal">
    <div class="modal">
        <h3>Edit Task</h3>
        <form action="actions/edit_task.php" method="POST">
            <input type="hidden" name="task_id" id="edit_task_id">
            <input type="text"   name="title"   id="edit_title"   placeholder="Task title..." required>
            <select name="priority" id="edit_priority">
                <option value="low">Low</option>
                <option value="medium">Medium</option>
                <option value="high">High</option>
            </select>
            <input type="date" name="due_date" id="edit_due_date">
            <div class="modal-buttons">
                <button type="submit">Save Changes</button>
                <button type="button" onclick="closeEdit()">Cancel</button>
            </div>
        </form>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>