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
    public function getMemberById($MemID) {
        $sql = "SELECT * FROM Member WHERE MemID = ?";
        return $this->fetch($sql, [$MemID]);
    }

    // Ambil data member berdasarkan Name
    public function getMemberByName($MemName) {
        $sql = "SELECT * FROM Member WHERE MemName = ?";
        return $this->fetch($sql, [$MemName]);
    }

    // Ambil data member berdasarkan email 
    public function getMemberByEmail($MemEmail) {
        $sql = "SELECT * FROM Member WHERE MemEmail = ?";
        return $this->fetch($sql, [$MemEmail]);
    }

    // Tambah member baru (saat registrasi) --------------
    public function addMember($username, $email, $password, $telp, $address) {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $sql = "CALL AddMember (?, ?, ?, ?, ?)";
        $this->query($sql, [$username, $email, $telp, $address, $hashedPassword]);
    }

    // Update data member------------
    public function updateMember($member_id, $name, $email, $address, $phone) {
        $sql = "UPDATE members SET name = ?, email = ?, address = ?, phone = ? WHERE id = ?";
        $this->query($sql, [$name, $email, $address, $phone, $member_id]);
    }

    // Hapus member---------------
    public function deleteMember($member_id) {
        $sql = "DELETE FROM members WHERE id = ?";
        $this->query($sql, [$member_id]);
    }

    // Cek apakah email sudah digunakan
    public function emailExists($email) {
        $sql = "SELECT id FROM members WHERE email = ?";
        $result = $this->fetch($sql, [$email]);
        return $result ? true : false;
    }
}
?>
