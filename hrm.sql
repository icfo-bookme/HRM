-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jul 29, 2026 at 11:52 AM
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
-- Database: `hrm`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `shift_id` bigint(20) UNSIGNED DEFAULT NULL,
  `attendance_date` date NOT NULL,
  `first_in_at` datetime DEFAULT NULL,
  `last_out_at` datetime DEFAULT NULL,
  `check_in_at` datetime DEFAULT NULL,
  `check_out_at` datetime DEFAULT NULL,
  `break_minutes` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `working_minutes` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `net_working_minutes` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `late_minutes` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `early_out_minutes` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `overtime_minutes` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `is_late` tinyint(1) NOT NULL DEFAULT 0,
  `is_early_out` tinyint(1) NOT NULL DEFAULT 0,
  `is_absent` tinyint(1) NOT NULL DEFAULT 0,
  `is_holiday_work` tinyint(1) NOT NULL DEFAULT 0,
  `attendance_status` enum('Present','Absent','Half Day','On Leave','Holiday','Weekend') NOT NULL DEFAULT 'Present',
  `approval_status` enum('Pending','Approved','Rejected') NOT NULL DEFAULT 'Approved',
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `source` enum('Device','Manual','CSV','API','Mobile App','Web','Leave Auto') NOT NULL DEFAULT 'Device',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `attendance_devices`
--

CREATE TABLE `attendance_devices` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED NOT NULL,
  `device_name` varchar(150) NOT NULL,
  `device_code` varchar(50) NOT NULL,
  `device_type` enum('Fingerprint','Face','Card','Mobile App','Web','Manual') NOT NULL,
  `brand` varchar(100) DEFAULT NULL,
  `model` varchar(100) DEFAULT NULL,
  `serial_number` varchar(100) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `port` varchar(10) DEFAULT NULL,
  `communication_type` enum('LAN','WAN','WiFi','Cloud API','USB') NOT NULL DEFAULT 'LAN',
  `firmware_version` varchar(50) DEFAULT NULL,
  `timezone` varchar(255) NOT NULL DEFAULT 'Asia/Dhaka',
  `location` varchar(255) DEFAULT NULL,
  `last_sync_at` datetime DEFAULT NULL,
  `sync_status` enum('Online','Offline','Syncing','Error') NOT NULL DEFAULT 'Offline',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `notes` text DEFAULT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `attendance_logs`
--

CREATE TABLE `attendance_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `device_id` bigint(20) UNSIGNED DEFAULT NULL,
  `punch_datetime` datetime NOT NULL,
  `punch_type` enum('IN','OUT','BREAK_IN','BREAK_OUT') NOT NULL,
  `source` enum('Device','Mobile App','Web','Manual','CSV','API') NOT NULL DEFAULT 'Device',
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `verification_method` enum('Fingerprint','Face','Card','PIN','GPS','Manual') DEFAULT NULL,
  `raw_log_id` varchar(255) DEFAULT NULL,
  `is_processed` tinyint(1) NOT NULL DEFAULT 0,
  `processing_date` datetime DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `branches`
--

CREATE TABLE `branches` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED DEFAULT NULL,
  `code` varchar(20) NOT NULL,
  `name` varchar(200) NOT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `country` varchar(100) NOT NULL DEFAULT 'Bangladesh',
  `zip_code` varchar(20) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `is_head_office` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `companies`
--

CREATE TABLE `companies` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(300) NOT NULL,
  `legal_name` varchar(300) DEFAULT NULL,
  `trade_license` varchar(100) DEFAULT NULL,
  `bin_number` varchar(50) DEFAULT NULL,
  `tin_number` varchar(50) DEFAULT NULL,
  `industry` varchar(150) DEFAULT NULL,
  `founded_year` year(4) DEFAULT NULL,
  `logo_path` varchar(500) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `country` varchar(100) NOT NULL DEFAULT 'Bangladesh',
  `phone` varchar(30) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `website` varchar(200) DEFAULT NULL,
  `timezone` varchar(50) NOT NULL DEFAULT 'Asia/Dhaka',
  `date_format` varchar(20) NOT NULL DEFAULT 'Y-m-d',
  `fiscal_year_start` date DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `settings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`settings`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED NOT NULL,
  `cost_center_id` bigint(20) UNSIGNED DEFAULT NULL,
  `parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `code` varchar(30) DEFAULT NULL,
  `name` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `head_employee_id` bigint(20) UNSIGNED DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `designations`
--

CREATE TABLE `designations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `department_id` bigint(20) UNSIGNED DEFAULT NULL,
  `grade_id` bigint(20) UNSIGNED DEFAULT NULL,
  `code` varchar(30) DEFAULT NULL,
  `title` varchar(200) NOT NULL,
  `level` tinyint(4) NOT NULL DEFAULT 1,
  `responsibilities` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`responsibilities`)),
  `requirements` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`requirements`)),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `document_categories`
--

CREATE TABLE `document_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `code` varchar(50) DEFAULT NULL,
  `requires_expiry` tinyint(1) NOT NULL DEFAULT 0,
  `is_mandatory` tinyint(1) NOT NULL DEFAULT 0,
  `retention_days` int(11) NOT NULL DEFAULT 0,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_code` varchar(50) NOT NULL,
  `branch_id` bigint(20) UNSIGNED NOT NULL,
  `department_id` bigint(20) UNSIGNED NOT NULL,
  `designation_id` bigint(20) UNSIGNED NOT NULL,
  `grade_id` bigint(20) UNSIGNED DEFAULT NULL,
  `shift_id` bigint(20) UNSIGNED DEFAULT NULL,
  `count_late_for_payroll` tinyint(1) NOT NULL DEFAULT 1,
  `count_overtime_for_payroll` tinyint(1) NOT NULL DEFAULT 1,
  `reports_to` bigint(20) UNSIGNED DEFAULT NULL,
  `employment_type` enum('Full-Time','Part-Time','Contractual','Intern','Probation','Freelance') NOT NULL DEFAULT 'Full-Time',
  `joining_date` date NOT NULL,
  `confirmation_date` date DEFAULT NULL,
  `probation_end_date` date DEFAULT NULL,
  `last_working_day` date DEFAULT NULL,
  `contract_end_date` date DEFAULT NULL,
  `status` enum('Active','Inactive','On Leave','Suspended','Terminated','Resigned','Retired') NOT NULL DEFAULT 'Inactive',
  `portal_active` tinyint(1) NOT NULL DEFAULT 0,
  `portal_last_login` datetime DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_addresses`
--

CREATE TABLE `employee_addresses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `address_type` enum('present','permanent','mailing','emergency') NOT NULL,
  `house_no` varchar(100) DEFAULT NULL,
  `road_no` varchar(100) DEFAULT NULL,
  `road_name` varchar(200) DEFAULT NULL,
  `village` varchar(200) DEFAULT NULL,
  `area` varchar(200) DEFAULT NULL,
  `post_office` varchar(100) DEFAULT NULL,
  `postal_code` varchar(20) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `upazila` varchar(100) DEFAULT NULL,
  `district` varchar(100) DEFAULT NULL,
  `division` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `country` varchar(100) NOT NULL DEFAULT 'Bangladesh',
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_attendance_rules`
--

CREATE TABLE `employee_attendance_rules` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `enable_overtime` tinyint(1) NOT NULL DEFAULT 1,
  `overtime_rate_per_hour` decimal(10,2) NOT NULL DEFAULT 0.00,
  `overtime_multiplier` decimal(4,2) NOT NULL DEFAULT 1.50,
  `enable_late_deduction` tinyint(1) NOT NULL DEFAULT 1,
  `late_deduction_type` enum('none','per_minute','half_day','full_day') DEFAULT 'per_minute',
  `late_grace_minutes` int(11) NOT NULL DEFAULT 0,
  `late_deduction_per_minute` decimal(10,4) NOT NULL DEFAULT 0.0000,
  `late_deduction_fixed` decimal(10,2) NOT NULL DEFAULT 0.00,
  `enable_half_day_deduction` tinyint(1) NOT NULL DEFAULT 1,
  `half_day_deduction_percent` decimal(5,2) NOT NULL DEFAULT 50.00,
  `enable_absent_deduction` tinyint(1) NOT NULL DEFAULT 1,
  `absent_deduction_days` decimal(5,2) NOT NULL DEFAULT 1.00,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_awards`
--

CREATE TABLE `employee_awards` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `award_name` varchar(200) NOT NULL,
  `award_date` date DEFAULT NULL,
  `awarded_by` varchar(200) DEFAULT NULL,
  `organization` varchar(200) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `certificate_path` varchar(500) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_banking`
--

CREATE TABLE `employee_banking` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `bank_name` varchar(200) DEFAULT NULL,
  `bank_branch` varchar(200) DEFAULT NULL,
  `bank_account` varchar(80) DEFAULT NULL,
  `bank_routing` varchar(50) DEFAULT NULL,
  `iban` varchar(50) DEFAULT NULL,
  `swift_code` varchar(20) DEFAULT NULL,
  `mfs_type` enum('bKash','Nagad','Rocket','Upay','Others') DEFAULT NULL,
  `mfs_number` varchar(20) DEFAULT NULL,
  `payment_method` enum('Bank','Cash','MFS','Cheque') NOT NULL DEFAULT 'Bank',
  `is_primary` tinyint(1) NOT NULL DEFAULT 1,
  `verification_status` enum('Pending','Verified','Rejected') NOT NULL DEFAULT 'Pending',
  `verified_at` datetime DEFAULT NULL,
  `verified_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_dependents`
--

CREATE TABLE `employee_dependents` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `full_name` varchar(200) NOT NULL,
  `relation` varchar(100) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `nid_number` varchar(50) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(200) DEFAULT NULL,
  `occupation` varchar(200) DEFAULT NULL,
  `is_nominee` tinyint(1) NOT NULL DEFAULT 0,
  `nominee_percent` decimal(5,2) DEFAULT NULL,
  `priority_order` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_documents`
--

CREATE TABLE `employee_documents` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `category_id` bigint(20) UNSIGNED DEFAULT NULL,
  `document_name` varchar(300) DEFAULT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_hash` varchar(64) DEFAULT NULL COMMENT 'SHA-256 for deduplication',
  `file_size` int(10) UNSIGNED DEFAULT NULL,
  `mime_type` varchar(100) DEFAULT NULL,
  `issue_date` date DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `document_number` varchar(100) DEFAULT NULL,
  `issuing_authority` varchar(300) DEFAULT NULL,
  `is_verified` tinyint(1) NOT NULL DEFAULT 0,
  `verified_by` bigint(20) UNSIGNED DEFAULT NULL,
  `verified_at` datetime DEFAULT NULL,
  `verification_notes` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `uploaded_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_education`
--

CREATE TABLE `employee_education` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `degree` varchar(200) NOT NULL,
  `major_subject` varchar(200) DEFAULT NULL,
  `institution` varchar(300) DEFAULT NULL,
  `board_university` varchar(300) DEFAULT NULL,
  `passing_year` year(4) DEFAULT NULL,
  `result_type` enum('CGPA','Percentage','Grade','Division') DEFAULT NULL,
  `result_value` varchar(50) DEFAULT NULL,
  `duration_from` date DEFAULT NULL,
  `duration_to` date DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `certificate_path` varchar(500) DEFAULT NULL,
  `is_highest` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_experience`
--

CREATE TABLE `employee_experience` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `company_name` varchar(300) NOT NULL,
  `designation` varchar(200) DEFAULT NULL,
  `department` varchar(200) DEFAULT NULL,
  `from_date` date DEFAULT NULL,
  `to_date` date DEFAULT NULL,
  `is_current` tinyint(1) NOT NULL DEFAULT 0,
  `responsibilities` text DEFAULT NULL,
  `achievements` text DEFAULT NULL,
  `reason_for_leaving` varchar(300) DEFAULT NULL,
  `salary_scale` varchar(100) DEFAULT NULL,
  `reference_name` varchar(200) DEFAULT NULL,
  `reference_phone` varchar(20) DEFAULT NULL,
  `reference_email` varchar(200) DEFAULT NULL,
  `certificate_path` varchar(500) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_job_history`
--

CREATE TABLE `employee_job_history` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `effective_date` date NOT NULL,
  `change_type` enum('Joining','Promotion','Demotion','Transfer','Designation Change','Grade Change','Salary Revision','Confirmation','Termination','Resignation','Retirement','Rehired') NOT NULL,
  `from_branch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `to_branch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `from_dept_id` bigint(20) UNSIGNED DEFAULT NULL,
  `to_dept_id` bigint(20) UNSIGNED DEFAULT NULL,
  `from_desig_id` bigint(20) UNSIGNED DEFAULT NULL,
  `to_desig_id` bigint(20) UNSIGNED DEFAULT NULL,
  `from_grade_id` bigint(20) UNSIGNED DEFAULT NULL,
  `to_grade_id` bigint(20) UNSIGNED DEFAULT NULL,
  `from_salary` decimal(14,2) DEFAULT NULL,
  `to_salary` decimal(14,2) DEFAULT NULL,
  `reason` text DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `document_ref` varchar(500) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_languages`
--

CREATE TABLE `employee_languages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `language_name` varchar(50) NOT NULL,
  `proficiency` enum('Basic','Conversational','Professional','Native') NOT NULL DEFAULT 'Basic',
  `can_read` tinyint(1) NOT NULL DEFAULT 0,
  `can_write` tinyint(1) NOT NULL DEFAULT 0,
  `can_speak` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_leave_balance`
--

CREATE TABLE `employee_leave_balance` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `leave_type_id` bigint(20) UNSIGNED NOT NULL,
  `fiscal_year_id` bigint(20) UNSIGNED NOT NULL,
  `opening_balance` decimal(5,1) DEFAULT 0.0,
  `earned_days` decimal(5,1) DEFAULT 0.0,
  `used_days` decimal(5,1) DEFAULT 0.0,
  `encashed_days` decimal(5,1) DEFAULT 0.0,
  `lapsed_days` decimal(5,1) DEFAULT 0.0,
  `pending_days` decimal(5,1) DEFAULT 0.0,
  `remaining_days` decimal(5,1) GENERATED ALWAYS AS (`opening_balance` + `earned_days` - `used_days` - `encashed_days` - `lapsed_days` - `pending_days`) STORED,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Leave balance per employee';

-- --------------------------------------------------------

--
-- Table structure for table `employee_personal_info`
--

CREATE TABLE `employee_personal_info` (
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `first_name` varchar(150) DEFAULT NULL,
  `last_name` varchar(150) DEFAULT NULL,
  `full_name` varchar(300) DEFAULT NULL,
  `display_name` varchar(200) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `phone_2` varchar(20) DEFAULT NULL,
  `email` varchar(200) DEFAULT NULL,
  `profile_photo` varchar(500) DEFAULT NULL,
  `signature_file` varchar(500) DEFAULT NULL,
  `gender` enum('Male','Female','Other','Prefer not to say') DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `nationality` varchar(100) DEFAULT 'Bangladeshi',
  `personal_email` varchar(200) DEFAULT NULL,
  `personal_mobile` varchar(20) DEFAULT NULL,
  `place_of_birth` varchar(200) DEFAULT NULL,
  `blood_group` enum('A+','A-','B+','B-','AB+','AB-','O+','O-') DEFAULT NULL,
  `religion` varchar(80) DEFAULT NULL,
  `marital_status` enum('Single','Married','Divorced','Widowed','Separated') DEFAULT NULL,
  `spouse_name` varchar(200) DEFAULT NULL,
  `father_name` varchar(200) DEFAULT NULL,
  `mother_name` varchar(200) DEFAULT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_rosters`
--

CREATE TABLE `employee_rosters` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `shift_id` bigint(20) UNSIGNED DEFAULT NULL,
  `roster_date` date NOT NULL,
  `is_day_off` tinyint(1) NOT NULL DEFAULT 0,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_salary_structure`
--

CREATE TABLE `employee_salary_structure` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `component_id` bigint(20) UNSIGNED NOT NULL,
  `amount` decimal(14,4) NOT NULL DEFAULT 0.0000,
  `effective_from` date NOT NULL,
  `effective_to` date DEFAULT NULL,
  `is_percentage` tinyint(1) NOT NULL DEFAULT 0,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_skills`
--

CREATE TABLE `employee_skills` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `category_id` bigint(20) UNSIGNED DEFAULT NULL,
  `skill_name` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `proficiency` enum('Beginner','Intermediate','Advanced','Expert','Master') NOT NULL DEFAULT 'Intermediate',
  `years_of_experience` decimal(3,1) DEFAULT NULL,
  `last_used_date` date DEFAULT NULL,
  `certification` varchar(300) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_weekends`
--

CREATE TABLE `employee_weekends` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `weekend_days` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`weekend_days`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fiscal_years`
--

CREATE TABLE `fiscal_years` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `label` varchar(20) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `is_current` tinyint(1) DEFAULT 0,
  `locked` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Financial year definitions';

-- --------------------------------------------------------

--
-- Table structure for table `holidays`
--

CREATE TABLE `holidays` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(300) NOT NULL,
  `holiday_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `total_days` int(11) GENERATED ALWAYS AS (to_days(coalesce(`end_date`,`holiday_date`)) - to_days(`holiday_date`) + 1) STORED,
  `holiday_type` enum('Public','Government','Company','Optional','Religious','Festival') NOT NULL DEFAULT 'Public',
  `applicable_to` enum('All','Specific','Branch','Department') NOT NULL DEFAULT 'All',
  `is_recurring` tinyint(1) NOT NULL DEFAULT 0,
  `yearly_recurring` tinyint(1) NOT NULL DEFAULT 0,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `holiday_assignments`
--

CREATE TABLE `holiday_assignments` (
  `id` int(10) UNSIGNED NOT NULL,
  `holiday_id` int(10) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `department_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kpi_categories`
--

CREATE TABLE `kpi_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `name_bn` varchar(255) DEFAULT NULL,
  `weight_percentage` decimal(5,2) NOT NULL,
  `calculation_type` enum('Daily Auto','Per Task','Monthly Optional') NOT NULL,
  `point_setting` enum('System Defined','Manager Assign','Manager Input') NOT NULL,
  `sort_order` int(11) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kpi_daily_tracking`
--

CREATE TABLE `kpi_daily_tracking` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `tracking_date` date NOT NULL,
  `is_working_day` tinyint(1) NOT NULL DEFAULT 0,
  `is_present` tinyint(1) NOT NULL DEFAULT 0,
  `is_late` tinyint(1) NOT NULL DEFAULT 0,
  `present_target` decimal(5,1) NOT NULL DEFAULT 0.0,
  `present_obtained` decimal(5,1) NOT NULL DEFAULT 0.0,
  `late_target` decimal(5,1) NOT NULL DEFAULT 0.0,
  `late_obtained` decimal(5,1) NOT NULL DEFAULT 0.0,
  `daily_target` decimal(8,2) NOT NULL DEFAULT 0.00,
  `daily_obtained` decimal(8,2) NOT NULL DEFAULT 0.00,
  `daily_percentage` decimal(5,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kpi_indicators`
--

CREATE TABLE `kpi_indicators` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `key` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `name_bn` varchar(255) DEFAULT NULL,
  `weight_percentage` decimal(5,2) NOT NULL,
  `point_per_unit` decimal(8,2) DEFAULT NULL,
  `default_max_score` decimal(8,2) DEFAULT NULL,
  `count_behavior` enum('Always Count','Optional Count') NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kpi_monthly_reviews`
--

CREATE TABLE `kpi_monthly_reviews` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `reviewer_id` bigint(20) UNSIGNED NOT NULL,
  `year` int(11) NOT NULL,
  `month` int(11) NOT NULL,
  `give_behavior` tinyint(1) NOT NULL DEFAULT 0,
  `behavior_score` decimal(4,1) DEFAULT NULL,
  `behavior_remarks` text DEFAULT NULL,
  `give_bonus` tinyint(1) NOT NULL DEFAULT 0,
  `bonus_score` decimal(4,1) DEFAULT NULL,
  `bonus_remarks` text DEFAULT NULL,
  `give_penalty` tinyint(1) NOT NULL DEFAULT 0,
  `penalty_score` decimal(4,1) DEFAULT NULL,
  `penalty_remarks` text DEFAULT NULL,
  `status` enum('Draft','Submitted','Approved') NOT NULL DEFAULT 'Draft',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kpi_monthly_scores`
--

CREATE TABLE `kpi_monthly_scores` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `year` int(11) NOT NULL,
  `month` int(11) NOT NULL,
  `working_days` int(11) NOT NULL DEFAULT 0,
  `present_days` int(11) NOT NULL DEFAULT 0,
  `late_days` int(11) NOT NULL DEFAULT 0,
  `attendance_target` decimal(8,2) NOT NULL DEFAULT 0.00,
  `attendance_obtained` decimal(8,2) NOT NULL DEFAULT 0.00,
  `attendance_percentage` decimal(5,2) NOT NULL DEFAULT 0.00,
  `total_assigned_tasks` int(11) NOT NULL DEFAULT 0,
  `completed_tasks` int(11) NOT NULL DEFAULT 0,
  `task_target` decimal(8,2) NOT NULL DEFAULT 0.00,
  `task_obtained` decimal(8,2) NOT NULL DEFAULT 0.00,
  `task_percentage` decimal(5,2) NOT NULL DEFAULT 0.00,
  `behavior_given` tinyint(1) NOT NULL DEFAULT 0,
  `behavior_target` decimal(8,2) NOT NULL DEFAULT 0.00,
  `behavior_obtained` decimal(8,2) NOT NULL DEFAULT 0.00,
  `behavior_percentage` decimal(5,2) NOT NULL DEFAULT 0.00,
  `bonus_given` tinyint(1) NOT NULL DEFAULT 0,
  `bonus_target` decimal(8,2) NOT NULL DEFAULT 0.00,
  `bonus_obtained` decimal(8,2) NOT NULL DEFAULT 0.00,
  `bonus_percentage` decimal(5,2) NOT NULL DEFAULT 0.00,
  `penalty_given` tinyint(1) NOT NULL DEFAULT 0,
  `penalty_target` decimal(8,2) NOT NULL DEFAULT 0.00,
  `penalty_obtained` decimal(8,2) NOT NULL DEFAULT 0.00,
  `penalty_percentage` decimal(5,2) NOT NULL DEFAULT 0.00,
  `total_target` decimal(8,2) NOT NULL DEFAULT 0.00,
  `total_obtained` decimal(8,2) NOT NULL DEFAULT 0.00,
  `overall_percentage` decimal(5,2) NOT NULL DEFAULT 0.00,
  `rating` enum('A+','A','B+','B','C','D') DEFAULT NULL,
  `status` enum('Open','Closed') NOT NULL DEFAULT 'Open',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kpi_tasks`
--

CREATE TABLE `kpi_tasks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `assigned_by` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `target_score` decimal(8,2) NOT NULL,
  `obtained_score` decimal(8,2) DEFAULT NULL,
  `priority` enum('Low','Medium','High','Critical') NOT NULL DEFAULT 'Medium',
  `assigned_date` date NOT NULL,
  `deadline` date DEFAULT NULL,
  `status` enum('Pending','In Progress','Completed','Cancelled','Overdue') NOT NULL DEFAULT 'Pending',
  `completed_at` datetime DEFAULT NULL,
  `completion_note` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `leave_applications`
--

CREATE TABLE `leave_applications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `leave_type_id` bigint(20) UNSIGNED NOT NULL,
  `application_no` varchar(30) DEFAULT NULL,
  `from_date` date NOT NULL,
  `to_date` date NOT NULL,
  `total_days` decimal(5,1) NOT NULL,
  `is_half_day` tinyint(1) DEFAULT 0,
  `half_day_period` enum('First Half','Second Half') DEFAULT NULL,
  `reason` text DEFAULT NULL,
  `professional_email` text DEFAULT NULL,
  `document_path` varchar(500) DEFAULT NULL,
  `substitute_employee_id` bigint(20) UNSIGNED DEFAULT NULL,
  `contact_during_leave` varchar(50) DEFAULT NULL,
  `status` enum('Draft','Pending','Approved','Rejected','Cancelled','Withdrawn') DEFAULT 'Pending',
  `rejection_reason` text DEFAULT NULL,
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `applied_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Leave request submissions';

-- --------------------------------------------------------

--
-- Table structure for table `leave_encashment`
--

CREATE TABLE `leave_encashment` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `leave_type_id` bigint(20) UNSIGNED NOT NULL,
  `encashment_date` date NOT NULL,
  `days_encashed` decimal(5,1) NOT NULL,
  `amount_per_day` decimal(14,2) DEFAULT NULL,
  `total_amount` decimal(14,2) DEFAULT NULL,
  `payroll_run_id` bigint(20) UNSIGNED DEFAULT NULL,
  `reason` text DEFAULT NULL,
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `status` enum('Pending','Approved','Paid') DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Leave encashment requests';

-- --------------------------------------------------------

--
-- Table structure for table `leave_types`
--

CREATE TABLE `leave_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `days_per_year` decimal(5,1) NOT NULL DEFAULT 0.0,
  `is_paid` tinyint(1) NOT NULL DEFAULT 1,
  `is_half_day_allowed` tinyint(1) NOT NULL DEFAULT 1,
  `carry_forward` tinyint(1) NOT NULL DEFAULT 0,
  `max_carry_days` decimal(5,1) NOT NULL DEFAULT 0.0,
  `max_consecutive_days` int(11) NOT NULL DEFAULT 0,
  `requires_document` tinyint(1) NOT NULL DEFAULT 0,
  `min_days_notice` int(11) NOT NULL DEFAULT 0,
  `applicable_gender` enum('All','Male','Female') NOT NULL DEFAULT 'All',
  `color_code` varchar(10) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `affects_balance` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `loans`
--

CREATE TABLE `loans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `loan_number` varchar(20) NOT NULL COMMENT 'Professional tracking ID: LN-YYYY-XXXX',
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `loan_type` enum('Personal','Emergency','Education','Medical','Vehicle','Home','Other') NOT NULL DEFAULT 'Personal',
  `loan_amount` decimal(14,2) NOT NULL DEFAULT 0.00,
  `interest_rate` decimal(5,2) NOT NULL DEFAULT 0.00,
  `total_interest` decimal(14,2) NOT NULL DEFAULT 0.00,
  `total_payable` decimal(14,2) NOT NULL DEFAULT 0.00,
  `installment_amount` decimal(14,2) NOT NULL DEFAULT 0.00,
  `total_installments` int(11) NOT NULL DEFAULT 1,
  `paid_installments` int(11) NOT NULL DEFAULT 0,
  `remaining_amount` decimal(14,2) NOT NULL DEFAULT 0.00,
  `purpose` text DEFAULT NULL,
  `application_date` date NOT NULL,
  `approval_date` date DEFAULT NULL,
  `first_installment_date` date DEFAULT NULL,
  `disbursement_date` date DEFAULT NULL,
  `status` enum('Pending','Approved','Rejected','Disbursed','Completed','Cancelled') NOT NULL DEFAULT 'Pending',
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `loan_installments`
--

CREATE TABLE `loan_installments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `loan_id` bigint(20) UNSIGNED NOT NULL,
  `installment_no` int(11) NOT NULL,
  `due_date` date NOT NULL,
  `amount` decimal(14,2) NOT NULL DEFAULT 0.00,
  `paid_amount` decimal(14,2) NOT NULL DEFAULT 0.00,
  `status` enum('Pending','Progress','Paid','Partial','Overdue','Waived') DEFAULT 'Pending',
  `payroll_run_id` bigint(20) UNSIGNED DEFAULT NULL,
  `paid_at` datetime DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notices`
--

CREATE TABLE `notices` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `notice_no` varchar(50) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `description` longtext NOT NULL,
  `notice_type` enum('General','HR','Holiday','Attendance','Payroll','Policy','Training','Event','Emergency') NOT NULL DEFAULT 'General',
  `priority` enum('Low','Medium','High','Urgent') NOT NULL DEFAULT 'Medium',
  `publish_date` datetime NOT NULL,
  `expiry_date` datetime DEFAULT NULL,
  `target_type` enum('All','Department','Designation','Branch','Employee') NOT NULL DEFAULT 'All',
  `attachment_path` varchar(500) DEFAULT NULL,
  `is_popup` tinyint(1) NOT NULL DEFAULT 0,
  `is_pinned` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notice_acknowledgements`
--

CREATE TABLE `notice_acknowledgements` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `notice_id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `comment` text DEFAULT NULL,
  `acknowledged_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notice_views`
--

CREATE TABLE `notice_views` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `notice_id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` char(36) NOT NULL,
  `type` varchar(255) NOT NULL,
  `notifiable_type` varchar(255) NOT NULL,
  `notifiable_id` bigint(20) UNSIGNED NOT NULL,
  `data` text NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payroll_runs`
--

CREATE TABLE `payroll_runs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `fiscal_year_id` bigint(20) UNSIGNED NOT NULL,
  `run_month` date NOT NULL COMMENT 'First day of month',
  `run_label` varchar(100) DEFAULT NULL,
  `run_type` enum('Regular','Bonus','Advance','Adjustment') NOT NULL DEFAULT 'Regular',
  `total_employees` int(11) NOT NULL DEFAULT 0,
  `total_gross` decimal(16,2) NOT NULL DEFAULT 0.00,
  `total_net` decimal(16,2) NOT NULL DEFAULT 0.00,
  `total_deductions` decimal(16,2) NOT NULL DEFAULT 0.00,
  `status` enum('Draft','Processing','Calculated','Reviewed','Approved','Disbursed','Locked','Cancelled') NOT NULL DEFAULT 'Draft',
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `disbursed_by` bigint(20) UNSIGNED DEFAULT NULL,
  `disbursed_at` datetime DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payroll_run_details`
--

CREATE TABLE `payroll_run_details` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `payroll_run_id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `employee_name` varchar(255) DEFAULT NULL COMMENT 'Snapshot of employee name at lock time',
  `employee_code` varchar(100) DEFAULT NULL COMMENT 'Snapshot of employee code at lock time',
  `basic_salary` decimal(14,4) NOT NULL DEFAULT 0.0000 COMMENT 'Basic salary used for this run',
  `gross` decimal(14,4) NOT NULL DEFAULT 0.0000 COMMENT 'Total earnings before deductions',
  `deductions` decimal(14,4) NOT NULL DEFAULT 0.0000 COMMENT 'Total deductions',
  `net` decimal(14,4) NOT NULL DEFAULT 0.0000 COMMENT 'Net pay = gross - deductions',
  `component_details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT 'Snapshot of all earning & deduction components with calculation breakdown' CHECK (json_valid(`component_details`)),
  `attendance_summary` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Attendance adjustment data (present, late, overtime, half-day, absent)' CHECK (json_valid(`attendance_summary`)),
  `payment_status` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0 = Unpaid, 1 = Paid',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `group` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `is_system` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `role_permission`
--

CREATE TABLE `role_permission` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `permission_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `salary_components`
--

CREATE TABLE `salary_components` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(200) NOT NULL,
  `type` enum('Earning','Deduction','Reimbursement','Bonus') NOT NULL,
  `category` enum('Basic','Allowance','Bonus','PF','Tax','Insurance','Loan','Other') NOT NULL DEFAULT 'Other',
  `calculation_type` enum('Fixed','Percentage of Basic','Percentage of Gross','Formula','Custom') NOT NULL DEFAULT 'Fixed',
  `default_value` decimal(14,4) NOT NULL DEFAULT 0.0000,
  `formula_expression` text DEFAULT NULL COMMENT 'For dynamic calculation',
  `is_taxable` tinyint(1) NOT NULL DEFAULT 0,
  `is_pf_basis` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `show_in_slip` tinyint(1) NOT NULL DEFAULT 1,
  `display_order` int(11) NOT NULL DEFAULT 0,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `salary_grades`
--

CREATE TABLE `salary_grades` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `min_salary` decimal(14,2) NOT NULL DEFAULT 0.00,
  `max_salary` decimal(14,2) NOT NULL DEFAULT 0.00,
  `currency` char(3) NOT NULL DEFAULT 'BDT',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `deduct_late_for_payroll` tinyint(1) NOT NULL DEFAULT 1,
  `pay_overtime_for_payroll` tinyint(1) NOT NULL DEFAULT 1,
  `late_deduction_per_minute` decimal(10,4) NOT NULL DEFAULT 0.0000,
  `half_day_deduction_percent` decimal(5,2) NOT NULL DEFAULT 50.00,
  `absent_deduction_days` decimal(5,2) NOT NULL DEFAULT 1.00,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `shifts`
--

CREATE TABLE `shifts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `break_minutes` int(11) NOT NULL DEFAULT 0,
  `grace_in_min` int(11) NOT NULL DEFAULT 0,
  `grace_out_min` int(11) NOT NULL DEFAULT 0,
  `work_hours` double DEFAULT NULL,
  `is_night_shift` tinyint(1) NOT NULL DEFAULT 0,
  `is_flexible` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `skill_categories`
--

CREATE TABLE `skill_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `role_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `attendance_employee_id_attendance_date_unique` (`employee_id`,`attendance_date`),
  ADD KEY `attendance_shift_id_foreign` (`shift_id`),
  ADD KEY `attendance_employee_id_index` (`employee_id`),
  ADD KEY `attendance_attendance_date_index` (`attendance_date`),
  ADD KEY `attendance_attendance_status_index` (`attendance_status`),
  ADD KEY `attendance_approval_status_index` (`approval_status`);

--
-- Indexes for table `attendance_devices`
--
ALTER TABLE `attendance_devices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `attendance_devices_device_code_unique` (`device_code`),
  ADD UNIQUE KEY `attendance_devices_serial_number_unique` (`serial_number`),
  ADD KEY `attendance_devices_branch_id_index` (`branch_id`),
  ADD KEY `attendance_devices_device_type_index` (`device_type`),
  ADD KEY `attendance_devices_sync_status_index` (`sync_status`);

--
-- Indexes for table `attendance_logs`
--
ALTER TABLE `attendance_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `attendance_logs_employee_id_punch_datetime_index` (`employee_id`,`punch_datetime`),
  ADD KEY `attendance_logs_device_id_punch_datetime_index` (`device_id`,`punch_datetime`),
  ADD KEY `attendance_logs_is_processed_index` (`is_processed`);

--
-- Indexes for table `branches`
--
ALTER TABLE `branches`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `branches_company_id_code_unique` (`company_id`,`code`),
  ADD KEY `branches_company_id_index` (`company_id`),
  ADD KEY `branches_city_index` (`city`),
  ADD KEY `branches_is_active_index` (`is_active`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `companies`
--
ALTER TABLE `companies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `companies_is_active_index` (`is_active`),
  ADD KEY `companies_city_index` (`city`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `departments_branch_id_code_unique` (`branch_id`,`code`),
  ADD KEY `departments_parent_id_foreign` (`parent_id`),
  ADD KEY `departments_branch_id_index` (`branch_id`),
  ADD KEY `departments_is_active_index` (`is_active`);

--
-- Indexes for table `designations`
--
ALTER TABLE `designations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `designations_grade_id_foreign` (`grade_id`),
  ADD KEY `designations_department_id_index` (`department_id`),
  ADD KEY `designations_title_index` (`title`);

--
-- Indexes for table `document_categories`
--
ALTER TABLE `document_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `document_categories_code_unique` (`code`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `employees_employee_code_unique` (`employee_code`),
  ADD KEY `employees_branch_id_foreign` (`branch_id`),
  ADD KEY `employees_designation_id_foreign` (`designation_id`),
  ADD KEY `employees_grade_id_foreign` (`grade_id`),
  ADD KEY `employees_shift_id_foreign` (`shift_id`),
  ADD KEY `employees_reports_to_foreign` (`reports_to`),
  ADD KEY `employees_status_index` (`status`),
  ADD KEY `employees_department_id_status_index` (`department_id`,`status`),
  ADD KEY `employees_joining_date_index` (`joining_date`);

--
-- Indexes for table `employee_addresses`
--
ALTER TABLE `employee_addresses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_addresses_employee_id_index` (`employee_id`),
  ADD KEY `employee_addresses_address_type_index` (`address_type`);

--
-- Indexes for table `employee_attendance_rules`
--
ALTER TABLE `employee_attendance_rules`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `employee_attendance_rules_employee_id_unique` (`employee_id`);

--
-- Indexes for table `employee_awards`
--
ALTER TABLE `employee_awards`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_awards_employee_id_index` (`employee_id`);

--
-- Indexes for table `employee_banking`
--
ALTER TABLE `employee_banking`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `employee_banking_employee_id_is_primary_unique` (`employee_id`,`is_primary`),
  ADD KEY `employee_banking_verified_by_foreign` (`verified_by`),
  ADD KEY `employee_banking_bank_account_index` (`bank_account`);

--
-- Indexes for table `employee_dependents`
--
ALTER TABLE `employee_dependents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_dependents_employee_id_index` (`employee_id`);

--
-- Indexes for table `employee_documents`
--
ALTER TABLE `employee_documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_documents_verified_by_foreign` (`verified_by`),
  ADD KEY `employee_documents_employee_id_index` (`employee_id`),
  ADD KEY `employee_documents_expiry_date_index` (`expiry_date`),
  ADD KEY `employee_documents_category_id_index` (`category_id`),
  ADD KEY `employee_documents_is_verified_index` (`is_verified`);

--
-- Indexes for table `employee_education`
--
ALTER TABLE `employee_education`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_education_employee_id_index` (`employee_id`);

--
-- Indexes for table `employee_experience`
--
ALTER TABLE `employee_experience`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_experience_employee_id_index` (`employee_id`),
  ADD KEY `employee_experience_from_date_to_date_index` (`from_date`,`to_date`);

--
-- Indexes for table `employee_job_history`
--
ALTER TABLE `employee_job_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_job_history_from_branch_id_foreign` (`from_branch_id`),
  ADD KEY `employee_job_history_to_branch_id_foreign` (`to_branch_id`),
  ADD KEY `employee_job_history_from_dept_id_foreign` (`from_dept_id`),
  ADD KEY `employee_job_history_to_dept_id_foreign` (`to_dept_id`),
  ADD KEY `employee_job_history_from_desig_id_foreign` (`from_desig_id`),
  ADD KEY `employee_job_history_to_desig_id_foreign` (`to_desig_id`),
  ADD KEY `employee_job_history_from_grade_id_foreign` (`from_grade_id`),
  ADD KEY `employee_job_history_to_grade_id_foreign` (`to_grade_id`),
  ADD KEY `employee_job_history_approved_by_foreign` (`approved_by`),
  ADD KEY `employee_job_history_employee_id_effective_date_index` (`employee_id`,`effective_date`),
  ADD KEY `employee_job_history_effective_date_index` (`effective_date`);

--
-- Indexes for table `employee_languages`
--
ALTER TABLE `employee_languages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `employee_languages_employee_id_language_name_unique` (`employee_id`,`language_name`);

--
-- Indexes for table `employee_leave_balance`
--
ALTER TABLE `employee_leave_balance`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_leave_balance` (`employee_id`,`leave_type_id`,`fiscal_year_id`),
  ADD KEY `fk_balance_leavetype` (`leave_type_id`),
  ADD KEY `idx_balance_employee` (`employee_id`),
  ADD KEY `idx_balance_fiscal` (`fiscal_year_id`);

--
-- Indexes for table `employee_personal_info`
--
ALTER TABLE `employee_personal_info`
  ADD PRIMARY KEY (`employee_id`);

--
-- Indexes for table `employee_rosters`
--
ALTER TABLE `employee_rosters`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_rosters_shift_id_foreign` (`shift_id`),
  ADD KEY `employee_rosters_created_by_foreign` (`created_by`),
  ADD KEY `employee_rosters_employee_id_index` (`employee_id`),
  ADD KEY `employee_rosters_roster_date_index` (`roster_date`);

--
-- Indexes for table `employee_salary_structure`
--
ALTER TABLE `employee_salary_structure`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_salary_structure_component_id_foreign` (`component_id`),
  ADD KEY `employee_salary_structure_created_by_foreign` (`created_by`),
  ADD KEY `idx_salary_employee` (`employee_id`),
  ADD KEY `idx_salary_effective` (`effective_from`,`effective_to`);

--
-- Indexes for table `employee_skills`
--
ALTER TABLE `employee_skills`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `employee_skills_employee_id_skill_name_unique` (`employee_id`,`skill_name`),
  ADD KEY `employee_skills_employee_id_index` (`employee_id`),
  ADD KEY `employee_skills_category_id_index` (`category_id`);

--
-- Indexes for table `employee_weekends`
--
ALTER TABLE `employee_weekends`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `employee_weekends_employee_id_unique` (`employee_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `fiscal_years`
--
ALTER TABLE `fiscal_years`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_fy_company_label` (`company_id`,`label`),
  ADD KEY `idx_fy_current` (`company_id`,`is_current`),
  ADD KEY `idx_fy_dates` (`start_date`,`end_date`);

--
-- Indexes for table `holidays`
--
ALTER TABLE `holidays`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_holiday_date` (`holiday_date`);

--
-- Indexes for table `holiday_assignments`
--
ALTER TABLE `holiday_assignments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `holiday_assignments_holiday_id_foreign` (`holiday_id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kpi_categories`
--
ALTER TABLE `kpi_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kpi_daily_tracking`
--
ALTER TABLE `kpi_daily_tracking`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kpi_daily_tracking_employee_id_tracking_date_unique` (`employee_id`,`tracking_date`),
  ADD KEY `kpi_daily_tracking_employee_id_tracking_date_index` (`employee_id`,`tracking_date`);

--
-- Indexes for table `kpi_indicators`
--
ALTER TABLE `kpi_indicators`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kpi_indicators_category_id_foreign` (`category_id`);

--
-- Indexes for table `kpi_monthly_reviews`
--
ALTER TABLE `kpi_monthly_reviews`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kpi_monthly_reviews_employee_id_year_month_unique` (`employee_id`,`year`,`month`),
  ADD KEY `kpi_monthly_reviews_reviewer_id_status_index` (`reviewer_id`,`status`);

--
-- Indexes for table `kpi_monthly_scores`
--
ALTER TABLE `kpi_monthly_scores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kpi_monthly_scores_employee_id_year_month_unique` (`employee_id`,`year`,`month`),
  ADD KEY `kpi_monthly_scores_year_month_index` (`year`,`month`);

--
-- Indexes for table `kpi_tasks`
--
ALTER TABLE `kpi_tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kpi_tasks_assigned_by_foreign` (`assigned_by`),
  ADD KEY `kpi_tasks_employee_id_status_index` (`employee_id`,`status`),
  ADD KEY `kpi_tasks_assigned_date_index` (`assigned_date`);

--
-- Indexes for table `leave_applications`
--
ALTER TABLE `leave_applications`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `application_no` (`application_no`),
  ADD KEY `fk_leaveapp_leavetype` (`leave_type_id`),
  ADD KEY `fk_leaveapp_approvedby` (`approved_by`),
  ADD KEY `fk_leaveapp_substitute` (`substitute_employee_id`),
  ADD KEY `idx_leave_employee` (`employee_id`),
  ADD KEY `idx_leave_dates` (`from_date`,`to_date`),
  ADD KEY `idx_leave_status` (`status`),
  ADD KEY `idx_leave_number` (`application_no`);

--
-- Indexes for table `leave_encashment`
--
ALTER TABLE `leave_encashment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_encash_leavetype` (`leave_type_id`),
  ADD KEY `fk_encash_approvedby` (`approved_by`),
  ADD KEY `idx_encashment_employee` (`employee_id`);

--
-- Indexes for table `leave_types`
--
ALTER TABLE `leave_types`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_leavetype_active` (`is_active`);

--
-- Indexes for table `loans`
--
ALTER TABLE `loans`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `loans_loan_number_unique` (`loan_number`),
  ADD KEY `loans_approved_by_foreign` (`approved_by`),
  ADD KEY `loans_created_by_foreign` (`created_by`),
  ADD KEY `idx_loans_employee` (`employee_id`),
  ADD KEY `idx_loans_status` (`status`),
  ADD KEY `idx_loans_type` (`loan_type`),
  ADD KEY `idx_loans_application_date` (`application_date`);

--
-- Indexes for table `loan_installments`
--
ALTER TABLE `loan_installments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_installment_no` (`loan_id`,`installment_no`),
  ADD KEY `loan_installments_payroll_run_id_foreign` (`payroll_run_id`),
  ADD KEY `idx_installments_loan` (`loan_id`),
  ADD KEY `idx_installments_status` (`status`),
  ADD KEY `idx_installments_due` (`due_date`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notices`
--
ALTER TABLE `notices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `notices_notice_no_unique` (`notice_no`),
  ADD KEY `notices_branch_id_foreign` (`branch_id`),
  ADD KEY `idx_notice_type` (`notice_type`),
  ADD KEY `idx_priority` (`priority`),
  ADD KEY `idx_publish_date` (`publish_date`),
  ADD KEY `idx_expiry_date` (`expiry_date`),
  ADD KEY `idx_active` (`is_active`);

--
-- Indexes for table `notice_acknowledgements`
--
ALTER TABLE `notice_acknowledgements`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `notice_acknowledgements_notice_id_employee_id_unique` (`notice_id`,`employee_id`);

--
-- Indexes for table `notice_views`
--
ALTER TABLE `notice_views`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `notice_views_notice_id_employee_id_unique` (`notice_id`,`employee_id`),
  ADD KEY `notice_views_employee_id_foreign` (`employee_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `payroll_runs`
--
ALTER TABLE `payroll_runs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_payroll_run` (`run_month`,`run_type`),
  ADD KEY `payroll_runs_fiscal_year_id_foreign` (`fiscal_year_id`),
  ADD KEY `payroll_runs_approved_by_foreign` (`approved_by`),
  ADD KEY `payroll_runs_disbursed_by_foreign` (`disbursed_by`),
  ADD KEY `payroll_runs_created_by_foreign` (`created_by`),
  ADD KEY `idx_payroll_status` (`status`),
  ADD KEY `idx_payroll_month` (`run_month`);

--
-- Indexes for table `payroll_run_details`
--
ALTER TABLE `payroll_run_details`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_payroll_run_employee` (`payroll_run_id`,`employee_id`),
  ADD KEY `payroll_run_details_created_by_foreign` (`created_by`),
  ADD KEY `idx_detail_payroll_run` (`payroll_run_id`),
  ADD KEY `idx_detail_employee` (`employee_id`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_slug_unique` (`slug`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_unique` (`name`),
  ADD UNIQUE KEY `roles_slug_unique` (`slug`);

--
-- Indexes for table `role_permission`
--
ALTER TABLE `role_permission`
  ADD PRIMARY KEY (`role_id`,`permission_id`),
  ADD KEY `role_permission_permission_id_foreign` (`permission_id`);

--
-- Indexes for table `salary_components`
--
ALTER TABLE `salary_components`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_component_type` (`type`);

--
-- Indexes for table `salary_grades`
--
ALTER TABLE `salary_grades`
  ADD PRIMARY KEY (`id`),
  ADD KEY `salary_grades_is_active_index` (`is_active`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `shifts`
--
ALTER TABLE `shifts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `shifts_is_active_index` (`is_active`);

--
-- Indexes for table `skill_categories`
--
ALTER TABLE `skill_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_employee_id_unique` (`employee_id`),
  ADD KEY `users_role_id_foreign` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `attendance_devices`
--
ALTER TABLE `attendance_devices`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `attendance_logs`
--
ALTER TABLE `attendance_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `branches`
--
ALTER TABLE `branches`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `companies`
--
ALTER TABLE `companies`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `designations`
--
ALTER TABLE `designations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `document_categories`
--
ALTER TABLE `document_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_addresses`
--
ALTER TABLE `employee_addresses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_attendance_rules`
--
ALTER TABLE `employee_attendance_rules`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_awards`
--
ALTER TABLE `employee_awards`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_banking`
--
ALTER TABLE `employee_banking`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_dependents`
--
ALTER TABLE `employee_dependents`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_documents`
--
ALTER TABLE `employee_documents`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_education`
--
ALTER TABLE `employee_education`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_experience`
--
ALTER TABLE `employee_experience`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_job_history`
--
ALTER TABLE `employee_job_history`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_languages`
--
ALTER TABLE `employee_languages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_leave_balance`
--
ALTER TABLE `employee_leave_balance`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_rosters`
--
ALTER TABLE `employee_rosters`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_salary_structure`
--
ALTER TABLE `employee_salary_structure`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_skills`
--
ALTER TABLE `employee_skills`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_weekends`
--
ALTER TABLE `employee_weekends`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fiscal_years`
--
ALTER TABLE `fiscal_years`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `holidays`
--
ALTER TABLE `holidays`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `holiday_assignments`
--
ALTER TABLE `holiday_assignments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kpi_categories`
--
ALTER TABLE `kpi_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kpi_daily_tracking`
--
ALTER TABLE `kpi_daily_tracking`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kpi_indicators`
--
ALTER TABLE `kpi_indicators`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kpi_monthly_reviews`
--
ALTER TABLE `kpi_monthly_reviews`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kpi_monthly_scores`
--
ALTER TABLE `kpi_monthly_scores`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kpi_tasks`
--
ALTER TABLE `kpi_tasks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `leave_applications`
--
ALTER TABLE `leave_applications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `leave_encashment`
--
ALTER TABLE `leave_encashment`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `leave_types`
--
ALTER TABLE `leave_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `loans`
--
ALTER TABLE `loans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `loan_installments`
--
ALTER TABLE `loan_installments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notices`
--
ALTER TABLE `notices`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notice_acknowledgements`
--
ALTER TABLE `notice_acknowledgements`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notice_views`
--
ALTER TABLE `notice_views`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payroll_runs`
--
ALTER TABLE `payroll_runs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payroll_run_details`
--
ALTER TABLE `payroll_run_details`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `salary_components`
--
ALTER TABLE `salary_components`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `salary_grades`
--
ALTER TABLE `salary_grades`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `shifts`
--
ALTER TABLE `shifts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `skill_categories`
--
ALTER TABLE `skill_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`),
  ADD CONSTRAINT `attendance_shift_id_foreign` FOREIGN KEY (`shift_id`) REFERENCES `shifts` (`id`);

--
-- Constraints for table `attendance_devices`
--
ALTER TABLE `attendance_devices`
  ADD CONSTRAINT `attendance_devices_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`);

--
-- Constraints for table `attendance_logs`
--
ALTER TABLE `attendance_logs`
  ADD CONSTRAINT `attendance_logs_device_id_foreign` FOREIGN KEY (`device_id`) REFERENCES `attendance_devices` (`id`),
  ADD CONSTRAINT `attendance_logs_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`);

--
-- Constraints for table `branches`
--
ALTER TABLE `branches`
  ADD CONSTRAINT `branches_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`);

--
-- Constraints for table `departments`
--
ALTER TABLE `departments`
  ADD CONSTRAINT `departments_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`),
  ADD CONSTRAINT `departments_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `designations`
--
ALTER TABLE `designations`
  ADD CONSTRAINT `designations_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `designations_grade_id_foreign` FOREIGN KEY (`grade_id`) REFERENCES `salary_grades` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `employees`
--
ALTER TABLE `employees`
  ADD CONSTRAINT `employees_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`),
  ADD CONSTRAINT `employees_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`),
  ADD CONSTRAINT `employees_designation_id_foreign` FOREIGN KEY (`designation_id`) REFERENCES `designations` (`id`),
  ADD CONSTRAINT `employees_grade_id_foreign` FOREIGN KEY (`grade_id`) REFERENCES `salary_grades` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `employees_reports_to_foreign` FOREIGN KEY (`reports_to`) REFERENCES `employees` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `employees_shift_id_foreign` FOREIGN KEY (`shift_id`) REFERENCES `shifts` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `employee_addresses`
--
ALTER TABLE `employee_addresses`
  ADD CONSTRAINT `employee_addresses_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `employee_attendance_rules`
--
ALTER TABLE `employee_attendance_rules`
  ADD CONSTRAINT `employee_attendance_rules_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `employee_awards`
--
ALTER TABLE `employee_awards`
  ADD CONSTRAINT `employee_awards_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `employee_banking`
--
ALTER TABLE `employee_banking`
  ADD CONSTRAINT `employee_banking_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `employee_banking_verified_by_foreign` FOREIGN KEY (`verified_by`) REFERENCES `employees` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `employee_dependents`
--
ALTER TABLE `employee_dependents`
  ADD CONSTRAINT `employee_dependents_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `employee_documents`
--
ALTER TABLE `employee_documents`
  ADD CONSTRAINT `employee_documents_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `document_categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `employee_documents_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `employee_documents_verified_by_foreign` FOREIGN KEY (`verified_by`) REFERENCES `employees` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `employee_education`
--
ALTER TABLE `employee_education`
  ADD CONSTRAINT `employee_education_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `employee_experience`
--
ALTER TABLE `employee_experience`
  ADD CONSTRAINT `employee_experience_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `employee_job_history`
--
ALTER TABLE `employee_job_history`
  ADD CONSTRAINT `employee_job_history_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `employees` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `employee_job_history_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `employee_job_history_from_branch_id_foreign` FOREIGN KEY (`from_branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `employee_job_history_from_dept_id_foreign` FOREIGN KEY (`from_dept_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `employee_job_history_from_desig_id_foreign` FOREIGN KEY (`from_desig_id`) REFERENCES `designations` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `employee_job_history_from_grade_id_foreign` FOREIGN KEY (`from_grade_id`) REFERENCES `salary_grades` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `employee_job_history_to_branch_id_foreign` FOREIGN KEY (`to_branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `employee_job_history_to_dept_id_foreign` FOREIGN KEY (`to_dept_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `employee_job_history_to_desig_id_foreign` FOREIGN KEY (`to_desig_id`) REFERENCES `designations` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `employee_job_history_to_grade_id_foreign` FOREIGN KEY (`to_grade_id`) REFERENCES `salary_grades` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `employee_languages`
--
ALTER TABLE `employee_languages`
  ADD CONSTRAINT `employee_languages_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `employee_leave_balance`
--
ALTER TABLE `employee_leave_balance`
  ADD CONSTRAINT `fk_balance_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_balance_leavetype` FOREIGN KEY (`leave_type_id`) REFERENCES `leave_types` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `employee_personal_info`
--
ALTER TABLE `employee_personal_info`
  ADD CONSTRAINT `employee_personal_info_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `employee_rosters`
--
ALTER TABLE `employee_rosters`
  ADD CONSTRAINT `employee_rosters_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `employees` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `employee_rosters_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `employee_rosters_shift_id_foreign` FOREIGN KEY (`shift_id`) REFERENCES `shifts` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `employee_salary_structure`
--
ALTER TABLE `employee_salary_structure`
  ADD CONSTRAINT `employee_salary_structure_component_id_foreign` FOREIGN KEY (`component_id`) REFERENCES `salary_components` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `employee_salary_structure_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `employee_salary_structure_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `employee_skills`
--
ALTER TABLE `employee_skills`
  ADD CONSTRAINT `employee_skills_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `skill_categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `employee_skills_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `employee_weekends`
--
ALTER TABLE `employee_weekends`
  ADD CONSTRAINT `employee_weekends_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `fiscal_years`
--
ALTER TABLE `fiscal_years`
  ADD CONSTRAINT `fk_fy_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`);

--
-- Constraints for table `holiday_assignments`
--
ALTER TABLE `holiday_assignments`
  ADD CONSTRAINT `holiday_assignments_holiday_id_foreign` FOREIGN KEY (`holiday_id`) REFERENCES `holidays` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `kpi_daily_tracking`
--
ALTER TABLE `kpi_daily_tracking`
  ADD CONSTRAINT `kpi_daily_tracking_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `kpi_indicators`
--
ALTER TABLE `kpi_indicators`
  ADD CONSTRAINT `kpi_indicators_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `kpi_categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `kpi_monthly_reviews`
--
ALTER TABLE `kpi_monthly_reviews`
  ADD CONSTRAINT `kpi_monthly_reviews_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `kpi_monthly_reviews_reviewer_id_foreign` FOREIGN KEY (`reviewer_id`) REFERENCES `employees` (`id`);

--
-- Constraints for table `kpi_monthly_scores`
--
ALTER TABLE `kpi_monthly_scores`
  ADD CONSTRAINT `kpi_monthly_scores_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `kpi_tasks`
--
ALTER TABLE `kpi_tasks`
  ADD CONSTRAINT `kpi_tasks_assigned_by_foreign` FOREIGN KEY (`assigned_by`) REFERENCES `employees` (`id`),
  ADD CONSTRAINT `kpi_tasks_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `leave_applications`
--
ALTER TABLE `leave_applications`
  ADD CONSTRAINT `fk_leaveapp_approvedby` FOREIGN KEY (`approved_by`) REFERENCES `employees` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_leaveapp_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_leaveapp_leavetype` FOREIGN KEY (`leave_type_id`) REFERENCES `leave_types` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_leaveapp_substitute` FOREIGN KEY (`substitute_employee_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `leave_encashment`
--
ALTER TABLE `leave_encashment`
  ADD CONSTRAINT `fk_encash_approvedby` FOREIGN KEY (`approved_by`) REFERENCES `employees` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_encash_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_encash_leavetype` FOREIGN KEY (`leave_type_id`) REFERENCES `leave_types` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `loans`
--
ALTER TABLE `loans`
  ADD CONSTRAINT `loans_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `loans_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `loans_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `loan_installments`
--
ALTER TABLE `loan_installments`
  ADD CONSTRAINT `loan_installments_loan_id_foreign` FOREIGN KEY (`loan_id`) REFERENCES `loans` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `loan_installments_payroll_run_id_foreign` FOREIGN KEY (`payroll_run_id`) REFERENCES `payroll_runs` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `notices`
--
ALTER TABLE `notices`
  ADD CONSTRAINT `notices_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`);

--
-- Constraints for table `notice_acknowledgements`
--
ALTER TABLE `notice_acknowledgements`
  ADD CONSTRAINT `notice_acknowledgements_notice_id_foreign` FOREIGN KEY (`notice_id`) REFERENCES `notices` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notice_views`
--
ALTER TABLE `notice_views`
  ADD CONSTRAINT `notice_views_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notice_views_notice_id_foreign` FOREIGN KEY (`notice_id`) REFERENCES `notices` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payroll_runs`
--
ALTER TABLE `payroll_runs`
  ADD CONSTRAINT `payroll_runs_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `payroll_runs_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `payroll_runs_disbursed_by_foreign` FOREIGN KEY (`disbursed_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `payroll_runs_fiscal_year_id_foreign` FOREIGN KEY (`fiscal_year_id`) REFERENCES `fiscal_years` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payroll_run_details`
--
ALTER TABLE `payroll_run_details`
  ADD CONSTRAINT `payroll_run_details_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `payroll_run_details_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payroll_run_details_payroll_run_id_foreign` FOREIGN KEY (`payroll_run_id`) REFERENCES `payroll_runs` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_permission`
--
ALTER TABLE `role_permission`
  ADD CONSTRAINT `role_permission_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_permission_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `users_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
