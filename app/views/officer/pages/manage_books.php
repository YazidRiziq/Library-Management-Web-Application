<?php
require_once '../../../config/connections.php';
require_once '../../models/BookModel.php';

$db = new Database();
$conn = $db->getConnection();

$bookModel = new BookModel($conn);
$keyword = $_GET['keyword'] ?? '';
$books = $bookModel->getAllBooksOfficer(); // ambil semua data buku

if (isset($_GET['keyword'])) {
  $keyword = trim($_GET['keyword']);
  $newbooks = $bookModel->searchBook($keyword);
  $books = $newbooks;
}

// Handle delete (jika ada)
if (isset($_GET['delete'])) {
  $id = ($_GET['delete']);

  if ($id) {
    $bookModel->DeleteBookAndCopies($id);
    echo "<script>alert('Book deleted successfully!'); window.location='dashboard.php?page=manage_books';</script>";
  } else {
    echo "<script>alert('Failed to delete book!');</script>";
  }
}
?>

<div class="bg-white p-6 rounded-lg shadow">
  <div class="flex justify-between items-center mb-4">
    <h2 class="text-2xl font-semibold">📖 Manage Books</h2>

    <form method="GET" action="dashboard.php" class="flex gap-2">
      <input type="hidden" name="page" value="manage_books">
      <input type="text" name="keyword" placeholder="Search by Title"
        value="<?= htmlspecialchars($keyword) ?>"
        class="border p-2 w-full rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400">
      <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">Search</button>
    </form>

    <a href="dashboard.php?page=add_book" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Add Book</a>

  </div>

  <table class="w-full border-collapse">
    <thead>
      <tr class="bg-gray-100 text-left">
        <th class="border p-2">#</th>
        <th class="border p-2">Book Code</th>
        <th class="border p-2">Category</th>
        <th class="border p-2">Title</th>
        <th class="border p-2">ISBN</th>
        <th class="border p-2">Author</th>
        <th class="border p-2">Publisher</th>
        <th class="border p-2">Year</th>
        <th class="border p-2">Number of Pages</th>
        <th class="border p-2">Total Copies</th>
        <th class="border p-2">Action</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($books && count($books) > 0): ?>
        <?php foreach ($books as $i => $book): ?>
          <tr class="hover:bg-gray-50">
            <td class="border p-2"><?= $i + 1 ?></td>
            <td class="border p-2"><?= htmlspecialchars($book['BookCode']) ?></td>
            <td class="border p-2"><?= htmlspecialchars($book['CatName']) ?></td>
            <td class="border p-2"><?= htmlspecialchars($book['BookTitle']) ?></td>
            <td class="border p-2"><?= htmlspecialchars($book['ISBN']) ?></td>
            <td class="border p-2"><?= htmlspecialchars($book['AutName']) ?></td>
            <td class="border p-2"><?= htmlspecialchars($book['Publisher']) ?></td>
            <td class="border p-2"><?= htmlspecialchars($book['PubYear']) ?></td>
            <td class="border p-2"><?= htmlspecialchars($book['NumPages']) ?></td>
            <td class="border p-2 text-center"><?= htmlspecialchars($book['TotalCopies']) ?></td>
            <td class="border p-2 text-center">
              <a href="dashboard.php?page=edit_book&id=<?= $book['BookCode'] ?>" class="text-blue-600 hover:underline">Edit</a>
              <a href="dashboard.php?page=manage_books&delete=<?= $book['BookCode'] ?>" onclick="return confirm('Are you sure to delete this book?')" class="text-red-600 hover:underline">Delete</a>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr><td colspan="9" class="border p-3 text-center text-gray-500">No books available</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>
