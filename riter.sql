-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 13, 2018 at 02:10 PM
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
-- Table structure for table `pr_bookmarks`
--

CREATE TABLE `pr_bookmarks` (
  `user_id` bigint(11) NOT NULL,
  `post_id` bigint(11) NOT NULL,
  `bookmark_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `pr_comments`
--

CREATE TABLE `pr_comments` (
  `id` bigint(11) NOT NULL,
  `parent_comment_id` bigint(11) NOT NULL DEFAULT '0',
  `user_id` bigint(11) NOT NULL,
  `post_id` bigint(11) NOT NULL,
  `comment` text NOT NULL,
  `comment_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `pr_followers`
--

CREATE TABLE `pr_followers` (
  `user_id` bigint(11) NOT NULL,
  `follower_id` bigint(11) NOT NULL,
  `follow_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `pr_likes`
--

CREATE TABLE `pr_likes` (
  `post_id` bigint(11) NOT NULL,
  `user_id` bigint(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `pr_notifications`
--

CREATE TABLE `pr_notifications` (
  `id` bigint(11) NOT NULL,
  `user_id` bigint(9) NOT NULL COMMENT 'trigger user id',
  `receiver_id` int(9) DEFAULT NULL,
  `event_id` smallint(4) NOT NULL COMMENT 'event triggered',
  `reference_type_id` smallint(3) DEFAULT NULL COMMENT 'reference i.e, post or comment',
  `reference_id` bigint(11) DEFAULT NULL COMMENT 'post or comment id',
  `notification_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `seen` tinyint(4) NOT NULL DEFAULT '0',
  `reference_name` varchar(120) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `pr_notification_events`
--

CREATE TABLE `pr_notification_events` (
  `id` smallint(3) NOT NULL,
  `name` varchar(60) NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `pr_notification_events`
--

INSERT INTO `pr_notification_events` (`id`, `name`, `enabled`) VALUES
(1, 'LIKE', 1),
(2, 'COMMENT', 1),
(3, 'FOLLOW', 1),
(4, 'BOOKMARK', 1),
(5, 'SHARE', 1),
(6, 'DELETE', 1);

-- --------------------------------------------------------

--
-- Table structure for table `pr_notification_reference_type`
--

CREATE TABLE `pr_notification_reference_type` (
  `id` smallint(4) NOT NULL,
  `name` varchar(60) NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `pr_notification_reference_type`
--

INSERT INTO `pr_notification_reference_type` (`id`, `name`, `enabled`) VALUES
(1, 'STORY', 1),
(2, 'COMMENT', 1);

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

-- --------------------------------------------------------

--
-- Table structure for table `pr_posts`
--

CREATE TABLE `pr_posts` (
  `id` bigint(11) NOT NULL,
  `user_id` bigint(11) NOT NULL,
  `title` varchar(60) NOT NULL,
  `body` mediumtext NOT NULL,
  `post_date` datetime NOT NULL,
  `modified_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `pr_sessions`
--

CREATE TABLE `pr_sessions` (
  `id` bigint(11) NOT NULL,
  `user_id` bigint(11) NOT NULL,
  `token` varchar(64) NOT NULL,
  `login_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_logout` tinyint(4) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `pr_sessions`
--

INSERT INTO `pr_sessions` (`id`, `user_id`, `token`, `login_time`, `is_logout`) VALUES
(1, 2, 'MihBAk9mWKYAR1uZBvdB10C481vBhFei', '2018-02-13 12:47:11', 0);

-- --------------------------------------------------------

--
-- Table structure for table `pr_users`
--

CREATE TABLE `pr_users` (
  `id` bigint(11) NOT NULL,
  `avtar` varchar(100) NOT NULL DEFAULT 'default.png',
  `first_name` varchar(40) NOT NULL,
  `last_name` varchar(40) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(256) NOT NULL,
  `reader_points` int(9) NOT NULL DEFAULT '0',
  `writer_points` int(9) NOT NULL DEFAULT '0',
  `registered_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `blocked` tinyint(1) NOT NULL DEFAULT '0',
  `email_verified` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `pr_users`
--

INSERT INTO `pr_users` (`id`, `avtar`, `first_name`, `last_name`, `email`, `password`, `reader_points`, `writer_points`, `registered_date`, `active`, `blocked`, `email_verified`) VALUES
(2, 'default.png', 'Gulfam', 'Ali', 'aligulfam6@gmail.com', '$2y$12$OKTpi9cIAO6KpYHcl3JlqeNL.0vZmqNFsCO/U4PIPViUHYXBF4gmm', 0, 0, '2018-02-13 12:47:01', 1, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `pr_views`
--

CREATE TABLE `pr_views` (
  `id` bigint(11) NOT NULL,
  `user_id` bigint(11) NOT NULL,
  `post_id` bigint(11) NOT NULL,
  `view_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `pr_bookmarks`
--
ALTER TABLE `pr_bookmarks`
  ADD PRIMARY KEY (`user_id`,`post_id`);

--
-- Indexes for table `pr_comments`
--
ALTER TABLE `pr_comments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pr_followers`
--
ALTER TABLE `pr_followers`
  ADD PRIMARY KEY (`user_id`,`follower_id`);

--
-- Indexes for table `pr_likes`
--
ALTER TABLE `pr_likes`
  ADD PRIMARY KEY (`post_id`,`user_id`);

--
-- Indexes for table `pr_notifications`
--
ALTER TABLE `pr_notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pr_notification_events`
--
ALTER TABLE `pr_notification_events`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pr_notification_reference_type`
--
ALTER TABLE `pr_notification_reference_type`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pr_password_reset`
--
ALTER TABLE `pr_password_reset`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pr_posts`
--
ALTER TABLE `pr_posts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pr_sessions`
--
ALTER TABLE `pr_sessions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pr_users`
--
ALTER TABLE `pr_users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pr_views`
--
ALTER TABLE `pr_views`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`,`post_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `pr_comments`
--
ALTER TABLE `pr_comments`
  MODIFY `id` bigint(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pr_notifications`
--
ALTER TABLE `pr_notifications`
  MODIFY `id` bigint(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pr_notification_events`
--
ALTER TABLE `pr_notification_events`
  MODIFY `id` smallint(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `pr_notification_reference_type`
--
ALTER TABLE `pr_notification_reference_type`
  MODIFY `id` smallint(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `pr_password_reset`
--
ALTER TABLE `pr_password_reset`
  MODIFY `id` int(6) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pr_posts`
--
ALTER TABLE `pr_posts`
  MODIFY `id` bigint(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pr_sessions`
--
ALTER TABLE `pr_sessions`
  MODIFY `id` bigint(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `pr_users`
--
ALTER TABLE `pr_users`
  MODIFY `id` bigint(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `pr_views`
--
ALTER TABLE `pr_views`
  MODIFY `id` bigint(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
