-- phpMyAdmin SQL Dump
-- version 4.0.10.17
-- https://www.phpmyadmin.net
--
-- Host: mysql.dur.ac.uk
-- Generation Time: Jan 21, 2017 at 08:15 PM
-- Server version: 5.1.39-community-log
-- PHP Version: 5.3.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `Idcs8s04_conCEPt`
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
-- Table structure for table `BaseForm`
--

CREATE TABLE IF NOT EXISTS `BaseForm` (
  `BForm_ID` int(3) NOT NULL AUTO_INCREMENT,
  `Form_Title` varchar(30) NOT NULL,
  `Deadline` date NOT NULL,
  PRIMARY KEY (`BForm_ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `BaseForm`
--

INSERT INTO `BaseForm` (`BForm_ID`, `Form_Title`, `Deadline`) VALUES
(1, 'Design Report', '2017-01-24'),
(2, 'Presentation', '2017-01-23'),
(3, 'Project Paper', '2017-01-31'),
(4, 'Project Poster', '2017-02-15');

-- --------------------------------------------------------

--
-- Table structure for table `Form`
--

CREATE TABLE IF NOT EXISTS `Form` (
  `Form_ID` int(4) NOT NULL AUTO_INCREMENT,
  `BForm_ID` int(3) NOT NULL,
  `IsSubmitted` tinyint(1) NOT NULL,
  `IsMerged` tinyint(1) NOT NULL,
  PRIMARY KEY (`Form_ID`),
  KEY `BForm_ID` (`BForm_ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;

--
-- Dumping data for table `Form`
--

INSERT INTO `Form` (`Form_ID`, `BForm_ID`, `IsSubmitted`, `IsMerged`) VALUES
(1, 1, 0, 0),
(2, 3, 1, 0),
(3, 1, 0, 0),
(4, 2, 1, 0),
(5, 3, 0, 0),
(6, 2, 1, 0),
(7, 4, 0, 0),
(8, 1, 1, 0),
(9, 2, 0, 0),
(10, 3, 1, 0);

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

--
-- Dumping data for table `Marker`
--

INSERT INTO `Marker` (`Marker_ID`, `Fname`, `Lname`) VALUES
('hkd4hdk', 'steve', 'smith'),
('knd6usj', 'mark', 'martin'),
('msg8dsf', 'david', 'cobley'),
('ops8wll', 'bob', 'clark');

-- --------------------------------------------------------

--
-- Table structure for table `MS`
--

CREATE TABLE IF NOT EXISTS `MS` (
  `MS_ID` int(3) NOT NULL AUTO_INCREMENT,
  `Marker_ID` char(8) NOT NULL,
  `Student_ID` char(7) NOT NULL,
  `IsSupervisor` int(1) NOT NULL,
  PRIMARY KEY (`MS_ID`),
  KEY `Marker_ID` (`Marker_ID`),
  KEY `Student_ID` (`Student_ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

--
-- Dumping data for table `MS`
--

INSERT INTO `MS` (`MS_ID`, `Marker_ID`, `Student_ID`, `IsSupervisor`) VALUES
(1, 'hkd4hdk', 'cnng04', 1),
(2, 'hkd4hdk', 'klwe45', 0),
(3, 'knd6usj', 'cnng04', 0),
(4, 'knd6usj', 'mloo98', 1),
(5, 'knd6usj', 'klwe45', 1),
(6, 'msg8dsf', 'mloo98', 0),
(7, 'ops8wll', 'powl14', 0),
(8, 'msg8dsf', 'powl14', 1);

-- --------------------------------------------------------

--
-- Table structure for table `MS_Form`
--

CREATE TABLE IF NOT EXISTS `MS_Form` (
  `MS_ID` int(3) NOT NULL,
  `Form_ID` int(4) NOT NULL,
  PRIMARY KEY (`MS_ID`,`Form_ID`),
  KEY `Form_ID` (`Form_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `MS_Form`
--

INSERT INTO `MS_Form` (`MS_ID`, `Form_ID`) VALUES
(1, 1),
(3, 1),
(2, 2),
(4, 6);

-- --------------------------------------------------------

--
-- Table structure for table `Section`
--

CREATE TABLE IF NOT EXISTS `Section` (
  `Sec_ID` int(2) NOT NULL AUTO_INCREMENT,
  `Sec_Name` varchar(30) NOT NULL,
  `BForm_ID` int(3) NOT NULL,
  `Sec_Criteria` text NOT NULL,
  `Sec_Percent` decimal(3,1) NOT NULL,
  `Sec_Order` int(2) NOT NULL,
  PRIMARY KEY (`Sec_ID`),
  KEY `BForm_ID` (`BForm_ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `Section`
--

INSERT INTO `Section` (`Sec_ID`, `Sec_Name`, `BForm_ID`, `Sec_Criteria`, `Sec_Percent`, `Sec_Order`) VALUES
(1, 'Design', 1, 'Adequacy of the proposed solution\r\nSpecification and design\r\nIdentification of requirements\r\nDescription of tools used\r\nOverview of architecture\r\nDescription of life cycle', '65.0', 3),
(2, 'General Comments', 1, '', '0.0', 5),
(3, 'Introduction', 1, 'Description of purpose of project\r\nSpecification of deliverables and aims', '15.0', 2),
(4, 'Structured Abstract', 1, 'Adequacy of summary of project\r\nUses required headings of Background/Context, Aims, Method, Proposed Solution', '10.0', 1),
(5, 'Writing Skills', 1, 'Clarity of presentation of ideas\r\nConformance to paper format standards as specified in Paper Template\r\nQuality of writing (readability, grammar)\r\nReferences', '10.0', 4);

-- --------------------------------------------------------

--
-- Table structure for table `SectionEvent`
--

CREATE TABLE IF NOT EXISTS `SectionEvent` (
  `Form_ID` char(8) NOT NULL,
  `Sec_ID` int(2) NOT NULL,
  `DateTime` date NOT NULL,
  `Author` char(30) NOT NULL,
  `Comment` text NOT NULL,
  PRIMARY KEY (`Form_ID`,`Sec_ID`,`DateTime`),
  KEY `Section_ID` (`Sec_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `SectionMarking`
--

CREATE TABLE IF NOT EXISTS `SectionMarking` (
  `Sec_ID` int(2) NOT NULL,
  `Form_ID` int(4) NOT NULL,
  `Comment` text NOT NULL,
  `Mark` decimal(3,1) NOT NULL,
  PRIMARY KEY (`Sec_ID`,`Form_ID`),
  KEY `Form_ID` (`Form_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `SectionMarking`
--

INSERT INTO `SectionMarking` (`Sec_ID`, `Form_ID`, `Comment`, `Mark`) VALUES
(1, 1, 'very bad', '20.0'),
(3, 2, 'GOOD', '60.0'),
(4, 1, 'bad', '50.0'),
(4, 2, 'bad', '50.0');

-- --------------------------------------------------------

--
-- Table structure for table `Student`
--

CREATE TABLE IF NOT EXISTS `Student` (
  `Student_ID` char(7) NOT NULL,
  `Fname` char(30) NOT NULL,
  `Lname` char(30) NOT NULL,
  `Year_Level` int(1) NOT NULL,
  PRIMARY KEY (`Student_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `Student`
--

INSERT INTO `Student` (`Student_ID`, `Fname`, `Lname`, `Year_Level`) VALUES
('cnng04', 'ryan', 'bright', 3),
('klwe45', 'ben', 'chen', 3),
('mloo98', 'calvin', 'lomis', 4),
('powl14', 'louis', 'johnson', 4);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `Form`
--
ALTER TABLE `Form`
  ADD CONSTRAINT `Form_ibfk_1` FOREIGN KEY (`BForm_ID`) REFERENCES `BaseForm` (`BForm_ID`);

--
-- Constraints for table `MS`
--
ALTER TABLE `MS`
  ADD CONSTRAINT `MS_ibfk_1` FOREIGN KEY (`Marker_ID`) REFERENCES `Marker` (`Marker_ID`),
  ADD CONSTRAINT `MS_ibfk_2` FOREIGN KEY (`Student_ID`) REFERENCES `Student` (`Student_ID`);

--
-- Constraints for table `MS_Form`
--
ALTER TABLE `MS_Form`
  ADD CONSTRAINT `MS_Form_ibfk_2` FOREIGN KEY (`Form_ID`) REFERENCES `Form` (`Form_ID`),
  ADD CONSTRAINT `MS_Form_ibfk_1` FOREIGN KEY (`MS_ID`) REFERENCES `MS` (`MS_ID`);

--
-- Constraints for table `Section`
--
ALTER TABLE `Section`
  ADD CONSTRAINT `Section_ibfk_1` FOREIGN KEY (`BForm_ID`) REFERENCES `BaseForm` (`BForm_ID`);

--
-- Constraints for table `SectionEvent`
--
ALTER TABLE `SectionEvent`
  ADD CONSTRAINT `SectionEvent_ibfk_1` FOREIGN KEY (`Sec_ID`) REFERENCES `SectionMarking` (`Sec_ID`);

--
-- Constraints for table `SectionMarking`
--
ALTER TABLE `SectionMarking`
  ADD CONSTRAINT `SectionMarking_ibfk_3` FOREIGN KEY (`Form_ID`) REFERENCES `Form` (`Form_ID`),
  ADD CONSTRAINT `SectionMarking_ibfk_2` FOREIGN KEY (`Sec_ID`) REFERENCES `Section` (`Sec_ID`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
