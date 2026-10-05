-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- 主機： 127.0.0.1
-- 產生時間： 2025-06-11 14:26:53
-- 伺服器版本： 10.4.32-MariaDB
-- PHP 版本： 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 資料庫： `hotel`
--

DELIMITER $$
--
-- 程序
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `AssignRoom` (IN `res_id` INT)   BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE room_id VARCHAR(25);
    DECLARE room_type VARCHAR(25);
    DECLARE qty INT;
    DECLARE checkin DATE;
    DECLARE checkout DATE;
    DECLARE cur CURSOR FOR 
    SELECT RoomID 
    FROM Room 
    WHERE RoomType = room_type AND RoomID NOT IN (
        SELECT rr.RoomID
        FROM Reservation_Room rr
        JOIN Reservation r ON rr.ReservationID = r.ReservationID
        WHERE r.RoomType = room_type
        AND ((r.CheckInDate < checkout AND r.CheckOutDate > checkin))
    )
    ORDER BY RoomID
    LIMIT qty;

    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

  -- 取得目前訂單資訊
SELECT RoomType, Quantity, CheckInDate, CheckOutDate INTO room_type, qty, checkin, checkout
FROM Reservation WHERE ReservationID = res_id;

    OPEN cur;
    read_loop: LOOP
        FETCH cur INTO room_id;
        IF done THEN
            LEAVE read_loop;
        END IF;
        INSERT INTO Reservation_Room (ReservationID, RoomID, RoomType)
        VALUES (res_id, room_id, room_type);
    END LOOP;
    CLOSE cur;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- 資料表結構 `customer`
--

CREATE TABLE `customer` (
  `CustomerID` int(11) NOT NULL,
  `CustomerName` varchar(25) NOT NULL,
  `CustomerTimes` int(11) NOT NULL,
  `Email` varchar(25) NOT NULL,
  `CustomerPassword` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- 傾印資料表的資料 `customer`
--

INSERT INTO `customer` (`CustomerID`, `CustomerName`, `CustomerTimes`, `Email`, `CustomerPassword`) VALUES
(1, '王小明', 53, '123456@gmail.com', 123456),
(2, '李美麗', 8, '234567@gmail.com', 234567),
(3, '張大同', 6, '345678@gmail.com', 345678),
(4, '陳曉雯', 3, '456789@gmail.com', 456789),
(5, '林育成', 7, '567890@gmail.com', 567890),
(6, '趙思雨', 11, '678901@gmail.com', 678901),
(7, '周子瑜', 2, '789012@gmail.com', 789012),
(8, '吳宗憲', 12, '890123@gmail.com', 890123),
(9, '賴冠霖', 10, '901234@gmail.com', 901234),
(10, '曾怡君', 1, '112233@gmail.com', 112233);

-- --------------------------------------------------------

--
-- 替換檢視表以便查看 `customerwithvip`
-- (請參考以下實際畫面)
--
CREATE TABLE `customerwithvip` (
`CustomerID` int(11)
,`CustomerName` varchar(25)
,`CustomerTimes` int(11)
,`VIPlevel` int(1)
,`VIPName` varchar(25)
,`Discount` float
);

-- --------------------------------------------------------

--
-- 資料表結構 `manager`
--

CREATE TABLE `manager` (
  `ManagerID` int(11) NOT NULL,
  `ManagerName` varchar(25) NOT NULL,
  `ManagerPassword` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- 傾印資料表的資料 `manager`
--

INSERT INTO `manager` (`ManagerID`, `ManagerName`, `ManagerPassword`) VALUES
(1, 'admin01', 654321),
(2, 'admin02', 237903),
(3, 'admin03', 247820),
(4, 'admin04', 104982),
(5, 'admin05', 987383),
(6, 'alice_wu', 567890),
(7, 'bob_chen', 222333);

-- --------------------------------------------------------

--
-- 資料表結構 `reservation`
--

CREATE TABLE `reservation` (
  `ReservationID` int(11) NOT NULL,
  `CustomerID` int(11) NOT NULL,
  `CheckInDate` date NOT NULL,
  `CheckOutDate` date NOT NULL,
  `RoomType` varchar(25) NOT NULL,
  `Quantity` int(11) NOT NULL,
  `People` int(11) NOT NULL,
  `Paid` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- 傾印資料表的資料 `reservation`
--

INSERT INTO `reservation` (`ReservationID`, `CustomerID`, `CheckInDate`, `CheckOutDate`, `RoomType`, `Quantity`, `People`, `Paid`) VALUES
(1, 1, '2025-06-09', '2025-06-11', 'triple', 1, 3, 1),
(2, 1, '2025-06-15', '2025-06-16', 'double', 1, 2, 1),
(3, 1, '2025-06-07', '2025-06-08', 'single', 1, 1, 1),
(4, 1, '2025-06-12', '2025-06-14', 'double', 1, 2, 1),
(5, 1, '2025-06-19', '2025-06-20', 'single', 1, 1, 0),
(6, 2, '2025-06-03', '2025-06-04', 'triple', 3, 9, 1),
(7, 2, '2025-06-26', '2025-06-27', 'single', 3, 3, 1),
(8, 2, '2025-06-06', '2025-06-09', 'single', 1, 1, 1),
(9, 2, '2025-06-21', '2025-06-24', 'double', 2, 4, 1),
(10, 2, '2025-06-24', '2025-06-27', 'double', 3, 6, 1),
(11, 2, '2025-06-27', '2025-06-30', 'single', 2, 2, 1),
(12, 2, '2025-06-23', '2025-06-24', 'quad', 1, 4, 0),
(13, 2, '2025-06-25', '2025-06-28', 'double', 1, 2, 0),
(14, 3, '2025-07-08', '2025-07-13', 'single', 3, 3, 1),
(15, 3, '2025-06-21', '2025-06-22', 'quad', 3, 11, 1),
(16, 3, '2025-06-24', '2025-06-26', 'single', 4, 4, 1),
(17, 3, '2025-06-27', '2025-06-29', 'single', 2, 2, 1),
(18, 3, '2025-06-01', '2025-06-02', 'double', 1, 2, 1),
(19, 3, '2025-06-30', '2025-07-03', 'triple', 1, 3, 0),
(20, 4, '2025-06-07', '2025-06-10', 'double', 1, 2, 1),
(21, 4, '2025-06-07', '2025-06-09', 'triple', 3, 9, 1),
(22, 4, '2025-06-03', '2025-06-05', 'double', 3, 6, 1),
(23, 5, '2025-06-16', '2025-06-17', 'triple', 1, 3, 1),
(24, 5, '2025-06-15', '2025-06-16', 'quad', 1, 4, 1),
(25, 5, '2025-06-20', '2025-06-23', 'quad', 2, 7, 1),
(26, 5, '2025-06-16', '2025-06-19', 'quad', 1, 4, 1),
(27, 5, '2025-06-20', '2025-06-23', 'single', 2, 2, 0),
(28, 5, '2025-06-23', '2025-06-26', 'double', 4, 7, 0),
(29, 5, '2025-06-28', '2025-07-01', 'single', 2, 2, 0),
(30, 6, '2025-06-26', '2025-06-28', 'quad', 2, 8, 1),
(31, 6, '2025-07-01', '2025-07-02', 'single', 3, 3, 1),
(32, 6, '2025-06-30', '2025-07-01', 'quad', 1, 4, 1),
(33, 6, '2025-06-18', '2025-06-20', 'quad', 1, 4, 1),
(34, 6, '2025-06-11', '2025-06-13', 'triple', 2, 6, 1),
(35, 6, '2025-06-30', '2025-07-01', 'single', 3, 3, 1),
(36, 6, '2025-06-27', '2025-06-29', 'triple', 1, 3, 1),
(37, 6, '2025-06-14', '2025-06-17', 'double', 1, 2, 1),
(38, 6, '2025-06-21', '2025-06-24', 'double', 3, 6, 1),
(39, 6, '2025-06-06', '2025-06-07', 'double', 3, 5, 1),
(40, 6, '2025-06-13', '2025-06-14', 'quad', 1, 4, 1),
(41, 7, '2025-06-17', '2025-06-19', 'quad', 2, 7, 1),
(42, 7, '2025-06-05', '2025-06-06', 'quad', 2, 8, 1),
(43, 8, '2025-06-16', '2025-06-17', 'double', 1, 2, 1),
(44, 8, '2025-06-29', '2025-07-02', 'double', 4, 8, 1),
(45, 8, '2025-06-15', '2025-06-17', 'single', 4, 4, 1),
(46, 8, '2025-06-18', '2025-06-21', 'triple', 1, 3, 1),
(47, 8, '2025-06-18', '2025-06-20', 'triple', 3, 9, 1),
(48, 8, '2025-06-15', '2025-06-17', 'triple', 1, 3, 1),
(49, 8, '2025-06-28', '2025-07-01', 'double', 4, 8, 1),
(50, 8, '2025-06-15', '2025-06-18', 'quad', 2, 8, 1),
(51, 8, '2025-06-29', '2025-07-02', 'quad', 2, 7, 0),
(52, 8, '2025-06-05', '2025-06-08', 'quad', 3, 12, 0),
(53, 8, '2025-06-08', '2025-06-09', 'double', 3, 6, 0),
(54, 8, '2025-06-01', '2025-06-03', 'double', 3, 5, 0),
(55, 9, '2025-06-26', '2025-06-28', 'double', 1, 2, 1),
(56, 9, '2025-06-08', '2025-06-09', 'double', 4, 7, 1),
(57, 9, '2025-06-26', '2025-06-27', 'triple', 2, 6, 1),
(58, 9, '2025-07-01', '2025-07-03', 'triple', 3, 9, 1),
(59, 9, '2025-06-14', '2025-06-16', 'single', 5, 5, 1),
(60, 9, '2025-06-23', '2025-06-24', 'quad', 2, 8, 1),
(61, 9, '2025-06-02', '2025-06-05', 'triple', 2, 6, 1),
(62, 9, '2025-06-25', '2025-06-27', 'double', 3, 6, 1),
(63, 9, '2025-06-11', '2025-06-13', 'double', 1, 2, 1),
(64, 9, '2025-06-30', '2025-07-01', 'double', 1, 2, 0),
(65, 10, '2025-06-19', '2025-06-21', 'double', 3, 6, 0),
(66, 1, '2025-05-07', '2025-05-08', 'single', 1, 1, 1),
(67, 1, '2025-05-15', '2025-05-16', 'single', 1, 1, 1),
(68, 1, '2025-04-02', '2025-04-03', 'single', 1, 1, 1),
(69, 1, '2025-05-18', '2025-05-19', 'single', 1, 1, 1),
(70, 1, '2025-04-07', '2025-04-08', 'single', 1, 1, 1),
(71, 1, '2025-04-25', '2025-04-26', 'single', 1, 1, 1),
(72, 1, '2025-03-06', '2025-03-07', 'single', 1, 1, 1),
(73, 1, '2025-04-27', '2025-04-28', 'single', 1, 1, 1),
(74, 1, '2025-04-13', '2025-04-14', 'single', 1, 1, 1),
(75, 1, '2025-03-25', '2025-03-26', 'single', 1, 1, 1),
(76, 1, '2025-02-05', '2025-02-07', 'single', 1, 1, 1),
(78, 1, '2025-03-28', '2025-03-29', 'single', 1, 1, 1),
(79, 1, '2025-03-20', '2025-03-21', 'single', 1, 1, 1),
(80, 1, '2025-02-01', '2025-02-02', 'single', 1, 1, 1),
(81, 1, '2025-03-16', '2025-03-17', 'single', 1, 1, 1),
(82, 1, '2025-02-28', '2025-03-01', 'single', 1, 1, 1),
(83, 1, '2025-02-08', '2025-02-09', 'single', 1, 1, 1),
(84, 1, '2025-01-04', '2025-01-05', 'single', 1, 1, 1),
(85, 1, '2025-01-13', '2025-01-14', 'single', 1, 1, 1),
(86, 1, '2025-01-07', '2025-01-08', 'single', 1, 1, 1),
(87, 1, '2025-01-09', '2025-01-10', 'single', 1, 1, 1),
(88, 1, '2025-01-15', '2025-01-16', 'single', 1, 1, 1),
(89, 1, '2025-01-16', '2025-01-17', 'single', 1, 1, 1),
(90, 1, '2025-01-18', '2025-01-19', 'single', 1, 1, 1),
(91, 1, '2025-01-24', '2025-01-25', 'single', 1, 1, 1),
(93, 1, '2025-01-20', '2025-01-21', 'single', 1, 1, 1),
(94, 1, '2025-01-22', '2025-01-23', 'single', 1, 1, 1),
(95, 1, '2025-02-03', '2025-02-04', 'single', 1, 1, 1),
(96, 1, '2025-02-10', '2025-02-11', 'single', 1, 1, 1),
(97, 1, '2025-02-13', '2025-02-14', 'single', 1, 1, 1),
(98, 1, '2025-02-15', '2025-02-16', 'single', 1, 1, 1),
(99, 1, '2025-02-17', '2025-02-18', 'single', 1, 1, 1),
(100, 1, '2025-02-19', '2025-02-20', 'single', 1, 1, 0),
(101, 1, '2025-02-20', '2025-02-21', 'single', 1, 1, 1),
(102, 1, '2025-02-22', '2025-02-23', 'single', 1, 1, 1),
(103, 1, '2025-02-24', '2025-02-25', 'single', 1, 1, 1),
(104, 1, '2025-02-26', '2025-02-27', 'single', 1, 1, 1),
(105, 1, '2025-05-01', '2025-05-02', 'single', 1, 1, 1),
(106, 1, '2025-05-03', '2025-05-04', 'single', 1, 1, 1),
(107, 1, '2025-05-05', '2025-05-06', 'single', 1, 1, 1),
(108, 1, '2025-05-09', '2025-05-10', 'single', 1, 1, 1),
(109, 1, '2025-05-11', '2025-05-12', 'single', 1, 1, 1),
(110, 1, '2025-05-13', '2025-05-14', 'single', 1, 1, 1),
(111, 1, '2025-05-20', '2025-05-21', 'single', 1, 1, 1),
(112, 1, '2025-05-22', '2025-05-23', 'single', 1, 1, 1),
(113, 1, '2025-05-24', '2025-05-25', 'single', 1, 1, 1),
(114, 1, '2025-05-26', '2025-05-27', 'single', 1, 1, 1),
(115, 1, '2025-05-28', '2025-05-29', 'single', 1, 1, 1);

-- --------------------------------------------------------

--
-- 資料表結構 `reservation_room`
--

CREATE TABLE `reservation_room` (
  `ReservationID` int(11) NOT NULL,
  `RoomType` varchar(25) NOT NULL,
  `RoomID` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- 傾印資料表的資料 `reservation_room`
--

INSERT INTO `reservation_room` (`ReservationID`, `RoomType`, `RoomID`) VALUES
(1, 'triple', 'C001'),
(2, 'double', 'B001'),
(3, 'single', 'A001'),
(4, 'double', 'B001'),
(5, 'single', 'A001'),
(6, 'triple', 'C001'),
(6, 'triple', 'C002'),
(6, 'triple', 'C003'),
(7, 'single', 'A001'),
(7, 'single', 'A002'),
(7, 'single', 'A003'),
(8, 'single', 'A002'),
(9, 'double', 'B001'),
(9, 'double', 'B002'),
(10, 'double', 'B001'),
(10, 'double', 'B002'),
(10, 'double', 'B003'),
(11, 'single', 'A001'),
(11, 'single', 'A002'),
(12, 'quad', 'D001'),
(13, 'double', 'B004'),
(14, 'single', 'A001'),
(14, 'single', 'A002'),
(14, 'single', 'A003'),
(15, 'quad', 'D001'),
(15, 'quad', 'D002'),
(15, 'quad', 'D003'),
(16, 'single', 'A001'),
(16, 'single', 'A002'),
(16, 'single', 'A003'),
(16, 'single', 'A004'),
(17, 'single', 'A003'),
(17, 'single', 'A004'),
(18, 'double', 'B001'),
(19, 'triple', 'C001'),
(20, 'double', 'B001'),
(21, 'triple', 'C001'),
(21, 'triple', 'C002'),
(21, 'triple', 'C003'),
(22, 'double', 'B001'),
(22, 'double', 'B002'),
(22, 'double', 'B003'),
(23, 'triple', 'C001'),
(24, 'quad', 'D002'),
(25, 'quad', 'D001'),
(25, 'quad', 'D002'),
(26, 'quad', 'D001'),
(27, 'single', 'A001'),
(27, 'single', 'A002'),
(28, 'double', 'B001'),
(28, 'double', 'B002'),
(28, 'double', 'B003'),
(28, 'double', 'B004'),
(29, 'single', 'A005'),
(29, 'single', 'A006'),
(30, 'quad', 'D001'),
(30, 'quad', 'D002'),
(31, 'single', 'A001'),
(31, 'single', 'A002'),
(31, 'single', 'A003'),
(32, 'quad', 'D001'),
(33, 'quad', 'D001'),
(34, 'triple', 'C001'),
(34, 'triple', 'C002'),
(35, 'single', 'A001'),
(35, 'single', 'A002'),
(35, 'single', 'A003'),
(36, 'triple', 'C001'),
(37, 'double', 'B002'),
(38, 'double', 'B004'),
(38, 'double', 'B005'),
(38, 'double', 'B006'),
(39, 'double', 'B002'),
(39, 'double', 'B003'),
(39, 'double', 'B004'),
(40, 'quad', 'D001'),
(41, 'quad', 'D002'),
(41, 'quad', 'D003'),
(42, 'quad', 'D001'),
(42, 'quad', 'D002'),
(43, 'double', 'B003'),
(44, 'double', 'B001'),
(44, 'double', 'B002'),
(44, 'double', 'B003'),
(44, 'double', 'B004'),
(45, 'single', 'A001'),
(45, 'single', 'A002'),
(45, 'single', 'A003'),
(45, 'single', 'A004'),
(46, 'triple', 'C003'),
(47, 'triple', 'C004'),
(47, 'triple', 'C005'),
(47, 'triple', 'C006'),
(48, 'triple', 'C002'),
(49, 'double', 'B005'),
(49, 'double', 'B006'),
(49, 'double', 'B007'),
(49, 'double', 'B008'),
(50, 'quad', 'D003'),
(50, 'quad', 'D004'),
(51, 'quad', 'D002'),
(51, 'quad', 'D003'),
(52, 'quad', 'D004'),
(52, 'quad', 'D005'),
(52, 'quad', 'D006'),
(53, 'double', 'B002'),
(53, 'double', 'B003'),
(53, 'double', 'B004'),
(54, 'double', 'B002'),
(54, 'double', 'B003'),
(54, 'double', 'B004'),
(55, 'double', 'B005'),
(56, 'double', 'B005'),
(56, 'double', 'B006'),
(56, 'double', 'B007'),
(56, 'double', 'B008'),
(57, 'triple', 'C001'),
(57, 'triple', 'C002'),
(58, 'triple', 'C001'),
(58, 'triple', 'C002'),
(58, 'triple', 'C003'),
(59, 'single', 'A005'),
(59, 'single', 'A006'),
(59, 'single', 'A007'),
(59, 'single', 'A008'),
(59, 'single', 'A009'),
(60, 'quad', 'D005'),
(60, 'quad', 'D006'),
(61, 'triple', 'C004'),
(61, 'triple', 'C005'),
(62, 'double', 'B006'),
(62, 'double', 'B007'),
(62, 'double', 'B008'),
(63, 'double', 'B002'),
(64, 'double', 'B009'),
(65, 'double', 'B001'),
(65, 'double', 'B002'),
(65, 'double', 'B003'),
(66, 'single', 'A001'),
(67, 'single', 'A001'),
(68, 'single', 'A001'),
(69, 'single', 'A001'),
(70, 'single', 'A001'),
(71, 'single', 'A001'),
(72, 'single', 'A001'),
(73, 'single', 'A001'),
(74, 'single', 'A001'),
(75, 'single', 'A001'),
(76, 'single', 'A001'),
(78, 'single', 'A001'),
(79, 'single', 'A001'),
(80, 'single', 'A001'),
(81, 'single', 'A001'),
(82, 'single', 'A001'),
(83, 'single', 'A001'),
(84, 'single', 'A001'),
(85, 'single', 'A001'),
(86, 'single', 'A001'),
(87, 'single', 'A001'),
(88, 'single', 'A001'),
(89, 'single', 'A001'),
(90, 'single', 'A001'),
(91, 'single', 'A001'),
(93, 'single', 'A001'),
(94, 'single', 'A001'),
(95, 'single', 'A001'),
(96, 'single', 'A001'),
(97, 'single', 'A001'),
(98, 'single', 'A001'),
(99, 'single', 'A001'),
(100, 'single', 'A001'),
(101, 'single', 'A001'),
(102, 'single', 'A001'),
(103, 'single', 'A001'),
(104, 'single', 'A001'),
(105, 'single', 'A001'),
(106, 'single', 'A001'),
(107, 'single', 'A001'),
(108, 'single', 'A001'),
(109, 'single', 'A001'),
(110, 'single', 'A001'),
(111, 'single', 'A001'),
(112, 'single', 'A001'),
(113, 'single', 'A001'),
(114, 'single', 'A001'),
(115, 'single', 'A001');

-- --------------------------------------------------------

--
-- 資料表結構 `reservation_service`
--

CREATE TABLE `reservation_service` (
  `ReservationID` int(11) NOT NULL,
  `ServiceID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- 傾印資料表的資料 `reservation_service`
--

INSERT INTO `reservation_service` (`ReservationID`, `ServiceID`) VALUES
(1, 2),
(1, 3),
(2, 1),
(3, 5),
(4, 3),
(5, 4),
(5, 3),
(5, 5),
(6, 2),
(7, 5),
(7, 2),
(8, 3),
(8, 1),
(9, 2),
(10, 5),
(11, 3),
(12, 5),
(12, 4),
(13, 1),
(13, 3),
(13, 2),
(14, 1),
(15, 2),
(16, 1),
(16, 5),
(16, 3),
(17, 5),
(17, 4),
(18, 4),
(18, 5),
(19, 3),
(19, 2),
(19, 4),
(20, 5),
(20, 2),
(20, 3),
(21, 1),
(21, 5),
(22, 3),
(23, 1),
(23, 4),
(24, 5),
(24, 2),
(25, 5),
(25, 1),
(25, 3),
(26, 4),
(26, 3),
(26, 5),
(27, 1),
(27, 4),
(27, 3),
(28, 5),
(29, 4),
(29, 3),
(30, 3),
(30, 4),
(31, 3),
(32, 2),
(32, 5),
(33, 3),
(33, 5),
(33, 2),
(34, 2),
(34, 3),
(34, 1),
(35, 5),
(35, 4),
(35, 1),
(36, 4),
(36, 1),
(37, 4),
(37, 2),
(38, 2),
(38, 1),
(39, 1),
(39, 5),
(40, 2),
(40, 5),
(40, 1),
(41, 3),
(42, 2),
(42, 5),
(42, 4),
(43, 2),
(44, 3),
(44, 5),
(44, 1),
(45, 5),
(45, 4),
(45, 1),
(46, 5),
(46, 4),
(46, 3),
(47, 2),
(47, 4),
(48, 2),
(48, 1),
(48, 4),
(49, 2),
(49, 5),
(50, 3),
(50, 2),
(50, 4),
(51, 5),
(51, 4),
(51, 3),
(52, 1),
(52, 2),
(52, 4),
(53, 4),
(53, 2),
(54, 5),
(55, 2),
(56, 4),
(56, 3),
(56, 5),
(57, 3),
(57, 4),
(57, 1),
(58, 1),
(58, 4),
(59, 4),
(60, 1),
(60, 4),
(61, 4),
(61, 5),
(61, 1),
(62, 2),
(62, 4),
(62, 1),
(63, 2),
(63, 5),
(63, 1),
(64, 5),
(65, 3),
(65, 5),
(65, 2),
(75, 2),
(76, 1),
(76, 2),
(78, 1),
(78, 2),
(79, 1),
(79, 2),
(80, 1),
(80, 2),
(82, 2),
(83, 2),
(84, 2),
(85, 1),
(85, 2),
(86, 1),
(86, 2),
(87, 2),
(88, 1),
(89, 2),
(90, 2),
(91, 1),
(94, 2),
(95, 2),
(96, 2),
(98, 2),
(100, 2),
(101, 1),
(102, 2),
(103, 1),
(104, 2),
(105, 2),
(106, 2),
(107, 2),
(108, 1),
(108, 2),
(109, 2),
(110, 1),
(110, 2),
(111, 1),
(112, 1),
(112, 2),
(113, 1),
(113, 2),
(114, 2),
(115, 1),
(115, 2);

-- --------------------------------------------------------

--
-- 資料表結構 `room`
--

CREATE TABLE `room` (
  `RoomID` varchar(25) NOT NULL,
  `RoomType` varchar(25) NOT NULL,
  `RoomPrice` int(11) NOT NULL,
  `Available` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- 傾印資料表的資料 `room`
--

INSERT INTO `room` (`RoomID`, `RoomType`, `RoomPrice`, `Available`) VALUES
('A001', 'single', 1600, 1),
('A002', 'single', 1600, 1),
('A003', 'single', 1600, 1),
('A004', 'single', 1600, 1),
('A005', 'single', 1600, 1),
('A006', 'single', 1600, 1),
('A007', 'single', 1600, 1),
('A008', 'single', 1600, 1),
('A009', 'single', 1600, 1),
('A010', 'single', 1600, 1),
('A011', 'single', 1600, 1),
('A012', 'single', 1600, 1),
('A013', 'single', 1600, 1),
('A014', 'single', 1600, 1),
('A015', 'single', 1600, 1),
('B001', 'double', 3500, 1),
('B002', 'double', 3500, 1),
('B003', 'double', 3500, 1),
('B004', 'double', 3500, 1),
('B005', 'double', 3500, 1),
('B006', 'double', 3500, 1),
('B007', 'double', 3500, 1),
('B008', 'double', 3500, 1),
('B009', 'double', 3500, 1),
('B010', 'double', 3500, 1),
('B011', 'double', 3500, 1),
('B012', 'double', 3500, 1),
('B013', 'double', 3500, 1),
('B014', 'double', 3500, 1),
('B015', 'double', 3500, 1),
('B016', 'double', 3500, 1),
('B017', 'double', 3500, 1),
('B018', 'double', 3500, 1),
('B019', 'double', 3500, 1),
('B020', 'double', 3500, 1),
('B021', 'double', 3500, 1),
('B022', 'double', 3500, 1),
('B023', 'double', 3500, 1),
('B024', 'double', 3500, 1),
('B025', 'double', 3500, 1),
('B026', 'double', 3500, 1),
('B027', 'double', 3500, 1),
('B028', 'double', 3500, 1),
('B029', 'double', 3500, 1),
('B030', 'double', 3500, 1),
('B031', 'double', 3500, 1),
('B032', 'double', 3500, 1),
('B033', 'double', 3500, 1),
('B034', 'double', 3500, 1),
('B035', 'double', 3500, 1),
('C001', 'triple', 5000, 1),
('C002', 'triple', 5000, 1),
('C003', 'triple', 5000, 1),
('C004', 'triple', 5000, 1),
('C005', 'triple', 5000, 1),
('C006', 'triple', 5000, 1),
('C007', 'triple', 5000, 1),
('C008', 'triple', 5000, 1),
('C009', 'triple', 5000, 1),
('C010', 'triple', 5000, 1),
('D001', 'quad', 7000, 1),
('D002', 'quad', 7000, 1),
('D003', 'quad', 7000, 1),
('D004', 'quad', 7000, 1),
('D005', 'quad', 7000, 1),
('D006', 'quad', 7000, 1),
('D007', 'quad', 7000, 1),
('D008', 'quad', 7000, 1),
('D009', 'quad', 7000, 1),
('D010', 'quad', 7000, 1),
('D011', 'quad', 7000, 1),
('D012', 'quad', 7000, 1),
('D013', 'quad', 7000, 1),
('D014', 'quad', 7000, 1),
('D015', 'quad', 7000, 1),
('D016', 'quad', 7000, 1),
('D017', 'quad', 7000, 1),
('D018', 'quad', 7000, 1),
('D019', 'quad', 7000, 1),
('D020', 'quad', 7000, 1);

-- --------------------------------------------------------

--
-- 資料表結構 `service`
--

CREATE TABLE `service` (
  `ServiceID` int(11) NOT NULL,
  `ServiceName` varchar(25) NOT NULL,
  `ServiceFee` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- 傾印資料表的資料 `service`
--

INSERT INTO `service` (`ServiceID`, `ServiceName`, `ServiceFee`) VALUES
(1, '早餐', 200),
(2, '晚餐', 500),
(3, 'spa', 200),
(4, '泳池', 150),
(5, '下午茶', 250);

-- --------------------------------------------------------

--
-- 資料表結構 `vip`
--

CREATE TABLE `vip` (
  `VIPlevel` int(11) NOT NULL,
  `VIPName` varchar(25) NOT NULL,
  `Discount` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- 傾印資料表的資料 `vip`
--

INSERT INTO `vip` (`VIPlevel`, `VIPName`, `Discount`) VALUES
(1, '相遇卡', 0.95),
(2, '綻放卡', 0.9),
(3, '璀璨卡', 0.8);

-- --------------------------------------------------------

--
-- 檢視表結構 `customerwithvip`
--
DROP TABLE IF EXISTS `customerwithvip`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `customerwithvip`  AS SELECT `c`.`CustomerID` AS `CustomerID`, `c`.`CustomerName` AS `CustomerName`, `c`.`CustomerTimes` AS `CustomerTimes`, CASE WHEN `c`.`CustomerTimes` >= 10 THEN 3 WHEN `c`.`CustomerTimes` >= 6 THEN 2 WHEN `c`.`CustomerTimes` >= 3 THEN 1 ELSE 0 END AS `VIPlevel`, `v`.`VIPName` AS `VIPName`, `v`.`Discount` AS `Discount` FROM (`customer` `c` left join `vip` `v` on(case when `c`.`CustomerTimes` >= 10 then 3 when `c`.`CustomerTimes` >= 6 then 2 when `c`.`CustomerTimes` >= 3 then 1 else 0 end = `v`.`VIPlevel`)) ;

--
-- 已傾印資料表的索引
--

--
-- 資料表索引 `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`CustomerID`);

--
-- 資料表索引 `manager`
--
ALTER TABLE `manager`
  ADD PRIMARY KEY (`ManagerID`);

--
-- 資料表索引 `reservation`
--
ALTER TABLE `reservation`
  ADD PRIMARY KEY (`ReservationID`),
  ADD KEY `Reservation_FK1` (`CustomerID`);

--
-- 資料表索引 `reservation_room`
--
ALTER TABLE `reservation_room`
  ADD KEY `Reservation_r_FK1` (`ReservationID`),
  ADD KEY `Reservation_r_FK2` (`RoomID`);

--
-- 資料表索引 `reservation_service`
--
ALTER TABLE `reservation_service`
  ADD KEY `Reservation_s1_FK1` (`ReservationID`),
  ADD KEY `Reservation_s1_FK2` (`ServiceID`);

--
-- 資料表索引 `room`
--
ALTER TABLE `room`
  ADD PRIMARY KEY (`RoomID`);

--
-- 資料表索引 `service`
--
ALTER TABLE `service`
  ADD PRIMARY KEY (`ServiceID`);

--
-- 資料表索引 `vip`
--
ALTER TABLE `vip`
  ADD PRIMARY KEY (`VIPlevel`);

--
-- 在傾印的資料表使用自動遞增(AUTO_INCREMENT)
--

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `customer`
--
ALTER TABLE `customer`
  MODIFY `CustomerID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `manager`
--
ALTER TABLE `manager`
  MODIFY `ManagerID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `reservation`
--
ALTER TABLE `reservation`
  MODIFY `ReservationID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=116;

--
-- 已傾印資料表的限制式
--

--
-- 資料表的限制式 `reservation`
--
ALTER TABLE `reservation`
  ADD CONSTRAINT `Reservation_FK1` FOREIGN KEY (`CustomerID`) REFERENCES `customer` (`CustomerID`);

--
-- 資料表的限制式 `reservation_room`
--
ALTER TABLE `reservation_room`
  ADD CONSTRAINT `Reservation_r_FK1` FOREIGN KEY (`ReservationID`) REFERENCES `reservation` (`ReservationID`),
  ADD CONSTRAINT `Reservation_r_FK2` FOREIGN KEY (`RoomID`) REFERENCES `room` (`RoomID`);

--
-- 資料表的限制式 `reservation_service`
--
ALTER TABLE `reservation_service`
  ADD CONSTRAINT `Reservation_s1_FK1` FOREIGN KEY (`ReservationID`) REFERENCES `reservation` (`ReservationID`),
  ADD CONSTRAINT `Reservation_s1_FK2` FOREIGN KEY (`ServiceID`) REFERENCES `service` (`ServiceID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
