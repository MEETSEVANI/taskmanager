<?php
session_start();

// Check if the user is an admin
if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'meet') {
    header('Location: index.html');
    exit;
}

$username = $_SESSION['username'];

// Database connection details
$host = 'sql103.infinityfree.com'; // MySQL Hostname
$dbname = 'if0_37981252_taskmanager'; // Database Name
$db_username = 'if0_37981252'; // MySQL Username
$password = 'lfKvWmpCa1y'; // MySQL Password

    

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $db_username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Handle Clear Database Request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['clear_database'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM tasks");
        $stmt->execute();
        $message = "Database cleared successfully!";
    } catch (PDOException $e) {
        $message = "Error clearing database: " . $e->getMessage();
    }
}

// Fetch all tasks from the database
$stmt = $pdo->prepare("SELECT tasks.task_id, tasks.title, tasks.description, tasks.is_completed, users.username FROM tasks JOIN users ON tasks.id = users.id");
$stmt->execute();
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - All Tasks</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" defer></script>
</head>
<body class="bg-gray-300 min-h-screen">
    <!-- Navbar -->
    <nav class="bg-blue-600 text-white py-4 px-6 flex justify-between items-center">
        <h1 class="text-xl font-bold">Admin - Task Manager</h1>
        <div class="flex items-center space-x-4">
            <p id="username" class="font-semibold">Welcome, <?php echo htmlspecialchars($username); ?></p>
            <form method="POST" style="display:inline;">
                <button name="clear_database" type="submit" class="bg-white text-blue-600 py-2 px-4 rounded-lg hover:bg-blue-100">Clear Database</button>
            </form>
            <button id="logoutBtn" class="bg-red-500 py-2 px-4 rounded-lg hover:bg-red-600">
                <a href="logout.php" class="text-white">Log Out</a>
            </button>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto py-8">
        <?php if (isset($message)): ?>
            <div class="bg-green-100 text-green-800 p-4 rounded-lg mb-4">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        <h2 class="text-2xl font-bold text-center mb-6">All User Tasks</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white shadow-md rounded-lg">
                <thead>
                    <tr class="bg-blue-600 text-white">
                        <th class="px-6 py-3 text-left text-sm font-medium uppercase">Task ID</th>
                        <th class="px-6 py-3 text-left text-sm font-medium uppercase">Username</th>
                        <th class="px-6 py-3 text-left text-sm font-medium uppercase">Title</th>
                        <th class="px-6 py-3 text-left text-sm font-medium uppercase">Description</th>
                        <th class="px-6 py-3 text-left text-sm font-medium uppercase">Completed</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tasks as $task): ?>
                        <tr class="border-t">
                            <td class="px-6 py-4 text-sm text-gray-900"><?php echo htmlspecialchars($task['task_id']); ?></td>
                            <td class="px-6 py-4 text-sm text-gray-900"><?php echo htmlspecialchars($task['username']); ?></td>
                            <td class="px-6 py-4 text-sm text-gray-900"><?php echo htmlspecialchars($task['title']); ?></td>
                            <td class="px-6 py-4 text-sm text-gray-900"><?php echo htmlspecialchars($task['description']); ?></td>
                            <td class="px-6 py-4 text-sm text-gray-900"><?php echo $task['is_completed'] ? 'Yes' : 'No'; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>
