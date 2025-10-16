<?php
// app/models/BookModel.php

require_once 'BaseModel.php';

class BookModel extends BaseModel {

    // Ambil semua buku
    public function getAllBooks() {
        $sql = "SELECT * FROM book ORDER BY BookTitle ASC";
        return $this->fetchAll($sql);
    }

    // Ambil satu buku berdasarkan ID
    public function getBookById($bookCode) {
        $sql = "SELECT * FROM book WHERE BookCode = ?";
        return $this->fetch($sql, [$bookCode]);
    }

    // Tambah buku baru
    public function addBook($catName, $isbn, $bookTitle, $autName, $publisher, $pubYear, $numPages, $totalCopies) {
        $sql = "CALL AddBook(?, ?, ?, ?, ?, ?, ?, ?)";
        $this->query($sql, [$catName, $isbn, $bookTitle, $autName, $publisher, $pubYear, $numPages, $totalCopies]);
    }

    // Update data buku
    public function updateBook($bookCode, $isbn, $bookTitle, $autName, $publisher, $pubYear, $numPages, $totalCopies) {
        $sql = "UPDATE book SET ISBN = ?, BookTitle = ?, AutName = ?, Publisher = ?, PubYear = ?, NumPages = ?, Total Copies = ? WHERE BookCode = ?";
        $this->query($sql, [$isbn, $bookTitle, $autName, $publisher, $pubYear, $numPages, $totalCopies, $bookCode]);
    }

    // Hapus buku
    public function deleteBook($bookCode) {
        $sql = "DELETE FROM book WHERE BookCode = ?";
        $this->query($sql, [$bookCode]);
    }

    // Cari buku berdasarkan judul
    public function searchBook($keyword) {
        $sql = "SELECT * FROM book WHERE BookTitle LIKE ?";
        return $this->fetchAll($sql, ["%$keyword%"]);
    }

    // Validasi ketersediaan buku
    # Perbaiki code ini untuk fetch kembali dari table View_BookStatus sesuai kebutuhan nantinya
    public function isBookAvailable($bookCode) {
        $book = $this->getBookById($bookCode);
        return isset($book['status']) && strtolower($book['status']) === 'available';
    }
}
?>
