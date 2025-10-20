<?php
// app/models/BookCopyModel.php

require_once 'BaseModel.php';

class BookCopyModel extends BaseModel {

    // Ambil semua Copy-an Buku
    public function getAllBookCopy($MemID) {
        $sql = "SELECT * FROM BookCopy WHERE CopyCode = ?";
        return $this->fetchAll($sql, [$MemID]);
    }

    public function getActiveBorrowings($MemID) {
        $sql = "SELECT * FROM view_borrowedbooks WHERE MemID = ?";
        return $this->fetchAll($sql, [$MemID]);
    }

}
?>