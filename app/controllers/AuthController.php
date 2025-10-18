<?php
require_once '../models/MemberModel.php';
require_once '../models/OfficerModel.php';

class AuthController {
    private $memberModel;
    private $officerModel;

    public function __construct($db) {
        $this->memberModel = new MemberModel($db);
        $this->officerModel = new OfficerModel($db);
        session_start();
    }

    // === UNIVERSAL LOGIN ===
    public function login($email, $password) {
        // 1️⃣ Cek di Officer
        $officer = $this->officerModel->getOfficerByEmail($email);
        if ($officer && $password == $officer['OffPassword']) {
            $_SESSION['OffID'] = $officer['OffID'];
            $_SESSION['OffName'] = $officer['OffName'];
            $_SESSION['Role'] = 'Officer';
            header("Location: ../views/officer/dashboard.php");
            exit();
        }

        // 2️⃣ Kalau bukan officer, cek di Member
        $member = $this->memberModel->getMemberByEmail($email);

        if ($member && password_verify($password, $member['MemPassword'])) {
            $_SESSION['MemID'] = $member['MemID'];
            $_SESSION['MemName'] = $member['MemName'];
            $_SESSION['Role'] = 'Member';
            header("Location: ../views/member/dashboard.php");
            exit();
        }

        // 3️⃣ Jika gagal dua-duanya
        return "Email or password is incorrect.";
    }

    // === REGISTER MEMBER ===
    public function registerMember($username, $email, $password, $telp, $address) {
        // Validasi sederhana
        if (empty($username) || empty($email) || empty($password) || empty($telp) || empty($address)) {
            return "All fields are required.";
        }

        // Simpan data
        $this->memberModel->addMember($username, $email, $password, $telp, $address);
        return "Registration successful! Please login.";
    }

    // === LOGOUT ===
    public function logout() {
        session_destroy();
        header("Location: ../views/auth/login.php");
        exit();
    }
}
?>
