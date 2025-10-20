<?php
require_once '../../../config/connections.php';
require_once '../../models/BorrowingModel.php';
require_once '../../models/ReturnBookModel.php';

$db = new Database();
$conn = $db->getConnection();

$borrowingModel = new BorrowingModel($conn);
$returnBookModel = new ReturnBookModel($conn);

$borrowed = $borrowingModel->getActiveBorrowings();
$return = $returnBookModel->getViewReturnSuccess();

// Proses Pengembalian Buku
if (isset($_GET['CopyCode']) && isset($_GET['LoanCode'])) {
    $CopyCode = ($_GET['CopyCode']);
    $LoanCode = ($_GET['LoanCode']);
    if ($CopyCode && $LoanCode) {
        $returnBookModel->ReturnBook($CopyCode, $LoanCode);
        $returnBookModel->AddOverdueFine($CopyCode, $LoanCode);
        echo "<script>alert('Return Book Success'); window.location='dashboard.php?page=manage_returnbook';</script>";
    } else {
        echo "<script>alert('Failed to delete member.');</script>";
    }
}

// search untuk Loan List
if (isset($_GET['keyword1'])) {
  $keyword1 = trim($_GET['keyword1']);
  $newborrowings = $borrowingModel->searchBorrowings($keyword1);
  $borrowed = $newborrowings;
}

// search untuk Return History
if (isset($_GET['keyword2'])) {
  $keyword2 = trim($_GET['keyword2']);
  $returnhistory = $returnBookModel->SearchReturnSuccess($keyword2);
  $return = $returnhistory;
}

?>

<div class="mt-5 bg-white p-6 rounded-lg shadow">
    <h2 class="text-2xl font-semibold mb-4">📚 Return Book</h2>

    <form method="GET" action="dashboard.php" class="flex items-center gap-3 mb-6">
        <input type="hidden" name="page" value="manage_returnbook">
        <input type="text" name="keyword1" placeholder="Search Loan List by Member Name"
        class="border rounded p-2 w-1/3 focus:outline-none focus:ring focus:ring-blue-300">
        <button type="submit" name="search1"
        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Search</button>
    </form>

    <div>
        <table class="w-full border-collapse">
            <thead>
            <tr class="bg-gray-100 text-left border-b">
                <th class="p-3">#</th>
                <th class="p-3">Member</th>
                <th class="p-3">BookTitle</th>
                <th class="p-3 text-center">Loan Date</th>
                <th class="p-3 text-center">Due Date</th>
                <th class="p-3">Action</th>
            </tr>
            </thead>
            <tbody>
            <?php if ($borrowed && count($borrowed) > 0): ?>
                <?php $no = 1; foreach ($borrowed as $bor): ?>
                <tr class="border-b hover:bg-gray-50">
                    <td class="p-3 text-center"><?= $no++; ?></td>
                    <td class="p-3"><?= htmlspecialchars($bor['MemName']); ?></td>
                    <td class="p-3"><?= htmlspecialchars($bor['BookTitle']); ?></td>
                    <td class="p-3 text-center">
                        <?= date('d M Y', strtotime($bor['LoanDate'])); ?>
                    </td>
                    <td class="p-3 text-center">
                        <?= date('d M Y', strtotime($bor['ReturnDate'])); ?>
                    </td>
                    <td>
                        <a href="dashboard.php?page=manage_returnbook&CopyCode=<?= $bor['CopyCode']?>&LoanCode=<?= $bor['LoanCode']?>" 
                        onclick="return confirm('Are you sure to return this book?')" 
                        class="bg-green-500 text-center text-white px-2 py-1 rounded hover:bg-green-600 inline-block">Return</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="3" class="p-3 text-center text-gray-500">No categories found.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="mt-5 bg-white p-6 rounded-lg shadow">
    <h2 class="text-2xl font-semibold mb-4">📚 Return History</h2>

    <form method="GET" action="dashboard.php" class="flex items-center gap-3 mb-6">
        <input type="hidden" name="page" value="manage_returnbook">
        <input type="text" name="keyword2" placeholder="Search Loan List by Member Name"
        class="border rounded p-2 w-1/3 focus:outline-none focus:ring focus:ring-blue-300">
        <button type="submit" name="search2"
        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Search</button>
    </form>

    <div>
        <table class="w-full border-collapse">
            <thead>
            <tr class="bg-gray-100 text-left border-b">
                <th class="p-3">#</th>
                <th class="p-3">Member</th>
                <th class="p-3">BookTitle</th>
                <th class="p-3 text-center">Return Date</th>
                <th class="p-3 text-center">Overdue Fine</th>
            </tr>
            </thead>
            <tbody>
            <?php if ($return && count($return) > 0): ?>
                <?php $no = 1; foreach ($return as $ret): ?>
                <tr class="border-b hover:bg-gray-50">
                    <td class="p-3 text-center"><?= $no++; ?></td>
                    <td class="p-3"><?= htmlspecialchars($ret['MemName']); ?></td>
                    <td class="p-3"><?= htmlspecialchars($ret['BookTitle']); ?></td>
                    <td class="p-3 text-center">
                        <?= date('d M Y', strtotime($ret['ActualReturnDate'])); ?>
                    </td>
                    <td class="p-3 text-center"><?= htmlspecialchars($ret['OverdueFine']); ?></td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="3" class="p-3 text-center text-gray-500">No categories found.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>