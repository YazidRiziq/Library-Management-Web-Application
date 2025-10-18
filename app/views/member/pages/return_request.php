<?php
// views/member/pages/return_request.php

require_once __DIR__ . '../../../../../config/connections.php';
require_once __DIR__ . '/../../../models/BorrowingModel.php';

if (session_status() === PHP_SESSION_NONE) session_start();

// Pastikan user login sebagai member
if (!isset($_SESSION['member_id'])) {
  header("Location: ../../../views/member/login.php");
  exit;
}

try {
  $db = new Database();
  $conn = $db->getConnection();
  $borrowingModel = new BorrowingModel($conn);

  // Jika ada request pengembalian dari tombol
  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['BorrowingID'])) {
    $borrowingID = $_POST['BorrowingID'];
    $borrowingModel->requestReturn($borrowingID);
    $success_message = "Book request has been submitted successfully!";
  }

  // Ambil semua buku yang sedang dipinjam (belum dikembalikan)
  $borrowedBooks = $borrowingModel->getActiveBorrowings($_SESSION['MemberID']);
} catch (Exception $e) {
  $borrowedBooks = [];
  $error_message = $e->getMessage();
}
?>

<div class="p-6">
  <h2 class="text-2xl font-semibold mb-4">📦 Return Book Request</h2>

  <?php if (isset($success_message)): ?>
    <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
      ✅ <?= htmlspecialchars($success_message) ?>
    </div>
  <?php endif; ?>

  <?php if (isset($error_message)): ?>
    <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
      ⚠️ <?= htmlspecialchars($error_message) ?>
    </div>
  <?php endif; ?>

  <?php if (empty($borrowedBooks)): ?>
    <div class="bg-white shadow rounded p-4 text-gray-500">
      You currently have no active borrowings to return.
    </div>
  <?php else: ?>
    <div class="overflow-x-auto bg-white rounded shadow">
      <table class="min-w-full border-collapse">
        <thead class="bg-gray-50 border-b text-gray-700">
          <tr class="text-left text-sm font-medium">
            <th class="p-3 border-b">#</th>
            <th class="p-3 border-b">Book Title</th>
            <th class="p-3 border-b">Borrow Date</th>
            <th class="p-3 border-b">Due Date</th>
            <th class="p-3 border-b text-center">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($borrowedBooks as $i => $book): ?>
            <tr class="hover:bg-gray-50 text-sm text-gray-800">
              <td class="p-3 border-b"><?= $i + 1 ?></td>
              <td class="p-3 border-b"><?= htmlspecialchars($book['BookTitle']) ?></td>
              <td class="p-3 border-b"><?= htmlspecialchars($book['BorrowDate']) ?></td>
              <td class="p-3 border-b"><?= htmlspecialchars($book['DueDate']) ?></td>
              <td class="p-3 border-b text-center">
                <?php if ($book['ReturnStatus'] === 'Requested'): ?>
                  <span class="text-yellow-600 font-medium">Requested</span>
                <?php else: ?>
                  <form method="POST" onsubmit="return confirm('Are you sure you want to request return for this book?');">
                    <input type="hidden" name="BorrowingID" value="<?= htmlspecialchars($book['BorrowingID']) ?>">
                    <button type="submit" class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">
                      Request Return
                    </button>
                  </form>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>
