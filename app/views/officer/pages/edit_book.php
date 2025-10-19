<?php
require_once '../../../config/connections.php';
require_once '../../models/BookModel.php';
require_once '../../models/CategoryModel.php';

$db = new Database();
$conn = $db->getConnection();

$bookModel = new BookModel($conn);
$categoryModel = new CategoryModel($conn);

if (!isset($_GET['id'])) {
  echo "<script>alert('Invalid request.'); window.location='dashboard.php?page=manage_books';</script>";
  exit();
}

$id = ($_GET['id']);
$book = $bookModel->getBookById($id);

if (!$book) {
    echo "<script>alert('Book not found.'); window.location='dashboard.php?page=manage_books';</script>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ISBN = $_POST['isbn'];
    $BookTitle = $_POST['title'];
    $AutName = $_POST['author'];
    $Publisher = $_POST['publisher'];
    $PubYear = $_POST['year'];
    $NumPages = $_POST['numpages'];

    if ($id) {
        $bookModel->updateBook($id, $ISBN, $BookTitle, $AutName, $Publisher, $PubYear, intval($NumPages));
        echo "<script>alert('Book updated successfully!'); window.location='dashboard.php?page=manage_books';</script>";
        exit();
    } else {
        echo "<script>alert('Failed to update book. Please try again.');</script>";
    }
}
?>

<div class="bg-white p-6 rounded-lg shadow">
    <h2 class="text-2xl font-semibold mb-4">✏️ Edit Book</h2>

    <form method="POST" class="grid grid-cols-2 gap-4">
        <div class="mt-3">
            <label class="block text-gray-700 mb-1">Title</label>
            <input type="text" name="title" value="<?= htmlspecialchars($book['BookTitle']) ?>" required class="w-full border rounded p-2">
        </div>

        <div class="mt-3">
            <label class="block text-gray-700 mb-1">ISBN</label>
            <input type="text" name="isbn" value="<?= htmlspecialchars($book['ISBN']) ?>" required class="w-full border rounded p-2">
        </div>


        <div class="mt-3">
            <label class="block text-gray-700 mb-1">Author</label>
            <input type="text" name="author" value="<?= htmlspecialchars($book['AutName']) ?>" required class="w-full border rounded p-2">
        </div>

        <div class="mt-3">
            <label class="block text-gray-700 mb-1">Publisher</label>
            <input type="text" name="publisher" value="<?= htmlspecialchars($book['Publisher']) ?>" required class="w-full border rounded p-2">
        </div>

        <div class="mt-3">
            <label class="block text-gray-700 mb-1">Year</label>
            <input type="number" name="year" value="<?= htmlspecialchars($book['PubYear']) ?>" required class="w-full border rounded p-2">
        </div>

        <div class="mt-3">
            <label class="block text-gray-700 mb-1">Number of Pages</label>
            <input type="number" name="numpages" value="<?= htmlspecialchars($book['NumPages']) ?>" required class="w-full border rounded p-2">
        </div>

        <div class="col-span-2 flex justify-end">
            <button type="submit" class="bg-green-600 text-white mt-6 px-6 py-2 rounded hover:bg-green-700">Update</button>
        </div>
  </form>
</div>
