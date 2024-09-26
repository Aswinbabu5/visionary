<?php
include 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Add task
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $due_date = $_POST['due_date'];

    $stmt = $conn->prepare("INSERT INTO tasks (user_id, title, description, due_date) VALUES (?, ?, ?, ?)");
    $stmt->execute([$user_id, $title, $description, $due_date]);
}

// Delete task
if (isset($_GET['delete'])) {
    $task_id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM tasks WHERE id = ? AND user_id = ?");
    $stmt->execute([$task_id, $user_id]);
}

// Mark task as completed
if (isset($_GET['complete'])) {
    $task_id = $_GET['complete'];
    $stmt = $conn->prepare("UPDATE tasks SET status = 'Completed' WHERE id = ? AND user_id = ?");
    $stmt->execute([$task_id, $user_id]);
}

// Fetch tasks
$stmt = $conn->prepare("SELECT * FROM tasks WHERE user_id = ? ORDER BY due_date ASC");
$stmt->execute([$user_id]);
$tasks = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Task Management</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h2>Your Tasks</h2>
        <form method="POST">
            <div class="form-group">
                <label>Title</label>
                <input type="text" name="title" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" class="form-control" required></textarea>
            </div>
            <div class="form-group">
                <label>Due Date</label>
                <input type="date" name="due_date" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Add Task</button>
        </form>
        <br>
        <h3>Task List</h3>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Due Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tasks as $task): ?>
                <tr>
                    <td><?= htmlspecialchars($task['title']) ?></td>
                    <td><?= htmlspecialchars($task['description']) ?></td>
                    <td><?= htmlspecialchars($task['due_date']) ?></td>
                    <td><?= htmlspecialchars($task['status']) ?></td>
                    <td>
                        <a href="?complete=<?= $task['id'] ?>" class="btn btn-success btn-sm">Mark as Completed</a>
                        <a href="?delete=<?= $task['id'] ?>" class="btn btn-danger btn-sm">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>