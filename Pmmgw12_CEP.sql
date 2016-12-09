-- phpMyAdmin SQL Dump
-- version 4.0.10.17
-- https://www.phpmyadmin.net
--
-- Host: mysql.dur.ac.uk
-- Generation Time: Nov 21, 2016 at 02:55 PM
-- Server version: 5.1.39-community-log
-- PHP Version: 5.3.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `Pmmgw12_CEP`
--

-- --------------------------------------------------------

--
-- Table structure for table `Admin`
--

CREATE TABLE IF NOT EXISTS `Admin` (
  `Admin_ID` char(8) NOT NULL,
  `Fname` char(30) NOT NULL,
  `Lname` char(30) NOT NULL,
  PRIMARY KEY (`Admin_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `Form`
--

CREATE TABLE IF NOT EXISTS `Form` (
  `Form_ID` char(8) NOT NULL,
  `MS_ID` char(8) NOT NULL,
  `Title` char(30) NOT NULL,
  PRIMARY KEY (`Form_ID`),
  KEY `MS_ID` (`MS_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `Marker`
--

CREATE TABLE IF NOT EXISTS `Marker` (
  `Marker_ID` char(8) NOT NULL,
  `Fname` char(30) NOT NULL,
  `Lname` char(30) NOT NULL,
  PRIMARY KEY (`Marker_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `MS`
--

CREATE TABLE IF NOT EXISTS `MS` (
  `MS_ID` char(8) NOT NULL,
  `Marker_ID` char(8) NOT NULL,
  `Student_ID` char(7) NOT NULL,
  `IsSupervisor` int(1) NOT NULL,
  PRIMARY KEY (`MS_ID`),
  KEY `Marker_ID` (`Marker_ID`),
  KEY `Student_ID` (`Student_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `Section`
--

CREATE TABLE IF NOT EXISTS `Section` (
  `Section_ID` int(2) NOT NULL AUTO_INCREMENT,
  `Form_ID` char(8) NOT NULL,
  `Title` char(30) NOT NULL,
  `Comment` text NOT NULL,
  `Mark` decimal(3,1) NOT NULL,
  `Order` int(2) NOT NULL,
  PRIMARY KEY (`Section_ID`,`Form_ID`),
  KEY `Form_ID` (`Form_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `Student`
--

CREATE TABLE IF NOT EXISTS `Student` (
  `Student_ID` char(7) NOT NULL,
  `Fname` char(30) NOT NULL,
  `Lname` char(30) NOT NULL,
  PRIMARY KEY (`Student_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `Form`
--
ALTER TABLE `Form`
  ADD CONSTRAINT `Form_ibfk_1` FOREIGN KEY (`MS_ID`) REFERENCES `MS` (`MS_ID`);

--
-- Constraints for table `MS`
--
ALTER TABLE `MS`
  ADD CONSTRAINT `MS_ibfk_2` FOREIGN KEY (`Student_ID`) REFERENCES `Student` (`Student_ID`),
  ADD CONSTRAINT `MS_ibfk_1` FOREIGN KEY (`Marker_ID`) REFERENCES `Marker` (`Marker_ID`);

--
-- Constraints for table `Section`
--
ALTER TABLE `Section`
  ADD CONSTRAINT `Section_ibfk_1` FOREIGN KEY (`Form_ID`) REFERENCES `Form` (`Form_ID`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
