<?php
// app/models/BorrowingModel.php

require_once 'BaseModel.php';

class BorrowingModel extends BaseModel {

    // Ambil semua buku
    public function getAllBorrowingStatus($MemID) {
        $sql = "SELECT * FROM view_memberborrowing WHERE MemName = ?";
        return $this->fetchAll($sql, [$MemID]);
    }

    public function getActiveBorrowings() {
        $sql = "SELECT * FROM view_borrowedbooks";
        return $this->fetchAll($sql);
    }

    public function getBookStatistics() {
        $sql = "SELECT * FROM view_bookstatistics";
        return $this->fetchAll($sql);
    }

    public function searchStatistics($keyword) {
        $sql = "SELECT * FROM view_bookstatistics WHERE BookTitle LIKE ?";
        return $this->fetchAll($sql, ["%$keyword%"]);
    }

    public function searchBorrowings($keyword) {
        $sql = "SELECT * FROM view_borrowedbooks WHERE MemName LIKE ?";
        return $this->fetchAll($sql, ["%$keyword%"]);
    }

    public function getBorrowedBooks() {
        $sql = "SELECT * FROM view_bookstatistics";
        return $this->fetchAll($sql);
    }

    //Model untuk Peminjaman -------------

    # Bagian Tabel Borrowing
    public function addBorrowing($OffName, $MemName, $BookTitel, $Quantity) {
        $sql = "CALL AddBorrowing(?, ?, ?, ?)";
        $this->query($sql, [$OffName, $MemName, $BookTitel, $Quantity]);
    }

    # Bagian Tabel Borrowed
    public function addBorrowedBook($LoanCode, $BookCode, $Quantity) {
        $sql = "CALL AddBorrowedBook(?, ?, ?)";
        $this->query($sql, [$LoanCode, $BookCode, $Quantity]);
    }

}
?>