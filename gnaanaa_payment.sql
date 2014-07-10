-- phpMyAdmin SQL Dump
-- version 3.4.11.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 18, 2013 at 10:01 PM
-- Server version: 5.5.23
-- PHP Version: 5.2.17

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `gnaanaa_payment`
--

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE IF NOT EXISTS `items` (
  `ItemID` varchar(10) NOT NULL,
  `ItemName` varchar(50) NOT NULL,
  `ItemPrice` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`ItemID`, `ItemName`, `ItemPrice`) VALUES
('LT832', 'Large tee shirt', 15.99),
('BS432', 'Black Socks', 2),
('SS453', 'Small Shorts', 9.99);

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE IF NOT EXISTS `transactions` (
  `transactionID` int(11) NOT NULL AUTO_INCREMENT,
  `orderReference` varchar(50) NOT NULL,
  `userName` varchar(100) NOT NULL,
  `userAddress` varchar(255) NOT NULL,
  `userEmail` varchar(100) NOT NULL,
  `ItemID` varchar(10) NOT NULL,
  `quantity` int(11) NOT NULL,
  `comment` varchar(255) NOT NULL,
  `ipAddress` varchar(15) NOT NULL,
  `staus` tinyint(1) NOT NULL,
  `transactionTime` datetime NOT NULL,
  PRIMARY KEY (`transactionID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=16 ;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`transactionID`, `orderReference`, `userName`, `userAddress`, `userEmail`, `ItemID`, `quantity`, `comment`, `ipAddress`, `staus`, `transactionTime`) VALUES
(1, 'Ref-514728be023742.37339941', 'Gnaanaa Siva', 'Bedok', 'gnaanaa@gmail.com', 'BS432', 4, 'test', '218.186.17.239', 0, '2013-03-18 14:46:46'),
(2, 'Ref-5147297f8002e1.86431032', 'Gnaanaa Siva', 'Bedok', 'gnaanaa@gmail.com', 'BS432', 4, 'test', '218.186.17.239', 1, '2013-03-18 14:49:46'),
(3, 'Ref-514743364dc306.20792814', 'Gnaanaa Siva', 'Bedok', 'gnaanaa@gmail.com', 'BS432', 4, 'test comment', '218.186.17.239', 1, '2013-03-18 16:39:45'),
(4, 'Ref-51477c9aeeb419.84760942', 'n', 'a', 'simon@hairylemon.co.nz', 'LT832 ', 1, 'c', '202.124.118.98', 0, '2013-03-18 20:44:35'),
(5, 'Ref-51477c9aeeb419.84760942', 'n', 'a', 'simon@hairylemon.co.nz', 'LT832 ', 1, 'c', '202.124.118.98', 0, '2013-03-18 20:47:07'),
(6, 'Ref-51477d5c90acc7.91606821', 'n', 'a', 'simon@hairylemon.co.nz', 'BS432', 1, 'c', '202.124.118.98', 0, '2013-03-18 20:47:42'),
(7, 'Ref-51477d5c90acc7.91606821', 'n', 'a', 'simon@hairylemon.co.nz', 'BS432', 1, 'c', '202.124.118.98', 0, '2013-03-18 20:48:38'),
(8, 'Ref-51477d5c90acc7.91606821', 'n', 'a', 'simon@hairylemon.co.nz', 'BS432', 1, 'c', '202.124.118.98', 0, '2013-03-18 20:49:55'),
(9, 'Ref-51477d5c90acc7.91606821', 'n', 'a', 'simon@hairylemon.co.nz', 'BS432', 1, 'c', '202.124.118.98', 0, '2013-03-18 20:50:38'),
(10, 'Ref-51477e4d222533.03101064', 'n', 'a', 'simon@hairylemon.co.nz', 'BS432', 4, 'c', '202.124.118.98', 1, '2013-03-18 20:56:49'),
(11, 'Ref-514780294dcb27.43836291', 'n', 'a', 'simon@hairylemon.co.nz', 'BS432', 1, 'c', '202.124.118.98', 0, '2013-03-18 21:00:54'),
(12, 'Ref-514780294dcb27.43836291', 'n', 'a', 'simon@hairylemon.co.nz', 'BS432', 1, 'c', '202.124.118.98', 0, '2013-03-18 21:08:24'),
(13, 'Ref-514780294dcb27.43836291', 'n', 'a', 'simon@hairylemon.co.nz', 'BS432', 1, 'c', '202.124.118.98', 0, '2013-03-19 00:43:07'),
(14, 'Ref-5147d3d72a7866.54092711', 'gnaanaa', 'Bedok', 'suhathipan@gmail.com', 'BS432', 2, 'Comment', '203.78.9.165', 0, '2013-03-19 02:56:55'),
(15, 'Ref-5147d402414094.28179987', 'gnaanaa', 'Bedok', 'suhathipan@gmail.com', 'BS432', 2, 'Comment', '203.78.9.165', 1, '2013-03-19 02:57:18');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
