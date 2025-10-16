<?php
session_start();
if (isset($_SESSION['role'])) {
    // Redirect langsung jika sudah login
    if ($_SESSION['role'] === 'member') {
        header("Location: ../member/dashboard.php");
        exit();
    } else if ($_SESSION['role'] === 'officer') {
        header("Location: ../officer/dashboard.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex justify-center items-center min-h-screen">

    <div class="bg-white p-8 rounded-2xl shadow-xl w-full max-w-md">
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Library Management Login</h2>

        <?php if (isset($_GET['error'])): ?>
            <p class="text-red-500 text-center mb-4"><?php echo htmlspecialchars($_GET['error']); ?></p>
        <?php endif; ?>

        <form action="../../controllers/login_process.php" method="POST" class="space-y-4">
            
            <div>
                <label class="block text-gray-700 font-medium mb-1">Login As</label>
                <select name="role" class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-400">
                    <option value="member">Member</option>
                    <option value="officer">Officer</option>
                </select>
            </div>

            <div>
                <label class="block text-gray-700 font-medium mb-1">Email / Username</label>
                <input type="text" name="username" required class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-400" placeholder="Enter your email or username">
            </div>

            <div>
                <label class="block text-gray-700 font-medium mb-1">Password</label>
                <input type="password" name="password" required class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-blue-400" placeholder="Enter your password">
            </div>

            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 rounded-lg transition">
                Login
            </button>

            <p class="text-center text-gray-600 text-sm mt-4">
                Don’t have an account? 
                <a href="register.php" class="text-blue-600 hover:underline">Register here</a>
            </p>
        </form>
    </div>

</body>
</html>
