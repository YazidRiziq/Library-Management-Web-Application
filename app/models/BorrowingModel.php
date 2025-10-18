<?php
// app/models/BorrowingModel.php

require_once 'BaseModel.php';

class BorrowingModel extends BaseModel {

    // Ambil semua buku
    public function getAllBorrowingStatus($MemID) {
        $sql = "SELECT * FROM view_memberborrowing WHERE MemID = ?";
        return $this->fetchAll($sql, [$MemID]);
    }

} 