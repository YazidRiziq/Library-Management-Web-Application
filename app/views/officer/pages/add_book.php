<?php
require_once '../../../config/connections.php';
require_once '../../models/BookModel.php';
require_once '../../models/CategoryModel.php';

$db = new Database();
$conn = $db->getConnection();

$categoryModel = new CategoryModel($conn);
$bookModel = new BookModel($conn);

$categories = $categoryModel->getAllCategories();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $CatName = $_POST['category'];
    $ISBN = $_POST['isbn'];
    $BookTitle = $_POST['title'];
    $AutName = $_POST['author'];
    $Publisher = $_POST['publisher'];
    $PubYear = $_POST['year'];
    $NumPages = $_POST['numpages'];
    $TotalCopies = $_POST['copies'];

    $result = $bookModel->addBook($CatName, $ISBN, $BookTitle, $AutName, $Publisher, $PubYear, $NumPages, $TotalCopies);
    $bookCheck = $bookModel->getBookByISBN($ISBN);

    if ($bookCheck) {
        echo "<script>alert('Book added successfully!'); window.location='dashboard.php?page=manage_books';</script>";
        exit();
    } else {
        echo "<script>alert('Failed to add book. Please try again.');</script>";
    }
}
?>

<div class="bg-white p-6 rounded-lg shadow">
    <h2 class="text-2xl font-semibold mb-4">➕ Add New Book</h2>
    <form method="POST" class="grid grid-cols-2 gap-4">
        <div>
        <label class="block text-gray-700 mb-1">Category</label>
        <select name="category" required class="w-full border rounded p-2">
            <option value="">-- Select Category --</option>
            <?php foreach ($categories as $cat): ?>
            <option value="<?= htmlspecialchars($cat['CatName']) ?>"><?= htmlspecialchars($cat['CatName']) ?></option>
            <?php endforeach; ?>
        </select>
        </div>

    <div>
        <label class="block text-gray-700 mb-1">ISBN</label>
        <input type="text" name="isbn" required class="w-full border rounded p-2">
    </div>

    <div>
        <label class="block text-gray-700 mb-1">Title</label>
        <input type="text" name="title" required class="w-full border rounded p-2">
    </div>

    <div>
        <label class="block text-gray-700 mb-1">Author</label>
        <input type="text" name="author" required class="w-full border rounded p-2">
        </div>

    <div>
        <label class="block text-gray-700 mb-1">Publisher</label>
        <input type="text" name="publisher" required class="w-full border rounded p-2">
    </div>

    <div>
        <label class="block text-gray-700 mb-1">Year</label>
        <input type="number" name="year" required class="w-full border rounded p-2">
    </div>

    <div>
        <label class="block text-gray-700 mb-1">Number of Pages</label>
        <input type="number" name="numpages" required class="w-full border rounded p-2">
    </div>

    <div>
        <label class="block text-gray-700 mb-1">Copies</label>
        <input type="number" name="copies" required class="w-full border rounded p-2">
    </div>

    <div class="col-span-2 flex justify-end">
      <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">Save</button>
    </div>
  </form>
</div>
