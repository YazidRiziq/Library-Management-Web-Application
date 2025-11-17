<div class="bg-white rounded-lg shadow p-6">
  <h2 class="text-2xl font-semibold mb-3">Welcome, <?= htmlspecialchars($_SESSION['name']) ?> 👋</h2>
  <p class="text-gray-600">
    It’s great to see you at the LIBRIX - Library Management System.<br>
    Here’s what you can do today:<br>
    <br>
    🔍 <b>Book List</b> - Browse and explore the latest books available in our collection.<br>
    📖 <b>Borrowed Books</b> - Check your active borrowings and see which books you’re currently reading.<br>
    🕓 <b>Loan History</b> - Review your borrowing history to revisit your past favorites.<br>
    <br>
    <b><i>#Note :</i></b><br>
    Each borrowed book can be kept for 7 days from the date of borrowing.
    If you wish to keep the book longer, you’ll need to <b>renew your borrowing</b> before the return deadline expires.
    <br>
    Please note that once the due date has passed, the book must be returned before you can borrow it again.
    Make sure to check your loan status regularly to avoid late returns or fines. <br>
    <br>
    Stay curious and keep reading — every page turned brings new knowledge! ✨
  </p>

  <div class="grid grid-cols-3 gap-4 mt-6">
    <a href="dashboard.php?page=book_list">
      <div class="bg-blue-50 p-4 rounded shadow text-center transition-all duration-300 hover:shadow-lg">
        <h3 class="text-lg font-medium">🔍 <b>Book List</b></h3>
        <p class="text-gray-500">Find the book you need</p>
      </div>
    </a>
    <a href="dashboard.php?page=manage_category">
      <div class="bg-green-50 p-4 rounded shadow text-center transition-all duration-300 hover:shadow-lg">
        <h3 class="text-lg font-medium">📖 <b>Borrowed Books</b></b></h3>
        <p class="text-gray-500">See what books you are borrowing</p>
      </div>
    </a>
    <a href="dashboard.php?page=manage_member">
      <div class="bg-yellow-50 p-4 rounded shadow text-center transition-all duration-300 hover:shadow-lg">
        <h3 class="text-lg font-medium">🕓 <b>Loan History</b></h3>
        <p class="text-gray-500">View your Loan History</p>
      </div>
    </a>
  </div>
</div>
