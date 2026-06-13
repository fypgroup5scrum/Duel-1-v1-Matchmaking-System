-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: sql206.infinityfree.com
-- Generation Time: Jun 11, 2026 at 11:41 AM
-- Server version: 11.4.12-MariaDB
-- PHP Version: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `if0_41849028_rps_game`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `admin_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`admin_id`, `username`, `password`, `created_at`) VALUES
(1, 'admin', '9b8769a4a742959a2d0298c36fb70623f2dfacda8436237df08d8dfd5b37374c', '2026-05-26 16:41:55');

-- --------------------------------------------------------

--
-- Table structure for table `matches`
--

CREATE TABLE `matches` (
  `match_id` int(11) NOT NULL,
  `player1_id` int(11) NOT NULL,
  `player2_id` int(11) NOT NULL,
  `player1_move` enum('rock','paper','scissors') DEFAULT NULL,
  `player2_move` enum('rock','paper','scissors') DEFAULT NULL,
  `winner_id` int(11) DEFAULT NULL,
  `result` enum('player1','player2','draw','pending') DEFAULT NULL,
  `match_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `matches`
--

INSERT INTO `matches` (`match_id`, `player1_id`, `player2_id`, `player1_move`, `player2_move`, `winner_id`, `result`, `match_date`) VALUES
(1, 18, 20, 'scissors', 'scissors', NULL, 'draw', '2026-06-01 14:27:31'),
(2, 18, 20, 'paper', 'paper', NULL, 'draw', '2026-06-01 14:27:54'),
(3, 18, 20, 'scissors', 'rock', 20, 'player2', '2026-06-01 14:42:23'),
(4, 18, 20, 'scissors', 'paper', 18, 'player1', '2026-06-01 14:44:08'),
(5, 18, 20, 'scissors', 'rock', 20, 'player2', '2026-06-01 14:44:53'),
(6, 18, 20, 'paper', 'paper', NULL, 'draw', '2026-06-01 14:46:51'),
(7, 18, 20, 'paper', 'paper', NULL, 'draw', '2026-06-01 14:54:03'),
(8, 18, 20, 'paper', 'paper', NULL, 'draw', '2026-06-01 15:07:23'),
(9, 18, 20, 'paper', 'paper', NULL, 'draw', '2026-06-01 15:12:29'),
(10, 18, 20, 'paper', 'paper', NULL, 'draw', '2026-06-01 15:17:48'),
(11, 18, 20, 'rock', 'paper', 20, 'player2', '2026-06-04 01:32:36'),
(12, 18, 20, 'scissors', 'rock', 20, 'player2', '2026-06-04 01:33:40'),
(13, 21, 18, 'scissors', 'rock', 18, 'player2', '2026-06-05 12:31:20'),
(14, 21, 18, 'paper', 'rock', 21, 'player1', '2026-06-05 12:31:51'),
(15, 18, 20, 'paper', 'rock', 18, 'player1', '2026-06-05 12:37:22'),
(16, 18, 20, 'scissors', 'scissors', NULL, 'draw', '2026-06-05 12:38:21');

-- --------------------------------------------------------

--
-- Table structure for table `match_history`
--

CREATE TABLE `match_history` (
  `history_id` int(11) NOT NULL,
  `match_id` int(11) DEFAULT NULL,
  `player_id` int(11) DEFAULT NULL,
  `opponent_id` int(11) DEFAULT NULL,
  `result` enum('win','loss','draw') DEFAULT NULL,
  `mmr_before` int(11) DEFAULT NULL,
  `mmr_after` int(11) DEFAULT NULL,
  `change_amount` int(11) DEFAULT NULL,
  `played_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `match_history`
--

INSERT INTO `match_history` (`history_id`, `match_id`, `player_id`, `opponent_id`, `result`, `mmr_before`, `mmr_after`, `change_amount`, `played_at`) VALUES
(1, 1, 18, 20, 'draw', 1000, 1000, 0, '2026-06-01 14:27:39'),
(2, 1, 20, 18, 'draw', 1000, 1000, 0, '2026-06-01 14:27:39'),
(3, 2, 18, 20, 'draw', 1000, 1000, 0, '2026-06-01 14:28:09'),
(4, 2, 20, 18, 'draw', 1000, 1000, 0, '2026-06-01 14:28:09'),
(5, 3, 18, 20, 'draw', 1000, 1000, 0, '2026-06-01 14:42:39'),
(6, 3, 20, 18, 'win', 1000, 1016, 16, '2026-06-01 14:42:39'),
(7, 4, 18, 20, 'win', 1000, 1017, 17, '2026-06-01 14:44:15'),
(8, 4, 20, 18, 'loss', 1016, 1000, -16, '2026-06-01 14:44:15'),
(9, 5, 18, 20, 'loss', 1017, 1000, -17, '2026-06-01 14:45:02'),
(10, 5, 20, 18, 'win', 1000, 1017, 17, '2026-06-01 14:45:02'),
(11, 6, 18, 20, 'loss', 1250, 1241, -9, '2026-06-01 14:47:01'),
(12, 6, 20, 18, 'win', 1017, 1026, 9, '2026-06-01 14:47:01'),
(13, 7, 18, 20, 'loss', 1241, 1232, -9, '2026-06-01 14:54:11'),
(14, 7, 20, 18, 'win', 1026, 1035, 9, '2026-06-01 14:54:11'),
(15, 8, 18, 20, 'draw', 1232, 1224, -8, '2026-06-01 15:07:31'),
(16, 8, 20, 18, 'draw', 1035, 1043, 8, '2026-06-01 15:07:31'),
(17, 8, 18, 20, 'draw', 1224, 1216, -8, '2026-06-01 15:07:35'),
(18, 8, 20, 18, 'draw', 1043, 1051, 8, '2026-06-01 15:07:35'),
(19, 9, 18, 20, 'draw', 1216, 1209, -7, '2026-06-01 15:12:38'),
(20, 9, 20, 18, 'draw', 1051, 1058, 7, '2026-06-01 15:12:38'),
(21, 9, 18, 20, 'draw', 1209, 1202, -7, '2026-06-01 15:12:39'),
(22, 9, 20, 18, 'draw', 1058, 1065, 7, '2026-06-01 15:12:39'),
(23, 10, 18, 20, 'draw', 1202, 1196, -6, '2026-06-01 15:17:54'),
(24, 10, 20, 18, 'draw', 1065, 1071, 6, '2026-06-01 15:17:54'),
(25, 11, 18, 20, 'loss', 1196, 1174, -22, '2026-06-04 01:32:58'),
(26, 11, 20, 18, 'win', 1071, 1093, 22, '2026-06-04 01:32:58'),
(27, 12, 18, 20, 'loss', 1174, 1154, -20, '2026-06-04 01:33:48'),
(28, 12, 20, 18, 'win', 1093, 1113, 20, '2026-06-04 01:33:48'),
(29, 13, 21, 18, 'loss', 1000, 1000, 0, '2026-06-05 12:31:29'),
(30, 13, 18, 21, 'win', 1154, 1163, 9, '2026-06-05 12:31:29'),
(31, 14, 21, 18, 'win', 1000, 1023, 23, '2026-06-05 12:32:44'),
(32, 14, 18, 21, 'loss', 1163, 1140, -23, '2026-06-05 12:32:44'),
(33, 15, 18, 20, 'win', 1140, 1155, 15, '2026-06-05 12:37:55'),
(34, 15, 20, 18, 'loss', 1113, 1098, -15, '2026-06-05 12:37:55'),
(35, 16, 18, 20, 'draw', 1155, 1152, -3, '2026-06-05 12:38:32'),
(36, 16, 20, 18, 'draw', 1098, 1101, 3, '2026-06-05 12:38:32');

-- --------------------------------------------------------

--
-- Table structure for table `players`
--

CREATE TABLE `players` (
  `player_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `mmr` int(11) DEFAULT 1000,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `reset_code` varchar(6) DEFAULT NULL,
  `code_expiry` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `players`
--

INSERT INTO `players` (`player_id`, `username`, `email`, `password`, `mmr`, `created_at`, `reset_code`, `code_expiry`) VALUES
(1, 'AliGaming', 'ali@gmail.com', 'pass123', 1000, '2026-05-06 06:23:18', NULL, NULL),
(2, 'AbuPro', 'abu@gmail.com', 'pass123', 1050, '2026-05-06 06:23:18', NULL, NULL),
(3, 'MuthuX', 'muthu@gmail.com', 'pass123', 980, '2026-05-06 06:23:18', NULL, NULL),
(4, 'SitiQueen', 'siti@gmail.com', 'pass123', 1100, '2026-05-06 06:23:18', NULL, NULL),
(17, 'sprint2', 'sprint2@gmail.com', '$2y$10$LZ.l8hR83kh5/b2.0VyDG.2MRcKo4R/OCf54af0IidH0pNNrIsEPS', 1000, '2026-05-18 01:49:56', NULL, NULL),
(18, 'Moon', 'mdfaizho@gmail.com', '$2y$10$.j13sfTk92dck9HxGn2djuMV3IXrtAgupsNgk2gY2fYNHhl/MYyj2', 1152, '2026-06-01 14:22:41', NULL, NULL),
(19, 'ajwad', 'ajwadnabil767@gmail.com', '$2y$10$4Uo/8Z5oE/eqJtGyxS9dGe1sxL2MFkkiL5yT7CNLqr79b7CB68iAe', 1000, '2026-06-01 14:25:38', NULL, NULL),
(20, 'OnlyRock', 'onlyrock@gmail.com', '$2y$10$/MmvLGejCmDUng3yw1B8MuAofzfZWddpPhsDazz5CrrL8riQqurvi', 1101, '2026-06-01 14:27:02', NULL, NULL),
(21, 'Test', 'test@gmail.com', '$2y$10$vyxxUhT/ZRAOp.C9gb2CMe8oPcZ8fO.blzwU5W2PvxpH0KAaSZF.a', 1023, '2026-06-05 12:31:01', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `queue`
--

CREATE TABLE `queue` (
  `queue_id` int(11) NOT NULL,
  `player_id` int(11) NOT NULL,
  `status` enum('waiting','matched') DEFAULT 'waiting',
  `joined_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `mmr` int(11) DEFAULT 1000,
  `rank_category` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `queue`
--

INSERT INTO `queue` (`queue_id`, `player_id`, `status`, `joined_at`, `mmr`, `rank_category`) VALUES
(62, 20, 'matched', '2026-06-01 14:42:21', 1000, 'Bronze'),
(64, 20, 'matched', '2026-06-01 14:43:56', 1016, 'Bronze'),
(66, 20, 'matched', '2026-06-01 14:44:39', 1000, 'Bronze'),
(70, 20, 'matched', '2026-06-01 14:46:36', 1017, 'Bronze'),
(73, 20, 'matched', '2026-06-01 14:53:55', 1026, 'Bronze'),
(75, 20, 'matched', '2026-06-01 15:06:46', 1035, 'Bronze'),
(78, 20, 'matched', '2026-06-01 15:12:12', 1051, 'Bronze'),
(81, 20, 'matched', '2026-06-01 15:17:37', 1065, 'Bronze'),
(84, 20, 'matched', '2026-06-04 01:32:22', 1071, 'Bronze'),
(87, 20, 'matched', '2026-06-04 01:33:30', 1093, 'Bronze'),
(94, 21, 'matched', '2026-06-05 12:31:49', 1000, 'Bronze'),
(96, 20, 'matched', '2026-06-05 12:37:07', 1113, 'Bronze'),
(98, 20, 'matched', '2026-06-05 12:38:09', 1098, 'Bronze');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `matches`
--
ALTER TABLE `matches`
  ADD PRIMARY KEY (`match_id`),
  ADD KEY `player1_id` (`player1_id`),
  ADD KEY `player2_id` (`player2_id`),
  ADD KEY `winner_id` (`winner_id`);

--
-- Indexes for table `match_history`
--
ALTER TABLE `match_history`
  ADD PRIMARY KEY (`history_id`),
  ADD KEY `player_id` (`player_id`);

--
-- Indexes for table `players`
--
ALTER TABLE `players`
  ADD PRIMARY KEY (`player_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `queue`
--
ALTER TABLE `queue`
  ADD PRIMARY KEY (`queue_id`),
  ADD KEY `player_id` (`player_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `matches`
--
ALTER TABLE `matches`
  MODIFY `match_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `match_history`
--
ALTER TABLE `match_history`
  MODIFY `history_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `players`
--
ALTER TABLE `players`
  MODIFY `player_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `queue`
--
ALTER TABLE `queue`
  MODIFY `queue_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=102;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `matches`
--
ALTER TABLE `matches`
  ADD CONSTRAINT `matches_ibfk_1` FOREIGN KEY (`player1_id`) REFERENCES `players` (`player_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `matches_ibfk_2` FOREIGN KEY (`player2_id`) REFERENCES `players` (`player_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `matches_ibfk_3` FOREIGN KEY (`winner_id`) REFERENCES `players` (`player_id`) ON DELETE SET NULL;

--
-- Constraints for table `match_history`
--
ALTER TABLE `match_history`
  ADD CONSTRAINT `match_history_ibfk_1` FOREIGN KEY (`player_id`) REFERENCES `players` (`player_id`);

--
-- Constraints for table `queue`
--
ALTER TABLE `queue`
  ADD CONSTRAINT `queue_ibfk_1` FOREIGN KEY (`player_id`) REFERENCES `players` (`player_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
