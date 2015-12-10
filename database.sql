-- phpMyAdmin SQL Dump
-- version 4.0.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Dec 10, 2015 at 07:56 AM
-- Server version: 5.6.12-log
-- PHP Version: 5.4.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `app_real_estate_v2`
--
CREATE DATABASE IF NOT EXISTS `app_real_estate_v2` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `app_real_estate_v2`;

-- --------------------------------------------------------

--
-- Table structure for table `arrears`
--

CREATE TABLE IF NOT EXISTS `arrears` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tid` int(11) NOT NULL,
  `amount` varchar(100) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `tid` (`tid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

--
-- Dumping data for table `arrears`
--

INSERT INTO `arrears` (`id`, `tid`, `amount`, `start_date`, `end_date`) VALUES
(1, 1, '4200', '2015-07-01', '2015-07-31'),
(2, 13, '3000', '2015-08-01', '2015-08-31'),
(3, 1, '4000', '2015-06-01', '2015-06-30'),
(4, 1, '4000', '2015-09-01', '2015-09-30'),
(6, 18, '1000', '2015-10-01', '2015-10-31');

-- --------------------------------------------------------

--
-- Table structure for table `arrears_paid`
--

CREATE TABLE IF NOT EXISTS `arrears_paid` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tid` int(11) NOT NULL,
  `amount` varchar(150) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `date_paid` datetime NOT NULL,
  `receipt_no` varchar(150) NOT NULL,
  `mode` varchar(100) NOT NULL,
  `agent` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `tid` (`tid`),
  KEY `start` (`start_date`),
  KEY `end` (`end_date`),
  KEY `receipt` (`receipt_no`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

--
-- Dumping data for table `arrears_paid`
--

INSERT INTO `arrears_paid` (`id`, `tid`, `amount`, `start_date`, `end_date`, `date_paid`, `receipt_no`, `mode`, `agent`) VALUES
(1, 9, '3000', '2015-04-01', '2015-04-30', '2015-09-02 10:10:52', '000001', 'cheque', 'Code Warrior'),
(2, 1, '500', '2015-07-01', '2015-07-31', '2015-09-14 10:10:17', '000002', 'cash', 'Code Warrior'),
(3, 1, '300', '2015-07-01', '2015-07-31', '2015-09-14 10:10:25', '000003', 'cheque', 'Code Warrior'),
(4, 13, '2000', '2015-08-01', '2015-08-31', '2015-09-23 10:10:25', '000004', 'cheque', 'Code Warrior'),
(6, 18, '1000', '2015-10-01', '2015-10-31', '2015-10-21 14:02:24', '000006', 'cash', 'Code Warrior');

-- --------------------------------------------------------

--
-- Table structure for table `cheque`
--

CREATE TABLE IF NOT EXISTS `cheque` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tid` int(11) NOT NULL,
  `cheque_no` varchar(150) NOT NULL,
  `bank` varchar(100) NOT NULL,
  `branch` varchar(150) NOT NULL,
  `drawer` varchar(100) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `date_paid` datetime NOT NULL,
  `type` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `tid` (`tid`),
  KEY `cheque` (`cheque_no`),
  KEY `start` (`start_date`),
  KEY `end` (`end_date`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

--
-- Dumping data for table `cheque`
--

INSERT INTO `cheque` (`id`, `tid`, `cheque_no`, `bank`, `branch`, `drawer`, `start_date`, `end_date`, `date_paid`, `type`) VALUES
(2, 1, '000135', 'I &amp; M Bank', 'Eldoret', 'Moiben Ventures', '2015-01-01', '2015-01-31', '2015-09-01 20:08:24', 'rent'),
(3, 9, '000143', 'Barclays Bank', 'Nakuru', 'Shiv Builders', '2015-04-01', '2015-04-30', '2015-09-02 10:10:00', 'arrears'),
(4, 1, '000165', 'Nic Bank', 'Eldoret', 'Four Ways Media', '2015-07-01', '2015-07-31', '2015-09-07 09:09:54', 'rent'),
(7, 1, '000043', 'Cfc Stanbic', 'Eldoret', 'Japara Ltd', '2015-07-01', '2015-07-31', '2015-09-14 11:11:52', 'arrears'),
(8, 13, '004589', 'Kcb', 'Eldoret', 'Techno Serve', '2015-08-01', '2015-08-31', '2015-09-23 11:11:36', 'arrears');

-- --------------------------------------------------------

--
-- Table structure for table `collection`
--

CREATE TABLE IF NOT EXISTS `collection` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `collection`
--

INSERT INTO `collection` (`id`, `name`, `description`) VALUES
(1, 'Administrators', 'All permissions'),
(2, 'Managers', 'Some Permissions'),
(3, 'Employee', 'Limited Permissions');

-- --------------------------------------------------------

--
-- Table structure for table `collection2permission`
--

CREATE TABLE IF NOT EXISTS `collection2permission` (
  `collection_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  PRIMARY KEY (`collection_id`,`permission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `collection2permission`
--

INSERT INTO `collection2permission` (`collection_id`, `permission_id`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(1, 5),
(1, 6),
(1, 7),
(1, 8),
(1, 9),
(1, 10),
(1, 11),
(1, 12),
(1, 13),
(1, 14),
(1, 15),
(1, 16),
(1, 17),
(3, 6),
(3, 8),
(3, 16);

-- --------------------------------------------------------

--
-- Table structure for table `deposits`
--

CREATE TABLE IF NOT EXISTS `deposits` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tid` int(11) NOT NULL,
  `rid` int(11) NOT NULL,
  `tenant_name` varchar(100) NOT NULL,
  `amount` varchar(150) NOT NULL,
  `receipt_no` varchar(150) NOT NULL,
  `agent` varchar(100) NOT NULL,
  `date_paid` date NOT NULL,
  `date_ref` date DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `tid` (`tid`),
  KEY `receipt` (`receipt_no`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12 ;

--
-- Dumping data for table `deposits`
--

INSERT INTO `deposits` (`id`, `tid`, `rid`, `tenant_name`, `amount`, `receipt_no`, `agent`, `date_paid`, `date_ref`, `status`) VALUES
(1, 1, 11, 'New Tenant', '12000', '000001', 'Code Warrior', '2015-08-20', NULL, 0),
(4, 9, 12, 'Another One', '16000', '000002', 'Code Warrior', '2015-08-10', NULL, 0),
(7, 18, 41, 'Gil Shwed', '8000', '000004', 'Code Warrior', '2015-10-05', '0000-00-00', 0),
(8, 19, 42, 'Sigmund Freud', '10000', '000005', 'Code Warrior', '2015-10-05', '0000-00-00', 0),
(9, 20, 45, 'Max Planc', '15000', '000006', 'Code Warrior', '2015-10-15', '0000-00-00', 0);

-- --------------------------------------------------------

--
-- Table structure for table `deposit_eldowas`
--

CREATE TABLE IF NOT EXISTS `deposit_eldowas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tid` int(11) NOT NULL,
  `rid` int(11) NOT NULL,
  `tenant_name` varchar(100) NOT NULL,
  `amount` varchar(150) NOT NULL,
  `receipt_no` varchar(150) NOT NULL,
  `agent` varchar(100) NOT NULL,
  `date_paid` date NOT NULL,
  `date_ref` date DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `tid` (`tid`),
  KEY `receipt` (`receipt_no`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `deposit_eldowas`
--

INSERT INTO `deposit_eldowas` (`id`, `tid`, `rid`, `tenant_name`, `amount`, `receipt_no`, `agent`, `date_paid`, `date_ref`, `status`) VALUES
(1, 14, 31, 'First Last', '1000', '000001', 'Code Warrior', '2015-10-05', '0000-00-00', 0),
(3, 18, 41, 'Gil Shwed', '200', '000002', 'Code Warrior', '2015-10-05', '0000-00-00', 0),
(4, 20, 45, 'Max Planc', '250', '000003', 'Code Warrior', '2015-10-15', '0000-00-00', 0),
(5, 21, 46, 'violet namukhosi', '600', '000004', 'Code Warrior', '2015-10-05', '0000-00-00', 0);

-- --------------------------------------------------------

--
-- Table structure for table `deposit_kplc`
--

CREATE TABLE IF NOT EXISTS `deposit_kplc` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tid` int(11) NOT NULL,
  `rid` int(11) NOT NULL,
  `tenant_name` varchar(100) NOT NULL,
  `amount` varchar(150) NOT NULL,
  `receipt_no` varchar(150) NOT NULL,
  `agent` varchar(100) NOT NULL,
  `date_paid` date NOT NULL,
  `date_ref` date DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `tid` (`tid`),
  KEY `receipt` (`receipt_no`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `deposit_kplc`
--

INSERT INTO `deposit_kplc` (`id`, `tid`, `rid`, `tenant_name`, `amount`, `receipt_no`, `agent`, `date_paid`, `date_ref`, `status`) VALUES
(1, 14, 31, 'First Last', '1000', '000001', 'Code Warrior', '2015-10-05', '0000-00-00', 0),
(3, 18, 41, 'Gil Shwed', '500', '000002', 'Code Warrior', '2015-10-05', '0000-00-00', 0),
(4, 20, 45, 'Max Planc', '600', '000003', 'Code Warrior', '2015-10-15', '0000-00-00', 0),
(5, 21, 46, 'violet namukhosi', '1000', '000004', 'Code Warrior', '2015-10-05', '0000-00-00', 0);

-- --------------------------------------------------------

--
-- Table structure for table `expense`
--

CREATE TABLE IF NOT EXISTS `expense` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `incurred` date NOT NULL,
  `amount` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `pid` (`pid`),
  KEY `start` (`start_date`),
  KEY `end` (`end_date`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=22 ;

--
-- Dumping data for table `expense`
--

INSERT INTO `expense` (`id`, `pid`, `name`, `start_date`, `end_date`, `incurred`, `amount`) VALUES
(3, 1, 'Interior Painting', '2015-10-01', '2015-10-31', '2015-10-02', '9000'),
(5, 1, 'Plumbing', '2015-10-01', '2015-10-31', '2015-10-02', '2000'),
(15, 1, 'Security Firm', '2015-10-01', '2015-10-31', '2015-10-12', '3000'),
(19, 1, 'Landscaping', '2015-09-01', '2015-09-30', '2015-09-12', '1000'),
(20, 9, 'Garbage Collection', '2015-10-01', '2015-10-31', '2015-10-21', '500');

-- --------------------------------------------------------

--
-- Table structure for table `logger`
--

CREATE TABLE IF NOT EXISTS `logger` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `action_time` datetime NOT NULL,
  `action` varchar(150) NOT NULL,
  `amount` varchar(150) NOT NULL,
  `user` varchar(100) NOT NULL,
  `message` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `action_time` (`action_time`),
  KEY `user` (`user`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=40 ;

--
-- Dumping data for table `logger`
--

INSERT INTO `logger` (`id`, `action_time`, `action`, `amount`, `user`, `message`) VALUES
(1, '2015-10-12 17:05:11', 'EXPENSE', '3000', 'Code Warrior', 'Payment to security firm'),
(2, '2015-10-15 09:09:38', 'DEPOSIT', '10000', 'Code Warrior', 'Deposit of Ben Gurion'),
(3, '2015-10-15 09:09:38', 'RENT', '7000', 'Code Warrior', 'Rent of Ben Gurion for October, 2015'),
(4, '2015-10-15 09:09:27', 'ARREARS', '3000', 'Code Warrior', 'Arrears payment of Ben Gurion for Oct,2015'),
(7, '2015-10-15 15:03:51', 'EXPENSE', '1000', 'Code Warrior', 'Plumbing'),
(9, '2015-10-17 14:02:09', 'KPLC DEPOSIT', '1000', 'Code Warrior', 'Deposit of First Last'),
(10, '2015-10-17 15:03:36', 'ELDOWAS DEPOSIT', '1000', 'Code Warrior', 'Deposit of First Last'),
(11, '2015-10-19 08:08:48', 'RENT', '8000', 'Code Warrior', 'Rent of First Last for October, 2015'),
(12, '2015-10-20 08:08:43', 'EXPENSE', '1000', 'Code Warrior', 'Landscaping'),
(13, '2015-10-20 09:09:32', 'DEPOSIT', '4000', 'Code Warrior', 'House Deposit of App User'),
(14, '2015-10-20 15:03:07', 'KPLC DEPOSIT', '500', 'Code Warrior', 'KPLC Deposit of App User'),
(15, '2015-10-20 19:07:47', 'ELDOWAS DEPOSIT', '200', 'Code Warrior', 'Eldowas Deposit of App User'),
(16, '2015-10-21 14:02:12', 'DEPOSIT', '8000', 'Code Warrior', 'House Deposit of Gil Shwed'),
(17, '2015-10-21 14:02:05', 'KPLC DEPOSIT', '500', 'Code Warrior', 'KPLC Deposit of Gil Shwed'),
(18, '2015-10-21 14:02:50', 'ELDOWAS DEPOSIT', '200', 'Code Warrior', 'Eldowas Deposit of Gil Shwed'),
(19, '2015-10-21 14:02:48', 'RENT', '8000', 'Code Warrior', 'Rent of Gil Shwed for October, 2015'),
(20, '2015-10-21 14:02:24', 'ARREARS', '1000', 'Code Warrior', 'Arrears payment of Gil Shwed for Oct, 2015'),
(21, '2015-10-21 14:02:22', 'DEPOSIT', '10000', 'Code Warrior', 'House Deposit of Sigmund Freud'),
(22, '2015-10-21 15:03:38', 'RENT', '0', 'Code Warrior', 'Rent of Sigmund Freud for October, 2015'),
(23, '2015-10-21 15:03:26', 'DEPOSIT', '15000', 'Code Warrior', 'House Deposit of Max Planc'),
(24, '2015-10-21 15:03:58', 'KPLC DEPOSIT', '600', 'Code Warrior', 'KPLC Deposit of Max Planc'),
(25, '2015-10-21 15:03:19', 'ELDOWAS DEPOSIT', '250', 'Code Warrior', 'Eldowas Deposit of Max Planc'),
(26, '2015-10-21 15:03:50', 'RENT', '15000', 'Code Warrior', 'Rent of Max Planc for October, 2015'),
(27, '2015-10-21 15:03:30', 'EXPENSE', '500', 'Code Warrior', 'Garbage Collection'),
(28, '2015-10-23 14:02:54', 'DEPOSIT', '3000', 'Code Warrior', 'House Deposit of violet namukhosi'),
(29, '2015-10-23 14:02:44', 'KPLC DEPOSIT', '1000', 'Code Warrior', 'KPLC Deposit of violet namukhosi'),
(30, '2015-10-23 14:02:53', 'ELDOWAS DEPOSIT', '600', 'Code Warrior', 'Eldowas Deposit of violet namukhosi'),
(31, '2015-10-23 15:03:39', 'RENT', '2000', 'Code Warrior', 'Rent of violet namukhosi for October, 2015'),
(32, '2015-10-23 15:03:52', 'DEPOSIT', '4000', 'Code Warrior', 'House Deposit of rose sakwa'),
(33, '2015-10-23 15:03:56', 'RENT', '4000', 'Code Warrior', 'Rent of rose sakwa for October, 2015'),
(34, '2015-10-23 15:03:17', 'RENT', '3000', 'Code Warrior', 'Rent of violet namukhosi for November, 2015'),
(35, '2015-10-23 15:03:57', 'RENT', '3000', 'Code Warrior', 'Rent of violet namukhosi for December, 2015'),
(36, '2015-10-23 15:03:27', 'ARREARS', '1000', 'Code Warrior', 'Arrears payment of violet namukhosi for Oct, 2015'),
(37, '2015-10-23 15:03:05', 'RENT', '3000', 'Code Warrior', 'Rent of alexander opicho for October, 2015'),
(38, '2015-10-23 15:03:48', 'RENT', '4000', 'Code Warrior', 'Rent of alexander opicho for November, 2015'),
(39, '2015-10-23 15:03:47', 'EXPENSE', '500', 'Code Warrior', 'Repairs');

-- --------------------------------------------------------

--
-- Table structure for table `payment_status`
--

CREATE TABLE IF NOT EXISTS `payment_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tid` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` tinyint(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `tid` (`tid`),
  KEY `start` (`start_date`),
  KEY `end` (`end_date`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=26 ;

--
-- Dumping data for table `payment_status`
--

INSERT INTO `payment_status` (`id`, `tid`, `start_date`, `end_date`, `status`) VALUES
(1, 1, '2015-08-01', '2015-08-31', 1),
(3, 9, '2015-08-01', '2015-08-31', 1),
(4, 1, '2015-04-01', '2015-04-30', 1),
(7, 1, '2015-05-01', '2015-05-31', 1),
(8, 1, '2015-06-01', '2015-06-30', 0),
(9, 9, '2015-04-01', '2015-04-30', 1),
(10, 1, '2015-01-01', '2015-01-31', 1),
(11, 1, '2015-07-01', '2015-07-31', 0),
(12, 13, '2015-08-01', '2015-08-31', 0),
(13, 1, '2015-09-01', '2015-09-30', 0),
(17, 18, '2015-10-01', '2015-10-31', 0),
(19, 20, '2015-10-01', '2015-10-31', 1);

-- --------------------------------------------------------

--
-- Table structure for table `permission`
--

CREATE TABLE IF NOT EXISTS `permission` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=20 ;

--
-- Dumping data for table `permission`
--

INSERT INTO `permission` (`id`, `name`, `description`) VALUES
(1, 'admin', 'admin'),
(2, 'create_prop', 'Enter a new property into the system'),
(3, 'edit_prop', 'Edit details of a property already in the system'),
(4, 'delete_prop', 'Delete a property from the system'),
(5, 'create_room', 'Add room(s) to a property in the system'),
(6, 'edit_room', 'Edit details of a room in the system'),
(7, 'delete_room', 'Remove room(s) from a property in the system'),
(8, 'post_rent', 'Record rent payment from a tenant into system'),
(9, 'edit_rent', 'Edit rent payment details for a particular tenant'),
(10, 'delete_rent', 'Delete rent payment record(s) for a particular tenant from the system'),
(11, 'create_user', 'Create a user account into the system'),
(12, 'edit_user', 'Edit user roles and permissions in the system'),
(13, 'delete_user', 'Delete a user account from the system'),
(15, 'create_tenant', 'Enter a new tenant into the system'),
(16, 'edit_tenant', 'Edit details of a particular tenant who is already in the system'),
(17, 'delete_tenant', 'Delete a tenant from the system'),
(18, 'change_room', 'Change the room occupied by a tenant'),
(19, 'move_out', 'Move out a tenant from building');

-- --------------------------------------------------------

--
-- Table structure for table `property`
--

CREATE TABLE IF NOT EXISTS `property` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `num_rooms` varchar(50) NOT NULL,
  `fee` varchar(50) NOT NULL,
  `landlord` varchar(100) NOT NULL,
  `added` date NOT NULL,
  `end` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `prop_name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;

--
-- Dumping data for table `property`
--

INSERT INTO `property` (`id`, `name`, `num_rooms`, `fee`, `landlord`, `added`, `end`) VALUES
(1, 'Petreshah Building', '52', '7', 'Mr.Tum', '2015-05-18', '0000-00-00'),
(3, 'Posta Flats', '78', '', 'Paul''s', '2015-05-18', '0000-00-00'),
(4, 'Nandi Arcade', '48', '', 'Kalya', '2015-05-19', '0000-00-00'),
(5, 'Zion Mall', '205', '', 'Lodwar', '2015-05-19', '0000-00-00'),
(7, 'Muya House', '10', '6', 'Landlord', '2015-10-06', '0000-00-00'),
(9, 'Crane Mall', '10', '5.8', 'Owner', '2015-10-01', '0000-00-00');

-- --------------------------------------------------------

--
-- Table structure for table `rent`
--

CREATE TABLE IF NOT EXISTS `rent` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL,
  `tid` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `date_paid` datetime NOT NULL,
  `amount` varchar(150) NOT NULL,
  `receipt_no` varchar(150) NOT NULL,
  `mode` varchar(150) NOT NULL,
  `agent` varchar(150) NOT NULL,
  `remarks` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pid` (`pid`),
  KEY `tid` (`tid`),
  KEY `start` (`start_date`),
  KEY `end` (`end_date`),
  KEY `receipt` (`receipt_no`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=45 ;

--
-- Dumping data for table `rent`
--

INSERT INTO `rent` (`id`, `pid`, `tid`, `start_date`, `end_date`, `date_paid`, `amount`, `receipt_no`, `mode`, `agent`, `remarks`) VALUES
(21, 1, 1, '2015-08-01', '2015-08-31', '2015-08-28 11:11:49', '14000', '000001', 'cheque', 'Code Warrior', NULL),
(23, 1, 9, '2015-08-01', '2015-08-31', '2015-08-28 14:02:26', '10000', '000002', 'cheque', 'Code Warrior', NULL),
(24, 1, 1, '2015-04-01', '2015-04-30', '2015-08-29 14:02:35', '14000', '000003', 'cheque', 'Code Warrior', NULL),
(27, 1, 1, '2015-05-01', '2015-05-31', '2015-08-29 20:08:09', '10000', '000004', 'cheque', 'Code Warrior', NULL),
(28, 1, 1, '2015-06-01', '2015-06-30', '2015-08-31 12:12:04', '10000', '000005', 'cheque', 'Code Warrior', NULL),
(29, 1, 9, '2015-04-01', '2015-04-30', '2015-08-31 16:04:58', '8000', '000006', 'cheque', 'Code Warrior', NULL),
(30, 1, 1, '2015-01-01', '2015-01-31', '2015-09-01 20:08:59', '14000', '000007', 'cheque', 'Code Warrior', NULL),
(31, 1, 1, '2015-07-01', '2015-07-31', '2015-09-07 09:09:40', '9000', '000008', 'cheque', 'Code Warrior', NULL),
(32, 1, 13, '2015-08-01', '2015-08-31', '2015-09-23 10:10:36', '11000', '000009', 'cash', 'Code Warrior', NULL),
(33, 1, 1, '2015-09-01', '2015-09-30', '2015-09-30 19:07:25', '10000', '000010', 'cash', 'Code Warrior', NULL),
(36, 9, 18, '2015-10-01', '2015-10-31', '2015-10-21 14:02:48', '8000', '000013', 'cash', 'Code Warrior', 'New Tenant'),
(38, 9, 20, '2015-10-01', '2015-10-31', '2015-10-21 15:03:50', '15000', '000014', 'cash', 'Code Warrior', '');

-- --------------------------------------------------------

--
-- Table structure for table `room`
--

CREATE TABLE IF NOT EXISTS `room` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `prop_id` int(11) NOT NULL,
  `tenant_id` int(11) DEFAULT NULL,
  `label` varchar(100) NOT NULL,
  `occupied` tinyint(3) NOT NULL,
  `rent_pm` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `rm_label` (`label`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=51 ;

--
-- Dumping data for table `room`
--

INSERT INTO `room` (`id`, `prop_id`, `tenant_id`, `label`, `occupied`, `rent_pm`) VALUES
(11, 1, 1, 'G1', 1, '14000'),
(12, 1, 9, 'G2', 1, '12000'),
(13, 1, 0, 'G3', 0, '16000'),
(14, 1, NULL, 'G4', 0, '22000'),
(15, 1, 12, 'G5', 1, '11000'),
(16, 3, NULL, 'Block A - Rm 001', 0, '15000'),
(17, 3, NULL, 'Block A - Rm 002', 0, '15000'),
(18, 3, NULL, 'Block A - Rm 003', 0, '15000'),
(19, 3, NULL, 'Block A - Rm 004', 0, '15000'),
(20, 3, NULL, 'Block A - Rm 005', 0, '15000'),
(21, 4, 0, 'NA/01', 0, '8000'),
(22, 4, NULL, 'NA/02', 0, '12000'),
(23, 4, NULL, 'NA/03', 0, '25000'),
(24, 4, NULL, 'NA/03-A', 0, '9000'),
(25, 4, NULL, 'NA/03-B', 0, '9000'),
(26, 5, NULL, 'LW-BS001', 0, '10000'),
(27, 5, NULL, 'LW-BS002', 0, '12000'),
(28, 5, NULL, 'RW-GF-001', 0, '18000'),
(29, 5, NULL, 'RW-GF-002', 0, '28000'),
(30, 5, NULL, 'EB-008', 0, '25000'),
(31, 6, 14, '1A', 1, '10000'),
(32, 6, 17, '1B', 1, '10000'),
(33, 6, NULL, '1C', 0, '12000'),
(34, 6, NULL, '1D', 0, '15000'),
(35, 6, NULL, '1E', 0, '10000'),
(36, 7, 16, '1A', 1, '10000'),
(37, 7, 0, '1B', 0, '11000'),
(38, 7, NULL, '1C', 0, '12000'),
(39, 7, NULL, '1D', 0, '10000'),
(40, 7, NULL, '1E', 0, '15000'),
(41, 9, 18, '1a', 1, '10000'),
(42, 9, 19, '1b', 1, '10000'),
(43, 9, NULL, '1c', 0, '12000'),
(44, 9, NULL, '1d', 0, '12000'),
(45, 9, 20, '1e', 1, '15000'),
(46, 10, 21, '1a', 1, '3000'),
(47, 10, 22, '1b', 1, '4000'),
(48, 10, 23, '1c', 1, '4000'),
(49, 10, 24, '1d', 1, '3000'),
(50, 10, 25, '1e', 1, '2000');

-- --------------------------------------------------------

--
-- Table structure for table `tenants`
--

CREATE TABLE IF NOT EXISTS `tenants` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL,
  `rid` int(11) NOT NULL,
  `fname` varchar(150) NOT NULL,
  `lname` varchar(150) NOT NULL,
  `phone_no` varchar(200) NOT NULL,
  `id_number` varchar(100) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `biz_name` varchar(200) DEFAULT NULL,
  `date_joined` date NOT NULL,
  `date_left` date DEFAULT '0000-00-00',
  `active` tinyint(3) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `phone_no` (`phone_no`),
  KEY `id_number` (`id_number`),
  KEY `pid` (`pid`),
  KEY `rid` (`rid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=26 ;

--
-- Dumping data for table `tenants`
--

INSERT INTO `tenants` (`id`, `pid`, `rid`, `fname`, `lname`, `phone_no`, `id_number`, `email`, `biz_name`, `date_joined`, `date_left`, `active`) VALUES
(1, 1, 11, 'New', 'Tenant', '0719644479', '29074310', 'ntu@mrm.co.ke', '', '2015-04-09', '0000-00-00', 1),
(9, 1, 12, 'Another', 'One', '0771746286', '29045781', 'sales@orbit.co.ke', 'Orbit Distributers', '2015-05-29', '0000-00-00', 1),
(12, 1, 15, 'App', 'User', '0720554747', '1967256', 'appuser@w3c.org', 'NetTier Solutions', '2015-09-12', '0000-00-00', 1),
(13, 1, 13, 'Stigg', 'Bakken', '0728261862', '28456712', 'stigg@devpulse.com', 'NetTier Solutions', '2015-05-29', '2015-09-23', 0),
(16, 7, 36, 'Kelvin', 'Macharia', '0722654321', '28456712', '', '', '2015-10-06', '0000-00-00', 1),
(18, 9, 41, 'Gil', 'Shwed', '0722123456', '21144501', '', '', '2015-10-05', '0000-00-00', 1),
(19, 9, 42, 'Sigmund', 'Freud', '0705958150', '1498745', '', '', '2015-10-05', '0000-00-00', 1),
(20, 9, 45, 'Max', 'Planc', '0705958150', '1498745', '', '', '2015-10-10', '0000-00-00', 1);

-- --------------------------------------------------------

--
-- Table structure for table `user2collection`
--

CREATE TABLE IF NOT EXISTS `user2collection` (
  `user_id` int(11) NOT NULL,
  `collection_id` int(11) NOT NULL,
  PRIMARY KEY (`user_id`,`collection_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user2collection`
--

INSERT INTO `user2collection` (`user_id`, `collection_id`) VALUES
(20, 2),
(27, 3),
(28, 1),
(28, 2),
(35, 2),
(35, 3),
(52, 2),
(52, 3),
(53, 1),
(54, 3);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `fname` varchar(150) NOT NULL,
  `lname` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `username` varchar(150) NOT NULL,
  `password` varchar(150) NOT NULL,
  `phone_no` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  KEY `fname` (`fname`,`lname`,`username`,`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=54 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fname`, `lname`, `email`, `username`, `password`, `phone_no`) VALUES
(20, 'Andy', 'Gutmans', 'a_gutmans@zend.com', 'phpguru', 'webdevsince1994', '0720554747'),
(21, 'Leon', 'O''Reilly', 'zsuraski@zend.com', 'phpmaster', 'codemaster32', '0722228092'),
(22, 'Stigg', 'Bakken', 's_bakken@zend.com', 'devpro', 'theserverside', '0720123456'),
(23, 'Rasmus', 'Lerdorf', 'ras_lerdorf@zendframework.com', 'php_ini', 'dynamic_scripting', '0722902261'),
(25, 'Ray', 'Ozzie', 'ozzie@lotusnotes.com', 'kernel_nt', 'hardcore_devt', '0719644479'),
(27, 'Code', 'Monkey', 'bpo@sweatshop.com', 'overseas', 'outsourcing101', '0771157731'),
(28, 'Code', 'Warrior', 'codewarrior@devbeat.com', 'CodeWarrior', 'lpi02H6q6F8opyyDC61299tky3Hl8Y', '0771746286'),
(52, 'App', 'User', '', 'username', 'password', ''),
(53, 'Default', 'User', 'defaultuser@server.com', 'admin', 'pass', '');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `arrears`
--
ALTER TABLE `arrears`
  ADD CONSTRAINT `arrears_ibfk_1` FOREIGN KEY (`tid`) REFERENCES `tenants` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `arrears_paid`
--
ALTER TABLE `arrears_paid`
  ADD CONSTRAINT `arrears_paid_ibfk_1` FOREIGN KEY (`tid`) REFERENCES `tenants` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `cheque`
--
ALTER TABLE `cheque`
  ADD CONSTRAINT `cheque_ibfk_1` FOREIGN KEY (`tid`) REFERENCES `tenants` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `deposits`
--
ALTER TABLE `deposits`
  ADD CONSTRAINT `deposits_ibfk_1` FOREIGN KEY (`tid`) REFERENCES `tenants` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `expense`
--
ALTER TABLE `expense`
  ADD CONSTRAINT `expense_ibfk_1` FOREIGN KEY (`pid`) REFERENCES `property` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `payment_status`
--
ALTER TABLE `payment_status`
  ADD CONSTRAINT `payment_status_ibfk_1` FOREIGN KEY (`tid`) REFERENCES `tenants` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `rent`
--
ALTER TABLE `rent`
  ADD CONSTRAINT `rent_ibfk_1` FOREIGN KEY (`pid`) REFERENCES `property` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `rent_ibfk_2` FOREIGN KEY (`tid`) REFERENCES `tenants` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tenants`
--
ALTER TABLE `tenants`
  ADD CONSTRAINT `tenants_ibfk_1` FOREIGN KEY (`pid`) REFERENCES `property` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tenants_ibfk_2` FOREIGN KEY (`rid`) REFERENCES `room` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
