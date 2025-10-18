<?php
// app/models/BookModel.php

require_once 'BaseModel.php';

class BookModel extends BaseModel {

    // Ambil semua buku
    public function getAllBooks() {
        $sql = "SELECT * FROM Book ORDER BY BookTitle ASC";
        return $this->fetchAll($sql);
    }

    // Ambil buku dari View for Book List Member Dashboard
    public function getAllBookList() {
        $sql = "SELECT * FROM view_booklistdashboard";
        return $this->fetchAll($sql);
    }

    // Ambil satu buku berdasarkan ID
    public function getBookById($BookCode) {
        $sql = "SELECT * FROM Book WHERE BookCode = ?";
        return $this->fetch($sql, [$BookCode]);
    }

    // Tambah buku baru
    public function addBook($CatName, $ISBN, $BookTitle, $AutName, $Publisher, $PubYear, $NumPages, $TotalCopies) {
        $sql = "CALL AddBook(?, ?, ?, ?, ?, ?, ?, ?)";
        $this->query($sql, [$CatName, $ISBN, $BookTitle, $AutName, $Publisher, $PubYear, $NumPages, $TotalCopies]);
    }

    // Update data buku
    public function updateBook($BookCode, $ISBN, $BookTitle, $AutName, $Publisher, $PubYear, $NumPages, $TotalCopies) {
        $sql = "UPDATE book SET ISBN = ?, BookTitle = ?, AutName = ?, Publisher = ?, PubYear = ?, NumPages = ?, Total Copies = ? WHERE BookCode = ?";
        $this->query($sql, [$ISBN, $BookTitle, $AutName, $Publisher, $PubYear, $NumPages, $TotalCopies, $BookCode]);
    }

    // Hapus buku
    public function deleteBook($BookCode) {
        $sql = "DELETE FROM Book WHERE BookCode = ?";
        $this->query($sql, [$BookCode]);
    }

    // Cari buku berdasarkan judul
    public function searchBook($keyword) {
        $sql = "SELECT * FROM Book WHERE BookTitle LIKE ?";
        return $this->fetchAll($sql, ["%$keyword%"]);
    }

    // Validasi ketersediaan buku --------------------------------------------------------
    # Perbaiki code ini untuk fetch kembali dari table View_BookStatus sesuai kebutuhan nantinya
    public function isBookAvailable($bookCode) {
        $book = $this->getBookById($bookCode);
        return isset($book['status']) && strtolower($book['status']) === 'available';
    }
}
?>
