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
  `description` text DEFAULT NULL,
  `philhealth_id` varchar(20) DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `status` enum('scheduled','done','canceled','ongoing') DEFAULT 'scheduled',
  `is_confirmed` tinyint(1) NOT NULL DEFAULT 0,
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

INSERT INTO `appointment` (`appoid`, `pid`, `apponum`, `scheduleid`, `appodate`, `scheduletime`, `is_self`, `other_patient_name`, `description`, `philhealth_id`, `age`, `status`, `is_confirmed`) VALUES
(2, 1, 1, 1, '2024-12-17', '10:00:00', 0, NULL, NULL, NULL, NULL, 'done', 0),
(3, 2, 2, 2, '2024-12-18', '14:00:00', 1, 'John Doe', 'General checkup', 'PH987654321', 30, 'done', 0);

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
    -- When is_self is 1 (appointment for others), these fields must not be NULL
    ELSEIF NEW.is_self = 1 THEN
        IF NEW.other_patient_name IS NULL OR NEW.description IS NULL OR NEW.philhealth_id IS NULL OR NEW.age IS NULL THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'All fields for other patient (name, description, PhilHealth ID, and age) must be provided when is_self is 1';
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
  `phone_number` varchar(15) NOT NULL,
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
  `phone_number` varchar(15) NOT NULL,
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

INSERT INTO `patient` (`pid`, `pemail`, `pname`, `ppassword`, `paddress`, `hasPhilhealth`, `pdob`, `phone_number`, `created_at`, `updated_at`, `is_deleted`) VALUES
(1, 'patient@gmail.com', 'test patient', 'test123', '#12a De Aro Avenue, Mabayuan, Olongapo City', 'yes', '1955-02-04', '+639685837376', '2025-02-05 16:04:24', '2025-02-05 16:04:24', 0),
(2, 'john.test@gmail.com', 'John Tester', 'John2Test', '#45 Amagis Avenue, Olongapo City', 'no', '1965-05-15', '+639123456789', '2025-02-05 16:10:00', '2025-02-05 16:10:00', 0),
(3, 'maria.grace@gmail.com', 'Maria Grace', 'Maria2Grace', '#78 Grace Pauline Street, Olongapo City', 'yes', '1975-11-20', '+639987654321', '2025-02-05 16:15:00', '2025-02-05 16:15:00', 0),
(4, 'alex.leyva@gmail.com', 'Alex Leyva', 'Alex2Leyva', '#22 Leyva Street, Olongapo City', 'no', '1985-08-10', '+639567890123', '2025-02-05 16:20:00', '2025-02-05 16:20:00', 0),
(5, 'sarah.mercurio@gmail.com', 'Sarah Mercurio', 'Sarah2Mercurio', '#56 Mercurio Street, Olongapo City', 'yes', '1995-03-25', '+639234567890', '2025-02-05 16:25:00', '2025-02-05 16:25:00', 0),
(6, 'emma.rose@gmail.com', 'Emma Rose', 'Emma2Rose', '#33 Rosete Street, Olongapo City', 'no', '1960-07-12', '+639876543210', '2025-02-05 16:30:00', '2025-02-05 16:30:00', 0),
(7, 'michael.park@gmail.com', 'Michael Park', 'Michael2Park', '#67 Napalan Street, Olongapo City', 'yes', '1970-09-18', '+639345678901', '2025-02-05 16:35:00', '2025-02-05 16:35:00', 0),
(8, 'lisa.wong@gmail.com', 'Lisa Wong', 'Lisa2Wong', '#89 Nieves Street, Olongapo City', 'no', '1980-12-30', '+639654321987', '2025-02-05 16:40:00', '2025-02-05 16:40:00', 0);

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
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`scheduleid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `schedule`
--

INSERT INTO `schedule` (`scheduleid`, `docid`, `title`, `scheduledate`, `scheduletime`, `session_duration`, `end_time`, `nop`, `deleted_at`) VALUES
(9, '1', 'Current Test Session 1', '2025-02-02', '18:39:38', 60, '19:39:38', 10, NULL);

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
('patient@gmail.com', 'p'),
('john.test@gmail.com', 'p'),
('maria.grace@gmail.com', 'p'),
('alex.leyva@gmail.com', 'p'),
('sarah.mercurio@gmail.com', 'p'),
('emma.rose@gmail.com', 'p'),
('michael.park@gmail.com', 'p'),
('lisa.wong@gmail.com', 'p');

DELIMITER $$
--
-- Events
--
CREATE DEFINER=`root`@`localhost` EVENT `update_appointment_status` ON SCHEDULE EVERY 1 MINUTE STARTS '2025-02-02 18:39:39' ON COMPLETION NOT PRESERVE ENABLE DO BEGIN
   -- Set appointments to 'ongoing' if the current date and time match the scheduled date/time
   UPDATE `appointment`
   SET `status` = 'ongoing'
   WHERE `appodate` = CURDATE() AND `scheduletime` <= CURTIME()
   AND `status` = 'scheduled';

   -- Set appointments to 'done' if the scheduled time has passed
   UPDATE `appointment`
   SET `status` = 'done'
   WHERE `appodate` < CURDATE() OR (`appodate` = CURDATE() AND `scheduletime` < CURTIME())
   AND `status` = 'ongoing';

   -- Optionally, set appointments to 'cancelled' if they haven't been attended yet
   UPDATE `appointment`
   SET `status` = 'canceled'
   WHERE `appodate` > CURDATE() OR (`appodate` = CURDATE() AND `scheduletime` > CURTIME())
   AND `status` = 'scheduled';
END$$

CREATE EVENT `cleanup_old_appointments` 
ON SCHEDULE EVERY 1 WEEK 
STARTS '2025-02-02 00:00:00' 
DO BEGIN
    -- Delete appointments older than 2 years
    DELETE FROM `appointment` 
    WHERE `appodate` < DATE_SUB(CURDATE(), INTERVAL 2 YEAR);
END$$

DELIMITER ;


/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
