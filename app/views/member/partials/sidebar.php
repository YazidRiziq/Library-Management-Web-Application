<?php
// views/partials/sidebar.php
if (session_status() === PHP_SESSION_NONE) session_start();

// Build menu and mark active via ?page=...
$current = isset($_GET['page']) ? $_GET['page'] : 'home';
$menu = [
  'home' => 'Dashboard',
  'book_list' => 'Book List',
  'search' => 'Search Book',
  'borrow_request' => 'Borrow Request',
  'return_request' => 'Return Request',
  'loan_status' => 'Loan Status',
  'history' => 'Borrowing History',
];
?>
<aside class="w-64 bg-white border-r hidden md:block">
  <nav class="p-4 space-y-1">
    <?php foreach ($menu as $key => $label): ?>
      <a href="?page=<?= urlencode($key) ?>"
         class="block px-3 py-2 rounded <?= $current === $key ? 'bg-blue-50 text-blue-600 font-medium' : 'text-gray-700 hover:bg-gray-100' ?>">
         <?= htmlspecialchars($label) ?>
      </a>
    <?php endforeach; ?>
    <hr class="my-3">
    <a href="logout.php" class="block px-3 py-2 text-red-600 hover:bg-gray-100 rounded">Logout</a>
  </nav>
</aside>
