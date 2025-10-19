<?php
require_once '../../../config/connections.php';
require_once '../../models/CategoryModel.php';

$db = new Database();
$conn = $db->getConnection();

$categoryModel = new CategoryModel($conn);

// ✅ Add Category
if (isset($_POST['add_category'])) {
  $CatName = trim($_POST['CatName']);

  if ($CatName != '') {
        $categoryModel->addCategory($CatName);
        echo "<script>alert('Category added successfully!'); window.location='dashboard.php?page=manage_category';</script>";
    } else {
        echo "<script>alert('Failed to add category.');</script>";
    }

}

// ✅ Delete Category
if (isset($_GET['delete'])) {
  $CatCode = $_GET['delete'];

  if ($CatCode) {
    $categoryModel->deleteCategory($CatCode);
    echo "<script>alert('Category deleted successfully!'); window.location='dashboard.php?page=manage_category';</script>";
  } else {
    echo "<script>alert('Failed to delete category.');</script>";
  }
}

// ✅ Get All Categories
$categories = $categoryModel->getAllCategories();
?>

<div class="bg-white p-6 rounded-lg shadow">
  <h2 class="text-2xl font-semibold mb-4">📚 Manage Book Categories</h2>

  <!-- ✅ Add New Category Form -->
  <form method="POST" class="flex items-center gap-3 mb-6">
    <input type="text" name="CatName" placeholder="New Category Name" required
      class="border rounded p-2 w-1/3 focus:outline-none focus:ring focus:ring-blue-300">
    <button type="submit" name="add_category"
      class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Add</button>
  </form>

  <!-- ✅ Category Table -->
  <table class="w-full border-collapse">
    <thead>
      <tr class="bg-gray-100 text-left border-b">
        <th class="p-3">#</th>
        <th class="p-3">Category Name</th>
        <th class="p-3 text-center">Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($categories && count($categories) > 0): ?>
        <?php $no = 1; foreach ($categories as $cat): ?>
          <tr class="border-b hover:bg-gray-50">
            <td class="p-3"><?= $no++; ?></td>
            <td class="p-3"><?= htmlspecialchars($cat['CatName']); ?></td>
            <td class="p-3 text-center">
              <a href="dashboard.php?page=edit_category&id=<?= $cat['CatCode']; ?>"
                class="bg-blue-500 text-white px-2 py-1 rounded hover:bg-blue-600">Edit</a>
            
              <a href="dashboard.php?page=manage_category&delete=<?= $cat['CatCode']; ?>"
                onclick="return confirm('Are you sure you want to delete this category?');"
                class="bg-red-500 text-white px-2 py-1 rounded hover:bg-red-600">Delete</a>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr><td colspan="3" class="p-3 text-center text-gray-500">No categories found.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>
