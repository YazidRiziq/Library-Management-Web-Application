<div class="bg-white rounded-lg shadow p-6">
  <h2 class="text-2xl font-semibold mb-3">Welcome, <?= htmlspecialchars($_SESSION['name']) ?> 👋</h2>
  <p class="text-gray-600">You can manage books, categories, members, and borrowing transactions from this dashboard.</p>

  <div class="grid grid-cols-3 gap-4 mt-6">
    <div class="bg-blue-50 p-4 rounded shadow text-center">
      <h3 class="text-lg font-medium">📚 Books</h3>
      <p class="text-gray-500">Manage all book data</p>
    </div>
    <div class="bg-green-50 p-4 rounded shadow text-center">
      <h3 class="text-lg font-medium">👥 Members</h3>
      <p class="text-gray-500">View and manage member data</p>
    </div>
    <div class="bg-yellow-50 p-4 rounded shadow text-center">
      <h3 class="text-lg font-medium">📦 Borrowings</h3>
      <p class="text-gray-500">Track and process borrowing</p>
    </div>
  </div>
</div>
