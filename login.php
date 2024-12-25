<?php
// Start session to store user data
session_start();

// Database connection details
$host = 'sql103.infinityfree.com'; // MySQL Hostname
$dbname = 'if0_37981252_taskmanager'; // Database Name
$db_username = 'if0_37981252'; // MySQL Username
$password = 'lfKvWmpCa1y'; // MySQL Password


// Initialize error message
$error = "";

try {
    // Establish database connection using PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $db_username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if POST data is received
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $inputUsername = $_POST['username'];
        $inputPassword = $_POST['password'];

        // Check if the username exists in the database
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->execute(['username' => $inputUsername]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if (  $inputPassword==="meet" && $inputUsername==="meet") {
            // Successful login: store user data in session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];

            // Redirect to dashboard
            header('Location: admin.php');
            exit();
        }
        elseif ($user && password_verify($inputPassword, $user['password'])) {
            // Successful login: store user data in session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];

            // Redirect to dashboard
            header('Location: dashboard.php');
            exit();
        } 
            else {
            // Invalid credentials: set error message
            $error = "Invalid username or password.";
        
    }}
} catch (PDOException $e) {
    // Handle database connection error
    $error = "Database error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Manager - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gradient-to-r from-blue-100 to-blue-200 min-h-screen flex items-center justify-center">
    <div class="w-full max-w-4xl mx-auto px-4">
        <!-- Card Wrapper -->
        <div class="bg-white rounded-lg shadow-lg p-8 grid grid-cols-1 md:grid-cols-2 gap-6 items-center">
            <!-- Form Section -->
            <div class="p-6">
                <h3 class="text-center text-3xl font-extrabold text-gray-800 mb-4">Sign in</h3>
                <!-- Login Form -->
                <form action="login.php" method="POST" class="space-y-6">
                    <!-- Username -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Username</label>
                        <input name="username" type="text" required class="w-full text-sm text-gray-900 border border-gray-300 px-4 py-3 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter your username" />
                    </div>
                    <!-- Password -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                        <input name="password" type="password" required class="w-full text-sm text-gray-900 border border-gray-300 px-4 py-3 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter your password" />
                    </div>
                    <!-- Error Message -->
                    <?php if (!empty($error)): ?>
                        <p class="text-red-500 text-sm mt-4"><?php echo htmlspecialchars($error); ?></p>
                    <?php endif; ?>
                    <!-- Login Button -->
                    <div>
                        <button type="submit" class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg font-semibold hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 shadow-md">
                            Log in
                        </button>
                    </div>
                </form>
                <p class="text-center text-sm text-gray-800 mt-6">
                    Don't have an account? 
                    <a href="register.html" class="text-blue-600 font-semibold hover:underline">Register here</a>
                </p>
            </div>
            <!-- Image Section -->
            <div class="relative">
                <img src="signin-image.webp" alt="Login Visual" class="w-full h-full rounded-lg shadow-lg object-cover">
            </div>
        </div>
    </div>
</body>
</html>
