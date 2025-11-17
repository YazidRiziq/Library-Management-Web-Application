<div class="bg-white shadow flex justify-between items-center px-6 py-3">
  <h1 class="text-xl font-semibold text-gray-700">📚 <strong class="text-blue-400">LIBRIX</strong> - <span class="text-stroke-black-500 text-green-500">Member</span></h1>
  <div class="flex items-center gap-3">
    <span class="text-gray-600">👋 <?= htmlspecialchars($_SESSION['name']) ?></span>
    <a href="logout.php" class="text-red-600 hover:text-red-700 font-medium">Logout</a>
  </div>
</div>
