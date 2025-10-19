<?php
require_once '../../../config/connections.php';
require_once '../../models/MemberModel.php';

$db = new Database();
$conn = $db->getConnection();

$memberModel = new MemberModel($conn);

// ✅ Handle Add Member
if (isset($_POST['add_member'])) {
    $username = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $telp = trim($_POST['telp']);
    $address = trim($_POST['address']);

    if ($username && $email && $password && $telp && $address) {
            $memberModel->addMember($username, $email, $password, $telp, $address);
            echo "<script>alert('Member added successfully!'); window.location='dashboard.php?page=manage_member';</script>";
        } else {
            echo "<script>alert('Failed to add member.');</script>";
        }
}

// ✅ Handle Delete Member
if (isset($_GET['delete'])) {
    $id = ($_GET['delete']);
    if ($id) {
        $memberModel->deleteMember($id);
        echo "<script>alert('Member deleted successfully!'); window.location='dashboard.php?page=manage_member';</script>";
    } else {
        echo "<script>alert('Failed to delete member.');</script>";
    }
}

// ✅ Fetch All Members
$members = $memberModel->getAllMembers();
?>

<div class="bg-white p-6 rounded-lg shadow">
    <h2 class="text-2xl font-semibold mb-4">👥 Manage Member Data</h2>

  <!-- Add Member Form -->
    <form method="POST" class="grid grid-cols-3 gap-3 mb-6">
        <input type="text" name="name" placeholder="Member Name" required class="border rounded p-2">
        <input type="email" name="email" placeholder="Email" required class="border rounded p-2">
        <input type="password" name="password" placeholder="Password" required class="border rounded p-2">
        <input type="telp" name="telp" placeholder="Telp" required class="border rounded p-2">
        <input type="textarea" name="address" placeholder="Address" required class="border rounded p-2">
        <div class="col-span-3 flex justify-end">
            <button type="submit" name="add_member" class="bg-blue-600 text-white px-5 py-2 rounded hover:bg-blue-700">Add Member</button>
        </div>
    </form>

    <!-- Member Table -->
    <table class="w-full border-collapse">
        <thead>
            <tr class="bg-gray-100 border-b">
                <th class="p-3 text-left">#</th>
                <th class="p-3 text-left">Name</th>
                <th class="p-3 text-left">Email</th>
                <th class="p-3 text-center">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($members && count($members) > 0): ?>
                <?php $no = 1; foreach ($members as $m): ?>
                <tr class="border-b hover:bg-gray-50">
                    <td class="p-3"><?= $no++; ?></td>
                    <td class="p-3"><?= htmlspecialchars($m['MemName']); ?></td>
                    <td class="p-3"><?= htmlspecialchars($m['MemEmail']); ?></td>
                    <td class="p-3 text-center">
                    <a href="dashboard.php?page=manage_member&delete=<?= $m['MemID']; ?>"
                        onclick="return confirm('Are you sure you want to delete this member?');"
                        class="bg-red-500 text-white px-2 py-1 rounded hover:bg-red-600">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
            <tr><td colspan="4" class="p-3 text-center text-gray-500">No members found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
