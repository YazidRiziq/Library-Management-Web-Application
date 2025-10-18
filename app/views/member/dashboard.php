<?php
// views/member/dashboard.php
session_start();

// SECURITY: pastikan session memiliki id dan nama member
if (!isset($_SESSION['member_id']) || $_SESSION['role'] !== 'member') {
    header('Location: ../auth/login.php');
    exit();
}

// Whitelist pages to prevent path traversal
$allowed_pages = [
  'home' => __DIR__ . '/pages/home.php',
  'book_list' => __DIR__ . '/pages/book_list.php',
  'search' => __DIR__ . '/pages/book_list.php', // reuse
  'loan_status' => __DIR__ . '/pages/loan_status.php',
  'history' => __DIR__ . '/pages/history.php',
  'borrow_request' => __DIR__ . '/pages/borrow_request.php',
  'return_request' => __DIR__ . '/pages/return_request.php',
];

// Determine page
$pageKey = isset($_GET['page']) ? $_GET['page'] : 'home';
$contentFile = isset($allowed_pages[$pageKey]) ? $allowed_pages[$pageKey] : $allowed_pages['home'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Member Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">

  <?php include __DIR__ . '/partials/navbar.php'; ?>

  <div class="flex">
    <?php include __DIR__ . '/partials/sidebar.php'; ?>

    <main class="flex-1">
      <?php
        // include the content file (safe because from whitelist)
        include $contentFile;
      ?>
    </main>
  </div>

</body>
</html>
