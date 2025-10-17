<?php
require_once 'AuthController.php';
require_once '../../config/connections.php';

$db = new Database();
$auth = new AuthController($db->getConnection());

$email = $_POST['email'];
$password = $_POST['password'];

$msg = $auth->login($email, $password);

if ($msg) {
    header("Location: ../views/auth/login.php?error=" . urlencode($msg));
    exit();
}
?>
