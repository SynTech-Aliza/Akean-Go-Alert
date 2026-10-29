-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 29, 2026 at 08:07 AM
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
-- Database: `akean_go-alert`
--

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_ID` int(11) NOT NULL,
  `user_Profile` text NOT NULL,
  `user_FirstName` varchar(100) NOT NULL,
  `user_MiddleName` varchar(100) NOT NULL,
  `user_LastName` varchar(100) NOT NULL,
  `user_ContactNumber` varchar(100) NOT NULL,
  `user_CPName` varchar(100) NOT NULL,
  `user_CPNumber` varchar(100) NOT NULL,
  `user_EmailAddress` varchar(100) NOT NULL,
  `user_Password` varchar(255) NOT NULL,
  `user_Status` int(11) NOT NULL,
  `user_OTP` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_ID`, `user_Profile`, `user_FirstName`, `user_MiddleName`, `user_LastName`, `user_ContactNumber`, `user_CPName`, `user_CPNumber`, `user_EmailAddress`, `user_Password`, `user_Status`, `user_OTP`) VALUES
(1, 'Default.png', 'Ma. Aliza', 'Solano', 'Lomugdang', '09984840243', 'Mhyren N. Segui', '09365866398', 'layslomugdang@gmail.com', '$2y$10$LF7FXAmGCxFDf/HFo043m.mwhC./xqaFoU8jqDCwuGocPsZGnbgem', 1, NULL),
(2, 'Default.png', 'Juan', 'Santos', 'Dela Cruz', '09365866398', 'Juan Santos', '09368566398', 'ashilomugdang@gmail.com', '$2y$10$VXwraeDhzfZ26ocix6wa1uHa8nwETWWUMMi0Lr6ULWXfRBVYgDoJ.', 1, NULL),
(3, 'Default.png', 'Alex', 'Juan', 'Cruz', '09984840243', 'Maria Jose', '09984840243', 'mariaalizalomugdang@gmail.com', '$2y$10$Z7xKGUBuy3YNThCLwam5Y.BvRrlPKuZ7IWOEp3CfqMlmK0s6PTwmS', 1, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_ID`),
  ADD UNIQUE KEY `uq_users_email_address` (`user_EmailAddress`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
