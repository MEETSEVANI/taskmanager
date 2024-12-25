<?php
session_start();
session_regenerate_id(true);
if (!isset($_SESSION['username'])) {
    header('Location: index.html');
    exit;
}

$username = $_SESSION['username'];
$user_id = $_SESSION['user_id'];

// Database connection details
$host = 'sql103.infinityfree.com'; // MySQL Hostname
$dbname = 'if0_37981252_taskmanager'; // Database Name
$db_username = 'if0_37981252'; // MySQL Username
$password = 'lfKvWmpCa1y'; // MySQL Password




try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $db_username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Fetch tasks for the logged-in user
$stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = :id");
$stmt->execute(['id' => $user_id]);
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Manager - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    />
    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
        defer
    ></script>
</head>
<body class="bg-gray-300 min-h-screen">
    <!-- Navbar -->
    <nav class="bg-blue-600 text-white py-4 px-6 flex justify-between items-center">
        <h1 class="text-xl font-bold">Task Manager</h1>
        <div class="flex items-center space-x-4">
            <p id="username" class="font-semibold " style="padding-top:3%">Welcome, <?php echo $username; ?></p>
            <button onclick="openModal('taskModal')" class="bg-white text-blue-600 py-2 px-4 rounded-lg hover:bg-blue-100">Add Task</button>
            <button id="logoutBtn" class="bg-red-500 py-2 px-4 rounded-lg hover:bg-red-600"> <a href="logout.php" class="text-white">Log Out</a></button>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-6xl mx-auto py-8">
        <!-- Task List -->
        <div id="taskList" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($tasks as $task): ?>
            <div class="bg-white rounded-lg shadow-md p-6 h-full" data-task-id="<?php echo $task['task_id']; ?>">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($task['title']); ?></h5>
                        <textarea name="description" readonly required class="w-full text-sm text-gray-900 border border-gray-300 px-4 py-3 rounded-lg focus:outline-none"><?php echo htmlspecialchars($task['description']); ?></textarea>
                        <div class="flex items-center justify-between mt-4">
                            <label>
                                <input type="checkbox" class="mr-2" onchange="updateStatus(<?php echo $task['task_id']; ?>, this.checked)" <?php echo $task['is_completed'] ? 'checked' : ''; ?>> Completed
                            </label>
                            <div class="space-x-2">
                                <button class="edit-task text-blue-600 hover:underline">Edit</button>
                                <button class="text-red-600 hover:underline" onclick="deleteTask(<?php echo $task['task_id']; ?>, this)">Delete</button>
                            </div>  
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </main>

    <!-- Add Task Modal -->     
    <div id="taskModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center">
        <div class="bg-white rounded-lg shadow-lg p-6 max-w-md w-full">
            <h2 class="text-xl font-bold mb-4">Add New Task</h2>
            <form id="taskForm">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Title</label>
                    <input type="text" name="title" required class="w-full text-sm text-gray-900 border border-gray-300 px-4 py-3 rounded-lg focus:outline-none">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" required class="w-full text-sm text-gray-900 border border-gray-300 px-4 py-3 rounded-lg focus:outline-none"></textarea>
                </div>
                <div class="flex justify-end space-x-4">
                    <button type="button" class="bg-gray-300 py-2 px-4 rounded-lg hover:bg-gray-400" onclick="closeModal('taskModal')">Cancel</button>
                    <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700">Add Task</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Task Modal -->
    <div id="editTaskModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-lg p-6 max-w-md w-full">
        <h2 class="text-xl font-bold mb-4">Edit Task</h2>
        <form id="editTaskForm">
          
            <input type="hidden" name="task_id" id="editTaskId">

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Title</label>
                <input type="text" name="editTitle" required class="w-full text-sm text-gray-900 border border-gray-300 px-4 py-3 rounded-lg focus:outline-none">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea name="editDescription" required class="w-full text-sm text-gray-900 border border-gray-300 px-4 py-3 rounded-lg focus:outline-none"></textarea>
            </div>
            <div class="flex justify-end space-x-4">
                <button type="button" class="bg-gray-300 py-2 px-4 rounded-lg hover:bg-gray-400" onclick="closeModal('editTaskModal')">Cancel</button>
                <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700">Save Changes</button>
            </div>
        </form>
    </div>
</div>


    <script>
        let currentTaskId = null;
        let currentTaskCard = null;

        const openModal = (modalId) => document.getElementById(modalId).classList.remove('hidden');
        const closeModal = (modalId) => document.getElementById(modalId).classList.add('hidden');

        // Add New Task
        document.getElementById('taskForm').addEventListener('submit', async (event) => {
            event.preventDefault();

            const formData = new FormData(event.target);
            formData.append('action', 'add');

            const response = await fetch('task_handler.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                location.reload(); // Reload to fetch tasks
            } else {
                alert(result.error);
            }
        });

        // Open Edit Task Modal
        document.addEventListener('click', (event) => {
            if (event.target.classList.contains('edit-task')) {
                const taskCard = event.target.closest('.bg-white');
                currentTaskId = taskCard.getAttribute('data-task-id');
                const title = taskCard.querySelector('.card-title').innerText;
                const description = taskCard.querySelector('textarea').value;

                // Populate form fields
                document.querySelector('#editTaskForm input[name="editTitle"]').value = title;
                document.querySelector('#editTaskForm textarea[name="editDescription"]').value = description;
                document.querySelector('#editTaskId').value = currentTaskId; // Set task_id in hidden input

                currentTaskCard = taskCard;
                openModal('editTaskModal');
            }
        });

        document.getElementById('editTaskForm').addEventListener('submit', (event) => {
    event.preventDefault();

    const title = event.target.elements['editTitle'].value.trim();
    const description = event.target.elements['editDescription'].value.trim();

    if (title === '') {
        alert('Title cannot be empty!');
        return;
    }

    const formData = new FormData(event.target);
    formData.append('action', 'edit');
    formData.append('task_id', currentTaskId);

    fetch('task_handler.php', {
        method: 'POST',
        body: formData,
    })
        .then((response) => response.json())
        .then((result) => {
            if (result.success) {
                currentTaskCard.querySelector('.card-title').innerText = title;
                currentTaskCard.querySelector('textarea').value = description;
                closeModal('editTaskModal');
            } else {
                alert(result.error);
            }
        })
        .catch((error) => {
            console.error('Error:', error);
        });
});

        // Delete Task
        const deleteTask = async (taskId, button) => {
            if (!confirm('Are you sure you want to delete this task?')) return;

            const formData = new FormData();
            formData.append('action', 'delete');
            formData.append('task_id', taskId);

            const response = await fetch('task_handler.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                button.closest('.bg-white').remove();
            } else {
                alert(result.error);
            }
        };

        // Update Task Status
        const updateStatus = async (taskId, isCompleted) => {
            const formData = new FormData();
            formData.append('action', 'update_status');
            formData.append('task_id', taskId);
            formData.append('is_completed', isCompleted ? 1 : 0);

            const response = await fetch('task_handler.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (!result.success) {
                alert(result.error);
            }
        };

        // Log Out
        document.getElementById('logoutBtn').addEventListener('click', () => {
            window.location.href = 'index.html'; 
        });
    </script>
</body>
</html>
