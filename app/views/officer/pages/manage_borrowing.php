<?php
require_once '../../../config/connections.php';
require_once '../../models/BorrowingModel.php';
require_once '../../models/MemberModel.php';
require_once '../../models/BookModel.php';

$db = new Database();
$conn = $db->getConnection();

$borrowingModel = new BorrowingModel($conn);
$memberModel = new MemberModel($conn);
$bookModel = new BookModel($conn);

$keyword = $_GET['keyword'] ?? '';
$statistics = $borrowingModel->getBookStatistics();
$borrowed = $borrowingModel->getActiveBorrowings();

// Search untuk Book Statistic
if (isset($_GET['keyword'])) {
  $keyword = trim($_GET['keyword']);
  $newstatistics = $borrowingModel->searchStatistics($keyword);
  $statistics = $newstatistics;
}

// search untuk Loan List
if (isset($_GET['keyword1'])) {
  $keyword1 = trim($_GET['keyword1']);
  $newborrowings = $borrowingModel->searchBorrowings($keyword1);
  $borrowed = $newborrowings;
}

// Proses Peminjaman
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $Officer = $_POST['officer'];
    $Member = $_POST['member'];
    $Title = $_POST['title'];
    $Quantity = $_POST['quantity'];

    if ($Officer && $Member && $Title && $Quantity) {
        $borrowingModel->addBorrowing($Officer, $Member, $Title, $Quantity);
        echo "<script>alert('Borrowing Book successfully!'); window.location='dashboard.php?page=manage_borrowing';</script>";
        exit();
    } else {
        echo "<script>alert('Failed to borrowing book. Please try again.');</script>";
    }
}

?>

<div class="bg-white p-6 rounded-lg shadow">
  <h2 class="text-2xl font-semibold mb-4">📦 Book Borrowing</h2>

<!-- Form Peminjaman -->
    <form method="POST" class="grid grid-cols-2 gap-4">

    <div>
        <label class="block text-gray-700 mb-1">Officer Name</label>
        <input type="text" name="officer" required class="w-full border rounded p-2">
    </div>

    <div>
        <label class="block text-gray-700 mb-1">Member Name</label>
        <input type="text" name="member" required class="w-full border rounded p-2">
    </div>

    <div>
        <label class="block text-gray-700 mb-1">Book Title</label>
        <input type="text" name="title" required class="w-full border rounded p-2">
        </div>

    <div>
        <label class="block text-gray-700 mb-1">Quantity</label>
        <input type="text" name="quantity" required class="w-full border rounded p-2">
    </div>

    <div class="col-span-2 flex justify-end">
      <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">Submit</button>
    </div>
  </form>
</div>

<div class="mt-5 bg-white p-6 rounded-lg shadow">
    <h2 class="text-2xl font-semibold mb-4">📚 Book Statistics</h2>

    <!-- Search Statistics -->
    <form method="GET" action="dashboard.php" class="flex items-center gap-3 mb-6">
        <input type="hidden" name="page" value="manage_borrowing">
        <input type="text" name="keyword" placeholder="Search Book by Title"
        class="border rounded p-2 w-1/3 focus:outline-none focus:ring focus:ring-blue-300">
        <button type="submit" name="search"
        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Search</button>
    </form>

    <div>
        <table class="w-full border-collapse">
            <thead>
            <tr class="bg-gray-100 text-left border-b">
                <th class="p-3">#</th>
                <th class="p-3">Book Title</th>
                <th class="p-3 text-center">Total Books</th>
                <th class="p-3 text-center">Availabel Books</th>
                <th class="p-3 text-center">Borrowed Books</th>
            </tr>
            </thead>
            <tbody>
            <?php if ($statistics && count($statistics) > 0): ?>
                <?php $no = 1; foreach ($statistics as $stat): ?>
                <tr class="border-b hover:bg-gray-50">
                    <td class="p-3 text-center"><?= $no++; ?></td>
                    <td class="p-3"><?= htmlspecialchars($stat['BookTitle']); ?></td>
                    <td class="p-3 text-center"><?= htmlspecialchars($stat['TotalBooks']); ?></td>
                    <td class="p-3 text-center"><?= htmlspecialchars($stat['AvailableBooks']); ?></td>
                    <td class="p-3 text-center"><?= htmlspecialchars($stat['BorrowedBooks']); ?></td>
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
    <h2 class="text-2xl font-semibold mb-4">📚 Loan List</h2>

    <form method="GET" action="dashboard.php" class="flex items-center gap-3 mb-6">
        <input type="hidden" name="page" value="manage_borrowing">
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
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="3" class="p-3 text-center text-gray-500">No categories found.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
