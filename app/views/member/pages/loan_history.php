<?php
require_once '../../../config/connections.php';
require_once '../../models/MemberModel.php';

$db = new Database();
$conn = $db->getConnection();

$memberModel = new MemberModel($conn);

$id = ($_GET['member_id']);
$keyword = $_GET['keyword'] ?? '';
$books = $memberModel->LoanListMember($id);
var_dump(($id));

if (isset($_GET['keyword'])) {
  $keyword = trim($_GET['keyword']);
  $newbooks = $bookModel->searchBook($keyword);
  $books = $newbooks;
}

?>

<div class="bg-white p-6 rounded-lg shadow">
  <div class="flex justify-between items-center mb-4">
    <h2 class="text-2xl font-semibold">📖 Book List</h2>

    <!-- Form untuk Search -->
    <form method="GET" action="dashboard.php" class="flex gap-2">
      <input type="hidden" name="page" value="book_list">
      <input type="text" name="keyword" placeholder="Search by Title"
        value="<?= htmlspecialchars($keyword) ?>"
        class="border p-2 w-full rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400">
      <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">Search</button>
    </form>
  </div>

  <table class="w-full border-collapse">
    <thead>
      <tr class="bg-blue-50 text-left">
        <th class="border p-2">#</th>
        <th class="border p-2">Category</th>
        <th class="border p-2">Title</th>
        <th class="border p-2">ISBN</th>
        <th class="border p-2">Author</th>
        <th class="border p-2">Publisher</th>
        <th class="border p-2">Year</th>
        <th class="border p-2">Number of Pages</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($books && count($books) > 0): ?>
        <?php foreach ($books as $i => $book): ?>
          <tr class="hover:bg-gray-50">
            <td class="border p-2"><?= $i + 1 ?></td>
            <td class="border p-2"><?= htmlspecialchars($book['CatName']) ?></td>
            <td class="border p-2"><?= htmlspecialchars($book['BookTitle']) ?></td>
            <td class="border p-2"><?= htmlspecialchars($book['ISBN']) ?></td>
            <td class="border p-2"><?= htmlspecialchars($book['AutName']) ?></td>
            <td class="border p-2"><?= htmlspecialchars($book['Publisher']) ?></td>
            <td class="border p-2"><?= htmlspecialchars($book['PubYear']) ?></td>
            <td class="border p-2"><?= htmlspecialchars($book['NumPages']) ?></td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr><td colspan="9" class="border p-3 text-center text-gray-500">No books available</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>
