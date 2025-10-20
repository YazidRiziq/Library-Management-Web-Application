<?php
// app/models/BookModel.php

require_once 'BaseModel.php';

class ReturnBookModel extends BaseModel {
    
    public function ReturnBook($CopyCode, $LoanCode) {
        $sql = "UPDATE BorrowedBook SET ActualReturnDate = Curdate() Where CopyCode = ? and LoanCode = ?";
        $this->query($sql, [$CopyCode, $LoanCode]);
    }

    public function AddOverdueFine($CopyCode, $LoanCode) {
        $sql = "Call AddOverdueFine(?,?)";
        $this->query($sql, [$CopyCode, $LoanCode]);
    }

    public function SearchReturnSuccess($keyword) {
        $sql = "SELECT * FROM view_BookReturned WHERE MemName LIKE ?";
        return $this->fetchAll($sql, ["%$keyword%"]);
    }

    public function getViewReturnSuccess() {
        $sql = "SELECT * FROM view_BookReturned";
        return $this->fetchAll($sql);
    }

}
?>