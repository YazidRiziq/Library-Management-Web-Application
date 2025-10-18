<?php
// controllers/logout.php
require_once 'AuthController.php';
require_once '../config/connections.php';

$db = new Database();
$auth = new AuthController($db->getConnection());
$auth->logout();

?>