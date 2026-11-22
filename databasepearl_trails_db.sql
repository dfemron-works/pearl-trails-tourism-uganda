-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 22, 2026 at 03:51 PM
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
-- Database: `pearl_trails_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_alerts`
--

CREATE TABLE `admin_alerts` (
  `alert_id` int(11) NOT NULL,
  `alert_type` enum('NEW_VISITOR','NEW_ACCOUNT','USER_INQUIRY','SOCIAL_MSG','ABUSE_DETECTED') NOT NULL,
  `severity` enum('INFO','WARNING','DANGER') DEFAULT 'INFO',
  `title` varchar(150) NOT NULL,
  `message` text NOT NULL,
  `source_channel` varchar(80) DEFAULT 'Web App',
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_alerts`
--

INSERT INTO `admin_alerts` (`alert_id`, `alert_type`, `severity`, `title`, `message`, `source_channel`, `is_read`, `created_at`) VALUES
(1, 'NEW_VISITOR', 'INFO', '👀 New Visitor on Web App', 'A new visitor (::1) opened login.php.', 'Web Browser', 1, '2026-11-17 08:18:04'),
(2, 'NEW_VISITOR', 'INFO', '👀 New Visitor on Web App', 'A new visitor (::1) opened login.php.', 'Web Browser', 1, '2026-11-17 08:19:14'),
(3, 'SOCIAL_MSG', 'WARNING', '📱 Outbound Social Message (WhatsApp)', 'System Admin initiated a message via our official WhatsApp handle.', 'WhatsApp', 1, '2026-11-17 08:35:04'),
(4, 'SOCIAL_MSG', 'WARNING', '📱 Outbound Social Message (WhatsApp)', 'System Admin initiated a message via our official WhatsApp handle.', 'WhatsApp', 1, '2026-11-17 08:38:29'),
(5, 'NEW_VISITOR', 'INFO', '👀 New Visitor on Web App', 'A new visitor (::1) opened login.php.', 'Web Browser', 1, '2026-11-17 09:14:50'),
(6, 'NEW_VISITOR', 'INFO', '👀 New Visitor on Web App', 'A new visitor (::1) opened login.php.', 'Web Browser', 1, '2026-11-17 11:58:12'),
(7, 'NEW_VISITOR', 'INFO', '👀 New Visitor on Web App', 'A new visitor (::1) opened login.php.', 'Web Browser', 1, '2026-11-17 13:19:24'),
(8, 'NEW_VISITOR', 'INFO', '👀 New Visitor on Web App', 'A new visitor (::1) opened login.php.', 'Web Browser', 1, '2026-11-17 14:44:25'),
(9, 'NEW_VISITOR', 'INFO', '👀 New Visitor on Web App', 'A new visitor (::1) opened login.php.', 'Web Browser', 1, '2026-11-17 14:54:08'),
(10, 'USER_INQUIRY', 'INFO', '💳 New Deposit Payment (Airtel Money Code)', 'kaburu Richard submitted $240.40 for Booking #5 via Airtel Money Code (Ref: EFT 345267890380808).', 'Airtel Money Code', 1, '2026-11-17 15:14:06'),
(11, 'NEW_VISITOR', 'INFO', '👀 New Visitor on Web App', 'A new visitor (::1) opened login.php.', 'Web Browser', 1, '2026-11-17 15:15:21'),
(12, 'NEW_VISITOR', 'INFO', '👀 New Visitor on Web App', 'A new visitor (::1) opened login.php.', 'Web Browser', 1, '2026-11-17 15:21:36'),
(13, 'NEW_VISITOR', 'INFO', '👀 New Visitor on Web App', 'A new visitor (::1) opened login.php.', 'Web Browser', 1, '2026-11-17 15:36:53'),
(14, 'NEW_VISITOR', 'INFO', '👀 New Visitor on Web App', 'A new visitor (::1) opened login.php.', 'Web Browser', 1, '2026-11-17 15:38:33'),
(15, 'NEW_VISITOR', 'INFO', '👀 New Visitor on Web App', 'A new visitor (::1) opened login.php.', 'Web Browser', 1, '2026-11-17 16:48:19'),
(16, 'NEW_VISITOR', 'INFO', '👀 New Visitor on Web App', 'A new visitor (::1) opened login.php.', 'Web Browser', 1, '2026-11-17 20:07:20'),
(17, 'NEW_ACCOUNT', 'INFO', '🆕 New Account Registered', 'Treecia Nuwemugabe (Ugandan, treecianuwemugabe@gmail.com) opened a new client account.', 'Registration Form', 1, '2026-11-17 20:12:32'),
(18, 'NEW_VISITOR', 'INFO', '👀 New Visitor on Web App', 'A new visitor (::1) opened login.php.', 'Web Browser', 1, '2026-11-17 20:13:38'),
(19, 'SOCIAL_MSG', 'WARNING', '📱 Outbound Social Message (WhatsApp)', 'System Admin initiated a message via our official WhatsApp handle.', 'WhatsApp', 1, '2026-11-17 20:19:46'),
(20, 'SOCIAL_MSG', 'WARNING', '📱 Outbound Social Message (YouTube)', 'System Admin initiated a message via our official YouTube handle.', 'YouTube', 1, '2026-11-18 08:51:00'),
(21, 'NEW_VISITOR', 'INFO', '👀 New Visitor on Web App', 'A new visitor (::1) opened login.php.', 'Web Browser', 1, '2026-11-18 10:25:48'),
(22, 'NEW_VISITOR', 'INFO', '👀 New Visitor on Web App', 'A new visitor (::1) opened login.php.', 'Web Browser', 1, '2026-11-18 10:26:53'),
(23, 'USER_INQUIRY', 'INFO', '💬 Live Chat Concierge Inquiry', 'Website Visitor asked via Live Chat: how much is 3 people booking to kasubi tombs', 'Live Safari Concierge', 1, '2026-11-18 12:29:49'),
(24, 'USER_INQUIRY', 'INFO', '💬 Live Chat Concierge Inquiry', 'Website Visitor asked via Live Chat: How do holiday offers work?', 'Live Safari Concierge', 1, '2026-11-18 12:39:58'),
(25, 'USER_INQUIRY', 'INFO', '💬 Live Chat Concierge Inquiry', 'Website Visitor asked via Live Chat: What payment methods do you accept?', 'Live Safari Concierge', 1, '2026-11-18 12:40:29'),
(26, 'USER_INQUIRY', 'INFO', '💬 Live Chat Concierge Inquiry', 'Website Visitor asked via Live Chat: Where are your offices located?', 'Live Safari Concierge', 1, '2026-11-18 12:40:49'),
(27, 'USER_INQUIRY', 'INFO', '💬 Live Chat Concierge Inquiry', 'Website Visitor asked via Live Chat: how are bookings done?', 'Live Safari Concierge', 1, '2026-11-18 12:41:32'),
(28, 'USER_INQUIRY', 'INFO', '💬 Live Chat Concierge Inquiry', 'Website Visitor asked via Live Chat: ok thank you for your help', 'Live Safari Concierge', 1, '2026-11-18 12:42:04'),
(29, 'NEW_VISITOR', 'INFO', '👀 New Visitor on Web App', 'A new visitor (::1) opened login.php.', 'Web Browser', 1, '2026-11-18 14:30:35'),
(30, 'NEW_VISITOR', 'INFO', '👀 New Visitor on Web App', 'A new visitor (::1) opened login.php.', 'Web Browser', 1, '2026-11-18 15:51:52'),
(31, 'NEW_VISITOR', 'INFO', '👀 New Visitor on Web App', 'A new visitor (::1) opened login.php.', 'Web Browser', 1, '2026-11-18 16:22:34'),
(32, 'USER_INQUIRY', 'INFO', '💳 New Full Balance Payment (MTN Mobile Money)', 'Treecia Nuwemugabe submitted $2,480.00 for Booking #6 via MTN Mobile Money (Ref: EFT 90876765456).', 'MTN Mobile Money', 1, '2026-11-18 16:25:33'),
(33, 'NEW_VISITOR', 'INFO', '👀 New Visitor on Web App', 'A new visitor (::1) opened login.php.', 'Web Browser', 1, '2026-11-18 16:30:02'),
(34, 'NEW_VISITOR', 'INFO', '👀 New Visitor on Web App', 'A new visitor (::1) opened login.php.', 'Web Browser', 1, '2026-11-18 20:33:07'),
(35, 'USER_INQUIRY', 'INFO', '🗺️ Custom Map Route Booked (#8)', 'System Admin mapped a 0 km (2-day) route for Historical Kasubi Tombs.', 'Live Safari Map', 1, '2026-11-19 00:53:49'),
(36, 'USER_INQUIRY', 'INFO', '💳 New Full Balance Payment (MTN Mobile Money)', 'System Admin submitted $800.00 for Booking #8 via MTN Mobile Money (Ref: OTT 90878654990).', 'MTN Mobile Money', 1, '2026-11-19 00:56:21'),
(37, 'USER_INQUIRY', 'INFO', '🗺️ Custom Map Route Booked (#9)', 'System Admin mapped a 665.35 km (3-day) route for Jinja Heritage site.', 'Live Safari Map', 1, '2026-11-19 01:46:52'),
(38, 'USER_INQUIRY', 'INFO', '💳 New Full Balance Payment (PayPal)', 'System Admin submitted $1,780.70 for Booking #9 via PayPal (Ref: MNM-9087890868).', 'PayPal', 1, '2026-11-19 01:48:25'),
(39, 'NEW_VISITOR', 'INFO', '👀 New Visitor on Web App', 'A new visitor (::1) opened login.php.', 'Web Browser', 1, '2026-11-19 02:10:03'),
(40, 'USER_INQUIRY', 'INFO', '✉️ New Query via Web Inquiry Form', 'From kaburu Richard (richardkaburu@gmail.com): I wanna go to Mweya safari lodge and kazinga channel tour on 26th December with my family of 6 members. how much will that cost, all services inclusive? get back as soon as possible so that i can work on my booking early enough. thx Kaburu Richard (Tel 0789898906)', 'Web Inquiry Form', 1, '2026-11-19 02:20:31'),
(41, 'NEW_VISITOR', 'INFO', '👀 New Visitor on Web App', 'A new visitor (::1) opened login.php.', 'Web Browser', 1, '2026-11-19 02:21:33'),
(42, 'USER_INQUIRY', 'INFO', '💬 Custom Concierge Request', 'System Admin asked: ok', 'Intelligent Safari Concierge', 0, '2026-11-19 04:43:59'),
(43, 'NEW_VISITOR', 'INFO', '👀 New Visitor on Web App', 'A new visitor (::1) opened login.php.', 'Web Browser', 0, '2026-11-19 08:50:11'),
(44, 'USER_INQUIRY', 'INFO', '💬 Custom Concierge Request', 'kaburu Richard asked: name is John, nice to meet you please', 'Intelligent Safari Concierge', 0, '2026-11-19 10:02:40'),
(45, 'NEW_VISITOR', 'INFO', '👀 New Visitor on Web App', 'A new visitor (::1) opened login.php.', 'Web Browser', 0, '2026-11-19 16:19:04'),
(46, 'NEW_VISITOR', 'INFO', '👀 New Visitor on Web App', 'A new visitor (::1) opened login.php.', 'Web Browser', 0, '2026-11-19 17:15:31'),
(47, 'USER_INQUIRY', 'INFO', '🗺️ Custom Map Route Booked (#10)', 'System Admin mapped a 406.12 km (2-day) route for Jinja Heritage site.', 'Live Safari Map', 0, '2026-11-19 17:28:25'),
(48, 'NEW_VISITOR', 'INFO', '👀 New Visitor on Web App', 'A new visitor (::1) opened login.php.', 'Web Browser', 0, '2026-11-19 18:17:04'),
(49, 'NEW_ACCOUNT', 'INFO', '🆕 New Account Registered', 'Mr Ntulume Elish (Ugandan, ntulumeelisha@gmail.com) opened a new client account.', 'Registration Form', 0, '2026-11-19 18:22:28'),
(50, 'USER_INQUIRY', 'INFO', '💳 New Deposit Payment (MTN Mobile Money)', 'Mr Ntulume Elish submitted $297.00 for Booking #11 via MTN Mobile Money (Ref: EFT 4527233833334).', 'MTN Mobile Money', 0, '2026-11-19 18:26:19'),
(51, 'NEW_VISITOR', 'INFO', '👀 New Visitor on Web App', 'A new visitor (::1) opened login.php.', 'Web Browser', 0, '2026-11-19 18:27:10'),
(52, 'NEW_VISITOR', 'INFO', '👀 New Visitor on Web App', 'A new visitor (::1) opened login.php.', 'Web Browser', 0, '2026-11-19 18:29:54'),
(53, 'NEW_VISITOR', 'INFO', '👀 New Visitor on Web App', 'A new visitor (::1) opened login.php.', 'Web Browser', 0, '2026-11-19 18:48:55'),
(54, 'NEW_VISITOR', 'INFO', '👀 New Visitor on Web App', 'A new visitor (::1) opened index.php.', 'Web Browser', 0, '2026-11-22 13:44:01'),
(55, 'NEW_VISITOR', 'INFO', '👀 New Visitor on Web App', 'A new visitor (::1) opened login.php.', 'Web Browser', 0, '2026-11-22 14:02:29'),
(56, 'NEW_VISITOR', 'INFO', '👀 New Visitor on Web App', 'A new visitor (::1) opened login.php.', 'Web Browser', 0, '2026-11-22 14:08:17');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `booking_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `package_id` int(11) NOT NULL,
  `travel_date` date NOT NULL,
  `num_guests` int(11) NOT NULL,
  `total_cost` decimal(10,2) NOT NULL,
  `status` enum('Pending','Confirmed','Concluded','Cancelled') DEFAULT 'Pending',
  `booked_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status_reason` varchar(255) DEFAULT 'Awaiting initial review',
  `route_summary` text DEFAULT NULL,
  `total_distance_km` decimal(10,2) DEFAULT 0.00,
  `total_travel_hours` decimal(10,2) DEFAULT 0.00,
  `field_days` int(11) DEFAULT 1,
  `hire_rate_per_km_usd` decimal(10,2) DEFAULT 1.50,
  `booking_time_weather` varchar(180) DEFAULT '26.5°C • Partly Cloudy (Recorded at Booking)',
  `trip_date_expected_weather` varchar(180) DEFAULT '27.0°C • Sunny & Clear Safari Conditions'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`booking_id`, `user_id`, `package_id`, `travel_date`, `num_guests`, `total_cost`, `status`, `booked_at`, `status_reason`, `route_summary`, `total_distance_km`, `total_travel_hours`, `field_days`, `hire_rate_per_km_usd`, `booking_time_weather`, `trip_date_expected_weather`) VALUES
(3, 4, 3, '2026-11-20', 6, 1440.00, 'Confirmed', '2026-11-16 18:01:46', 'Deposit paid and safari permits verified', NULL, 0.00, 0.00, 1, 1.50, '26.5°C • Partly Cloudy (Recorded at Booking)', '27.0°C • Sunny & Clear Safari Conditions'),
(4, 4, 5, '2026-11-24', 4, 800.00, 'Pending', '2026-11-17 14:25:53', 'Weekday booking (Standard rate applied; 5% offer valid only on Weekends & UG Holidays)', NULL, 0.00, 0.00, 1, 1.50, '26.5°C • Partly Cloudy (Recorded at Booking)', '27.0°C • Sunny & Clear Safari Conditions'),
(5, 4, 3, '2026-12-05', 2, 408.00, 'Confirmed', '2026-11-17 14:41:36', 'Electronic Payment #1 Verified & Confirmed by Admin', NULL, 0.00, 0.00, 1, 1.50, '26.5°C • Partly Cloudy (Recorded at Booking)', '27.0°C • Sunny & Clear Safari Conditions'),
(6, 5, 2, '2026-11-22', 4, 2480.00, 'Confirmed', '2026-11-18 16:24:08', 'Full Balance Payment of $2,480.00 submitted via MTN Mobile Money (Ref: EFT 90876765456) - Awaiting Admin Verification', NULL, 0.00, 0.00, 1, 1.50, '26.5°C • Partly Cloudy (Recorded at Booking)', '27.0°C • Sunny & Clear Safari Conditions'),
(7, 5, 1, '2026-11-21', 4, 2720.00, 'Pending', '2026-11-18 16:26:56', 'Booked under 20% Offer (Saturday Weekend)', NULL, 0.00, 0.00, 1, 1.50, '26.5°C • Partly Cloudy (Recorded at Booking)', '27.0°C • Sunny & Clear Safari Conditions'),
(8, 1, 5, '2026-11-28', 4, 800.00, 'Cancelled', '2026-11-19 00:53:49', 'Full Balance Payment of $800.00 submitted via MTN Mobile Money (Ref: OTT 90878654990) - Awaiting Admin Verification', '', 0.00, 0.00, 2, 1.50, '26.5°C • Partly Cloudy (Recorded at Booking)', '27.0°C • Sunny & Clear Safari Conditions'),
(9, 1, 7, '2026-12-12', 3, 1780.70, 'Confirmed', '2026-11-19 01:46:52', 'Electronic Payment #5 Verified & Confirmed by Admin', 'Pearl Trails Head Office (Nagojje) (Landing Site) -> Red Chilli Rest Camp (Camping Site) -> Entebbe International Airport Landing (Landing Site)', 665.35, 12.10, 3, 2.00, '26.5°C • Partly Cloudy (Recorded at Booking)', '27.0°C • Sunny & Clear Safari Conditions'),
(10, 1, 7, '2026-11-20', 2, 909.18, 'Confirmed', '2026-11-19 17:28:25', 'Custom Mapped Route (406.12 km, 7.38 hrs, 2 field days @ $1.5/km)', 'jinja -> QE NP', 406.12, 7.38, 2, 1.50, '26.5°C • Partly Cloudy (Recorded at Booking)', '27.0°C • Sunny & Clear Safari Conditions'),
(11, 6, 3, '2026-11-28', 2, 408.00, 'Confirmed', '2026-11-19 18:24:12', 'Deposit Payment of $297.00 submitted via MTN Mobile Money (Ref: EFT 4527233833334) - Awaiting Admin Verification', NULL, 0.00, 0.00, 1, 1.50, '26.5°C • Partly Cloudy (Recorded at Booking)', '27.0°C • Sunny & Clear Safari Conditions');

-- --------------------------------------------------------

--
-- Table structure for table `climate_records`
--

CREATE TABLE `climate_records` (
  `record_id` int(11) NOT NULL,
  `region_name` varchar(100) NOT NULL,
  `period_label` varchar(100) NOT NULL,
  `record_type` enum('Past / Happened','Expected / Forecast') NOT NULL,
  `weather_rating` enum('Good Weather','Harsh Weather') NOT NULL,
  `avg_temp_c` decimal(5,1) NOT NULL,
  `rainfall_mm` decimal(6,1) NOT NULL,
  `summary_notes` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `climate_records`
--

INSERT INTO `climate_records` (`record_id`, `region_name`, `period_label`, `record_type`, `weather_rating`, `avg_temp_c`, `rainfall_mm`, `summary_notes`) VALUES
(1, 'Bwindi Impenetrable NP', 'Jan – Feb 2025 (Dry Peak)', 'Past / Happened', 'Good Weather', 21.5, 48.0, 'Clear forest trails; optimal gorilla tracking visibility'),
(2, 'Bwindi Impenetrable NP', 'Apr – May 2025 (Heavy Rains)', 'Past / Happened', 'Harsh Weather', 16.0, 245.0, 'Steep muddy trails, dense mist & heavy afternoon downpours'),
(3, 'Murchison Falls NP', 'Jun – Aug 2025 (Dry Savanna)', 'Past / Happened', 'Good Weather', 30.2, 32.0, 'High wildlife concentration along Victoria Nile; dry tracks'),
(4, 'Queen Elizabeth NP', 'Oct – Nov 2025 (Short Rains)', 'Past / Happened', 'Harsh Weather', 22.4, 195.0, 'Localized flooding on southern Ishasha clay tracks'),
(5, 'Nagojje & Central Uganda', 'Dec 2026 – Feb 2027', 'Expected / Forecast', 'Good Weather', 27.8, 40.0, 'Warm sunny skies; ideal highway & Lake Victoria excursions'),
(6, 'Bwindi & Rwenzori Highlands', 'Mar – May 2027 (Long Rains)', 'Expected / Forecast', 'Harsh Weather', 15.8, 260.0, 'Heavy mountain precipitation; 4x4 high-clearance mandatory'),
(7, 'Kidepo & Northern Savanna', 'Jun – Sep 2027 (Dry Season)', 'Expected / Forecast', 'Good Weather', 31.5, 28.0, 'Clear skies and peak game-viewing across Narus Valley'),
(8, 'Global / European & North American Hubs', 'Dec 2026 – Feb 2027', 'Expected / Forecast', 'Harsh Weather', -1.5, 110.0, 'Winter snow & freezing temperatures driving peak tourist arrivals to Uganda');

-- --------------------------------------------------------

--
-- Table structure for table `company_staff`
--

CREATE TABLE `company_staff` (
  `staff_id` int(11) NOT NULL,
  `full_name` varchar(120) NOT NULL,
  `staff_category` enum('Management','Support Staff') NOT NULL,
  `job_title` varchar(100) NOT NULL,
  `phone_contact` varchar(40) NOT NULL,
  `monthly_salary_usd` decimal(10,2) NOT NULL,
  `employment_status` enum('Active','On Leave','Terminated') DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `company_staff`
--

INSERT INTO `company_staff` (`staff_id`, `full_name`, `staff_category`, `job_title`, `phone_contact`, `monthly_salary_usd`, `employment_status`) VALUES
(1, 'Mugisha Robert', 'Management', 'Managing Director', '+256 772 845 910', 1500.00, 'Active'),
(2, 'Namazzi Prossy', 'Management', 'Head of Operations & Tours', '+256 752 441 209', 1100.00, 'Active'),
(3, 'Okello Daniel', 'Support Staff', 'Senior Safari Driver & Tour Guide', '+256 782 310 884', 550.00, 'Active'),
(4, 'Nabwire Immaculate', 'Support Staff', 'Front Desk & Reservations Officer', '+256 701 992 114', 450.00, 'Active'),
(5, 'Amanya Jojomilo Braise', 'Management', 'HRM', '0786787879', 650.00, 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `company_vehicles`
--

CREATE TABLE `company_vehicles` (
  `vehicle_id` int(11) NOT NULL,
  `plate_number` varchar(30) NOT NULL,
  `model_name` varchar(100) NOT NULL,
  `vehicle_category` enum('Saloon','Wagon','Pickup','SURV','Lorry') NOT NULL,
  `seating_capacity` int(11) DEFAULT 4,
  `valuation_usd` decimal(12,2) NOT NULL,
  `operational_status` enum('Available','On Safari','In Garage') DEFAULT 'Available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `company_vehicles`
--

INSERT INTO `company_vehicles` (`vehicle_id`, `plate_number`, `model_name`, `vehicle_category`, `seating_capacity`, `valuation_usd`, `operational_status`) VALUES
(1, 'UBG 412K', 'Toyota Land Cruiser Extended Safari', 'SURV', 8, 45000.00, 'Available'),
(2, 'UBD 890M', 'Toyota Hilux Double Cabin 4x4', 'Pickup', 5, 28000.00, 'On Safari'),
(3, 'UAK 215P', 'Toyota Premio Executive', 'Saloon', 5, 11000.00, 'Available'),
(4, 'UBE 774R', 'Toyota Fielder Tour Wagon', 'Wagon', 5, 13500.00, 'Available'),
(5, 'UAM 603T', 'Isuzu Forward Camp Logistics Truck', 'Lorry', 3, 32000.00, 'Available');

-- --------------------------------------------------------

--
-- Table structure for table `financial_ledger`
--

CREATE TABLE `financial_ledger` (
  `entry_id` int(11) NOT NULL,
  `entry_type` enum('Operational Expense','Other Income','Current Liability','Long-Term Liability','Owner Capital') NOT NULL,
  `account_title` varchar(150) NOT NULL,
  `amount_usd` decimal(12,2) NOT NULL,
  `entry_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `financial_ledger`
--

INSERT INTO `financial_ledger` (`entry_id`, `entry_type`, `account_title`, `amount_usd`, `entry_date`) VALUES
(1, 'Owner Capital', 'Initial Shareholders Paid-In Capital', 140000.00, '2026-01-01'),
(2, 'Long-Term Liability', 'Stanbic Bank Fleet Asset Loan', 18000.00, '2026-02-01'),
(3, 'Current Liability', 'Accrued UWA Park Permit Payables', 2400.00, '2026-10-15'),
(4, 'Operational Expense', 'Safari Fuel, Park Fees & Vehicle Maintenance', 3100.00, '2026-10-20'),
(5, 'Operational Expense', 'Office Rent, Utilities & Internet (Mukono)', 1200.00, '2026-11-01');

-- --------------------------------------------------------

--
-- Table structure for table `gallery`
--

CREATE TABLE `gallery` (
  `photo_id` int(11) NOT NULL,
  `package_id` int(11) DEFAULT NULL,
  `caption` varchar(200) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gallery`
--

INSERT INTO `gallery` (`photo_id`, `package_id`, `caption`, `image_path`, `uploaded_at`) VALUES
(1, 1, 'The beautiful beds at Bwindi Gorrilla  NP', 'uploads/safari_1794917802_482.png', '2026-11-17 12:16:42'),
(2, 1, 'The Gorrilla at Bwindi   NP', 'uploads/safari_1794918039_665.jpg', '2026-11-17 12:20:39'),
(3, 1, 'The Gorrilla at Bwindi   NP', 'uploads/safari_1794918354_101.jpg', '2026-11-17 12:25:54'),
(4, 1, 'The furrnature  at Bwindi   NP', 'uploads/safari_1794918447_685.jpg', '2026-11-17 12:27:27');

-- --------------------------------------------------------

--
-- Table structure for table `office_equipment`
--

CREATE TABLE `office_equipment` (
  `equip_id` int(11) NOT NULL,
  `item_name` varchar(120) NOT NULL,
  `category` enum('Electronic','Non-Electronic') NOT NULL,
  `serial_or_tag` varchar(80) NOT NULL,
  `quantity` int(11) DEFAULT 1,
  `purchase_value_usd` decimal(10,2) NOT NULL,
  `condition_status` enum('Good','Needs Repair','Retired') DEFAULT 'Good',
  `added_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `office_equipment`
--

INSERT INTO `office_equipment` (`equip_id`, `item_name`, `category`, `serial_or_tag`, `quantity`, `purchase_value_usd`, `condition_status`, `added_at`) VALUES
(1, 'HP ProBook 450 G9 Laptops', 'Electronic', 'PTU-EL-001', 4, 3200.00, 'Good', '2026-11-17 10:41:57'),
(2, 'Epson EcoTank L3250 Printer', 'Electronic', 'PTU-EL-002', 1, 280.00, 'Good', '2026-11-17 10:41:57'),
(3, 'Executive Mahogany Desks & Chairs', 'Non-Electronic', 'PTU-NE-001', 6, 1500.00, 'Good', '2026-11-17 10:41:57'),
(4, 'Fireproof Steel Filing Cabinets', 'Non-Electronic', 'PTU-NE-002', 3, 750.00, 'Good', '2026-11-17 10:41:57'),
(5, 'Desktop HP computers', 'Electronic', '23, 21, 22, 10, 30, 04', 6, 240.00, 'Good', '2026-11-17 12:07:40'),
(6, 'Desktop HP computers', 'Electronic', '23, 21, 22, 10, 30, 04', 6, 240.00, 'Good', '2026-11-17 12:09:01');

-- --------------------------------------------------------

--
-- Table structure for table `office_files`
--

CREATE TABLE `office_files` (
  `file_id` int(11) NOT NULL,
  `file_reference` varchar(60) NOT NULL,
  `file_title` varchar(150) NOT NULL,
  `department` varchar(80) NOT NULL,
  `cabinet_location` varchar(80) NOT NULL,
  `created_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `office_files`
--

INSERT INTO `office_files` (`file_id`, `file_reference`, `file_title`, `department`, `cabinet_location`, `created_date`) VALUES
(1, 'PTU/UWA/2026/01', 'Uganda Wildlife Authority Gorilla & Park Permits', 'Operations', 'Cabinet A - Drawer 1', '2026-01-10'),
(2, 'PTU/URA/2026/02', 'URA Tax Clearance & Tourism License Certificates', 'Administration', 'Cabinet A - Drawer 2', '2026-02-04'),
(3, 'PTU/FIN/2026/03', 'Bank Reconciliation & Mobile Money Audit Vouchers', 'Finance', 'Cabinet B - Safe', '2026-03-15');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `payment_category` enum('Mobile Money & Wallets','Merchant & App Codes','International Remittance','Bank Transfer / EFT') NOT NULL,
  `payment_method` varchar(100) NOT NULL,
  `company_destination_account` varchar(150) NOT NULL,
  `payer_account_or_phone` varchar(100) NOT NULL,
  `transaction_reference` varchar(100) NOT NULL,
  `amount_paid_usd` decimal(10,2) NOT NULL,
  `currency_used` varchar(10) DEFAULT 'USD',
  `amount_in_currency` varchar(60) NOT NULL,
  `payment_status` enum('Pending Verification','Verified & Paid','Rejected') DEFAULT 'Pending Verification',
  `paid_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`payment_id`, `booking_id`, `user_id`, `payment_category`, `payment_method`, `company_destination_account`, `payer_account_or_phone`, `transaction_reference`, `amount_paid_usd`, `currency_used`, `amount_in_currency`, `payment_status`, `paid_at`) VALUES
(1, 5, 4, 'Merchant & App Codes', 'Airtel Money Code (Deposit Payment)', 'Airtel Pay Merchant Code: 619044 (Pearl Trails)', '+256789454677', 'EFT 345267890380808', 240.40, 'USD', '$240.40', 'Verified & Paid', '2026-11-17 15:14:06'),
(2, 6, 5, 'Mobile Money & Wallets', 'MTN Mobile Money (Full Balance Payment)', 'MTN MoMo: +256 772 845 910 (Pearl Trails Uganda Ltd)', '0786897645', 'EFT 90876765456', 2480.00, 'USD', '$2,480.00', 'Verified & Paid', '2026-11-18 16:25:33'),
(4, 8, 1, 'Mobile Money & Wallets', 'MTN Mobile Money (Full Balance Payment)', 'MTN MoMo: +256 772 845 910 (Pearl Trails Uganda Ltd)', '0789008976', 'OTT 90878654990', 800.00, 'USD', '$800.00', 'Pending Verification', '2026-11-19 00:56:21'),
(5, 9, 1, 'International Remittance', 'PayPal (Full Balance Payment)', 'PayPal Business Email: payments@pearltrails.ug', '0789909090', 'MNM-9087890868', 1780.70, 'USD', '$1,780.70', 'Verified & Paid', '2026-11-19 01:48:25'),
(6, 11, 6, 'Mobile Money & Wallets', 'MTN Mobile Money (Deposit Payment)', 'MTN MoMo: +256 772 845 910 (Pearl Trails Uganda Ltd)', '0789654543', 'EFT 4527233833334', 297.00, 'USD', '$297.00', 'Pending Verification', '2026-11-19 18:26:19');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `review_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `package_id` int(11) NOT NULL,
  `rating` tinyint(4) NOT NULL CHECK (`rating` between 1 and 5),
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`review_id`, `user_id`, `package_id`, `rating`, `comment`, `created_at`) VALUES
(2, 4, 1, 5, 'it was an amazing experience travelling with Pearl trails Ug. you guys are real hospitality masters. thank you and be blessed more. i will see u again in December when i travel to the Queen Elizabeth NP', '2026-11-17 14:30:33');

-- --------------------------------------------------------

--
-- Table structure for table `system_logs`
--

CREATE TABLE `system_logs` (
  `log_id` int(11) NOT NULL,
  `event_type` enum('visit','login') NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `page_url` varchar(150) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `system_logs`
--

INSERT INTO `system_logs` (`log_id`, `event_type`, `user_id`, `page_url`, `ip_address`, `created_at`) VALUES
(1, 'visit', 1, 'index.php', '::1', '2026-11-17 04:17:10'),
(2, 'visit', NULL, 'login.php', '::1', '2026-11-17 04:17:57'),
(3, 'login', 1, 'login.php', '::1', '2026-11-17 04:18:06'),
(4, 'visit', 1, 'admin_dashboard.php', '::1', '2026-11-17 04:18:06'),
(5, 'visit', 1, 'reviews.php', '::1', '2026-11-17 04:18:11'),
(6, 'visit', 1, 'gallery.php', '::1', '2026-11-17 04:18:19'),
(7, 'visit', 1, 'index.php', '::1', '2026-11-17 04:18:26'),
(8, 'visit', 1, 'reports.php', '::1', '2026-11-17 04:19:08'),
(9, 'visit', 1, 'reports.php', '::1', '2026-11-17 04:20:47'),
(10, 'visit', 1, 'index.php', '::1', '2026-11-17 04:28:55'),
(11, 'visit', NULL, 'login.php', '::1', '2026-11-17 04:29:27'),
(12, 'login', NULL, 'login.php', '::1', '2026-11-17 04:29:42'),
(13, 'visit', NULL, 'index.php', '::1', '2026-11-17 04:29:42'),
(14, 'visit', NULL, 'index.php', '::1', '2026-11-17 05:00:37'),
(15, 'visit', NULL, 'index.php', '::1', '2026-11-17 05:03:50'),
(16, 'visit', NULL, 'index.php', '::1', '2026-11-17 05:05:04'),
(17, 'visit', NULL, 'index.php', '::1', '2026-11-17 05:16:34'),
(18, 'visit', NULL, 'my_bookings.php', '::1', '2026-11-17 05:17:34'),
(19, 'visit', NULL, 'my_bookings.php', '::1', '2026-11-17 05:24:59'),
(20, 'visit', NULL, 'reviews.php', '::1', '2026-11-17 05:25:49'),
(21, 'visit', NULL, 'index.php', '::1', '2026-11-17 05:26:12'),
(22, 'visit', NULL, 'my_bookings.php', '::1', '2026-11-17 05:27:27'),
(23, 'visit', NULL, 'reviews.php', '::1', '2026-11-17 05:27:31'),
(24, 'visit', NULL, 'reviews.php', '::1', '2026-11-17 06:02:05'),
(25, 'visit', NULL, 'reviews.php', '::1', '2026-11-17 06:06:48'),
(26, 'visit', NULL, 'index.php', '::1', '2026-11-17 07:42:01'),
(27, 'visit', NULL, 'reviews.php', '::1', '2026-11-17 07:42:55'),
(28, 'visit', NULL, 'login.php', '::1', '2026-11-17 07:43:24'),
(29, 'visit', NULL, 'reviews.php', '::1', '2026-11-17 07:43:41'),
(30, 'visit', NULL, 'gallery.php', '::1', '2026-11-17 07:43:48'),
(31, 'visit', NULL, 'register.php', '::1', '2026-11-17 07:44:20'),
(32, 'visit', NULL, 'login.php', '::1', '2026-11-17 07:44:34'),
(33, 'visit', NULL, 'index.php', '::1', '2026-11-17 07:45:04'),
(34, 'visit', NULL, 'register.php', '::1', '2026-11-17 07:45:33'),
(35, 'visit', NULL, 'reviews.php', '::1', '2026-11-17 07:46:14'),
(36, 'visit', NULL, 'login.php', '::1', '2026-11-17 08:18:04'),
(37, 'login', NULL, 'login.php', '::1', '2026-11-17 08:18:32'),
(38, 'visit', NULL, 'index.php', '::1', '2026-11-17 08:18:32'),
(39, 'visit', NULL, 'my_bookings.php', '::1', '2026-11-17 08:18:46'),
(40, 'visit', NULL, 'login.php', '::1', '2026-11-17 08:19:14'),
(41, 'login', 1, 'login.php', '::1', '2026-11-17 08:19:25'),
(42, 'visit', 1, 'admin_dashboard.php', '::1', '2026-11-17 08:19:25'),
(43, 'visit', 1, 'manage_users.php', '::1', '2026-11-17 08:19:58'),
(44, 'visit', 1, 'reports.php', '::1', '2026-11-17 08:20:00'),
(45, 'visit', 1, 'about.php', '::1', '2026-11-17 08:33:35'),
(46, 'visit', 1, 'about.php', '::1', '2026-11-17 08:35:04'),
(47, 'visit', 1, 'about.php', '::1', '2026-11-17 08:38:17'),
(48, 'visit', 1, 'reports.php', '::1', '2026-11-17 08:50:21'),
(49, 'visit', 1, 'reviews.php', '::1', '2026-11-17 08:50:24'),
(50, 'visit', 1, 'reports.php', '::1', '2026-11-17 09:00:43'),
(51, 'visit', 1, 'about.php', '::1', '2026-11-17 09:00:51'),
(52, 'visit', 1, 'admin_dashboard.php', '::1', '2026-11-17 09:00:53'),
(53, 'visit', 1, 'reviews.php', '::1', '2026-11-17 09:02:27'),
(54, 'visit', 1, 'about.php', '::1', '2026-11-17 09:02:52'),
(55, 'visit', 1, 'index.php', '::1', '2026-11-17 09:03:03'),
(56, 'visit', 1, 'about.php', '::1', '2026-11-17 09:10:02'),
(57, 'visit', 1, 'reviews.php', '::1', '2026-11-17 09:11:23'),
(58, 'visit', 1, 'index.php', '::1', '2026-11-17 09:11:25'),
(59, 'visit', 1, 'index.php', '::1', '2026-11-17 09:12:47'),
(60, 'visit', 1, 'admin_dashboard.php', '::1', '2026-11-17 09:14:44'),
(61, 'visit', 1, 'manage_users.php', '::1', '2026-11-17 09:14:46'),
(62, 'visit', 1, 'reviews.php', '::1', '2026-11-17 09:14:47'),
(63, 'visit', 1, 'gallery.php', '::1', '2026-11-17 09:14:48'),
(64, 'visit', NULL, 'login.php', '::1', '2026-11-17 09:14:50'),
(65, 'login', 1, 'login.php', '::1', '2026-11-17 09:15:04'),
(66, 'visit', 1, 'admin_dashboard.php', '::1', '2026-11-17 09:15:04'),
(67, 'visit', 1, 'admin_dashboard.php', '::1', '2026-11-17 09:22:24'),
(68, 'visit', 1, 'reports.php', '::1', '2026-11-17 09:28:25'),
(69, 'visit', 1, 'reviews.php', '::1', '2026-11-17 09:28:29'),
(70, 'visit', 1, 'reports.php', '::1', '2026-11-17 10:22:50'),
(71, 'visit', 1, 'about.php', '::1', '2026-11-17 10:23:05'),
(72, 'visit', 1, 'admin_dashboard.php', '::1', '2026-11-17 10:23:21'),
(73, 'visit', 1, 'admin_dashboard.php', '::1', '2026-11-17 10:52:27'),
(74, 'visit', 1, 'gallery.php', '::1', '2026-11-17 11:54:01'),
(75, 'visit', 1, 'reports.php', '::1', '2026-11-17 11:54:21'),
(76, 'visit', 1, 'manage_users.php', '::1', '2026-11-17 11:54:48'),
(77, 'visit', 1, 'admin_dashboard.php', '::1', '2026-11-17 11:54:58'),
(78, 'visit', 1, 'index.php', '::1', '2026-11-17 11:55:14'),
(79, 'visit', 1, 'index.php', '::1', '2026-11-17 11:57:18'),
(80, 'visit', 1, 'gallery.php', '::1', '2026-11-17 11:57:19'),
(81, 'visit', 1, 'reviews.php', '::1', '2026-11-17 11:57:20'),
(82, 'visit', 1, 'about.php', '::1', '2026-11-17 11:57:21'),
(83, 'visit', 1, 'admin_dashboard.php', '::1', '2026-11-17 11:57:22'),
(84, 'visit', 1, 'manage_users.php', '::1', '2026-11-17 11:57:23'),
(85, 'visit', 1, 'reports.php', '::1', '2026-11-17 11:57:49'),
(86, 'visit', NULL, 'login.php', '::1', '2026-11-17 11:58:12'),
(87, 'login', 1, 'login.php', '::1', '2026-11-17 11:58:44'),
(88, 'visit', 1, 'admin_dashboard.php', '::1', '2026-11-17 11:58:44'),
(89, 'visit', 1, 'reviews.php', '::1', '2026-11-17 12:04:39'),
(90, 'visit', 1, 'inventory.php', '::1', '2026-11-17 12:04:46'),
(91, 'visit', 1, 'inventory.php', '::1', '2026-11-17 12:07:40'),
(92, 'visit', 1, 'inventory.php', '::1', '2026-11-17 12:09:01'),
(93, 'visit', 1, 'about.php', '::1', '2026-11-17 12:11:10'),
(94, 'visit', 1, 'admin_dashboard.php', '::1', '2026-11-17 12:11:34'),
(95, 'visit', 1, 'reviews.php', '::1', '2026-11-17 12:12:56'),
(96, 'visit', 1, 'gallery.php', '::1', '2026-11-17 12:13:07'),
(97, 'visit', 1, 'gallery.php', '::1', '2026-11-17 12:16:42'),
(98, 'visit', 1, 'gallery.php', '::1', '2026-11-17 12:20:39'),
(99, 'visit', 1, 'gallery.php', '::1', '2026-11-17 12:25:54'),
(100, 'visit', 1, 'gallery.php', '::1', '2026-11-17 12:27:27'),
(101, 'visit', 1, 'finances.php', '::1', '2026-11-17 12:27:41'),
(102, 'visit', 1, 'inventory.php', '::1', '2026-11-17 12:29:14'),
(103, 'visit', 1, 'manage_users.php', '::1', '2026-11-17 12:29:20'),
(104, 'visit', 1, 'admin_dashboard.php', '::1', '2026-11-17 12:29:24'),
(105, 'visit', 1, 'admin_dashboard.php', '::1', '2026-11-17 13:13:54'),
(106, 'visit', NULL, 'login.php', '::1', '2026-11-17 13:19:24'),
(107, 'login', 4, 'login.php', '::1', '2026-11-17 13:19:50'),
(108, 'visit', 4, 'index.php', '::1', '2026-11-17 13:19:50'),
(109, 'visit', 4, 'my_bookings.php', '::1', '2026-11-17 13:19:56'),
(110, 'visit', 4, 'about.php', '::1', '2026-11-17 13:20:07'),
(111, 'visit', 4, 'reviews.php', '::1', '2026-11-17 13:20:19'),
(112, 'visit', 4, 'gallery.php', '::1', '2026-11-17 13:20:25'),
(113, 'visit', 4, 'gallery.php', '::1', '2026-11-17 14:10:45'),
(114, 'visit', 4, 'index.php', '::1', '2026-11-17 14:12:33'),
(115, 'visit', 4, 'index.php', '::1', '2026-11-17 14:21:42'),
(116, 'visit', 4, 'my_bookings.php', '::1', '2026-11-17 14:22:59'),
(117, 'visit', 4, 'about.php', '::1', '2026-11-17 14:23:02'),
(118, 'visit', 4, 'gallery.php', '::1', '2026-11-17 14:23:07'),
(119, 'visit', 4, 'index.php', '::1', '2026-11-17 14:23:15'),
(120, 'visit', 4, 'index.php', '::1', '2026-11-17 14:24:33'),
(121, 'visit', 4, 'index.php', '::1', '2026-11-17 14:25:53'),
(122, 'visit', 4, 'reviews.php', '::1', '2026-11-17 14:27:29'),
(123, 'visit', 4, 'reviews.php', '::1', '2026-11-17 14:30:33'),
(124, 'visit', 4, 'my_bookings.php', '::1', '2026-11-17 14:32:03'),
(125, 'visit', 4, 'my_bookings.php', '::1', '2026-11-17 14:38:49'),
(126, 'visit', 4, 'index.php', '::1', '2026-11-17 14:39:10'),
(127, 'visit', 4, 'index.php', '::1', '2026-11-17 14:41:36'),
(128, 'visit', 4, 'my_bookings.php', '::1', '2026-11-17 14:42:33'),
(129, 'visit', 4, 'index.php', '::1', '2026-11-17 14:43:06'),
(130, 'visit', 4, 'gallery.php', '::1', '2026-11-17 14:43:09'),
(131, 'visit', NULL, 'login.php', '::1', '2026-11-17 14:44:25'),
(132, 'login', 1, 'login.php', '::1', '2026-11-17 14:44:37'),
(133, 'visit', 1, 'admin_dashboard.php', '::1', '2026-11-17 14:44:37'),
(134, 'visit', 1, 'manage_users.php', '::1', '2026-11-17 14:45:32'),
(135, 'visit', 1, 'about.php', '::1', '2026-11-17 14:46:14'),
(136, 'visit', 1, 'gallery.php', '::1', '2026-11-17 14:46:51'),
(137, 'visit', 1, 'index.php', '::1', '2026-11-17 14:47:05'),
(138, 'visit', 1, 'reviews.php', '::1', '2026-11-17 14:47:41'),
(139, 'visit', 1, 'inventory.php', '::1', '2026-11-17 14:47:57'),
(140, 'visit', 1, 'finances.php', '::1', '2026-11-17 14:49:03'),
(141, 'visit', 1, 'reports.php', '::1', '2026-11-17 14:49:16'),
(142, 'visit', 1, 'manage_users.php', '::1', '2026-11-17 14:52:32'),
(143, 'visit', 1, 'index.php', '::1', '2026-11-17 14:52:59'),
(144, 'visit', 1, 'admin_dashboard.php', '::1', '2026-11-17 14:53:11'),
(145, 'visit', NULL, 'login.php', '::1', '2026-11-17 14:54:08'),
(146, 'login', 4, 'login.php', '::1', '2026-11-17 14:54:21'),
(147, 'visit', 4, 'index.php', '::1', '2026-11-17 14:54:21'),
(148, 'visit', 4, 'my_bookings.php', '::1', '2026-11-17 14:55:58'),
(149, 'visit', 4, 'my_bookings.php', '::1', '2026-11-17 15:07:24'),
(150, 'visit', 4, 'payment_checkout.php', '::1', '2026-11-17 15:08:02'),
(151, 'visit', 4, 'payment_checkout.php', '::1', '2026-11-17 15:14:06'),
(152, 'visit', NULL, 'login.php', '::1', '2026-11-17 15:15:21'),
(153, 'login', 1, 'login.php', '::1', '2026-11-17 15:15:35'),
(154, 'visit', 1, 'admin_dashboard.php', '::1', '2026-11-17 15:15:35'),
(155, 'visit', 1, 'admin_dashboard.php', '::1', '2026-11-17 15:16:53'),
(156, 'visit', 1, 'reviews.php', '::1', '2026-11-17 15:17:00'),
(157, 'visit', 1, 'gallery.php', '::1', '2026-11-17 15:17:02'),
(158, 'visit', 1, 'about.php', '::1', '2026-11-17 15:17:33'),
(159, 'visit', 1, 'manage_users.php', '::1', '2026-11-17 15:17:34'),
(160, 'visit', 1, 'inventory.php', '::1', '2026-11-17 15:17:35'),
(161, 'visit', 1, 'finances.php', '::1', '2026-11-17 15:17:36'),
(162, 'visit', 1, 'reports.php', '::1', '2026-11-17 15:17:38'),
(163, 'visit', 1, 'admin_dashboard.php', '::1', '2026-11-17 15:18:46'),
(164, 'visit', 1, 'admin_dashboard.php', '::1', '2026-11-17 15:20:11'),
(165, 'visit', NULL, 'login.php', '::1', '2026-11-17 15:21:36'),
(166, 'login', 4, 'login.php', '::1', '2026-11-17 15:21:45'),
(167, 'login', 4, 'login.php', '::1', '2026-11-17 15:22:12'),
(168, 'visit', 4, 'admin_dashboard.php', '::1', '2026-11-17 15:23:02'),
(169, 'visit', 4, 'login.php', '::1', '2026-11-17 15:23:02'),
(170, 'login', 4, 'login.php', '::1', '2026-11-17 15:23:07'),
(171, 'visit', 4, 'reports.php', '::1', '2026-11-17 15:23:22'),
(172, 'visit', 4, 'finances.php', '::1', '2026-11-17 15:23:23'),
(173, 'visit', 4, 'my_bookings.php', '::1', '2026-11-17 15:23:26'),
(174, 'visit', 4, 'my_bookings.php', '::1', '2026-11-17 15:36:45'),
(175, 'visit', 4, 'index.php', '::1', '2026-11-17 15:36:46'),
(176, 'visit', 4, 'gallery.php', '::1', '2026-11-17 15:36:49'),
(177, 'visit', 4, 'reviews.php', '::1', '2026-11-17 15:36:50'),
(178, 'visit', 4, 'about.php', '::1', '2026-11-17 15:36:51'),
(179, 'visit', NULL, 'login.php', '::1', '2026-11-17 15:36:53'),
(180, 'login', 4, 'login.php', '::1', '2026-11-17 15:37:07'),
(181, 'visit', 4, 'index.php', '::1', '2026-11-17 15:37:07'),
(182, 'visit', 4, 'my_bookings.php', '::1', '2026-11-17 15:38:05'),
(183, 'visit', NULL, 'login.php', '::1', '2026-11-17 15:38:33'),
(184, 'login', 1, 'login.php', '::1', '2026-11-17 15:38:43'),
(185, 'visit', 1, 'admin_dashboard.php', '::1', '2026-11-17 15:38:44'),
(186, 'visit', 1, 'reports.php', '::1', '2026-11-17 15:39:16'),
(187, 'visit', 1, 'reports.php', '::1', '2026-11-17 15:46:07'),
(188, 'visit', 1, 'reports.php', '::1', '2026-11-17 15:52:15'),
(189, 'visit', 1, 'index.php', '::1', '2026-11-17 15:52:47'),
(190, 'visit', 1, 'gallery.php', '::1', '2026-11-17 15:52:49'),
(191, 'visit', 1, 'reviews.php', '::1', '2026-11-17 15:52:50'),
(192, 'visit', 1, 'about.php', '::1', '2026-11-17 15:52:50'),
(193, 'visit', 1, 'admin_dashboard.php', '::1', '2026-11-17 15:52:51'),
(194, 'visit', 1, 'manage_users.php', '::1', '2026-11-17 15:52:52'),
(195, 'visit', NULL, 'login.php', '::1', '2026-11-17 16:48:19'),
(196, 'visit', NULL, 'login.php', '::1', '2026-11-17 16:56:07'),
(197, 'login', 1, 'login.php', '::1', '2026-11-17 16:57:03'),
(198, 'visit', 1, 'admin_dashboard.php', '::1', '2026-11-17 16:57:03'),
(199, 'visit', 1, 'index.php', '::1', '2026-11-17 16:57:18'),
(200, 'visit', 1, 'index.php', '::1', '2026-11-17 16:58:26'),
(201, 'visit', 1, 'gallery.php', '::1', '2026-11-17 16:59:15'),
(202, 'visit', 1, 'reviews.php', '::1', '2026-11-17 16:59:42'),
(203, 'visit', 1, 'about.php', '::1', '2026-11-17 17:00:01'),
(204, 'visit', 1, 'admin_dashboard.php', '::1', '2026-11-17 17:00:49'),
(205, 'visit', 1, 'admin_dashboard.php', '::1', '2026-11-17 17:02:33'),
(206, 'visit', 1, 'inventory.php', '::1', '2026-11-17 17:02:55'),
(207, 'visit', 1, 'finances.php', '::1', '2026-11-17 17:02:55'),
(208, 'visit', 1, 'reports.php', '::1', '2026-11-17 17:02:56'),
(209, 'visit', 1, 'gallery.php', '::1', '2026-11-17 17:21:31'),
(210, 'visit', 1, 'reviews.php', '::1', '2026-11-17 17:21:35'),
(211, 'visit', 1, 'about.php', '::1', '2026-11-17 17:21:38'),
(212, 'visit', 1, 'admin_dashboard.php', '::1', '2026-11-17 17:21:42'),
(213, 'visit', 1, 'manage_users.php', '::1', '2026-11-17 17:21:56'),
(214, 'visit', 1, 'inventory.php', '::1', '2026-11-17 17:21:59'),
(215, 'visit', 1, 'finances.php', '::1', '2026-11-17 17:22:05'),
(216, 'visit', 1, 'reports.php', '::1', '2026-11-17 17:22:21'),
(217, 'visit', 1, 'index.php', '::1', '2026-11-17 20:03:14'),
(218, 'visit', 1, 'index.php', '::1', '2026-11-17 20:05:43'),
(219, 'visit', 1, 'inventory.php', '::1', '2026-11-17 20:06:08'),
(220, 'visit', 1, 'manage_users.php', '::1', '2026-11-17 20:06:46'),
(221, 'visit', NULL, 'login.php', '::1', '2026-11-17 20:07:20'),
(222, 'visit', NULL, 'register.php', '::1', '2026-11-17 20:07:40'),
(223, 'visit', NULL, 'register.php', '::1', '2026-11-17 20:12:32'),
(224, 'visit', NULL, 'login.php', '::1', '2026-11-17 20:12:32'),
(225, 'login', 5, 'login.php', '::1', '2026-11-17 20:12:53'),
(226, 'visit', 5, 'index.php', '::1', '2026-11-17 20:12:53'),
(227, 'visit', NULL, 'login.php', '::1', '2026-11-17 20:13:38'),
(228, 'login', 1, 'login.php', '::1', '2026-11-17 20:14:03'),
(229, 'visit', 1, 'admin_dashboard.php', '::1', '2026-11-17 20:14:03'),
(230, 'visit', 1, 'inventory.php', '::1', '2026-11-17 20:14:07'),
(231, 'visit', 1, 'finances.php', '::1', '2026-11-17 20:14:22'),
(232, 'visit', 1, 'reports.php', '::1', '2026-11-17 20:14:42'),
(233, 'visit', 1, 'reports.php', '::1', '2026-11-17 20:17:28'),
(234, 'visit', 1, 'admin_dashboard.php', '::1', '2026-11-17 20:17:44'),
(235, 'visit', 1, 'gallery.php', '::1', '2026-11-17 20:17:52'),
(236, 'visit', 1, 'about.php', '::1', '2026-11-17 20:19:06'),
(237, 'visit', 1, 'about.php', '::1', '2026-11-18 08:49:45'),
(238, 'visit', 1, 'about.php', '::1', '2026-11-18 08:51:00'),
(239, 'visit', 1, 'gallery.php', '::1', '2026-11-18 08:51:30'),
(240, 'visit', 1, 'admin_dashboard.php', '::1', '2026-11-18 08:51:54'),
(241, 'visit', 1, 'admin_dashboard.php', '::1', '2026-11-18 08:52:59'),
(242, 'visit', 1, 'manage_users.php', '::1', '2026-11-18 09:08:24'),
(243, 'visit', 1, 'inventory.php', '::1', '2026-11-18 09:08:25'),
(244, 'visit', 1, 'finances.php', '::1', '2026-11-18 09:08:26'),
(245, 'visit', 1, 'reports.php', '::1', '2026-11-18 09:08:28'),
(246, 'visit', NULL, 'login.php', '::1', '2026-11-18 10:25:48'),
(247, 'login', 5, 'login.php', '::1', '2026-11-18 10:26:00'),
(248, 'visit', 5, 'index.php', '::1', '2026-11-18 10:26:00'),
(249, 'visit', NULL, 'login.php', '::1', '2026-11-18 10:26:53'),
(250, 'visit', NULL, 'index.php', '::1', '2026-11-18 10:27:01'),
(251, 'visit', NULL, 'gallery.php', '::1', '2026-11-18 10:27:23'),
(252, 'visit', NULL, 'reviews.php', '::1', '2026-11-18 10:27:27'),
(253, 'visit', NULL, 'about.php', '::1', '2026-11-18 10:27:43'),
(254, 'visit', NULL, 'register.php', '::1', '2026-11-18 10:27:51'),
(255, 'visit', NULL, 'index.php', '::1', '2026-11-18 12:19:00'),
(256, 'visit', NULL, 'index.php', '::1', '2026-11-18 12:28:08'),
(257, 'visit', NULL, 'chat_widget.php', '::1', '2026-11-18 12:29:49'),
(258, 'visit', NULL, 'index.php', '::1', '2026-11-18 12:37:12'),
(259, 'visit', NULL, 'index.php', '::1', '2026-11-18 12:39:00'),
(260, 'visit', NULL, 'chat_widget.php', '::1', '2026-11-18 12:39:58'),
(261, 'visit', NULL, 'chat_widget.php', '::1', '2026-11-18 12:41:32'),
(262, 'visit', NULL, 'gallery.php', '::1', '2026-11-18 12:59:44'),
(263, 'visit', NULL, 'reviews.php', '::1', '2026-11-18 12:59:48'),
(264, 'visit', NULL, 'login.php', '::1', '2026-11-18 12:59:52'),
(265, 'login', 1, 'login.php', '::1', '2026-11-18 12:59:57'),
(266, 'visit', 1, 'admin_dashboard.php', '::1', '2026-11-18 12:59:57'),
(267, 'visit', 1, 'manage_users.php', '::1', '2026-11-18 13:00:05'),
(268, 'visit', 1, 'inventory.php', '::1', '2026-11-18 13:00:09'),
(269, 'visit', 1, 'finances.php', '::1', '2026-11-18 13:00:46'),
(270, 'visit', 1, 'reports.php', '::1', '2026-11-18 13:01:15'),
(271, 'visit', 1, 'finances.php', '::1', '2026-11-18 13:01:57'),
(272, 'visit', 1, 'about.php', '::1', '2026-11-18 13:02:06'),
(273, 'visit', 1, 'about.php', '::1', '2026-11-18 14:30:07'),
(274, 'visit', 1, 'manage_users.php', '::1', '2026-11-18 14:30:30'),
(275, 'visit', 1, 'admin_dashboard.php', '::1', '2026-11-18 14:30:31'),
(276, 'visit', NULL, 'login.php', '::1', '2026-11-18 14:30:35'),
(277, 'login', 5, 'login.php', '::1', '2026-11-18 14:30:47'),
(278, 'visit', 5, 'index.php', '::1', '2026-11-18 14:30:47'),
(279, 'visit', 5, 'index.php', '::1', '2026-11-18 14:34:06'),
(280, 'visit', 5, 'index.php', '::1', '2026-11-18 14:50:30'),
(281, 'visit', 5, 'reviews.php', '::1', '2026-11-18 14:51:34'),
(282, 'visit', 5, 'index.php', '::1', '2026-11-18 14:51:39'),
(283, 'visit', 5, 'index.php', '::1', '2026-11-18 14:57:07'),
(284, 'visit', 5, 'reviews.php', '::1', '2026-11-18 15:00:06'),
(285, 'visit', 5, 'index.php', '::1', '2026-11-18 15:00:07'),
(286, 'visit', 5, 'login.php', '::1', '2026-11-18 15:00:08'),
(287, 'visit', 5, 'index.php', '::1', '2026-11-18 15:29:45'),
(288, 'visit', 5, 'index.php', '::1', '2026-11-18 15:43:11'),
(289, 'visit', 5, 'index.php', '::1', '2026-11-18 15:51:28'),
(290, 'visit', NULL, 'login.php', '::1', '2026-11-18 15:51:52'),
(291, 'login', 1, 'login.php', '::1', '2026-11-18 15:52:08'),
(292, 'visit', 1, 'admin_dashboard.php', '::1', '2026-11-18 15:52:08'),
(293, 'visit', 1, 'index.php', '::1', '2026-11-18 15:52:27'),
(294, 'visit', 1, 'index.php', '::1', '2026-11-18 15:54:25'),
(295, 'visit', 1, 'index.php', '::1', '2026-11-18 16:07:01'),
(296, 'visit', 1, 'index.php', '::1', '2026-11-18 16:12:58'),
(297, 'visit', 1, 'index.php', '::1', '2026-11-18 16:20:15'),
(298, 'visit', 1, 'index.php', '::1', '2026-11-18 16:22:09'),
(299, 'visit', NULL, 'login.php', '::1', '2026-11-18 16:22:34'),
(300, 'login', 5, 'login.php', '::1', '2026-11-18 16:23:14'),
(301, 'visit', 5, 'index.php', '::1', '2026-11-18 16:23:14'),
(302, 'visit', 5, 'payment_checkout.php', '::1', '2026-11-18 16:24:09'),
(303, 'visit', 5, 'payment_checkout.php', '::1', '2026-11-18 16:25:33'),
(304, 'visit', 5, 'index.php', '::1', '2026-11-18 16:26:14'),
(305, 'visit', 5, 'payment_checkout.php', '::1', '2026-11-18 16:26:56'),
(306, 'visit', NULL, 'login.php', '::1', '2026-11-18 16:30:02'),
(307, 'login', 1, 'login.php', '::1', '2026-11-18 16:30:18'),
(308, 'visit', 1, 'admin_dashboard.php', '::1', '2026-11-18 16:30:18'),
(309, 'visit', 1, 'voucher.php', '::1', '2026-11-18 16:32:18'),
(310, 'visit', 1, 'inventory.php', '::1', '2026-11-18 16:35:00'),
(311, 'visit', 1, 'admin_dashboard.php', '::1', '2026-11-18 16:35:06'),
(312, 'visit', 1, 'voucher.php', '::1', '2026-11-18 16:35:45'),
(313, 'visit', 1, 'voucher.php', '::1', '2026-11-18 16:51:41'),
(314, 'visit', 1, 'admin_dashboard.php', '::1', '2026-11-18 16:53:03'),
(315, 'visit', 1, 'voucher.php', '::1', '2026-11-18 16:53:18'),
(316, 'visit', 1, 'voucher.php', '::1', '2026-11-18 16:54:50'),
(317, 'visit', 1, 'admin_dashboard.php', '::1', '2026-11-18 16:54:53'),
(318, 'visit', 1, 'admin_dashboard.php', '::1', '2026-11-18 16:59:07'),
(319, 'visit', 1, 'voucher.php', '::1', '2026-11-18 16:59:14'),
(320, 'visit', 1, 'admin_dashboard.php', '::1', '2026-11-18 17:05:30'),
(321, 'visit', 1, 'voucher.php', '::1', '2026-11-18 17:05:39'),
(322, 'visit', 1, 'manage_users.php', '::1', '2026-11-18 17:06:08'),
(323, 'visit', 1, 'about.php', '::1', '2026-11-18 17:06:10'),
(324, 'visit', 1, 'admin_dashboard.php', '::1', '2026-11-18 17:31:05'),
(325, 'visit', 1, 'voucher.php', '::1', '2026-11-18 17:31:24'),
(326, 'visit', 1, 'admin_dashboard.php', '::1', '2026-11-18 17:34:01'),
(327, 'visit', 1, 'voucher.php', '::1', '2026-11-18 17:34:14'),
(328, 'visit', 1, 'admin_dashboard.php', '::1', '2026-11-18 17:35:06'),
(329, 'visit', 1, 'voucher.php', '::1', '2026-11-18 17:36:07'),
(330, 'visit', 1, 'admin_dashboard.php', '::1', '2026-11-18 17:37:07'),
(331, 'visit', 1, 'voucher.php', '::1', '2026-11-18 17:37:47'),
(332, 'visit', 1, 'index.php', '::1', '2026-11-18 17:43:01'),
(333, 'visit', 1, 'gallery.php', '::1', '2026-11-18 17:44:56'),
(334, 'visit', 1, 'index.php', '::1', '2026-11-18 17:45:04'),
(335, 'visit', 1, 'admin_dashboard.php', '::1', '2026-11-18 17:45:11'),
(336, 'visit', 1, 'manage_users.php', '::1', '2026-11-18 17:51:57'),
(337, 'visit', 1, 'admin_dashboard.php', '::1', '2026-11-18 17:52:03'),
(338, 'visit', 1, 'index.php', '::1', '2026-11-18 17:52:07'),
(339, 'visit', 1, 'index.php', '::1', '2026-11-18 18:14:57'),
(340, 'visit', 1, 'index.php', '::1', '2026-11-18 19:11:57'),
(341, 'visit', 1, 'index.php', '::1', '2026-11-18 19:17:22'),
(342, 'visit', 1, 'index.php', '::1', '2026-11-18 19:21:15'),
(343, 'visit', 1, 'index.php', '::1', '2026-11-18 19:25:58'),
(344, 'visit', 1, 'index.php', '::1', '2026-11-18 19:38:31'),
(345, 'visit', 1, 'index.php', '::1', '2026-11-18 19:40:04'),
(346, 'visit', 1, 'index.php', '::1', '2026-11-18 19:44:29'),
(347, 'visit', 1, 'index.php', '::1', '2026-11-18 20:31:53'),
(348, 'visit', 1, 'admin_dashboard.php', '::1', '2026-11-18 20:32:54'),
(349, 'visit', NULL, 'login.php', '::1', '2026-11-18 20:33:07'),
(350, 'visit', NULL, 'register.php', '::1', '2026-11-18 20:33:21'),
(351, 'visit', NULL, 'about.php', '::1', '2026-11-18 20:33:24'),
(352, 'visit', NULL, 'index.php', '::1', '2026-11-18 20:33:27'),
(353, 'visit', NULL, 'gallery.php', '::1', '2026-11-18 20:33:33'),
(354, 'visit', NULL, 'reviews.php', '::1', '2026-11-18 20:33:46'),
(355, 'visit', NULL, 'index.php', '::1', '2026-11-18 20:36:49'),
(356, 'visit', NULL, 'gallery.php', '::1', '2026-11-18 20:36:51'),
(357, 'visit', NULL, 'reviews.php', '::1', '2026-11-18 20:36:52'),
(358, 'visit', NULL, 'about.php', '::1', '2026-11-18 20:36:53'),
(359, 'visit', NULL, 'login.php', '::1', '2026-11-18 20:36:53'),
(360, 'visit', NULL, 'register.php', '::1', '2026-11-18 20:36:54'),
(361, 'visit', NULL, 'register.php', '::1', '2026-11-18 21:15:09'),
(362, 'visit', NULL, 'gallery.php', '::1', '2026-11-18 21:16:17'),
(363, 'visit', NULL, 'reviews.php', '::1', '2026-11-18 21:16:19'),
(364, 'visit', NULL, 'about.php', '::1', '2026-11-18 21:16:20'),
(365, 'visit', NULL, 'login.php', '::1', '2026-11-18 21:16:23'),
(366, 'visit', NULL, 'register.php', '::1', '2026-11-18 21:16:26'),
(367, 'visit', NULL, 'index.php', '::1', '2026-11-18 21:16:29'),
(368, 'visit', NULL, 'index.php', '::1', '2026-11-18 22:25:26'),
(369, 'visit', NULL, 'index.php', '::1', '2026-11-18 22:30:16'),
(370, 'visit', NULL, 'index.php', '::1', '2026-11-18 22:31:39'),
(371, 'visit', NULL, 'about.php', '::1', '2026-11-18 22:32:35'),
(372, 'visit', NULL, 'about.php', '::1', '2026-11-18 22:42:03'),
(373, 'visit', NULL, 'about.php', '::1', '2026-11-18 22:57:04'),
(374, 'visit', NULL, 'about.php', '::1', '2026-11-18 23:26:26'),
(375, 'visit', NULL, 'about.php', '::1', '2026-11-18 23:40:17'),
(376, 'visit', NULL, 'about.php', '::1', '2026-11-18 23:41:19'),
(377, 'visit', NULL, 'about.php', '::1', '2026-11-18 23:44:37'),
(378, 'visit', NULL, 'safari_map.php', '::1', '2026-11-18 23:44:42'),
(379, 'visit', NULL, 'gallery.php', '::1', '2026-11-18 23:45:05'),
(380, 'visit', NULL, 'index.php', '::1', '2026-11-18 23:45:11'),
(381, 'visit', NULL, 'reviews.php', '::1', '2026-11-18 23:45:20'),
(382, 'visit', NULL, 'register.php', '::1', '2026-11-18 23:45:34'),
(383, 'visit', NULL, 'login.php', '::1', '2026-11-18 23:45:37'),
(384, 'visit', NULL, 'about.php', '::1', '2026-11-18 23:45:40'),
(385, 'visit', NULL, 'about.php', '::1', '2026-11-18 23:46:45'),
(386, 'visit', NULL, 'gallery.php', '::1', '2026-11-18 23:47:03'),
(387, 'visit', NULL, 'index.php', '::1', '2026-11-18 23:47:07'),
(388, 'visit', NULL, 'about.php', '::1', '2026-11-18 23:53:58'),
(389, 'visit', NULL, 'about.php', '::1', '2026-11-18 23:57:07'),
(390, 'visit', NULL, 'about.php', '::1', '2026-11-19 00:04:19'),
(391, 'visit', NULL, 'about.php', '::1', '2026-11-19 00:09:54'),
(392, 'visit', NULL, 'index.php', '::1', '2026-11-19 00:11:21'),
(393, 'visit', NULL, 'gallery.php', '::1', '2026-11-19 00:11:28'),
(394, 'visit', NULL, 'gallery.php', '::1', '2026-11-19 00:29:59'),
(395, 'visit', NULL, 'gallery.php', '::1', '2026-11-19 00:31:03'),
(396, 'visit', NULL, 'index.php', '::1', '2026-11-19 00:31:26'),
(397, 'visit', NULL, 'safari_map.php', '::1', '2026-11-19 00:31:37'),
(398, 'visit', NULL, 'login.php', '::1', '2026-11-19 00:33:41'),
(399, 'login', 1, 'login.php', '::1', '2026-11-19 00:33:45'),
(400, 'visit', 1, 'admin_dashboard.php', '::1', '2026-11-19 00:33:45'),
(401, 'visit', 1, 'safari_map.php', '::1', '2026-11-19 00:35:55'),
(402, 'visit', 1, 'safari_map.php', '::1', '2026-11-19 00:41:54'),
(403, 'visit', 1, 'reviews.php', '::1', '2026-11-19 00:42:13'),
(404, 'visit', 1, 'safari_map.php', '::1', '2026-11-19 00:51:58'),
(405, 'visit', 1, 'safari_map.php', '::1', '2026-11-19 00:53:49'),
(406, 'visit', 1, 'payment_checkout.php', '::1', '2026-11-19 00:53:49'),
(407, 'visit', 1, 'payment_checkout.php', '::1', '2026-11-19 00:54:50'),
(408, 'visit', 1, 'payment_checkout.php', '::1', '2026-11-19 00:56:21'),
(409, 'visit', 1, 'safari_map.php', '::1', '2026-11-19 00:57:09'),
(410, 'visit', 1, 'safari_map.php', '::1', '2026-11-19 01:10:54'),
(411, 'visit', 1, 'safari_map.php', '::1', '2026-11-19 01:34:05'),
(412, 'visit', 1, 'safari_map.php', '::1', '2026-11-19 01:38:43'),
(413, 'visit', 1, 'safari_map.php', '::1', '2026-11-19 01:46:52'),
(414, 'visit', 1, 'payment_checkout.php', '::1', '2026-11-19 01:46:52'),
(415, 'visit', 1, 'payment_checkout.php', '::1', '2026-11-19 01:48:25'),
(416, 'visit', 1, 'safari_map.php', '::1', '2026-11-19 01:49:00'),
(417, 'visit', 1, 'admin_dashboard.php', '::1', '2026-11-19 01:49:47'),
(418, 'visit', 1, 'admin_dashboard.php', '::1', '2026-11-19 01:51:03'),
(419, 'visit', 1, 'voucher.php', '::1', '2026-11-19 01:51:28'),
(420, 'visit', 1, 'finances.php', '::1', '2026-11-19 01:52:11'),
(421, 'visit', 1, 'reports.php', '::1', '2026-11-19 01:52:43'),
(422, 'visit', 1, 'index.php', '::1', '2026-11-19 02:00:59'),
(423, 'visit', 1, 'safari_map.php', '::1', '2026-11-19 02:01:01'),
(424, 'visit', 1, 'reviews.php', '::1', '2026-11-19 02:01:03'),
(425, 'visit', 1, 'about.php', '::1', '2026-11-19 02:01:04'),
(426, 'visit', 1, 'admin_dashboard.php', '::1', '2026-11-19 02:01:06'),
(427, 'visit', 1, 'manage_users.php', '::1', '2026-11-19 02:01:08'),
(428, 'visit', 1, 'inventory.php', '::1', '2026-11-19 02:01:11'),
(429, 'visit', 1, 'gallery.php', '::1', '2026-11-19 02:01:16'),
(430, 'visit', 1, 'manage_users.php', '::1', '2026-11-19 02:02:40'),
(431, 'visit', 1, 'admin_dashboard.php', '::1', '2026-11-19 02:03:37'),
(432, 'visit', 1, 'admin_dashboard.php', '::1', '2026-11-19 02:07:22'),
(433, 'visit', 1, 'gallery.php', '::1', '2026-11-19 02:09:36'),
(434, 'visit', NULL, 'login.php', '::1', '2026-11-19 02:10:03'),
(435, 'login', 4, 'login.php', '::1', '2026-11-19 02:10:19'),
(436, 'visit', 4, 'index.php', '::1', '2026-11-19 02:10:19'),
(437, 'visit', 4, 'safari_map.php', '::1', '2026-11-19 02:10:44'),
(438, 'visit', 4, 'safari_map.php', '::1', '2026-11-19 02:14:00'),
(439, 'visit', 4, 'about.php', '::1', '2026-11-19 02:15:53'),
(440, 'visit', 4, 'about.php', '::1', '2026-11-19 02:20:31'),
(441, 'visit', NULL, 'login.php', '::1', '2026-11-19 02:21:33'),
(442, 'login', 1, 'login.php', '::1', '2026-11-19 02:21:43'),
(443, 'visit', 1, 'admin_dashboard.php', '::1', '2026-11-19 02:21:43'),
(444, 'visit', 1, 'admin_dashboard.php', '::1', '2026-11-19 02:31:54'),
(445, 'visit', 1, 'safari_map.php', '::1', '2026-11-19 02:35:44'),
(446, 'visit', 1, 'index.php', '::1', '2026-11-19 02:36:12'),
(447, 'visit', 1, 'inventory.php', '::1', '2026-11-19 02:36:50'),
(448, 'visit', 1, 'inventory.php', '::1', '2026-11-19 02:39:16'),
(449, 'visit', 1, 'inventory.php', '::1', '2026-11-19 02:42:14'),
(450, 'visit', 1, 'reports.php', '::1', '2026-11-19 02:42:28'),
(451, 'visit', 1, 'inventory.php', '::1', '2026-11-19 02:43:33'),
(452, 'visit', 1, 'inventory.php', '::1', '2026-11-19 02:58:24'),
(453, 'visit', 1, 'index.php', '::1', '2026-11-19 02:59:06'),
(454, 'visit', 1, 'index.php', '::1', '2026-11-19 03:02:36'),
(455, 'visit', 1, 'safari_map.php', '::1', '2026-11-19 03:02:54'),
(456, 'visit', 1, 'admin_dashboard.php', '::1', '2026-11-19 03:02:56'),
(457, 'visit', 1, 'login.php', '::1', '2026-11-19 03:02:58'),
(458, 'visit', 1, 'admin_dashboard.php', '::1', '2026-11-19 03:04:50'),
(459, 'visit', 1, 'index.php', '::1', '2026-11-19 03:05:05'),
(460, 'visit', 1, 'gallery.php', '::1', '2026-11-19 03:05:06'),
(461, 'visit', 1, 'safari_map.php', '::1', '2026-11-19 03:05:07'),
(462, 'visit', 1, 'reviews.php', '::1', '2026-11-19 03:05:08'),
(463, 'visit', 1, 'about.php', '::1', '2026-11-19 03:05:09'),
(464, 'visit', 1, 'manage_users.php', '::1', '2026-11-19 03:05:10'),
(465, 'visit', 1, 'inventory.php', '::1', '2026-11-19 03:05:11'),
(466, 'visit', 1, 'finances.php', '::1', '2026-11-19 03:05:12'),
(467, 'visit', 1, 'reports.php', '::1', '2026-11-19 03:05:13'),
(468, 'visit', 1, 'reports.php', '::1', '2026-11-19 03:06:34'),
(469, 'visit', 1, 'reports.php', '::1', '2026-11-19 03:43:35'),
(470, 'visit', 1, 'reports.php', '::1', '2026-11-19 03:44:48'),
(471, 'visit', 1, 'reports.php', '::1', '2026-11-19 03:46:08'),
(472, 'visit', 1, 'reports.php', '::1', '2026-11-19 03:48:33'),
(473, 'visit', 1, 'reports.php', '::1', '2026-11-19 03:56:26'),
(474, 'visit', 1, 'index.php', '::1', '2026-11-19 03:56:54'),
(475, 'visit', 1, 'gallery.php', '::1', '2026-11-19 03:56:57'),
(476, 'visit', 1, 'safari_map.php', '::1', '2026-11-19 03:57:03'),
(477, 'visit', 1, 'reviews.php', '::1', '2026-11-19 03:57:07'),
(478, 'visit', 1, 'about.php', '::1', '2026-11-19 03:57:35'),
(479, 'visit', 1, 'admin_dashboard.php', '::1', '2026-11-19 03:57:36'),
(480, 'visit', 1, 'manage_users.php', '::1', '2026-11-19 03:57:37'),
(481, 'visit', 1, 'inventory.php', '::1', '2026-11-19 03:57:38'),
(482, 'visit', 1, 'finances.php', '::1', '2026-11-19 03:57:39'),
(483, 'visit', 1, 'reports.php', '::1', '2026-11-19 03:57:40'),
(484, 'visit', 1, 'index.php', '::1', '2026-11-19 03:58:31'),
(485, 'visit', 1, 'reviews.php', '::1', '2026-11-19 03:58:33'),
(486, 'visit', 1, 'reviews.php', '::1', '2026-11-19 04:07:15'),
(487, 'visit', 1, 'reviews.php', '::1', '2026-11-19 04:12:03'),
(488, 'visit', 1, 'reviews.php', '::1', '2026-11-19 04:13:06'),
(489, 'visit', 1, 'reviews.php', '::1', '2026-11-19 04:15:15'),
(490, 'visit', 1, 'index.php', '::1', '2026-11-19 04:15:34'),
(491, 'visit', 1, 'reviews.php', '::1', '2026-11-19 04:23:07'),
(492, 'visit', 1, 'safari_map.php', '::1', '2026-11-19 04:23:24'),
(493, 'visit', 1, 'about.php', '::1', '2026-11-19 04:24:48'),
(494, 'visit', 1, 'admin_dashboard.php', '::1', '2026-11-19 04:24:49'),
(495, 'visit', 1, 'inventory.php', '::1', '2026-11-19 04:24:51'),
(496, 'visit', 1, 'finances.php', '::1', '2026-11-19 04:24:52'),
(497, 'visit', 1, 'reviews.php', '::1', '2026-11-19 04:24:54'),
(498, 'visit', 1, 'reviews.php', '::1', '2026-11-19 04:40:51'),
(499, 'visit', 1, 'chat_widget.php', '::1', '2026-11-19 04:41:46'),
(500, 'visit', 1, 'chat_widget.php', '::1', '2026-11-19 04:43:08'),
(501, 'visit', 1, 'chat_widget.php', '::1', '2026-11-19 04:44:48'),
(502, 'visit', 1, 'reviews.php', '::1', '2026-11-19 05:26:00'),
(503, 'visit', 1, 'reviews.php', '::1', '2026-11-19 05:35:42'),
(504, 'visit', 1, 'reviews.php', '::1', '2026-11-19 05:38:24'),
(505, 'visit', 1, 'reviews.php', '::1', '2026-11-19 05:45:41'),
(506, 'visit', 1, 'reviews.php', '::1', '2026-11-19 05:52:53'),
(507, 'visit', 1, 'about.php', '::1', '2026-11-19 05:53:14'),
(508, 'visit', 1, 'safari_map.php', '::1', '2026-11-19 05:53:19'),
(509, 'visit', 1, 'gallery.php', '::1', '2026-11-19 05:53:20'),
(510, 'visit', 1, 'index.php', '::1', '2026-11-19 05:53:22'),
(511, 'visit', 1, 'admin_dashboard.php', '::1', '2026-11-19 05:53:26'),
(512, 'visit', 1, 'manage_users.php', '::1', '2026-11-19 05:53:28'),
(513, 'visit', 1, 'inventory.php', '::1', '2026-11-19 05:53:29'),
(514, 'visit', 1, 'inventory.php', '::1', '2026-11-19 05:55:13'),
(515, 'visit', 1, 'inventory.php', '::1', '2026-11-19 06:15:33'),
(516, 'visit', 1, 'inventory.php', '::1', '2026-11-19 06:26:37'),
(517, 'visit', 1, 'weather_geography.php', '::1', '2026-11-19 06:26:49'),
(518, 'visit', 1, 'weather_geography.php', '::1', '2026-11-19 06:43:31'),
(519, 'visit', 1, 'weather_geography.php', '::1', '2026-11-19 06:54:42'),
(520, 'visit', 1, 'weather_geography.php', '::1', '2026-11-19 06:57:43'),
(521, 'visit', 1, 'weather_geography.php', '::1', '2026-11-19 07:04:45'),
(522, 'visit', 1, 'index.php', '::1', '2026-11-19 07:06:06'),
(523, 'visit', 1, 'safari_map.php', '::1', '2026-11-19 07:06:36'),
(524, 'visit', 1, 'gallery.php', '::1', '2026-11-19 07:06:39'),
(525, 'visit', 1, 'about.php', '::1', '2026-11-19 07:06:50'),
(526, 'visit', 1, 'weather_geography.php', '::1', '2026-11-19 07:07:00'),
(527, 'visit', 1, 'admin_dashboard.php', '::1', '2026-11-19 07:07:06'),
(528, 'visit', 1, 'manage_users.php', '::1', '2026-11-19 07:07:14'),
(529, 'visit', 1, 'inventory.php', '::1', '2026-11-19 07:07:19'),
(530, 'visit', 1, 'weather_geography.php', '::1', '2026-11-19 07:15:21'),
(531, 'visit', 1, 'weather_geography.php', '::1', '2026-11-19 07:21:54'),
(532, 'visit', 1, 'weather_geography.php', '::1', '2026-11-19 07:38:01'),
(533, 'visit', 1, 'weather_geography.php', '::1', '2026-11-19 07:57:25'),
(534, 'visit', 1, 'weather_geography.php', '::1', '2026-11-19 08:09:25'),
(535, 'visit', 1, 'weather_geography.php', '::1', '2026-11-19 08:12:00'),
(536, 'visit', 1, 'weather_geography.php', '::1', '2026-11-19 08:14:39'),
(537, 'visit', 1, 'weather_geography.php', '::1', '2026-11-19 08:35:52'),
(538, 'visit', 1, 'chat_widget.php', '::1', '2026-11-19 08:37:52'),
(539, 'visit', 1, 'index.php', '::1', '2026-11-19 08:38:20'),
(540, 'visit', 1, 'safari_map.php', '::1', '2026-11-19 08:38:37'),
(541, 'visit', 1, 'gallery.php', '::1', '2026-11-19 08:38:50'),
(542, 'visit', 1, 'reviews.php', '::1', '2026-11-19 08:38:52'),
(543, 'visit', 1, 'about.php', '::1', '2026-11-19 08:38:54'),
(544, 'visit', 1, 'about.php', '::1', '2026-11-19 08:45:53'),
(545, 'visit', 1, 'about.php', '::1', '2026-11-19 08:47:27'),
(546, 'visit', 1, 'about.php', '::1', '2026-11-19 08:48:48'),
(547, 'visit', NULL, 'login.php', '::1', '2026-11-19 08:50:11'),
(548, 'login', 4, 'login.php', '::1', '2026-11-19 08:50:54'),
(549, 'visit', 4, 'index.php', '::1', '2026-11-19 08:50:54'),
(550, 'visit', 4, 'weather_geography.php', '::1', '2026-11-19 08:57:38'),
(551, 'visit', 4, 'my_bookings.php', '::1', '2026-11-19 08:58:26'),
(552, 'visit', 4, 'voucher.php', '::1', '2026-11-19 08:58:46'),
(553, 'visit', 4, 'my_bookings.php', '::1', '2026-11-19 09:00:15'),
(554, 'visit', 4, 'weather_geography.php', '::1', '2026-11-19 09:00:24'),
(555, 'visit', 4, 'gallery.php', '::1', '2026-11-19 09:01:13'),
(556, 'visit', 4, 'reviews.php', '::1', '2026-11-19 09:01:15'),
(557, 'visit', 4, 'safari_map.php', '::1', '2026-11-19 09:01:16'),
(558, 'visit', 4, 'index.php', '::1', '2026-11-19 09:01:25'),
(559, 'visit', 4, 'index.php', '::1', '2026-11-19 10:01:18'),
(560, 'visit', 4, 'chat_widget.php', '::1', '2026-11-19 10:01:59'),
(561, 'visit', 4, 'index.php', '::1', '2026-11-19 16:04:07'),
(562, 'visit', 4, 'index.php', '::1', '2026-11-19 16:12:38'),
(563, 'visit', 4, 'index.php', '::1', '2026-11-19 16:18:13'),
(564, 'visit', 4, 'safari_map.php', '::1', '2026-11-19 16:18:37'),
(565, 'visit', 4, 'gallery.php', '::1', '2026-11-19 16:18:41'),
(566, 'visit', 4, 'reviews.php', '::1', '2026-11-19 16:18:43'),
(567, 'visit', 4, 'about.php', '::1', '2026-11-19 16:18:49'),
(568, 'visit', 4, 'weather_geography.php', '::1', '2026-11-19 16:18:53'),
(569, 'visit', 4, 'my_bookings.php', '::1', '2026-11-19 16:19:01'),
(570, 'visit', NULL, 'login.php', '::1', '2026-11-19 16:19:04'),
(571, 'login', 5, 'login.php', '::1', '2026-11-19 16:19:21'),
(572, 'visit', 5, 'index.php', '::1', '2026-11-19 16:19:21'),
(573, 'visit', NULL, 'login.php', '::1', '2026-11-19 17:15:31'),
(574, 'visit', NULL, 'login.php', '::1', '2026-11-19 17:25:08'),
(575, 'login', 1, 'login.php', '::1', '2026-11-19 17:25:08'),
(576, 'visit', 1, 'admin_dashboard.php', '::1', '2026-11-19 17:25:08'),
(577, 'visit', 1, 'index.php', '::1', '2026-11-19 17:25:27'),
(578, 'visit', 1, 'safari_map.php', '::1', '2026-11-19 17:26:38'),
(579, 'visit', 1, 'safari_map.php', '::1', '2026-11-19 17:28:25'),
(580, 'visit', 1, 'payment_checkout.php', '::1', '2026-11-19 17:28:25'),
(581, 'visit', 1, 'gallery.php', '::1', '2026-11-19 17:29:02'),
(582, 'visit', 1, 'reviews.php', '::1', '2026-11-19 17:29:32'),
(583, 'visit', 1, 'about.php', '::1', '2026-11-19 17:29:49'),
(584, 'visit', 1, 'weather_geography.php', '::1', '2026-11-19 17:30:47'),
(585, 'visit', 1, 'admin_dashboard.php', '::1', '2026-11-19 17:32:49'),
(586, 'visit', 1, 'admin_dashboard.php', '::1', '2026-11-19 17:34:28'),
(587, 'visit', 1, 'admin_dashboard.php', '::1', '2026-11-19 17:36:29'),
(588, 'visit', 1, 'voucher.php', '::1', '2026-11-19 17:36:53'),
(589, 'visit', 1, 'admin_dashboard.php', '::1', '2026-11-19 17:38:05'),
(590, 'visit', 1, 'admin_dashboard.php', '::1', '2026-11-19 17:39:21'),
(591, 'visit', 1, 'manage_users.php', '::1', '2026-11-19 17:40:03'),
(592, 'visit', 1, 'manage_users.php', '::1', '2026-11-19 17:41:14'),
(593, 'visit', 1, 'inventory.php', '::1', '2026-11-19 17:41:55'),
(594, 'visit', 1, 'inventory.php', '::1', '2026-11-19 17:45:11'),
(595, 'visit', 1, 'index.php', '::1', '2026-11-19 17:45:13'),
(596, 'visit', 1, 'index.php', '::1', '2026-11-19 17:49:20'),
(597, 'visit', 1, 'finances.php', '::1', '2026-11-19 17:57:54'),
(598, 'visit', 1, 'index.php', '::1', '2026-11-19 18:00:38'),
(599, 'visit', 1, 'admin_dashboard.php', '::1', '2026-11-19 18:01:20'),
(600, 'visit', 1, 'index.php', '::1', '2026-11-19 18:02:41'),
(601, 'visit', 1, 'index.php', '::1', '2026-11-19 18:05:17'),
(602, 'visit', 1, 'weather_geography.php', '::1', '2026-11-19 18:14:25'),
(603, 'visit', 1, 'admin_dashboard.php', '::1', '2026-11-19 18:15:53'),
(604, 'visit', 1, 'about.php', '::1', '2026-11-19 18:15:59'),
(605, 'visit', 1, 'reviews.php', '::1', '2026-11-19 18:15:59'),
(606, 'visit', 1, 'gallery.php', '::1', '2026-11-19 18:16:24'),
(607, 'visit', NULL, 'login.php', '::1', '2026-11-19 18:17:04'),
(608, 'visit', NULL, 'index.php', '::1', '2026-11-19 18:17:32'),
(609, 'visit', NULL, 'safari_map.php', '::1', '2026-11-19 18:17:36'),
(610, 'visit', NULL, 'gallery.php', '::1', '2026-11-19 18:17:56'),
(611, 'visit', NULL, 'reviews.php', '::1', '2026-11-19 18:18:00'),
(612, 'visit', NULL, 'about.php', '::1', '2026-11-19 18:18:03'),
(613, 'visit', NULL, 'weather_geography.php', '::1', '2026-11-19 18:18:08'),
(614, 'visit', NULL, 'register.php', '::1', '2026-11-19 18:18:18'),
(615, 'visit', NULL, 'register.php', '::1', '2026-11-19 18:22:28'),
(616, 'visit', NULL, 'login.php', '::1', '2026-11-19 18:22:28'),
(617, 'login', 6, 'login.php', '::1', '2026-11-19 18:22:49'),
(618, 'visit', 6, 'index.php', '::1', '2026-11-19 18:22:49'),
(619, 'visit', 6, 'index.php', '::1', '2026-11-19 18:24:12'),
(620, 'visit', 6, 'payment_checkout.php', '::1', '2026-11-19 18:24:12'),
(621, 'visit', 6, 'payment_checkout.php', '::1', '2026-11-19 18:26:19'),
(622, 'visit', NULL, 'login.php', '::1', '2026-11-19 18:27:10'),
(623, 'login', 1, 'login.php', '::1', '2026-11-19 18:27:24'),
(624, 'visit', 1, 'admin_dashboard.php', '::1', '2026-11-19 18:27:24'),
(625, 'visit', 1, 'admin_dashboard.php', '::1', '2026-11-19 18:28:54'),
(626, 'visit', 1, 'voucher.php', '::1', '2026-11-19 18:29:21'),
(627, 'visit', NULL, 'login.php', '::1', '2026-11-19 18:29:54'),
(628, 'login', 6, 'login.php', '::1', '2026-11-19 18:30:06'),
(629, 'visit', 6, 'index.php', '::1', '2026-11-19 18:30:06'),
(630, 'visit', 6, 'my_bookings.php', '::1', '2026-11-19 18:30:18'),
(631, 'visit', 6, 'voucher.php', '::1', '2026-11-19 18:31:02'),
(632, 'visit', 6, 'reviews.php', '::1', '2026-11-19 18:33:25'),
(633, 'visit', 6, 'gallery.php', '::1', '2026-11-19 18:48:49'),
(634, 'visit', 6, 'index.php', '::1', '2026-11-19 18:48:50'),
(635, 'visit', 6, 'reviews.php', '::1', '2026-11-19 18:48:53'),
(636, 'visit', 6, 'weather_geography.php', '::1', '2026-11-19 18:48:53'),
(637, 'visit', 6, 'my_bookings.php', '::1', '2026-11-19 18:48:54'),
(638, 'visit', NULL, 'login.php', '::1', '2026-11-19 18:48:55'),
(639, 'visit', NULL, 'index.php', '::1', '2026-11-19 19:01:55'),
(640, 'visit', NULL, 'index.php', '::1', '2026-11-22 13:44:01'),
(641, 'visit', NULL, 'index.php', '::1', '2026-11-22 14:00:15'),
(642, 'visit', NULL, 'safari_map.php', '::1', '2026-11-22 14:00:28'),
(643, 'visit', NULL, 'gallery.php', '::1', '2026-11-22 14:00:37'),
(644, 'visit', NULL, 'reviews.php', '::1', '2026-11-22 14:00:40'),
(645, 'visit', NULL, 'about.php', '::1', '2026-11-22 14:00:44'),
(646, 'visit', NULL, 'weather_geography.php', '::1', '2026-11-22 14:00:49'),
(647, 'visit', NULL, 'login.php', '::1', '2026-11-22 14:00:59'),
(648, 'login', 1, 'login.php', '::1', '2026-11-22 14:01:12'),
(649, 'visit', 1, 'admin_dashboard.php', '::1', '2026-11-22 14:01:12'),
(650, 'visit', 1, 'manage_users.php', '::1', '2026-11-22 14:01:35'),
(651, 'visit', 1, 'inventory.php', '::1', '2026-11-22 14:01:40'),
(652, 'visit', 1, 'finances.php', '::1', '2026-11-22 14:01:51'),
(653, 'visit', 1, 'reports.php', '::1', '2026-11-22 14:01:58'),
(654, 'visit', NULL, 'login.php', '::1', '2026-11-22 14:02:29'),
(655, 'login', 1, 'login.php', '::1', '2026-11-22 14:02:39'),
(656, 'visit', 1, 'admin_dashboard.php', '::1', '2026-11-22 14:02:39'),
(657, 'visit', 1, 'admin_dashboard.php', '::1', '2026-11-22 14:06:40'),
(658, 'visit', 1, 'weather_report.php', '::1', '2026-11-22 14:06:42'),
(659, 'visit', 1, 'reports.php', '::1', '2026-11-22 14:06:46'),
(660, 'visit', 1, 'reports.php', '::1', '2026-11-22 14:07:52'),
(661, 'visit', NULL, 'login.php', '::1', '2026-11-22 14:08:17'),
(662, 'login', 5, 'login.php', '::1', '2026-11-22 14:08:28'),
(663, 'visit', 5, 'index.php', '::1', '2026-11-22 14:08:28'),
(664, 'visit', 5, 'safari_map.php', '::1', '2026-11-22 14:09:03'),
(665, 'visit', 5, 'safari_map.php', '::1', '2026-11-22 14:10:26'),
(666, 'visit', 5, 'safari_map.php', '::1', '2026-11-22 14:16:20'),
(667, 'visit', 5, 'chat_api.php', '::1', '2026-11-22 14:16:41'),
(668, 'visit', 5, 'safari_map.php', '::1', '2026-11-22 14:18:46'),
(669, 'visit', 5, 'chat_api.php', '::1', '2026-11-22 14:18:56'),
(670, 'visit', 5, 'chat_api.php', '::1', '2026-11-22 14:20:18'),
(671, 'visit', 5, 'safari_map.php', '::1', '2026-11-22 14:35:31'),
(672, 'visit', 5, 'chat_api.php', '::1', '2026-11-22 14:35:54'),
(673, 'visit', 5, 'chat_api.php', '::1', '2026-11-22 14:37:51'),
(674, 'visit', 5, 'chat_api.php', '::1', '2026-11-22 14:38:56');

-- --------------------------------------------------------

--
-- Table structure for table `tour_packages`
--

CREATE TABLE `tour_packages` (
  `package_id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `destination` varchar(100) NOT NULL,
  `duration_days` int(11) NOT NULL,
  `price_usd` decimal(10,2) NOT NULL,
  `max_capacity` int(11) NOT NULL,
  `description` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `discount_percent` tinyint(3) UNSIGNED DEFAULT 0,
  `promo_validity` enum('WEEKENDS_AND_UG_HOLIDAYS') DEFAULT 'WEEKENDS_AND_UG_HOLIDAYS'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tour_packages`
--

INSERT INTO `tour_packages` (`package_id`, `title`, `destination`, `duration_days`, `price_usd`, `max_capacity`, `description`, `created_at`, `discount_percent`, `promo_validity`) VALUES
(1, 'Bwindi Gorilla Trekking Adventure', 'Bwindi Impenetrable Forest', 3, 850.00, 8, 'Guided mountain gorilla tracking experience with eco-lodge accommodation.', '2026-11-16 15:45:16', 20, 'WEEKENDS_AND_UG_HOLIDAYS'),
(2, 'Murchison Falls Wildlife & Nile Cruise', 'Murchison Falls National Park', 4, 620.00, 12, 'Big Five game drives and boat safari to the base of Murchison Falls.', '2026-11-16 15:45:16', 0, 'WEEKENDS_AND_UG_HOLIDAYS'),
(3, 'Jinja Source of the Nile White-Water Rafting', 'Jinja', 2, 240.00, 15, 'Full-day Grade 5 rafting, quad biking, and sunset boat cruise.', '2026-11-16 15:45:16', 15, 'WEEKENDS_AND_UG_HOLIDAYS'),
(4, 'Siipi falls mountain climbing and falls water viewing', 'Mount Elgon- Kapichorwa', 1, 200.00, 54, 'Viewing natural water falls spreading like spiles down to the cliff from the mountain top to the valley, take a cool moist weather under the falls and enjoy the experience of a life time', '2026-11-16 17:06:18', 0, 'WEEKENDS_AND_UG_HOLIDAYS'),
(5, 'Historical Kasubi Tombs', 'Mengo-Bulange', 1, 200.00, 100, 'Kasubi tombs are famous for the Historical reigning traditional kings of Buganda kingdoms. The visit will acquit you with all Historical records on the reign and fall of the former kings and their Tombs where they were laid to rest', '2026-11-16 18:08:00', 5, 'WEEKENDS_AND_UG_HOLIDAYS'),
(6, 'Kazinga channel park', 'Mweya safari park', 3, 750.00, 50, 'A beautiful place to call home, view Hippos, Rhinos, Birds, Elephants, Lions beautiful fresh water sceneries and a range of wild animals feeding at leisure near you in breathe taking lodging cottages', '2026-11-17 00:13:46', 10, 'WEEKENDS_AND_UG_HOLIDAYS'),
(7, 'Jinja Heritage site', 'Jinja', 3, 150.00, 3, 'This where you', '2026-11-17 17:02:33', 7, 'WEEKENDS_AND_UG_HOLIDAYS'),
(8, 'masaka rounge', 'Masaka district', 2, 300.00, 50, 'Drescribe the site details', '2026-11-19 17:34:28', 10, 'WEEKENDS_AND_UG_HOLIDAYS');

-- --------------------------------------------------------

--
-- Table structure for table `tour_waypoints`
--

CREATE TABLE `tour_waypoints` (
  `waypoint_id` int(11) NOT NULL,
  `place_name` varchar(150) NOT NULL,
  `category` enum('Tour Site','Hotel / Lodge','Camping Site','Landing Site') NOT NULL,
  `district_or_park` varchar(100) NOT NULL,
  `latitude` decimal(10,6) NOT NULL,
  `longitude` decimal(10,6) NOT NULL,
  `notes` varchar(255) DEFAULT '',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tour_waypoints`
--

INSERT INTO `tour_waypoints` (`waypoint_id`, `place_name`, `category`, `district_or_park`, `latitude`, `longitude`, `notes`, `created_at`) VALUES
(1, 'Pearl Trails Head Office (Nagojje)', 'Landing Site', 'Mukono District', 0.416700, 32.883300, 'Main Safari Departure & Fleet Dispatch Base', '2026-11-18 21:46:45'),
(2, 'Entebbe International Airport Landing', 'Landing Site', 'Wakiso / Entebbe', 0.042400, 32.443500, 'International Tourist Arrival & Charter Airstrip', '2026-11-18 21:46:45'),
(3, 'Bwindi Impenetrable National Park', 'Tour Site', 'Kanungu / Rubanda', -1.045100, 29.660200, 'Mountain Gorilla Trekking Trailhead', '2026-11-18 21:46:45'),
(4, 'Murchison Falls National Park', 'Tour Site', 'Nwoya / Masindi', 2.277500, 31.686100, 'Top of the Falls & Nile Game Drive Track', '2026-11-18 21:46:45'),
(5, 'Queen Elizabeth National Park', 'Tour Site', 'Kasese / Rubirizi', -0.191700, 30.088100, 'Kazinga Channel & Ishasha Tree-Climbing Lions', '2026-11-18 21:46:45'),
(6, 'Paraa Safari Lodge', 'Hotel / Lodge', 'Murchison Falls', 2.299400, 31.576100, 'Luxury Riverfront Safari Lodge', '2026-11-18 21:46:45'),
(7, 'Buhoma Lodge Bwindi', 'Hotel / Lodge', 'Bwindi Forest', -0.985200, 29.615500, 'Eco-Luxury Forest Lodge near Gorilla Briefing Point', '2026-11-18 21:46:45'),
(8, 'Red Chilli Rest Camp', 'Camping Site', 'Murchison Falls', 2.283900, 31.584400, 'Equipped Safari Tents & Overland Camp', '2026-11-18 21:46:45'),
(9, 'Ishasha Wilderness Camp', 'Camping Site', 'Queen Elizabeth NP', -0.612200, 29.668500, 'Riverside Tented Safari Camp', '2026-11-18 21:46:45'),
(10, 'Source of the Nile Jinja', 'Tour Site', 'Jinja', 5.000000, 22.000000, 'Move from kla to jinja is 86km by vehicle and can take only 1Hour', '2026-11-19 02:07:22');

-- --------------------------------------------------------

--
-- Table structure for table `uganda_holidays`
--

CREATE TABLE `uganda_holidays` (
  `holiday_id` int(11) NOT NULL,
  `holiday_date` date NOT NULL,
  `holiday_name` varchar(120) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `uganda_holidays`
--

INSERT INTO `uganda_holidays` (`holiday_id`, `holiday_date`, `holiday_name`, `is_active`) VALUES
(1, '2026-01-01', 'New Year\'s Day', 1),
(2, '2026-01-26', 'NRM Liberation Day', 1),
(3, '2026-02-16', 'Archbishop Janani Luwum Day', 1),
(4, '2026-03-08', 'International Women\'s Day', 1),
(5, '2026-03-20', 'Eid al-Fitr (Tentative)', 1),
(6, '2026-04-03', 'Good Friday', 1),
(7, '2026-04-06', 'Easter Monday', 1),
(8, '2026-05-01', 'Labour Day', 1),
(9, '2026-05-27', 'Eid al-Adha (Tentative)', 1),
(10, '2026-06-03', 'Uganda Martyrs\' Day', 1),
(11, '2026-06-09', 'National Heroes\' Day', 1),
(12, '2026-10-09', 'Uganda Independence Day', 1),
(13, '2026-12-25', 'Christmas Day', 1),
(14, '2026-12-26', 'Boxing Day', 1),
(15, '2027-01-01', 'New Year\'s Day', 1),
(16, '2027-01-26', 'NRM Liberation Day', 1),
(17, '2027-02-16', 'Archbishop Janani Luwum Day', 1),
(18, '2027-03-08', 'International Women\'s Day', 1),
(19, '2027-03-09', 'Eid al-Fitr (Tentative)', 1),
(20, '2027-03-26', 'Good Friday', 1),
(21, '2027-03-29', 'Easter Monday', 1),
(22, '2027-05-01', 'Labour Day', 1),
(23, '2027-05-16', 'Eid al-Adha (Tentative)', 1),
(24, '2027-06-03', 'Uganda Martyrs\' Day', 1),
(25, '2027-06-09', 'National Heroes\' Day', 1),
(26, '2027-10-09', 'Uganda Independence Day', 1),
(27, '2027-12-25', 'Christmas Day', 1),
(28, '2027-12-26', 'Boxing Day', 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `full_name` varchar(120) NOT NULL,
  `email` varchar(120) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('admin','tourist') DEFAULT 'tourist',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `account_status` enum('Active','Suspended','Flagged') DEFAULT 'Active',
  `flag_reason` varchar(255) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `nationality` varchar(80) DEFAULT 'Ugandan',
  `id_number` varchar(50) DEFAULT NULL,
  `gender` enum('Male','Female','Prefer not to say') DEFAULT NULL,
  `marital_status` varchar(30) DEFAULT NULL,
  `mobile_contact` varchar(30) DEFAULT NULL,
  `office_line` varchar(30) DEFAULT NULL,
  `designation` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `full_name`, `email`, `password_hash`, `role`, `created_at`, `account_status`, `flag_reason`, `dob`, `nationality`, `id_number`, `gender`, `marital_status`, `mobile_contact`, `office_line`, `designation`) VALUES
(1, 'System Admin', 'admin@pearltrails.ug', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', '2026-11-16 15:45:16', 'Active', NULL, NULL, 'Ugandan', NULL, NULL, NULL, NULL, NULL, NULL),
(4, 'kaburu Richard', 'kabururichard@trial.com', '$2y$10$QCkFh3I3gccGzDRJq0.Y/.59NHL4NNXrGlcjgr4lY3OenjHU86bB2', 'tourist', '2026-11-16 17:58:18', 'Active', NULL, NULL, 'Ugandan', NULL, NULL, NULL, NULL, NULL, NULL),
(5, 'Treecia Nuwemugabe', 'treecianuwemugabe@gmail.com', '$2y$10$Z8jrSgPIXbh4lG5jfS0cHuO2oJWJQ6/SB1K02hBRlWfIE0KbjUNM6', 'tourist', '2026-11-17 20:12:32', 'Active', NULL, '2001-04-30', 'Ugandan', 'CM 3457876556784', 'Female', 'Married', '0789657654', '034897654', 'Teacher'),
(6, 'Mr Ntulume Elish', 'ntulumeelisha@gmail.com', '$2y$10$P4n4mTjNGdWhOM4yAcPcne72y2F316lbi1XmSjSlaO4EM8pqFGtba', 'tourist', '2026-11-19 18:22:28', 'Active', NULL, '1996-10-19', 'Ugandan', 'CM45828385VH5', 'Male', 'Married', '0786598789', NULL, 'Clinic Officer');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_alerts`
--
ALTER TABLE `admin_alerts`
  ADD PRIMARY KEY (`alert_id`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `package_id` (`package_id`);

--
-- Indexes for table `climate_records`
--
ALTER TABLE `climate_records`
  ADD PRIMARY KEY (`record_id`);

--
-- Indexes for table `company_staff`
--
ALTER TABLE `company_staff`
  ADD PRIMARY KEY (`staff_id`);

--
-- Indexes for table `company_vehicles`
--
ALTER TABLE `company_vehicles`
  ADD PRIMARY KEY (`vehicle_id`),
  ADD UNIQUE KEY `plate_number` (`plate_number`);

--
-- Indexes for table `financial_ledger`
--
ALTER TABLE `financial_ledger`
  ADD PRIMARY KEY (`entry_id`);

--
-- Indexes for table `gallery`
--
ALTER TABLE `gallery`
  ADD PRIMARY KEY (`photo_id`),
  ADD KEY `package_id` (`package_id`);

--
-- Indexes for table `office_equipment`
--
ALTER TABLE `office_equipment`
  ADD PRIMARY KEY (`equip_id`);

--
-- Indexes for table `office_files`
--
ALTER TABLE `office_files`
  ADD PRIMARY KEY (`file_id`),
  ADD UNIQUE KEY `file_reference` (`file_reference`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD UNIQUE KEY `transaction_reference` (`transaction_reference`),
  ADD KEY `booking_id` (`booking_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `package_id` (`package_id`);

--
-- Indexes for table `system_logs`
--
ALTER TABLE `system_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `tour_packages`
--
ALTER TABLE `tour_packages`
  ADD PRIMARY KEY (`package_id`);

--
-- Indexes for table `tour_waypoints`
--
ALTER TABLE `tour_waypoints`
  ADD PRIMARY KEY (`waypoint_id`);

--
-- Indexes for table `uganda_holidays`
--
ALTER TABLE `uganda_holidays`
  ADD PRIMARY KEY (`holiday_id`),
  ADD UNIQUE KEY `holiday_date` (`holiday_date`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_alerts`
--
ALTER TABLE `admin_alerts`
  MODIFY `alert_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `climate_records`
--
ALTER TABLE `climate_records`
  MODIFY `record_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `company_staff`
--
ALTER TABLE `company_staff`
  MODIFY `staff_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `company_vehicles`
--
ALTER TABLE `company_vehicles`
  MODIFY `vehicle_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `financial_ledger`
--
ALTER TABLE `financial_ledger`
  MODIFY `entry_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `gallery`
--
ALTER TABLE `gallery`
  MODIFY `photo_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `office_equipment`
--
ALTER TABLE `office_equipment`
  MODIFY `equip_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `office_files`
--
ALTER TABLE `office_files`
  MODIFY `file_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `system_logs`
--
ALTER TABLE `system_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=675;

--
-- AUTO_INCREMENT for table `tour_packages`
--
ALTER TABLE `tour_packages`
  MODIFY `package_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `tour_waypoints`
--
ALTER TABLE `tour_waypoints`
  MODIFY `waypoint_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `uganda_holidays`
--
ALTER TABLE `uganda_holidays`
  MODIFY `holiday_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`package_id`) REFERENCES `tour_packages` (`package_id`) ON DELETE CASCADE;

--
-- Constraints for table `gallery`
--
ALTER TABLE `gallery`
  ADD CONSTRAINT `gallery_ibfk_1` FOREIGN KEY (`package_id`) REFERENCES `tour_packages` (`package_id`) ON DELETE SET NULL;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`booking_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`package_id`) REFERENCES `tour_packages` (`package_id`) ON DELETE CASCADE;

--
-- Constraints for table `system_logs`
--
ALTER TABLE `system_logs`
  ADD CONSTRAINT `system_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
