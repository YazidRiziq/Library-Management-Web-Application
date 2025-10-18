<?php
// views/member/pages/book_list.php

require_once __DIR__ . '../../../../../config/connections.php';
require_once __DIR__ . '/../../../models/BookModel.php';

if (session_status() === PHP_SESSION_NONE) session_start();

try {
    // Inisialisasi koneksi dan model
    $db = new Database();
    $conn = $db->getConnection();
    $bookModel = new BookModel($conn);

    // Ambil semua data buku
    $books = $bookModel->getAllBookList();
} catch (Exception $e) {
    $books = [];
    $error_message = $e->getMessage();
}
?>

<div class="p-6">
  <h2 class="text-2xl font-semibold mb-4">📚 Book List</h2>

  <?php if (isset($error_message)): ?>
    <div class="bg-red-100 text-red-600 p-3 rounded mb-4">
      <strong>Error:</strong> <?= htmlspecialchars($error_message) ?>
    </div>
  <?php endif; ?>

  <?php if (empty($books)): ?>
    <div class="bg-white shadow rounded p-4 text-gray-500">
      No books found in the database.
    </div>
  <?php else: ?>
    <div class="overflow-x-auto bg-white rounded shadow">
      <table class="min-w-full border-collapse">
        <thead class="bg-gray-50 border-b">
          <tr class="text-left text-sm font-medium text-gray-700">
            <th class="p-3 border-b">#</th>
            <th class="p-3 border-b">Title</th>
            <th class="p-3 border-b">Category</th>
            <th class="p-3 border-b">Author</th>
            <th class="p-3 border-b">Year</th>
            <th class="p-3 border-b text-center">Availability</th>
            <th class="p-3 border-b text-center">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($books as $index => $book): ?>
            <tr class="hover:bg-gray-50 text-sm text-gray-800">
              <td class="p-3 border-b"><?= $index + 1 ?></td>
              <td class="p-3 border-b"><?= htmlspecialchars($book['BookTitle']) ?></td>
              <td class="p-3 border-b"><?= htmlspecialchars($book['CatName']) ?></td>
              <td class="p-3 border-b"><?= htmlspecialchars($book['AutName']) ?></td>
              <td class="p-3 border-b"><?= htmlspecialchars($book['PubYear']) ?></td>
              <td class="p-3 border-b"><?= htmlspecialchars($book['Availability']) ?></td>
              <td class="p-3 border-b text-center">
                <?php if (!empty($book['Availability']) && $book['Availability'] > 0): ?>
                  <span class="text-green-600 font-medium"><?= $book['Availability'] ?> copies</span>
                <?php else: ?>
                  <span class="text-red-500 font-medium">Out of stock</span>
                <?php endif; ?>
              </td>
              <td class="p-3 border-b text-center">
                <?php if (!empty($book['Availability']) && $book['Availability'] > 0): ?>
                  <form method="POST" action="../../../controllers/BorrowingController.php">
                    <input type="hidden" name="BookID" value="<?= htmlspecialchars($book['BookID']) ?>">
                    <button type="submit"
                            class="px-3 py-1 bg-blue-600 text-white rounded text-sm hover:bg-blue-700">
                      Borrow
                    </button>
                  </form>
                <?php else: ?>
                  <button disabled
                          class="px-3 py-1 bg-gray-300 text-gray-600 rounded text-sm cursor-not-allowed">
                    Borrow
                  </button>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>
