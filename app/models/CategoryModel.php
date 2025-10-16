<?php
// app/models/CategoryModel.php

require_once 'BaseModel.php';

class CategoryModel extends BaseModel {

    // Ambil semua kategori
    public function getAllCategories() {
        $sql = "SELECT * FROM BookCategory ORDER BY CatName ASC";
        return $this->fetchAll($sql);
    }

    // Ambil satu kategori berdasarkan ID
    public function getCategoryById($CatCode) {
        $sql = "SELECT * FROM BookCategory WHERE CatCode = ?";
        return $this->fetch($sql, [$CatCode]);
    }

    // Tambah kategori baru
    public function addCategory($CatName) {
        $sql = "CALL AddCategory(?)";
        $this->query($sql, [$CatName]);
    }

    // Update kategori
    public function updateCategory($CatName, $CatCode) {
        $sql = "UPDATE BookCategory SET CatName = ? WHERE CatCode = ?";
        $this->query($sql, [$CatName, $CatCode]);
    }

    // Hapus kategori
    public function deleteCategory($CatCode) {
        $sql = "DELETE FROM BookCategory WHERE CatCode = ?";
        $this->query($sql, [$CatCode]);
    }
}
?>
