-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 07, 2018 at 02:08 PM
-- Server version: 10.1.30-MariaDB
-- PHP Version: 5.6.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `riter`
--

-- --------------------------------------------------------

--
-- Table structure for table `pr_password_reset`
--

CREATE TABLE `pr_password_reset` (
  `id` int(6) NOT NULL,
  `user_id` int(9) NOT NULL,
  `reset_code` mediumint(5) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `expired` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `pr_password_reset`
--

INSERT INTO `pr_password_reset` (`id`, `user_id`, `reset_code`, `created_date`, `expired`) VALUES
(1, 6, 60760, '2018-02-07 12:41:46', 0),
(2, 7, 26472, '2018-02-07 13:02:30', 0),
(3, 6, 56965, '2018-02-07 13:06:23', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `pr_password_reset`
--
ALTER TABLE `pr_password_reset`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `pr_password_reset`
--
ALTER TABLE `pr_password_reset`
  MODIFY `id` int(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
