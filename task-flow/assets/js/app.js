// ─── Edit Modal ───────────────────────────────────────────
function openEdit(id, title, priority, due_date) {
    document.getElementById('edit_task_id').value  = id;
    document.getElementById('edit_title').value    = title;
    document.getElementById('edit_priority').value = priority;
    document.getElementById('edit_due_date').value = due_date;
    document.getElementById('editModal').classList.add('active');
}

function closeEdit() {
    document.getElementById('editModal').classList.remove('active');
}

document.getElementById('editModal').addEventListener('click', function (e) {
    if (e.target === this) closeEdit();
});


// ─── AJAX Toggle ──────────────────────────────────────────
function toggleTask(btn) {
    const taskId     = btn.dataset.id;
    const curStatus  = btn.dataset.status;
    const newStatus  = curStatus === 'pending' ? 'completed' : 'pending';

    fetch('/task-flow/actions/ajax_toggle.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ task_id: taskId, new_status: newStatus })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            const li = document.getElementById('task-' + taskId);

            // Update button
            btn.dataset.status = newStatus;
            btn.textContent    = newStatus === 'completed' ? '✅' : '⬜';

            // Toggle strikethrough style
            li.classList.toggle('done', newStatus === 'completed');

            showToast(newStatus === 'completed' ? 'Task completed! 🎉' : 'Task reopened.');
        }
    })
    .catch(() => showToast('Something went wrong.', 'error'));
}


// ─── AJAX Delete ──────────────────────────────────────────
function deleteTask(btn) {
    if (!confirm('Delete this task?')) return;

    const taskId = btn.dataset.id;

    fetch('/task-flow/actions/ajax_delete.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ task_id: taskId })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            const li = document.getElementById('task-' + taskId);
            li.style.transition = 'opacity 0.3s';
            li.style.opacity    = '0';
            setTimeout(() => li.remove(), 300);
            showToast('Task deleted.');
        }
    })
    .catch(() => showToast('Something went wrong.', 'error'));
}


// ─── Live Search ──────────────────────────────────────────
document.getElementById('searchInput').addEventListener('input', function () {
    const query = this.value.toLowerCase();
    const items = document.querySelectorAll('.task-item');

    items.forEach(item => {
        const title = item.dataset.title || '';
        item.style.display = title.includes(query) ? '' : 'none';
    });
});


// ─── Toast Notifications ──────────────────────────────────
function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = 'toast ' + type;
    toast.textContent = message;
    document.body.appendChild(toast);

    setTimeout(() => toast.classList.add('show'), 10);
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 300);
    }, 2500);
}