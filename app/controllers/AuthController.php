<?php
require_once 'models/MemberModel.php';
require_once 'models/OfficerModel.php';

class AuthController {
    private $memberModel;
    private $officerModel;

    public function __construct($db) {
        $this->memberModel = new MemberModel($db);
        $this->officerModel = new OfficerModel($db);
        session_start();
    }

    // === MEMBER LOGIN ===
    public function memberLogin($email, $password) {
        $member = $this->memberModel->getMemberById($email);
        if ($member && password_verify($password, $member['password'])) {
            $_SESSION['member_id'] = $member['member_id'];
            $_SESSION['member_name'] = $member['name'];
            $_SESSION['role'] = 'member';
            header("Location: views/member/dashboard.php");
            exit();
        } else {
            return "Invalid email or password.";
        }
    }

    // === OFFICER LOGIN ===
    public function officerLogin($username, $password) {
        $officer = $this->officerModel->getOfficerById($username);
        if ($officer && password_verify($password, $officer['password'])) {
            $_SESSION['officer_id'] = $officer['officer_id'];
            $_SESSION['officer_name'] = $officer['name'];
            $_SESSION['role'] = 'officer';
            header("Location: views/officer/dashboard.php");
            exit();
        } else {
            return "Invalid username or password.";
        }
    }

    // === REGISTER MEMBER ===
    public function registerMember($data) {
        // Validasi sederhana
        if (empty($data['name']) || empty($data['email']) || empty($data['password'])) {
            return "All fields are required.";
        }

        // Enkripsi password
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

        // Simpan data
        $this->memberModel->addMember($name, $email, $password, $address, $phone);
        return "Registration successful! Please login.";
    }

    // === LOGOUT ===
    public function logout() {
        session_destroy();
        header("Location: views/login.php");
        exit();
    }
}
?>
