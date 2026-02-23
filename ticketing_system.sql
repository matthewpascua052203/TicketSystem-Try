-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: Feb 23, 2026 at 06:44 AM
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
-- Database: `ticketing_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `employee_tickets`
--

CREATE TABLE `employee_tickets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `category` varchar(100) NOT NULL,
  `priority` enum('Low','Medium','High','Critical') NOT NULL,
  `department` enum('IT','HR','Marketing','Admin','Technical','Accounting','Supply Chain','MPDC','E-Comm') DEFAULT NULL,
  `assigned_department` enum('IT','HR','Marketing','Admin','Technical','Accounting','Supply Chain','MPDC','E-Comm') DEFAULT NULL,
  `description` text DEFAULT NULL,
  `attachment` varchar(255) DEFAULT NULL,
  `status` enum('Open','In Progress','Resolved') DEFAULT 'Open',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_read` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employee_tickets`
--

INSERT INTO `employee_tickets` (`id`, `user_id`, `subject`, `category`, `priority`, `department`, `assigned_department`, `description`, `attachment`, `status`, `created_at`, `is_read`) VALUES
(1, 2, 'netowrk', 'Network Issue', 'Medium', 'IT', 'E-Comm', '', NULL, 'In Progress', '2026-02-21 08:12:09', 1),
(2, 2, 'issue yarn', 'Software Issue', 'Critical', 'IT', 'MPDC', '', NULL, 'Resolved', '2026-02-21 08:30:22', 1),
(3, 5, 'issue yarn', 'Email Problem', 'Low', 'Technical', 'E-Comm', '', NULL, 'In Progress', '2026-02-21 11:59:18', 1),
(4, 2, 'issue yarn', 'Account Access', 'Medium', 'IT', 'Supply Chain', 'dasdasdasd', '1771676254_6999a25e14011.pdf', 'Resolved', '2026-02-21 12:17:34', 1),
(5, 2, 'issue yarn', 'Network Issue', 'Critical', 'IT', 'IT', '', NULL, 'Open', '2026-02-22 06:42:02', 0),
(6, 4, 'issue yarn', 'Hardware Issue', 'Low', 'HR', 'HR', '', NULL, 'Open', '2026-02-22 07:35:56', 1),
(7, 4, 'issue yarn', 'Email Problem', 'Medium', 'HR', 'HR', '', NULL, 'Open', '2026-02-22 07:52:52', 0),
(8, 4, 'issue yarn', 'Hardware Issue', 'Low', 'HR', 'HR', '', NULL, 'Open', '2026-02-22 07:57:23', 0),
(9, 2, 'issue yarn', 'Hardware Issue', 'Low', 'IT', 'IT', '', NULL, 'Open', '2026-02-22 08:35:07', 0),
(10, 2, 'eto na ', 'Network Issue', 'Low', 'IT', 'IT', 'sdasd', '1771756846_699add2ec9ed2.pdf', 'Open', '2026-02-22 10:40:46', 0),
(11, 2, 'netowrk', 'Network Issue', 'Medium', 'IT', 'IT', 'sadsa', '1771757550_699adfeec0d19.pdf', 'Open', '2026-02-22 10:52:30', 0),
(12, 2, 'maybe ', 'Network Issue', 'High', 'IT', 'IT', 'sda', '1771759249_699ae6913e22b.pdf', 'Open', '2026-02-22 11:20:49', 1),
(13, 2, 'dasdsadsadasd', 'Hardware Issue', 'Medium', 'IT', 'IT', 'dasdas', '1771759847_699ae8e731514.pdf', 'Open', '2026-02-22 11:30:47', 1),
(14, 2, 'dasdsadsadasd', 'Hardware Issue', 'Medium', 'IT', 'IT', 'dasdas', '1771759854_699ae8ee561ec.pdf', 'Open', '2026-02-22 11:30:54', 1),
(15, 2, 'enzo ', 'Software Issue', 'Low', 'IT', 'IT', 'dasdasdas', '1771763096_699af598dd767.pdf', 'Open', '2026-02-22 12:24:56', 0),
(16, 2, 'rachelle ambayan', 'Software Issue', 'Medium', 'IT', 'IT', 'sdasdasdas', '1771805454_699b9b0eb711e.pdf', 'Open', '2026-02-23 00:10:54', 1),
(17, 7, 'dsada', 'Network Issue', 'Medium', 'Marketing', 'Marketing', 'asdasd', '1771807089_699ba171cce2d.pdf', 'Open', '2026-02-23 00:38:09', 0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `department` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','employee') DEFAULT 'employee',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `otp_code` varchar(10) DEFAULT NULL,
  `is_verified` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `department`, `password`, `role`, `created_at`, `otp_code`, `is_verified`) VALUES
(2, 'Matthew Pascua', 'matthewpascua22@gmail.com', 'IT', '$2y$10$3p7ZezP5KSiXjC.nxLqrzul1ylqHydI5o/g89hKOVoN0e/Keuit.i', 'employee', '2026-02-19 12:28:47', NULL, 0),
(3, 'Matthew Pascua', 'admin@gmail.com', 'IT', 'admin123', 'admin', '2026-02-19 13:24:09', NULL, 0),
(5, 'Ange herrera', 'ange.herrera@gmail.com', 'Technical', '$2y$10$Mh9ok2qbG3WgGEM7gYp2K.VBSTQCSqm8kxUE8CNh/Es86rWaf.vT.', 'employee', '2026-02-21 11:58:56', NULL, 0),
(6, 'Matthew Alexis Pascua', 'matthewpascua052203@gmail.com', 'IT', '12345', 'admin', '2026-02-22 08:01:48', NULL, 0),
(10, 'Enzo Mendoza', 'enzomendoza8teen@gmail.com', 'IT', '$2y$10$peLa8mt99ALYhY0Lm/xM9OEiKrQu6KUgstIGXwfaDGAX7tjwep9ny', 'employee', '2026-02-23 05:42:05', NULL, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `employee_tickets`
--
ALTER TABLE `employee_tickets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `employee_tickets`
--
ALTER TABLE `employee_tickets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
