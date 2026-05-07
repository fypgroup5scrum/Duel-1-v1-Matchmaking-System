-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 06, 2026 at 05:06 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `rps_game`
--

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
  `result` enum('player1','player2','draw') DEFAULT NULL,
  `match_date` timestamp NOT NULL DEFAULT current_timestamp()
) ;

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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `players`
--

INSERT INTO `players` (`player_id`, `username`, `email`, `password`, `mmr`, `created_at`) VALUES
(1, 'AliGaming', 'ali@gmail.com', 'pass123', 1000, '2026-05-06 06:23:18'),
(2, 'AbuPro', 'abu@gmail.com', 'pass123', 1050, '2026-05-06 06:23:18'),
(3, 'MuthuX', 'muthu@gmail.com', 'pass123', 980, '2026-05-06 06:23:18'),
(4, 'SitiQueen', 'siti@gmail.com', 'pass123', 1100, '2026-05-06 06:23:18'),
(5, 'AhmadLegend', 'ahmad@gmail.com', 'pass123', 1200, '2026-05-06 06:23:18');

-- --------------------------------------------------------

--
-- Table structure for table `queue`
--

CREATE TABLE `queue` (
  `queue_id` int(11) NOT NULL,
  `player_id` int(11) NOT NULL,
  `status` enum('waiting','matched') DEFAULT 'waiting',
  `joined_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `queue`
--

INSERT INTO `queue` (`queue_id`, `player_id`, `status`, `joined_at`) VALUES
(1, 1, 'waiting', '2026-05-06 06:26:08'),
(2, 2, 'waiting', '2026-05-06 06:26:08'),
(3, 3, 'waiting', '2026-05-06 06:26:08'),
(4, 4, 'waiting', '2026-05-06 06:26:08'),
(5, 5, 'waiting', '2026-05-06 06:26:08');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `matches`
--
ALTER TABLE `matches`
  ADD PRIMARY KEY (`match_id`),
  ADD KEY `player1_id` (`player1_id`),
  ADD KEY `player2_id` (`player2_id`),
  ADD KEY `winner_id` (`winner_id`);

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
-- AUTO_INCREMENT for table `matches`
--
ALTER TABLE `matches`
  MODIFY `match_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `players`
--
ALTER TABLE `players`
  MODIFY `player_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `queue`
--
ALTER TABLE `queue`
  MODIFY `queue_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

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
-- Constraints for table `queue`
--
ALTER TABLE `queue`
  ADD CONSTRAINT `queue_ibfk_1` FOREIGN KEY (`player_id`) REFERENCES `players` (`player_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
