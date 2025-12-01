-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 01, 2025 at 06:09 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `librix`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `AddBook` (IN `prCatName` VARCHAR(30), IN `prISBN` CHAR(17), IN `prBookTitle` VARCHAR(50), IN `prAutName` VARCHAR(30), IN `prPublisher` VARCHAR(30), IN `prPubYear` INT(4), IN `prNumPages` INT, IN `prTotalCopies` INT)   Begin
	Declare NewBookCode Char(8);
	Declare prCatCode Char(6);
    
    Start Transaction;
    
    -- Mengambil CatCode dari kategori buku yang di input
    Select CatCode into prCatCode From BookCategory
    Where CatName = prCatName;
    
    -- Mengirim pesan jika Kategori tidak ditemukan
    IF prCatCode IS Null Then
		SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Category not found!';
    END IF;
    
    -- Mencari nilai yang kosong dan menetapkan BookCode
    Select Concat(
			'BK',
            Substring(prCatCode, 5,2),
			'-',
            LPAD(Coalesce(Min(Cast(Substring(BookCode,7,2) AS Unsigned)),0)+1,3,'0'))
	Into NewBookCode From Book
    Where Substring(prCatCode,5,2) = Substring(BookCode,3,2) 
		And Concat(
				'BK',
				Substring(prCatCode, 5,2),
				'-',
				LPAD(Cast(Substring(BookCode,7,2) AS Unsigned)+1,3,'0'))
		Not in (Select BookCode From Book);
    
    -- Memasukkan data ke Table Book
    Insert into Book (BookCode, CatCode, ISBN, BookTitle, AutName, Publisher, PubYear, NumPages, TotalCopies)
    Values (NewBookCode, prCatCode, prISBN, prBookTitle, prAutName, prPublisher, prPubYear, prNumPages, prTotalCopies);
    Call AddCopy(NewBookCode);
    
    Commit;
End$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `AddBorrowedBook` (`sLoanCode` CHAR(13), `sBookCode` CHAR(8), `Quantity` INT)   BEGIN
	Declare prCopyCode Char(12);
    Declare prLoanCode Char(13);
    Declare prTotalCopies int;
    Declare prCounter int Default 1;
    
	-- Memverifikasi LoanCode
	Select LoanCode Into prLoanCode
	From Borrowing
	Where LoanCode = sLoanCode;
    
    -- Pengulangan memasukkan data
    REPEAT
        
		-- Mengambil CopyCode yang tersedia
		Select bc.CopyCode Into prCopyCode
		From BookCopy bc Left Outer Join BorrowedBook bb
		On bc.CopyCode = bb.CopyCode
        Where bc.BookCode = sBookCode
        And bc.BookStatus = 'Available'
        And (bb.ReturnCond = 'Good' OR bb.ReturnCond is Null)
        Order By bb.ActualReturnDate Desc
        Limit 1;
		
		-- Pemberitahuan jika CopyCode tidak tersedia
		IF prCopyCode IS NULL THEN
				SIGNAL SQLSTATE '45000'
				SET MESSAGE_TEXT = 'No available copies for the requested book.';
		END IF;
        
		-- Quantity Peminjaman tidak boleh lebih dari TotalCopies
		Select TotalCopies into prTotalCopies
		From Book
		Where BookCode = sBookCode;
		
		IF Quantity > prTotalCopies Then
			Signal SQLSTATE '45000'
			Set Message_Text = 'Not enough available copies for the requested book.';
		END IF;
		
		-- Memasukkan data ke tabel BorrowedBook
		Insert Into BorrowedBook (CopyCode, LoanCode, ReturnCond, ActualReturnDate, OverdueFine)
		Values (prCopyCode, prLoanCode, 'Good', NULL, NULL);
		
		-- Update Status Buku
		Update BookCopy
		Set BookStatus = 'Borrowed'
		Where CopyCode = prCopyCode;
        
		SET prCounter = prCounter + 1;
	
    Until prCounter > Quantity
	END REPEAT;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `AddBorrowing` (`sOffName` VARCHAR(50), `sMemName` VARCHAR(50), `sBookTitle` VARCHAR(50), `sQuantity` INT)   BEGIN
	-- Declare semua di atas biar aman
	Declare NewLoanCode Char(13);
    Declare prBookCode Char(8);
    Declare prOffID Char(10);
    Declare prMemID Char(11);
	Declare prLoanDate Date;
    Declare prReturnDate Date;
    
    Declare prCopyCode Char(12);
    Declare prTotalCopies int;

    -- Set tanggal otomatis
    Set prLoanDate = curdate();
    Set prReturnDate = Date_Add(prLoanDate, Interval 7 Day);

    -- Ambil data penting dulu
    Select BookCode into prBookCode From Book Where BookTitle = sBookTitle;
    Select OffID into prOffID From Officer Where OffName = sOffName;
    Select MemID into prMemID From Member Where MemName = sMemName;

    -- Cek apakah buku tersedia (minimal 1 copy)
    Select bc.CopyCode Into prCopyCode
	From BookCopy bc 
    Left Join BorrowedBook bb On bc.CopyCode = bb.CopyCode
	Where bc.BookCode = prBookCode
	And bc.BookStatus = 'Available'
	And (bb.ReturnCond = 'Good' OR bb.ReturnCond IS NULL)
	Order By bb.ActualReturnDate Desc
	Limit 1;

	IF prCopyCode IS NULL THEN
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'No available copies for the requested book.';
	END IF;

	-- Cek apakah quantity valid
	Select TotalCopies into prTotalCopies From Book Where BookCode = prBookCode;
	IF sQuantity > prTotalCopies THEN
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Not enough available copies for the requested book.';
	END IF;

	-- Membuat LoanCode Otomatis
	Select Concat(
		'LN', '-', Substring(prMemID,4,3), '-', 
		Upper(Substring(md5(prLoanDate),1,4)), 
		LPAD(Coalesce(Min(Cast(Substring(LoanCode,12,2) As Unsigned)),0)+1,2,'0')
	) Into NewLoanCode
	From Borrowing
	Where Cast(Substring(LoanCode,12,2) As Unsigned)+1
	Not In (Select Cast(Substring(LoanCode,12,2) As Unsigned) From Borrowing);

	-- Insert ke Borrowing
	Insert Into Borrowing (LoanCode, OffID, MemID, LoanDate, ReturnDate)
	Values (NewLoanCode, prOffID, prMemID, prLoanDate, prReturnDate);

	-- Insert ke AddBorrowedBook
	Call AddBorrowedBook(NewLoanCode, prBookCode, sQuantity);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `AddCategory` (IN `CatName` VARCHAR(30))   Begin
	Declare NewCatCode Char(6);
    -- Cek kode yang kosong
    Select Concat('CAT', LPAD(Coalesce(Min(Cast(Substring(CatCode,4) AS Unsigned)),0)+1, 3, '0'))
	into NewCatCode From BookCategory
    Where Cast(Substring(CatCode,4) AS Unsigned)+1
    Not In (Select Cast(Substring(CatCode,4) AS Unsigned) From BookCategory);
    
    -- Masukkan data ke tabel
    Insert into BookCategory (CatCode, CatName)
    Values (NewCatCode, CatName);
End$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `AddCopy` (`sBookCode` CHAR(8))   Begin
	Declare NewBookCopy Char(12);
    Declare prBookCode Char(8);
    Declare prCopyNumber int Default 1;
    Declare prTotalCopies int;
    Declare prBookStatus Varchar(20);
    Set prBookStatus = 'Available';
    
    -- Memvalidasi BookCode dari Table Book
    Select BookCode into prBookCode
    From Book
    Where BookCode = sBookCode;
    
        -- Mengambil Nilai TotalCopies dari Table Book
    Select TotalCopies Into prTotalCopies 
    From Book
    Where BookCode = sBookCode;
    
    -- Memasukkan data ke Table BookCopy
    REPEAT
    
    -- Membuat CopyCode Otomatis
    Select Concat(
				'CPY',
                '-',
                Substring(prBookCode,3,2),
                Substring(prBookCode,6,3),
                '-',
                LPAD(Coalesce(Min(Cast(Substring(CopyCode,11,2) AS Unsigned)),0)+1,2,'0')
            )
    Into NewBookCopy
    From BookCopy
    Where Substring(CopyCode,5,2) = Substring(prBookCode,3,2)
    And Substring(CopyCode,7,3) = Substring(prBookCode,6,3)
    And Concat(
				'CPY',
                '-',
                Substring(prBookCode,3,2),
                Substring(prBookCode,6,3),
                '-',
                LPAD(Cast(Substring(CopyCode,11,2) AS Unsigned)+1,2,'0')
            )
    Not In (Select CopyCode From BookCopy);
    
		Insert Into BookCopy (CopyCode, BookCode, BookStatus, CopyNumber)
		Values (NewBookCopy, prBookCode, prBookStatus, prCopyNumber);
	Set prCopyNumber = prCopyNumber + 1;
	UNTIL prCopyNumber > prTotalCopies
    END REPEAT;
End$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `AddMember` (`prMemName` VARCHAR(50), `prMemEmail` VARCHAR(30), `prMemTelp` VARCHAR(15), `prMemAddress` VARCHAR(100), `prMemPassword` VARCHAR(255))   BEGIN
	Declare NewMemID Char(11);
    
    -- MemID Otomatis
    Select Concat(
				'ID',
                '-',
                Substring(prMemName,1,3),
                '-',
                LPAD(Coalesce(Min(Cast(Substring(MemID,10,2) AS Unsigned)),0)+1, 4, '0')
            )
	Into NewMemID
    From Member
    Where Cast(Substring(MemID,10,2) AS Unsigned)+1
	Not In (Select Cast(Substring(MemID,10,2) AS Unsigned) From Member);
    
    -- Memasukkan data ke tabel Member
    Insert Into Member (MemID, MemName, MemEmail, MemTelp, MemAddress, RegDate, MemPassword)
    Values (NewMemID, prMemName, prMemEmail, prMemTelp, prMemAddress, curdate(), prMemPassword);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `AddOfficer` (`prOffName` VARCHAR(50), `PrOffEmail` VARCHAR(30), `prOffTelp` VARCHAR(15))   Begin
	Declare NewOffID Char(10);
    
    -- OffID Otomatis
    Select Concat(
				'ID',
                '-',
                'OFF',
                '-',
                LPAD(Coalesce(Min(Cast(Substring(OffID,8) AS Unsigned)),0)+1, 3, '0')
			)
    Into NewOffID
    From Officer
    Where Cast(Substring(OffID,8) AS Unsigned)+1
    Not In (Select Cast(Substring(OffID,8) AS Unsigned) From Officer);
    
    -- Memasukkan Data ke Table Officer
    Insert Into Officer (OffID, OffName, OffEmail, OffTelp)
    Values (NewOffID, prOffName, prOffEmail, prOffTelp);
End$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `AddOverduefine` (`sCopyCode` CHAR(12), `sLoanCode` CHAR(13))   BEGIN
	DECLARE DaysLate Int;
    DECLARE FinePerDay DECIMAL(10, 2) DEFAULT 1000;
	DECLARE trReturnDate DATE;
	DECLARE trActualReturnDate DATE;
    
	SELECT ReturnDate INTO trReturnDate
    FROM Borrowing
    WHERE LoanCode = sLoanCode;
    
    Select ActualReturnDate INTO trActualReturnDate
    FROM BorrowedBook
    WHERE CopyCode = sCopyCode
    AND LoanCode = sLoanCode;
    
    IF trActualReturnDate > trReturnDate THEN
		SET DaysLate = DATEDIFF(trActualReturnDate, trReturnDate);
	ELSE
		SET DaysLate = 0;
	END IF;

	-- Update OverdueFine pada BorrowedBook
	Update BorrowedBook
    Set OverdueFine = DaysLate * FinePerDay
	WHERE CopyCode = sCopyCode
    AND LoanCode = sLoanCode;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `DeleteBookAndCopies` (IN `sBookCode` CHAR(8))   BEGIN
    START TRANSACTION;
    
    DELETE FROM BookCopy WHERE BookCode = sBookCode;
    DELETE FROM Book WHERE BookCode = sBookCode;

    COMMIT;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `book`
--

CREATE TABLE `book` (
  `BookCode` char(8) NOT NULL,
  `CatCode` char(6) NOT NULL,
  `ISBN` char(17) DEFAULT NULL,
  `BookTitle` varchar(50) DEFAULT NULL,
  `AutName` varchar(30) DEFAULT NULL,
  `Publisher` varchar(30) DEFAULT NULL,
  `PubYear` int(11) DEFAULT NULL,
  `NumPages` int(11) DEFAULT NULL,
  `TotalCopies` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `book`
--

INSERT INTO `book` (`BookCode`, `CatCode`, `ISBN`, `BookTitle`, `AutName`, `Publisher`, `PubYear`, `NumPages`, `TotalCopies`) VALUES
('BK01-001', 'CAT001', '9786020633176', 'Atomic Habits', 'James Clear', 'Gramedia Pustaka Utama', 2019, 352, 6),
('BK01-002', 'CAT001', '9786235151854', 'Berpikir Logis Bertindak Tepat', 'Dewi Indra', 'Anak Hebat Indonesia', 2024, 240, 4),
('BK02-001', 'CAT002', '9786238036165', 'Terapi Psikologis', 'Dr. Adil Shadiq', 'Pustaka Alvabet', 2024, 352, 4),
('BK02-002', 'CAT002', '9786027143555', 'Seni Berdialog dengan Diri Sendiri', 'Dr. Muhammad Ibrahim', 'Qaf', 2024, 227, 7),
('BK03-001', 'CAT003', '9786231607843', 'Theology of Hope', 'Komaruddin Hidayat', 'Penerbit Buku Kompas', 2024, 236, 7),
('BK04-001', 'CAT004', '9786235031248', 'Asas Keadilan dan Kebebasan Berkontrak', 'Dr. Andri C. Sihombing, S.H., ', 'Pt. Refika Aditama', 2025, 310, 2);

-- --------------------------------------------------------

--
-- Table structure for table `bookcategory`
--

CREATE TABLE `bookcategory` (
  `CatCode` char(6) NOT NULL,
  `CatName` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookcategory`
--

INSERT INTO `bookcategory` (`CatCode`, `CatName`) VALUES
('CAT001', 'Self Improvement'),
('CAT002', 'Psychology'),
('CAT003', 'Religion'),
('CAT004', 'Law'),
('CAT005', 'Family');

-- --------------------------------------------------------

--
-- Table structure for table `bookcopy`
--

CREATE TABLE `bookcopy` (
  `CopyCode` char(12) NOT NULL,
  `BookCode` char(8) NOT NULL,
  `BookStatus` varchar(20) DEFAULT NULL,
  `CopyNumber` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookcopy`
--

INSERT INTO `bookcopy` (`CopyCode`, `BookCode`, `BookStatus`, `CopyNumber`) VALUES
('CPY-01001-01', 'BK01-001', 'Available', 1),
('CPY-01001-02', 'BK01-001', 'Available', 2),
('CPY-01001-03', 'BK01-001', 'Borrowed', 3),
('CPY-01001-04', 'BK01-001', 'Available', 4),
('CPY-01001-05', 'BK01-001', 'Available', 5),
('CPY-01001-06', 'BK01-001', 'Borrowed', 6),
('CPY-01002-01', 'BK01-002', 'Borrowed', 1),
('CPY-01002-02', 'BK01-002', 'Available', 2),
('CPY-01002-03', 'BK01-002', 'Borrowed', 3),
('CPY-01002-04', 'BK01-002', 'Borrowed', 4),
('CPY-02001-01', 'BK02-001', 'Borrowed', 1),
('CPY-02001-02', 'BK02-001', 'Borrowed', 2),
('CPY-02001-03', 'BK02-001', 'Borrowed', 3),
('CPY-02001-04', 'BK02-001', 'Available', 4),
('CPY-02002-01', 'BK02-002', 'Available', 1),
('CPY-02002-02', 'BK02-002', 'Available', 2),
('CPY-02002-03', 'BK02-002', 'Available', 3),
('CPY-02002-04', 'BK02-002', 'Available', 4),
('CPY-02002-05', 'BK02-002', 'Borrowed', 5),
('CPY-02002-06', 'BK02-002', 'Available', 6),
('CPY-02002-07', 'BK02-002', 'Borrowed', 7),
('CPY-03001-01', 'BK03-001', 'Available', 1),
('CPY-03001-02', 'BK03-001', 'Available', 2),
('CPY-03001-03', 'BK03-001', 'Borrowed', 3),
('CPY-03001-04', 'BK03-001', 'Borrowed', 4),
('CPY-03001-05', 'BK03-001', 'Available', 5),
('CPY-03001-06', 'BK03-001', 'Available', 6),
('CPY-03001-07', 'BK03-001', 'Available', 7),
('CPY-04001-01', 'BK04-001', 'Borrowed', 1),
('CPY-04001-02', 'BK04-001', 'Borrowed', 2);

-- --------------------------------------------------------

--
-- Table structure for table `bookcopy1`
--

CREATE TABLE `bookcopy1` (
  `CopyCode` char(12) NOT NULL,
  `BookCode` char(8) NOT NULL,
  `BookStatus` varchar(20) DEFAULT NULL,
  `CopyNumber` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `borrowedbook`
--

CREATE TABLE `borrowedbook` (
  `CopyCode` char(12) NOT NULL,
  `LoanCode` char(13) NOT NULL,
  `ReturnCond` varchar(15) DEFAULT NULL,
  `ActualReturnDate` date DEFAULT NULL,
  `OverdueFine` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `borrowedbook`
--

INSERT INTO `borrowedbook` (`CopyCode`, `LoanCode`, `ReturnCond`, `ActualReturnDate`, `OverdueFine`) VALUES
('CPY-01001-01', 'LN-Air-D33403', 'Good', '2025-03-08', 0),
('CPY-01001-02', 'LN-Air-D33403', 'Good', '2025-03-08', 0),
('CPY-01001-02', 'LN-Bat-831E07', 'Good', '2025-03-10', 0),
('CPY-01001-02', 'LN-Hab-22F601', 'Good', '2025-10-20', 223000),
('CPY-01001-03', 'LN-Air-D33403', 'Good', NULL, NULL),
('CPY-01001-06', 'LN-Air-D33403', 'Good', NULL, NULL),
('CPY-01001-06', 'LN-Hab-22F601', 'Good', NULL, NULL),
('CPY-01002-01', 'LN-Sup-8C8406', 'Good', NULL, NULL),
('CPY-01002-03', 'LN-Sup-8C8406', 'Good', NULL, NULL),
('CPY-01002-04', 'LN-Sup-8C8406', 'Good', NULL, NULL),
('CPY-02001-01', 'LN-Hab-D33402', 'Good', NULL, NULL),
('CPY-02001-02', 'LN-Hab-D33402', 'Good', NULL, NULL),
('CPY-02001-03', 'LN-Hab-D33402', 'Good', NULL, NULL),
('CPY-02002-05', 'LN-Bai-1E5A08', 'Good', NULL, NULL),
('CPY-02002-05', 'LN-Bat-D33404', 'Good', '2025-03-05', 7000),
('CPY-02002-06', 'LN-Bat-D33404', 'Good', '2025-03-05', 7000),
('CPY-02002-07', 'LN-Bai-1E5A08', 'Good', NULL, NULL),
('CPY-02002-07', 'LN-Bat-D33404', 'Good', '2025-03-08', 10000),
('CPY-03001-01', 'LN-Hul-8C8405', 'Good', '2025-03-08', 9000),
('CPY-03001-02', 'LN-Hul-8C8405', 'Good', '2025-03-10', 11000),
('CPY-03001-02', 'LN-uja-1E5A09', 'Good', '2025-10-20', 0),
('CPY-03001-03', 'LN-Hul-8C8405', 'Good', NULL, NULL),
('CPY-03001-04', 'LN-Hul-8C8405', 'Good', NULL, NULL),
('CPY-04001-01', 'LN-Bai-1E5A10', 'Good', NULL, NULL),
('CPY-04001-02', 'LN-Bai-1E5A10', 'Good', NULL, NULL);

--
-- Triggers `borrowedbook`
--
DELIMITER $$
CREATE TRIGGER `UpdateBorrowedBook` AFTER UPDATE ON `borrowedbook` FOR EACH ROW BEGIN
    -- Update BookStatus menjadi 'Available' di tabel BookCopy
    IF NEW.ActualReturnDate IS NOT NULL THEN
        UPDATE BookCopy
        SET BookStatus = 'Available'
        WHERE CopyCode = NEW.CopyCode;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `borrowing`
--

CREATE TABLE `borrowing` (
  `LoanCode` char(13) NOT NULL,
  `OffID` char(10) NOT NULL,
  `MemID` char(11) NOT NULL,
  `LoanDate` date DEFAULT NULL,
  `ReturnDate` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `borrowing`
--

INSERT INTO `borrowing` (`LoanCode`, `OffID`, `MemID`, `LoanDate`, `ReturnDate`) VALUES
('LN-Air-D33403', 'ID-OFF-001', 'ID-Air-0002', '2025-03-04', '2025-03-11'),
('LN-Bai-1E5A08', 'ID-OFF-001', 'ID-Bai-0008', '2025-10-20', '2025-10-27'),
('LN-Bai-1E5A10', 'ID-OFF-001', 'ID-Bai-0008', '2025-10-20', '2025-10-27'),
('LN-Bat-831E07', 'ID-OFF-001', 'ID-Bat-0003', '2025-03-10', '2025-03-17'),
('LN-Bat-D33404', 'ID-OFF-001', 'ID-Bat-0003', '2025-02-19', '2025-02-26'),
('LN-Hab-22F601', 'ID-OFF-001', 'ID-Hab-0001', '2025-03-04', '2025-03-11'),
('LN-Hab-D33402', 'ID-OFF-003', 'ID-Hab-0001', '2025-02-10', '2025-02-17'),
('LN-Hul-8C8405', 'ID-OFF-002', 'ID-Hul-0006', '2025-02-20', '2025-02-27'),
('LN-Sup-8C8406', 'ID-OFF-003', 'ID-Sup-0004', '2025-03-05', '2025-03-12'),
('LN-uja-1E5A09', 'ID-OFF-004', 'ID-uja-0007', '2025-10-20', '2025-10-27');

-- --------------------------------------------------------

--
-- Table structure for table `login`
--

CREATE TABLE `login` (
  `ID` int(11) NOT NULL,
  `NameUser` varchar(100) NOT NULL,
  `Username` varchar(100) NOT NULL,
  `PasswordUser` varchar(100) NOT NULL,
  `TypeUser` varchar(1) NOT NULL DEFAULT 'U'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `login`
--

INSERT INTO `login` (`ID`, `NameUser`, `Username`, `PasswordUser`, `TypeUser`) VALUES
(1, 'Officer', 'admin', '8c6976e5b5410415bde908bd4dee15dfb167a9c873fc4bb8a81f6f2ab448a918', 'A');

-- --------------------------------------------------------

--
-- Table structure for table `member`
--

CREATE TABLE `member` (
  `MemID` char(11) NOT NULL,
  `MemName` varchar(50) NOT NULL,
  `MemEmail` varchar(30) DEFAULT NULL,
  `MemTelp` varchar(15) DEFAULT NULL,
  `MemAddress` varchar(100) DEFAULT NULL,
  `RegDate` date NOT NULL,
  `MemPassword` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `member`
--

INSERT INTO `member` (`MemID`, `MemName`, `MemEmail`, `MemTelp`, `MemAddress`, `RegDate`, `MemPassword`) VALUES
('ID-Air-0002', 'Aira', 'aira@gmail.com', '081236475976', 'Jl. Durian Kel.Salak Kec.Manggis Kota Nanas', '2025-03-02', NULL),
('ID-Bai-0008', 'Baim', 'baim@gmail.com', '089988374625', 'Jl. Antah Berantah', '2025-10-20', '$2y$10$Z6kkxV6ooSQt2N8F1uPTfe0hHt9UGEg0iAHbulHpkpn0sSVQf1EsC'),
('ID-Bat-0003', 'Batman', 'batman@gmail.com', '081234544456', 'Jl. Apapun', '2025-03-04', NULL),
('ID-Hab-0001', 'Habib Riziq', 'habib95@gmail.com', '084365763867', 'Jl.Bandar Buat Kel. Koto Lalang Kec.Limau Manih Kota Semangka', '2025-03-02', NULL),
('ID-Hul-0006', 'Hulk', 'hulk@gmail.com', '083187923647', 'Jl.Ajapokoknya', '2025-03-05', NULL),
('ID-Iro-0005', 'Ironman', 'ironman@gmail.com', '083187785678', 'Jl.Dimanapun', '2025-03-04', NULL),
('ID-Sup-0004', 'Superman', 'superman@gmail.com', '083182178974', 'Jl.Kapanpun', '2025-03-04', NULL),
('ID-uja-0007', 'ujang', 'ujang@gmail.com', '083189456734', 'Jl. Kipas Angin', '2025-10-17', '$2y$10$alEbS5Ap.YbZVMeHFCMd/ecJX/wPxlBRiwOcxrgptpLBt6T/kuQZ6');

-- --------------------------------------------------------

--
-- Table structure for table `officer`
--

CREATE TABLE `officer` (
  `OffID` char(10) NOT NULL,
  `OffName` varchar(50) NOT NULL,
  `OffEmail` varchar(30) DEFAULT NULL,
  `OffTelp` varchar(15) DEFAULT NULL,
  `OffPassword` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `officer`
--

INSERT INTO `officer` (`OffID`, `OffName`, `OffEmail`, `OffTelp`, `OffPassword`) VALUES
('ID-OFF-001', 'Yazid Putra Muhammad Riziq', 'yazid@gmail.com', '081234567890', 'yazid123'),
('ID-OFF-002', 'Muhammad Hasryl Natawijaya', 'hasryl@gmail.com', '081245678392', NULL),
('ID-OFF-003', 'Muhammad Ainul Hakim', 'ainul@gmail.com', '083178649387', NULL),
('ID-OFF-004', 'Naufalnadi', 'naufal@gmail.com', '085891665911', 'naufal123');

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_booklistdashboard`
-- (See below for the actual view)
--
CREATE TABLE `view_booklistdashboard` (
`BookTitle` varchar(50)
,`CatName` varchar(50)
,`AutName` varchar(30)
,`PubYear` int(11)
,`TotalBooks` bigint(21)
,`Availability` decimal(22,0)
,`BorrowedBooks` decimal(22,0)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_bookreturned`
-- (See below for the actual view)
--
CREATE TABLE `view_bookreturned` (
`CopyCode` char(12)
,`LoanCode` char(13)
,`MemName` varchar(50)
,`BookTitle` varchar(50)
,`ActualReturnDate` date
,`OverdueFine` int(11)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_bookstatistics`
-- (See below for the actual view)
--
CREATE TABLE `view_bookstatistics` (
`BookTitle` varchar(50)
,`TotalBooks` bigint(21)
,`AvailableBooks` decimal(22,0)
,`BorrowedBooks` decimal(22,0)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_bookstatus`
-- (See below for the actual view)
--
CREATE TABLE `view_bookstatus` (
`BookTitle` varchar(50)
,`BookCode` char(8)
,`CopyCode` char(12)
,`CatName` varchar(50)
,`BookStatus` varchar(20)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_borrowedbooks`
-- (See below for the actual view)
--
CREATE TABLE `view_borrowedbooks` (
`CopyCode` char(12)
,`BookTitle` varchar(50)
,`LoanCode` char(13)
,`MemID` char(11)
,`MemName` varchar(50)
,`LoanDate` date
,`ReturnDate` date
,`ActualReturnDate` date
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_borrowmember`
-- (See below for the actual view)
--
CREATE TABLE `view_borrowmember` (
`MemID` char(11)
,`CatName` varchar(50)
,`BookTitle` varchar(50)
,`LoanDate` date
,`ReturnDate` date
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_getallbooks`
-- (See below for the actual view)
--
CREATE TABLE `view_getallbooks` (
`BookCode` char(8)
,`CatCode` char(6)
,`CatName` varchar(50)
,`ISBN` char(17)
,`BookTitle` varchar(50)
,`AutName` varchar(30)
,`Publisher` varchar(30)
,`PubYear` int(11)
,`NumPages` int(11)
,`TotalCopies` int(11)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_loanlist`
-- (See below for the actual view)
--
CREATE TABLE `view_loanlist` (
`LoanCode` char(13)
,`OffName` varchar(50)
,`MemName` varchar(50)
,`LoanDate` date
,`ReturnDate` date
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_memberborrowing`
-- (See below for the actual view)
--
CREATE TABLE `view_memberborrowing` (
`CopyCode` char(12)
,`LoanCode` char(13)
,`BookTitle` varchar(50)
,`MemName` varchar(50)
,`MemID` char(11)
,`LoanDate` date
,`ReturnDate` date
,`ActualReturnDate` date
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_topfinedmembers`
-- (See below for the actual view)
--
CREATE TABLE `view_topfinedmembers` (
`MemID` char(11)
,`MemName` varchar(50)
,`TotalFine` decimal(32,0)
);

-- --------------------------------------------------------

--
-- Structure for view `view_booklistdashboard`
--
DROP TABLE IF EXISTS `view_booklistdashboard`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_booklistdashboard`  AS SELECT `b`.`BookTitle` AS `BookTitle`, `c`.`CatName` AS `CatName`, `b`.`AutName` AS `AutName`, `b`.`PubYear` AS `PubYear`, count(distinct `b`.`BookCode`) AS `TotalBooks`, sum(case when `bc`.`BookStatus` = 'Available' then 1 else 0 end) AS `Availability`, sum(case when `bc`.`BookStatus` = 'Borrowed' then 1 else 0 end) AS `BorrowedBooks` FROM ((`bookcategory` `c` join `book` `b` on(`c`.`CatCode` = `b`.`CatCode`)) join `bookcopy` `bc` on(`b`.`BookCode` = `bc`.`BookCode`)) GROUP BY `b`.`BookTitle` ;

-- --------------------------------------------------------

--
-- Structure for view `view_bookreturned`
--
DROP TABLE IF EXISTS `view_bookreturned`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_bookreturned`  AS SELECT `bb`.`CopyCode` AS `CopyCode`, `br`.`LoanCode` AS `LoanCode`, `m`.`MemName` AS `MemName`, `b`.`BookTitle` AS `BookTitle`, `bb`.`ActualReturnDate` AS `ActualReturnDate`, `bb`.`OverdueFine` AS `OverdueFine` FROM ((((`borrowedbook` `bb` join `bookcopy` `bc` on(`bb`.`CopyCode` = `bc`.`CopyCode`)) join `book` `b` on(`bc`.`BookCode` = `b`.`BookCode`)) join `borrowing` `br` on(`bb`.`LoanCode` = `br`.`LoanCode`)) join `member` `m` on(`br`.`MemID` = `m`.`MemID`)) WHERE `bb`.`ActualReturnDate` is not null ;

-- --------------------------------------------------------

--
-- Structure for view `view_bookstatistics`
--
DROP TABLE IF EXISTS `view_bookstatistics`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_bookstatistics`  AS SELECT `b`.`BookTitle` AS `BookTitle`, count(distinct `bc`.`CopyCode`) AS `TotalBooks`, sum(case when `bc`.`BookStatus` = 'Available' then 1 else 0 end) AS `AvailableBooks`, sum(case when `bc`.`BookStatus` = 'Borrowed' then 1 else 0 end) AS `BorrowedBooks` FROM (`book` `b` join `bookcopy` `bc` on(`b`.`BookCode` = `bc`.`BookCode`)) GROUP BY `b`.`BookTitle` ;

-- --------------------------------------------------------

--
-- Structure for view `view_bookstatus`
--
DROP TABLE IF EXISTS `view_bookstatus`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_bookstatus`  AS SELECT `b`.`BookTitle` AS `BookTitle`, `b`.`BookCode` AS `BookCode`, `bc`.`CopyCode` AS `CopyCode`, `c`.`CatName` AS `CatName`, `bc`.`BookStatus` AS `BookStatus` FROM ((`book` `b` join `bookcopy` `bc` on(`b`.`BookCode` = `bc`.`BookCode`)) join `bookcategory` `c` on(`b`.`CatCode` = `c`.`CatCode`)) ;

-- --------------------------------------------------------

--
-- Structure for view `view_borrowedbooks`
--
DROP TABLE IF EXISTS `view_borrowedbooks`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_borrowedbooks`  AS SELECT `bb`.`CopyCode` AS `CopyCode`, `b`.`BookTitle` AS `BookTitle`, `br`.`LoanCode` AS `LoanCode`, `m`.`MemID` AS `MemID`, `m`.`MemName` AS `MemName`, `br`.`LoanDate` AS `LoanDate`, `br`.`ReturnDate` AS `ReturnDate`, `bb`.`ActualReturnDate` AS `ActualReturnDate` FROM ((((`borrowedbook` `bb` join `bookcopy` `bc` on(`bb`.`CopyCode` = `bc`.`CopyCode`)) join `book` `b` on(`bc`.`BookCode` = `b`.`BookCode`)) join `borrowing` `br` on(`bb`.`LoanCode` = `br`.`LoanCode`)) join `member` `m` on(`br`.`MemID` = `m`.`MemID`)) WHERE `bb`.`ActualReturnDate` is null ;

-- --------------------------------------------------------

--
-- Structure for view `view_borrowmember`
--
DROP TABLE IF EXISTS `view_borrowmember`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_borrowmember`  AS SELECT `br`.`MemID` AS `MemID`, `bc`.`CatName` AS `CatName`, `b`.`BookTitle` AS `BookTitle`, `br`.`LoanDate` AS `LoanDate`, `br`.`ReturnDate` AS `ReturnDate` FROM ((((`book` `b` join `bookcategory` `bc` on(`b`.`CatCode` = `bc`.`CatCode`)) join `bookcopy` `bcy` on(`b`.`BookCode` = `bcy`.`BookCode`)) join `borrowedbook` `bb` on(`bcy`.`CopyCode` = `bb`.`CopyCode`)) join `borrowing` `br` on(`br`.`LoanCode` = `bb`.`LoanCode`)) ;

-- --------------------------------------------------------

--
-- Structure for view `view_getallbooks`
--
DROP TABLE IF EXISTS `view_getallbooks`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_getallbooks`  AS SELECT `b`.`BookCode` AS `BookCode`, `b`.`CatCode` AS `CatCode`, `bc`.`CatName` AS `CatName`, `b`.`ISBN` AS `ISBN`, `b`.`BookTitle` AS `BookTitle`, `b`.`AutName` AS `AutName`, `b`.`Publisher` AS `Publisher`, `b`.`PubYear` AS `PubYear`, `b`.`NumPages` AS `NumPages`, `b`.`TotalCopies` AS `TotalCopies` FROM (`book` `b` join `bookcategory` `bc` on(`b`.`CatCode` = `bc`.`CatCode`)) ;

-- --------------------------------------------------------

--
-- Structure for view `view_loanlist`
--
DROP TABLE IF EXISTS `view_loanlist`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_loanlist`  AS SELECT `b`.`LoanCode` AS `LoanCode`, `o`.`OffName` AS `OffName`, `m`.`MemName` AS `MemName`, `b`.`LoanDate` AS `LoanDate`, `b`.`ReturnDate` AS `ReturnDate` FROM ((`borrowing` `b` join `officer` `o` on(`b`.`OffID` = `o`.`OffID`)) join `member` `m` on(`b`.`MemID` = `m`.`MemID`)) ;

-- --------------------------------------------------------

--
-- Structure for view `view_memberborrowing`
--
DROP TABLE IF EXISTS `view_memberborrowing`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_memberborrowing`  AS SELECT `bb`.`CopyCode` AS `CopyCode`, `br`.`LoanCode` AS `LoanCode`, `b`.`BookTitle` AS `BookTitle`, `m`.`MemName` AS `MemName`, `br`.`MemID` AS `MemID`, `br`.`LoanDate` AS `LoanDate`, `br`.`ReturnDate` AS `ReturnDate`, `bb`.`ActualReturnDate` AS `ActualReturnDate` FROM ((((`borrowedbook` `bb` join `bookcopy` `bc` on(`bb`.`CopyCode` = `bc`.`CopyCode`)) join `book` `b` on(`bc`.`BookCode` = `b`.`BookCode`)) join `borrowing` `br` on(`bb`.`LoanCode` = `br`.`LoanCode`)) join `member` `m` on(`br`.`MemID` = `m`.`MemID`)) ORDER BY `br`.`LoanDate` DESC ;

-- --------------------------------------------------------

--
-- Structure for view `view_topfinedmembers`
--
DROP TABLE IF EXISTS `view_topfinedmembers`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_topfinedmembers`  AS SELECT `m`.`MemID` AS `MemID`, `m`.`MemName` AS `MemName`, sum(`bb`.`OverdueFine`) AS `TotalFine` FROM ((`member` `m` join `borrowing` `br` on(`m`.`MemID` = `br`.`MemID`)) join `borrowedbook` `bb` on(`br`.`LoanCode` = `bb`.`LoanCode`)) GROUP BY `m`.`MemID`, `m`.`MemName` ORDER BY sum(`bb`.`OverdueFine`) DESC ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `book`
--
ALTER TABLE `book`
  ADD PRIMARY KEY (`BookCode`),
  ADD KEY `fkBook` (`CatCode`);
ALTER TABLE `book` ADD FULLTEXT KEY `ftx_BookTitle` (`BookTitle`);

--
-- Indexes for table `bookcategory`
--
ALTER TABLE `bookcategory`
  ADD PRIMARY KEY (`CatCode`);

--
-- Indexes for table `bookcopy`
--
ALTER TABLE `bookcopy`
  ADD PRIMARY KEY (`CopyCode`),
  ADD KEY `idx_BookCode` (`BookCode`),
  ADD KEY `idx_BookStatus` (`BookStatus`);

--
-- Indexes for table `borrowedbook`
--
ALTER TABLE `borrowedbook`
  ADD PRIMARY KEY (`CopyCode`,`LoanCode`),
  ADD KEY `idx_CopyCode` (`CopyCode`),
  ADD KEY `idx_LoanCode` (`LoanCode`);

--
-- Indexes for table `borrowing`
--
ALTER TABLE `borrowing`
  ADD PRIMARY KEY (`LoanCode`),
  ADD KEY `idx_MemID` (`MemID`),
  ADD KEY `idx_OffID` (`OffID`),
  ADD KEY `idx_LoanDate` (`LoanDate`),
  ADD KEY `idx_ReturnDate` (`ReturnDate`);

--
-- Indexes for table `login`
--
ALTER TABLE `login`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `member`
--
ALTER TABLE `member`
  ADD PRIMARY KEY (`MemID`);
ALTER TABLE `member` ADD FULLTEXT KEY `ftx_MemName` (`MemName`);
ALTER TABLE `member` ADD FULLTEXT KEY `ftx_MemAddress` (`MemAddress`);

--
-- Indexes for table `officer`
--
ALTER TABLE `officer`
  ADD PRIMARY KEY (`OffID`);
ALTER TABLE `officer` ADD FULLTEXT KEY `ftx_OffName` (`OffName`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `book`
--
ALTER TABLE `book`
  ADD CONSTRAINT `fkBook` FOREIGN KEY (`CatCode`) REFERENCES `bookcategory` (`CatCode`);

--
-- Constraints for table `bookcopy`
--
ALTER TABLE `bookcopy`
  ADD CONSTRAINT `fkBookCopy` FOREIGN KEY (`BookCode`) REFERENCES `book` (`BookCode`);

--
-- Constraints for table `borrowedbook`
--
ALTER TABLE `borrowedbook`
  ADD CONSTRAINT `fkBorrowedBookCopy` FOREIGN KEY (`CopyCode`) REFERENCES `bookcopy` (`CopyCode`),
  ADD CONSTRAINT `fkBorrowedBookLoan` FOREIGN KEY (`LoanCode`) REFERENCES `borrowing` (`LoanCode`);

--
-- Constraints for table `borrowing`
--
ALTER TABLE `borrowing`
  ADD CONSTRAINT `fkBorrowingMem` FOREIGN KEY (`MemID`) REFERENCES `member` (`MemID`),
  ADD CONSTRAINT `fkBorrowingOff` FOREIGN KEY (`OffID`) REFERENCES `officer` (`OffID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
