<?php
session_start();

if (!isset($_SESSION['member_id'])) {
  header('Location: ../auth/login.php');
  exit;
}

$page = isset($_GET['page']) ? $_GET['page'] : 'home';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Officer Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

  <div class="flex h-screen">
    <!-- Sidebar -->
    <?php include 'partials/sidebar.php'; ?>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col">
      <?php include 'partials/navbar.php'; ?>

      <main class="flex-1 p-6 overflow-y-auto">
        <?php
          $file = "pages/{$page}.php";
          if (file_exists($file)) {
            include $file;
          } else {
            echo "<div class='text-gray-500'>Page not found.</div>";
          }
        ?>
      </main>
    </div>
  </div>
</body>
</html>
