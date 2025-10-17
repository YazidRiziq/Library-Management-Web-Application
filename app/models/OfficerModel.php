<?php
// app/models/OfficerModel.php

require_once 'BaseModel.php';

class OfficerModel extends BaseModel {

    // Ambil semua officer
    public function getAllOfficers() {
        $sql = "SELECT * FROM Officer ORDER BY OffID ASC";
        return $this->fetchAll($sql);
    }

    // Ambil officer berdasarkan ID
    public function getOfficerById($OffID) {
        $sql = "SELECT * FROM Officer WHERE OffID = ?";
        return $this->fetch($sql, [$OffID]);
    }

    // Ambil officer berdasarkan Email
    public function getOfficerByEmail($OffEmail) {
        $sql = "SELECT * FROM Officer WHERE OffEmail = ?";
        return $this->fetch($sql, [$OffEmail]);
    }

    // Tambah officer baru (opsional, jika ada fitur tambah petugas) ----------
    public function addOfficer($name, $email, $password, $role = 'staff') {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO officers (name, email, password, role) VALUES (?, ?, ?, ?)";
        $this->query($sql, [$name, $email, $hashedPassword, $role]);
    }

    // Update data officer --------
    public function updateOfficer($officer_id, $name, $email, $role) {
        $sql = "UPDATE officers SET name = ?, email = ?, role = ? WHERE id = ?";
        $this->query($sql, [$name, $email, $role, $officer_id]);
    }

    // Hapus officer ----------
    public function deleteOfficer($officer_id) {
        $sql = "DELETE FROM officers WHERE id = ?";
        $this->query($sql, [$officer_id]);
    }

    // Cek apakah email sudah digunakan ----------
    public function emailExists($email) {
        $sql = "SELECT id FROM officers WHERE email = ?";
        $result = $this->fetch($sql, [$email]);
        return $result ? true : false;
    }
}
?>
