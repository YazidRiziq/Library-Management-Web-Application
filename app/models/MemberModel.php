<?php
// app/models/MemberModel.php

require_once 'BaseModel.php';

class MemberModel extends BaseModel {

    // Ambil semua data member
    public function getAllMembers() {
        $sql = "SELECT * FROM Member ORDER BY MemName ASC";
        return $this->fetchAll($sql);
    }

    // Ambil data member berdasarkan ID
    public function getMemberById($member_id) {
        $sql = "SELECT * FROM Member WHERE id = ?";
        return $this->fetch($sql, [$member_id]);
    }

    // Tambah member baru (saat registrasi)
    public function addMember($name, $email, $password, $address, $phone) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO members (name, email, password, address, phone) VALUES (?, ?, ?, ?, ?)";
        $this->query($sql, [$name, $email, $hashedPassword, $address, $phone]);
    }

    // Update data member
    public function updateMember($member_id, $name, $email, $address, $phone) {
        $sql = "UPDATE members SET name = ?, email = ?, address = ?, phone = ? WHERE id = ?";
        $this->query($sql, [$name, $email, $address, $phone, $member_id]);
    }

    // Hapus member
    public function deleteMember($member_id) {
        $sql = "DELETE FROM members WHERE id = ?";
        $this->query($sql, [$member_id]);
    }

    // Validasi login member
    public function validateLogin($email, $password) {
        $sql = "SELECT * FROM members WHERE email = ?";
        $member = $this->fetch($sql, [$email]);

        if ($member && password_verify($password, $member['password'])) {
            return $member;
        }
        return false;
    }

    // Cek apakah email sudah digunakan
    public function emailExists($email) {
        $sql = "SELECT id FROM members WHERE email = ?";
        $result = $this->fetch($sql, [$email]);
        return $result ? true : false;
    }
}
?>
