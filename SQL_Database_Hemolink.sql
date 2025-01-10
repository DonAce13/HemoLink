-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
-- Host: 127.0.0.1:3306
-- Generation Time: Dec 15, 2024 at 01:39 PM
-- Server version: 5.7.26
-- PHP Version: 7.3.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

-- --------------------------------------------------------
-- Table structure for table `admin`
DROP TABLE IF EXISTS `admin`;
CREATE TABLE IF NOT EXISTS `admin` (
  `aemail` varchar(255) NOT NULL,
  `apassword` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`aemail`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`aemail`, `apassword`) VALUES
('administrator@gmail.com', '123');
-- --------------------------------------------------------


-- Table structure for table `appointment`
DROP TABLE IF EXISTS `appointment`;
CREATE TABLE IF NOT EXISTS `appointment` (
  `appoid` int(11) NOT NULL AUTO_INCREMENT,  -- Primary key
  `pid` int(10) DEFAULT NULL,  -- Patient ID (foreign key to patient table)
  `apponum` int(3) DEFAULT NULL,  -- Appointment number
  `scheduleid` int(10) DEFAULT NULL,  -- Schedule ID (foreign key to schedule table)
  `appodate` date DEFAULT NULL,  -- Appointment date
  `scheduletime` TIME DEFAULT NULL,  -- Appointment time
  `is_self` BOOLEAN NOT NULL DEFAULT 0,  -- 0 means appointment for self, 1 means appointment for others
  `other_patient_name` VARCHAR(255) DEFAULT NULL,  -- Name of other patient (if is_self is 1)
  `description` TEXT DEFAULT NULL,  -- Appointment description or reason
  `philhealth_id` VARCHAR(20) DEFAULT NULL,  -- PhilHealth ID (for other patients)
  `age` INT(3) DEFAULT NULL,  -- Age of the patient (for other patients)
  `status` ENUM('scheduled', 'done', 'canceled', 'ongoing') DEFAULT 'scheduled',  -- Appointment status
  PRIMARY KEY (`appoid`),
  KEY `pid` (`pid`),
  KEY `scheduleid` (`scheduleid`),
  KEY `idx_appodate` (`appodate`),
  KEY `idx_pid_scheduleid` (`pid`, `scheduleid`),
  KEY `idx_is_self` (`is_self`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

-- Create Trigger to validate the `is_self` logic
DELIMITER $$

CREATE TRIGGER validate_is_self_fields
BEFORE INSERT ON `appointment`
FOR EACH ROW
BEGIN
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
END $$

DELIMITER ;

-- Sample data for appointments

-- Sample appointment for self (is_self = 0)
INSERT INTO `appointment` (`pid`, `apponum`, `scheduleid`, `appodate`, `scheduletime`, `is_self`, `status`) 
VALUES
  (1, 1, 1, '2024-12-17', '10:00:00', 0, 'scheduled');

-- Sample appointment for others (is_self = 1)
INSERT INTO `appointment` (`pid`, `apponum`, `scheduleid`, `appodate`, `scheduletime`, `is_self`, `other_patient_name`, `description`, `philhealth_id`, `age`, `status`) 
VALUES
  (2, 2, 2, '2024-12-18', '14:00:00', 1, 'John Doe', 'General checkup', 'PH987654321', 30, 'scheduled');


-- --------------------------------------------------------
-- Table structure for table `doctor`
DROP TABLE IF EXISTS `doctor`;
CREATE TABLE IF NOT EXISTS `doctor` (
  `docid` int(11) NOT NULL AUTO_INCREMENT,
  `docemail` varchar(255) DEFAULT NULL,
  `docname` varchar(255) DEFAULT NULL,
  `docpassword` varchar(255) DEFAULT NULL,
  `docnic` varchar(15) DEFAULT NULL,
  `doctel` varchar(15) DEFAULT NULL,
  `specialties` int(2) DEFAULT NULL,
  PRIMARY KEY (`docid`),
  KEY `specialties` (`specialties`),
  KEY `idx_docemail` (`docemail`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

-- Dumping data for table `doctor`
INSERT INTO `doctor` (`docid`, `docemail`, `docname`, `docpassword`, `docnic`, `doctel`, `specialties`) VALUES
(1, 'doctor@gmail.com', 'Test Doctor', '123', '000000000', '0110000000', 1);

-- --------------------------------------------------------
-- Table structure for table `patient`
DROP TABLE IF EXISTS `patient`;
CREATE TABLE IF NOT EXISTS `patient` (
  `pid` int(11) NOT NULL AUTO_INCREMENT,
  `pemail` varchar(255) DEFAULT NULL,
  `pname` varchar(255) DEFAULT NULL,
  `ppassword` varchar(255) DEFAULT NULL,
  `paddress` varchar(255) DEFAULT NULL,
  `pnic` varchar(15) DEFAULT NULL,
  `pdob` date DEFAULT NULL,
  `ptel` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`pid`),
  KEY `idx_pemail` (`pemail`),
  KEY `idx_ptel` (`ptel`),
  KEY `idx_pnic` (`pnic`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

-- Create a trigger to enforce address format using REGEXP
DELIMITER $$

CREATE TRIGGER validate_address_format
BEFORE INSERT ON `patient`
FOR EACH ROW
BEGIN
  IF NOT NEW.paddress REGEXP '^[0-9]+, [A-Za-z ]+$' THEN
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = 'Invalid address format. Address must start with a number followed by a comma and space, and then the street name.';
  END IF;
END $$

DELIMITER ;

-- Sample data insertion
INSERT INTO `patient` (`pid`, `pemail`, `pname`, `ppassword`, `paddress`, `pnic`, `pdob`, `ptel`) VALUES
(1, 'patient@gmail.com', 'Test Patient', '123', '87, Otero Avenue', '0000000000', '2000-01-01', '0120000000');

-- --------------------------------------------------------
DROP TABLE IF EXISTS `schedule`;
CREATE TABLE IF NOT EXISTS `schedule` (
  `scheduleid` int(11) NOT NULL AUTO_INCREMENT,  -- Primary key
  `docid` varchar(255) DEFAULT NULL,  -- Doctor ID
  `title` varchar(255) DEFAULT NULL,  -- Title of the session
  `scheduledate` date DEFAULT NULL,  -- Date of the session
  `scheduletime` time DEFAULT NULL,  -- Start time of the session
  `session_duration` int(4) DEFAULT NULL,  -- Session duration (in minutes)
  `end_time` time DEFAULT NULL,  -- End time of the session
  `nop` int(4) DEFAULT NULL,  -- Number of patients
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,  -- Soft delete flag
  PRIMARY KEY (`scheduleid`),
  KEY `docid` (`docid`),
  KEY `idx_scheduledate_time` (`scheduledate`, `scheduletime`),
  KEY `idx_docid_date` (`docid`, `scheduledate`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;


-- Insert data with dynamic current date, time, session duration, and calculated end time
INSERT INTO `schedule` (`docid`, `title`, `scheduledate`, `scheduletime`, `session_duration`, `end_time`, `nop`) 
VALUES
('1', 
 'Current Test Session 1', 
 CURDATE(), 
 CURTIME(), 
 60,  -- Session duration in minutes
 ADDTIME(CURTIME(), SEC_TO_TIME(60 * 60)),  -- End time (adding session duration in seconds)
 10);  -- Number of patients


-- --------------------------------------------------------
-- Create table `archived_schedule` for soft-deleted sessions

CREATE TABLE IF NOT EXISTS `archived_schedule` (
  `scheduleid` int(11) NOT NULL,
  `docid` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `scheduledate` date DEFAULT NULL,
  `scheduletime` time DEFAULT NULL,
  `nop` int(4) DEFAULT NULL,
  `deleted_at` TIMESTAMP NOT NULL,
  PRIMARY KEY (`scheduleid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------
-- Table structure for table `specialties`
DROP TABLE IF EXISTS `specialties`;
CREATE TABLE IF NOT EXISTS `specialties` (
  `id` int(2) NOT NULL,
  `sname` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table `specialties`
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
-- Table structure for table `webuser`
DROP TABLE IF EXISTS `webuser`;
CREATE TABLE IF NOT EXISTS `webuser` (
  `email` varchar(255) NOT NULL,
  `usertype` char(1) DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Dumping data for table `webuser`
INSERT INTO `webuser` (`email`, `usertype`) VALUES
('administrator@gmail.com', 'a'),
('doctor@gmail.com', 'd'),
('patient@gmail.com', 'p'),
('wakuwaku@gmail.com', 'p');

-- --------------------------------------------------------
-- MySQL Event to update appointment statuses periodically

DELIMITER $$

CREATE EVENT IF NOT EXISTS update_appointment_status
ON SCHEDULE EVERY 1 MINUTE
DO
BEGIN
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

DELIMITER ;

COMMIT;
