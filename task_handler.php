<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized access.']);
    exit;
}

require_once 'db_connect.php';

$user_id = $_SESSION['user_id'];
$action = $_POST['action'] ?? '';

try {
    if ($action === 'add') {
        // Add a new task
        $title = $_POST['title'] ?? '';
        $description = $_POST['description'] ?? '';

        if (empty($title)) {
            echo json_encode(['error' => 'Title is required.']);
            exit;
        }

        $stmt = $pdo->prepare("INSERT INTO tasks (id, title, description) VALUES (:id, :title, :description)");
        $stmt->execute(['id' => $user_id, 'title' => $title, 'description' => $description]);

        echo json_encode(['success' => true, 'message' => 'Task added successfully']);
    } elseif ($action === 'edit') {
        // Edit an existing task
        $task_id = $_POST['task_id'] ?? '';
        $title = trim($_POST['editTitle'] ?? '');
        $description = trim($_POST['editDescription'] ?? '');
    
        if (empty($title)) {
            echo json_encode(['success' => false, 'error' => 'Task title cannot be empty.']);
            exit;
        }
    
        // Update the task in the database
        $stmt = $pdo->prepare("UPDATE tasks SET title = :title, description = :description WHERE task_id = :task_id AND id = :id");
        $stmt->execute([
            'title' => $title,
            'description' => $description,
            'task_id' => $task_id,
            'id' => $_SESSION['user_id'],
        ]);
    
        echo json_encode(['success' => true]);
        exit;
    
       
    } elseif ($action === 'delete') {
        // Delete a task
        $task_id = $_POST['task_id'] ?? '';

        if (empty($task_id)) {
            echo json_encode(['error' => 'Task ID is required.']);
            exit;
        }

        $stmt = $pdo->prepare("DELETE FROM tasks WHERE task_id = :task_id AND id = :id");
        $stmt->execute(['task_id' => $task_id, 'id' => $user_id]);

        echo json_encode(['success' => true, 'message' => 'Task deleted successfully']);
    } elseif ($action === 'update_status') {
        // Update task completion status
        $task_id = $_POST['task_id'] ?? '';
        $is_completed = isset($_POST['is_completed']) ? (bool)$_POST['is_completed'] : false;

        if (empty($task_id)) {
            echo json_encode(['error' => 'Task ID is required.']);
            exit;
        }

        $stmt = $pdo->prepare("UPDATE tasks SET is_completed = :is_completed WHERE task_id = :task_id AND id = :id");
        $stmt->execute([
            'is_completed' => $is_completed,
            'task_id' => $task_id,
            'id' => $user_id
        ]);

        echo json_encode(['success' => true, 'message' => 'Task status updated successfully']);
    } else {
        echo json_encode(['error' => 'Invalid action.']);
    }
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
