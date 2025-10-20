<div class="bg-white rounded-lg shadow p-6">
  <h2 class="text-2xl font-semibold mb-3">Welcome, <?= htmlspecialchars($_SESSION['name']) ?> 👋</h2>
  <p class="text-gray-600">You can manage books, categories, members, borrowing transactions and return books from this dashboard.</p>

  <div class="grid grid-cols-3 gap-4 mt-6">
    <a href="dashboard.php?page=manage_books">
      <div class="bg-blue-50 p-4 rounded shadow text-center transition-all duration-300 hover:shadow-lg">
        <h3 class="text-lg font-medium">📚 Books</h3>
        <p class="text-gray-500">Manage all book data</p>
      </div>
    </a>
    <a href="dashboard.php?page=manage_category">
      <div class="bg-green-50 p-4 rounded shadow text-center transition-all duration-300 hover:shadow-lg">
        <h3 class="text-lg font-medium">🏷️ Manage Category</h3>
        <p class="text-gray-500">Manage all book category</p>
      </div>
    </a>
    <a href="dashboard.php?page=manage_member">
      <div class="bg-yellow-50 p-4 rounded shadow text-center transition-all duration-300 hover:shadow-lg">
        <h3 class="text-lg font-medium">👥 Members</h3>
        <p class="text-gray-500">View and manage member data</p>
      </div>
    </a>
    <a href="dashboard.php?page=manage_borrowing">
      <div class="bg-red-50 p-4 rounded shadow text-center transition-all duration-300 hover:shadow-lg">
        <h3 class="text-lg font-medium">📦 Borrowings</h3>
        <p class="text-gray-500">Track and process borrowing</p>
      </div>
    </a>
    <a href="dashboard.php?page=manage_returnbook">
      <div class="bg-purple-50 p-4 rounded shadow text-center transition-all duration-300 hover:shadow-lg">
        <h3 class="text-lg font-medium">📊 Return Book</h3>
        <p class="text-gray-500">Track Loan History and Return Book</p>
      </div>
    </a>
  </div>
</div>
