-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 05, 2025 at 06:25 PM
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
-- Database: `sql_database_hemolink`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `aemail` varchar(255) NOT NULL,
  `apassword` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`aemail`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`aemail`, `apassword`) VALUES
('administrator@gmail.com', '123');

-- --------------------------------------------------------

--
-- Table structure for table `appointment`
--

CREATE TABLE `appointment` (
  `appoid` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) DEFAULT NULL,
  `apponum` int(11) DEFAULT NULL,
  `scheduleid` int(11) DEFAULT NULL,
  `appodate` date DEFAULT NULL,
  `scheduletime` time DEFAULT NULL,
  `is_self` tinyint(1) NOT NULL DEFAULT 0,
  `other_patient_name` varchar(255) DEFAULT NULL,
  `philhealth_id` varchar(20) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `status` enum('scheduled','done','canceled','ongoing') DEFAULT 'scheduled',
  `is_confirmed` tinyint(1) NOT NULL DEFAULT 0,
  `rejection_timestamp` DATETIME DEFAULT NULL,
  `rejection_reason` VARCHAR(255) DEFAULT NULL,
  `booking_attempt_timestamp` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `attended` TINYINT(1) NOT NULL DEFAULT 0, -- 1 = Attended, 0 = Pending, -1 = Not Visited
  PRIMARY KEY (`appoid`),
  KEY `pid` (`pid`),
  KEY `scheduleid` (`scheduleid`),
  KEY `idx_appodate` (`appodate`),
  KEY `idx_pid_scheduleid` (`pid`,`scheduleid`),
  KEY `idx_is_self` (`is_self`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `appointment`
--

INSERT INTO `appointment` (`appoid`, `pid`, `apponum`, `scheduleid`, `appodate`, `scheduletime`, `is_self`, `other_patient_name`, `philhealth_id`, `description`, `age`, `status`, `is_confirmed`, `rejection_timestamp`, `rejection_reason`, `booking_attempt_timestamp`, `attended`) VALUES
(2, 1, 1, 1, '2024-12-17', '10:00:00', 0, NULL, NULL, NULL, NULL, 'done', 0, NULL, NULL, '2025-03-04 15:06:00', 0),
(3, 2, 2, 2, '2024-12-18', '14:00:00', 1, 'John Doe', 'PH987654321', 'General checkup', 30, 'done', 0, NULL, NULL, '2025-03-04 15:06:00', 0),
-- DEMO APPROVED APPOINTMENTS FOR TESTING
(1001, 1, 3, 1, '2025-03-04', '18:39:38', 0, NULL, NULL, NULL, NULL, 'scheduled', 1, NULL, NULL, '2025-03-04 16:00:00', 0),
(1002, 2, 4, 2, '2025-03-04', '13:00:00', 1, 'Jane Test', 'PH123456789', 'Follow-up', 40, 'scheduled', 1, NULL, NULL, '2025-03-04 16:05:00', 0),
(1003, 1, 5, 1, '2025-03-05', '10:00:00', 0, NULL, NULL, NULL, NULL, 'done', 1, NULL, NULL, '2025-03-05 10:00:00', 0),
(1004, 2, 6, 2, '2025-03-05', '14:00:00', 1, 'John Doe', 'PH987654321', 'Checkup', 30, 'done', 1, NULL, NULL, '2025-03-05 10:05:00', 0),
-- SAMPLE FOR NOT VISITED
(1005, 3, 7, 3, '2025-03-06', '09:00:00', 0, NULL, NULL, NULL, NULL, 'scheduled', 1, NULL, NULL, '2025-03-06 08:00:00', -1),
(1006, 4, 8, 4, '2025-03-07', '10:00:00', 0, NULL, NULL, NULL, NULL, 'scheduled', 1, NULL, NULL, '2025-03-07 09:00:00', 0),
(1007, 5, 9, 5, '2025-03-08', '11:00:00', 0, NULL, NULL, NULL, NULL, 'scheduled', 1, NULL, NULL, '2025-03-08 10:00:00', 0),
(1008, 6, 10, 6, '2025-03-09', '12:00:00', 0, NULL, NULL, NULL, NULL, 'scheduled', 1, NULL, NULL, '2025-03-09 11:00:00', 0);

--
-- Triggers `appointment`
--
DELIMITER $$
CREATE TRIGGER `validate_is_self_fields` BEFORE INSERT ON `appointment` FOR EACH ROW BEGIN
    -- When is_self is 0 (appointment for self), other fields must be NULL
    IF NEW.is_self = 0 THEN
        SET NEW.other_patient_name = NULL;
        SET NEW.description = NULL;
        SET NEW.philhealth_id = NULL;
        SET NEW.age = NULL;
        
        -- Set booking_attempt_timestamp to current time if not already set
        IF NEW.booking_attempt_timestamp IS NULL THEN
            SET NEW.booking_attempt_timestamp = CURRENT_TIMESTAMP;
        END IF;
    
    -- When is_self is 1 (appointment for others), these fields must not be NULL
    ELSEIF NEW.is_self = 1 THEN
        IF NEW.other_patient_name IS NULL OR NEW.description IS NULL OR NEW.philhealth_id IS NULL OR NEW.age IS NULL THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'All fields for other patient (name, description, PhilHealth ID, and age) must be provided when is_self is 1';
        END IF;
        
        -- Set booking_attempt_timestamp to current time if not already set
        IF NEW.booking_attempt_timestamp IS NULL THEN
            SET NEW.booking_attempt_timestamp = CURRENT_TIMESTAMP;
        END IF;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `archived_schedule`
--

CREATE TABLE `archived_schedule` (
  `scheduleid` int(11) NOT NULL,
  `docid` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `scheduledate` date DEFAULT NULL,
  `scheduletime` time DEFAULT NULL,
  `nop` int(11) DEFAULT NULL,
  `deleted_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`scheduleid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `doctor`
--

CREATE TABLE `doctor` (
  `docid` int(11) NOT NULL,
  `docemail` varchar(255) DEFAULT NULL,
  `docname` varchar(255) DEFAULT NULL,
  `docpassword` varchar(255) DEFAULT NULL,
  `docnic` varchar(15) DEFAULT NULL,
  `doctel` varchar(15) DEFAULT NULL,
  `specialties` int(2) DEFAULT NULL,
  PRIMARY KEY (`docid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `doctor`
--

INSERT INTO `doctor` (`docid`, `docemail`, `docname`, `docpassword`, `docnic`, `doctel`, `specialties`) VALUES
(1, 'doctor@gmail.com', 'Test Doctor', '123', '000000000', '0110000000', 1);

-- --------------------------------------------------------

--
-- Table structure for table `otp_verifications`
--

CREATE TABLE `otp_verifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pemail` varchar(255) NOT NULL UNIQUE,
  `otp` varchar(6) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `patient`
--

CREATE TABLE `patient` (
  `pid` int(11) NOT NULL AUTO_INCREMENT,
  `pemail` varchar(255) NOT NULL,
  `pname` varchar(255) NOT NULL,
  `ppassword` char(60) NOT NULL,
  `paddress` varchar(255) DEFAULT NULL,
  `hasPhilhealth` varchar(15) DEFAULT NULL,
  `pdob` date DEFAULT NULL,
  `phone_number` varchar(15) NULL,
  `patient_category` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_deleted` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`pid`),
  UNIQUE KEY `pemail` (`pemail`),
  UNIQUE KEY `phone_number` (`phone_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `patient`
--

INSERT INTO `patient` (`pid`, `pemail`, `pname`, `ppassword`, `paddress`, `hasPhilhealth`, `pdob`, `phone_number`, `patient_category`, `created_at`, `updated_at`, `is_deleted`) VALUES
(1, 'seannandreidatu@gmail.com', 'Seann Andrei Datu', '@Kindred130605', '#12a Otero Avenue, Mabayuan, Olongapo City', 'no', '2002-12-10', NULL, NULL, '2025-06-02 09:04:04', '2025-06-02 09:04:04', 0),
(2, 'ravenlegarde23@gmail.com', 'Raven Legarde', '123!', '#12a Leyva Avenue, Mabayuan, Olongapo City', 'no', '2007-06-09', NULL, NULL, '2025-06-09 07:32:54', '2025-06-09 07:32:54', 0),
(3, 'markbuffalo232@gmail.com', 'Mark Corea', 'Test12345@', '#35 Labrador Avenue, Mabayuan, Olongapo City', 'no', '1972-07-02', NULL, NULL, '2025-06-12 10:26:02', '2025-06-12 10:26:02', 0),
(4, 'juliusrusscruz@gmail.com', 'Julius Russ Cruz', 'cruzfam1992@', '#35 Otero Avenue, Mabayuan, Olongapo City', 'yes', '1972-07-02', NULL, 'SENIOR CITIZEN', '2025-06-12 13:24:10', '2025-06-12 13:24:10', 0),
(5, 'evalyndorigo@gmail.com', 'EVALYN DORIGO', 'evalyn08!', '#123 Amagis Avenue, Mabayuan, Olongapo City', 'no', '1987-04-08', NULL, 'PWD', '2025-06-12 16:15:26', '2025-06-12 16:15:26', 0),
(6, 'cruzjedediahyco@gmail.com', 'Juan Dela Cruz', 'juanD123@', '#35a Otero Avenue, Mabayuan, Olongapo City', 'no', '1964-06-01', NULL, 'SENIOR CITIZEN', '2025-06-12 23:14:15', '2025-06-12 23:14:15', 0);

-- --------------------------------------------------------

--
-- Table structure for table `schedule`
--
CREATE TABLE `schedule` (
  `scheduleid` int(11) NOT NULL AUTO_INCREMENT,
  `docid` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `scheduledate` date DEFAULT NULL,
  `scheduletime` time DEFAULT NULL,
  `session_duration` int(4) DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `nop` int(4) DEFAULT NULL,
  `total_slots` int(4) DEFAULT NULL,
  `available_slots` int(4) DEFAULT NULL,
  `approved_bookings` int(4) DEFAULT 0,
  `max_approved_bookings` int(4) DEFAULT 5,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`scheduleid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `schedule`
--

INSERT INTO schedule (scheduleid, docid, title, scheduledate, scheduletime, session_duration, end_time, nop, total_slots, available_slots, approved_bookings, max_approved_bookings, deleted_at) VALUES
(1, 1, 'Sample Session', '2025-07-01', '09:00:00', 60, '10:00:00', 10, 10, 10, 0, 5, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `specialties`
--

CREATE TABLE `specialties` (
  `id` int(2) NOT NULL AUTO_INCREMENT,
  `sname` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `specialties`
--

INSERT INTO `specialties` (`id`, `sname`) VALUES
(1, 'Accident and emergency medicine'),
(2, 'Allergology'),
(3, 'Anaesthetics'),
(4, 'Biological hematology'),
(5, 'Cardiology'),
(6, 'Child psychiatry'),
(7, 'Clinical biology'),
(8, 'Clinical chemistry'),
(9, 'Clinical neurophysiology'),
(10, 'Clinical radiology'),
(11, 'Dental, oral and maxillo-facial surgery'),
(12, 'Dermato-venerology'),
(13, 'Dermatology'),
(14, 'Endocrinology'),
(15, 'Gastro-enterologic surgery'),
(16, 'Gastroenterology'),
(17, 'General hematology'),
(18, 'General Practice'),
(19, 'General surgery'),
(20, 'Geriatrics'),
(21, 'Immunology'),
(22, 'Infectious diseases'),
(23, 'Internal medicine'),
(24, 'Laboratory medicine'),
(25, 'Maxillo-facial surgery'),
(26, 'Microbiology'),
(27, 'Nephrology'),
(28, 'Neuro-psychiatry'),
(29, 'Neurology'),
(30, 'Neurosurgery'),
(31, 'Nuclear medicine'),
(32, 'Obstetrics and gynecology'),
(33, 'Occupational medicine'),
(34, 'Ophthalmology'),
(35, 'Orthopaedics'),
(36, 'Otorhinolaryngology'),
(37, 'Paediatric surgery'),
(38, 'Paediatrics'),
(39, 'Pathology'),
(40, 'Pharmacology'),
(41, 'Physical medicine and rehabilitation'),
(42, 'Plastic surgery'),
(43, 'Podiatric Medicine'),
(44, 'Podiatric Surgery'),
(45, 'Psychiatry'),
(46, 'Public health and Preventive Medicine'),
(47, 'Radiology'),
(48, 'Radiotherapy'),
(49, 'Respiratory medicine'),
(50, 'Rheumatology'),
(51, 'Stomatology'),
(52, 'Thoracic surgery'),
(53, 'Tropical medicine'),
(54, 'Urology'),
(55, 'Vascular surgery'),
(56, 'Venereology');

-- --------------------------------------------------------

--
-- Table structure for table `webuser`
--

CREATE TABLE `webuser` (
  `email` varchar(255) NOT NULL,
  `usertype` char(1) DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `webuser`
--

INSERT INTO `webuser` (`email`, `usertype`) VALUES
('administrator@gmail.com', 'a'),
('doctor@gmail.com', 'd'),
('seannandreidatu@gmail.com', 'p'),
('ravenlegarde23@gmail.com', 'p'),
('markbuffalo232@gmail.com', 'p'),
('juliusrusscruz@gmail.com', 'p'),
('evalyndorigo@gmail.com', 'p'),
('cruzjedediahyco@gmail.com', 'p');

DELETE FROM webuser WHERE email NOT IN (SELECT pemail FROM patient);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
