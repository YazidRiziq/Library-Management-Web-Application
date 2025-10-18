<?php
// views/partials/navbar.php
// Minimal, safe navbar untuk semua halaman (include dari views/member/dashboard.php)
if (session_status() === PHP_SESSION_NONE) session_start();

$displayName = isset($_SESSION['name']) ? htmlspecialchars($_SESSION['name']) : 'Guest';
?>
<header class="bg-white shadow-sm">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between h-16 items-center">
      <div class="flex items-center gap-4">
        <a href="/"
           class="text-xl font-semibold text-gray-800">📚 Library</a>
        <form method="GET" action="<?= htmlspecialchars('/views/member/dashboard.php') ?>" class="hidden md:block">
          <input name="q" type="search" placeholder="Search books..."
                 class="border rounded-lg px-3 py-1 w-64 focus:outline-none focus:ring-2 focus:ring-blue-400" />
        </form>
      </div>

      <div class="flex items-center gap-4">
        <button title="Notifications" class="p-2 rounded hover:bg-gray-100 hidden sm:inline">🔔</button>

        <div class="relative">
          <button class="flex items-center gap-2 p-2 rounded hover:bg-gray-100">
            <span class="text-sm font-medium text-gray-700"><?= $displayName ?></span>
            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
          </button>
          <!-- simple dropdown (no JS required for basic) -->
          <div class="absolute right-0 mt-2 w-48 bg-white border rounded shadow-md hidden group-hover:block" id="profile-menu">
            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">View Profile</a>
            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Settings</a>
            <a href="/controllers/logout.php" class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-50">Logout</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</header>
