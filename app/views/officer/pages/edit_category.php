<?php
require_once '../../../config/connections.php';
require_once '../../models/CategoryModel.php';

$db = new Database();
$conn = $db->getConnection();

$categoryModel = new CategoryModel($conn);

if (!isset($_GET['id'])) {
  echo "<script>alert('Invalid request.'); window.location='dashboard.php?page=manage_category';</script>";
  exit();
}

$id = ($_GET['id']);
$category = $categoryModel->getCategoryById($id);

if (!$category) {
  echo "<script>alert('Category not found.'); window.location='dashboard.php?page=manage_category';</script>";
  exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $CatName = trim($_POST['CatName']);
    if ($CatName != '') {
            $categoryModel->updateCategory($CatName, $id);
            echo "<script>alert('Category updated successfully!'); window.location='dashboard.php?page=manage_category';</script>";
        } else {
            echo "<script>alert('Failed to update category.');</script>";
        } 
}
?>

<div class="bg-white p-6 rounded-lg shadow">
  <h2 class="text-2xl font-semibold mb-4">✏️ Edit Category</h2>

  <form method="POST" class="grid gap-4 w-1/2">
    <div>
      <label class="block text-gray-700 mb-1">Category Name</label>
      <input type="text" name="CatName" value="<?= htmlspecialchars($category['CatName']); ?>"
        required class="border rounded p-2 w-full">
    </div>
    <div class="flex justify-end">
      <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700">Update</button>
    </div>
  </form>
</div>
