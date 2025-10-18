<?php
// views/member/pages/loan_status.php

require_once __DIR__ . '../../../../../config/connections.php';
require_once __DIR__ . '/../../../models/BorrowingModel.php';

if (session_status() === PHP_SESSION_NONE) session_start();

// Pastikan user sudah login
if (!isset($_SESSION['MemName'])) {
  header("Location: ../../../views/member/login.php");
  exit;
}

try {
  $db = new Database();
  $conn = $db->getConnection();
  $borrowingModel = new BorrowingModel($conn);

  // Ambil semua data peminjaman milik member yang sedang login
  $loans = $borrowingModel->getAllBorrowingStatus($_SESSION['MemID']);
} catch (Exception $e) {
  $loans = [];
  $error_message = $e->getMessage();
}
?>

<div class="p-6">
  <h2 class="text-2xl font-semibold mb-4">📖 Loan Status</h2>

  <?php if (isset($error_message)): ?>
    <div class="bg-red-100 text-red-600 p-3 rounded mb-4">
      <strong>Error:</strong> <?= htmlspecialchars($error_message) ?>
    </div>
  <?php endif; ?>

  <?php if (empty($loans)): ?>
    <div class="bg-white shadow rounded p-4 text-gray-500">
      You have no current or past loan records.
    </div>
  <?php else: ?>
    <div class="overflow-x-auto bg-white rounded shadow">
      <table class="min-w-full border-collapse">
        <thead class="bg-gray-50 border-b">
          <tr class="text-left text-sm font-medium text-gray-700">
            <th class="p-3 border-b">#</th>
            <th class="p-3 border-b">Book Title</th>
            <th class="p-3 border-b">Loan Date</th>
            <th class="p-3 border-b">Due Date</th>
            <th class="p-3 border-b">Return Date</th>
            <th class="p-3 border-b text-center">Status</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($loans as $index => $loan): ?>
            <tr class="hover:bg-gray-50 text-sm text-gray-800">
              <td class="p-3 border-b"><?= $index + 1 ?></td>
              <td class="p-3 border-b"><?= htmlspecialchars($loan['BookTitle']) ?></td>
              <td class="p-3 border-b"><?= htmlspecialchars($loan['LoanDate']) ?></td>
              <td class="p-3 border-b"><?= htmlspecialchars($loan['ReturnDate']) ?></td>
              <td class="p-3 border-b">
                <?= $loan['ActualReturnDate'] ? htmlspecialchars($loan['ActualReturnDate']) : '-' ?>
              </td>
              <td class="p-3 border-b text-center">
                <?php if (!$loan['ActualReturnDate']): ?>
                  <span class="text-yellow-600 font-medium">Borrowed</span>
                <?php else: ?>
                  <span class="text-green-600 font-medium">Returned</span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>
