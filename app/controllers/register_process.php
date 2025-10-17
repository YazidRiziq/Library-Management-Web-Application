<?php
require_once 'AuthController.php';
require_once '../../config/connections.php';

$db = new Database();
$auth = new AuthController($db->getConnection());

$username = $_POST['username'];
$email = $_POST['email'];
$password = $_POST['password'];
$telp = $_POST['telp'];
$address = $_POST['address'];

# ini ubah bagian parameter
if (true) {
    $msg = $auth->registerMember($username, $email, $password, $telp, $address);
}

if ($msg) {
    header("Location: ../views/auth/register.php?error=" . urlencode($msg));
    exit();
}
?>
