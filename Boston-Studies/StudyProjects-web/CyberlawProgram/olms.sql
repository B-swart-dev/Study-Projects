-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jun 10, 2024 at 04:03 PM
-- Server version: 8.0.31
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `olms`
--

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `book_id` int NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `author` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `quantity` int NOT NULL,
  `image_path` varchar(255) COLLATE utf8mb4_general_ci DEFAULT 'images/noimg.png',
  `status` enum('Available','None in Library','Preview Only') COLLATE utf8mb4_general_ci NOT NULL,
  `category` enum('Fantasy','Horror','Educational','IT','Romance','Not Specified') COLLATE utf8mb4_general_ci DEFAULT 'Not Specified'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`book_id`, `title`, `author`, `quantity`, `image_path`, `status`, `category`) VALUES
(1, 'Control Your Mind and Master Your Feelings', 'Eric Robertson', 20, 'images/control_your_mind.jpg', 'Preview Only', 'Not Specified'),
(2, 'Court of Mist and Fury', 'Sarah J. Maas', 0, 'images/court_of_mist_fury.jpg', 'None in Library', 'Not Specified'),
(3, 'Harry Potter and the Goblet of Fire', 'J.K. Rowling', 1, 'images/harry_potter.jpg', 'None in Library', 'Not Specified'),
(4, 'Si Lo Crees, Lo Creas', 'Brian Tracy', 0, 'images/si_lo_crees.jpg', 'None in Library', 'Not Specified'),
(5, 'Game of Thrones', 'George R.R. Martin', 19, 'images/game_of_thrones.jpg', 'Available', 'Not Specified'),
(8, 'test', 'test', 1, 'images/test.jpg', 'Preview Only', 'Not Specified'),
(9, 'Test2', 'test', 0, 'images/noimg.png', 'None in Library', 'IT');

-- --------------------------------------------------------

--
-- Table structure for table `loans`
--

CREATE TABLE `loans` (
  `loan_id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `book_id` int DEFAULT NULL,
  `loan_date` date DEFAULT NULL,
  `return_date` timestamp NULL DEFAULT NULL,
  `status` enum('Borrowed','Returned') COLLATE utf8mb4_general_ci DEFAULT 'Borrowed'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `loans`
--

INSERT INTO `loans` (`loan_id`, `user_id`, `book_id`, `loan_date`, `return_date`, `status`) VALUES
(3, 1, 5, NULL, '2024-06-02 12:15:27', 'Returned'),
(4, 1, 5, NULL, '2024-06-02 13:09:17', 'Returned'),
(5, 1, 5, NULL, '2024-06-02 13:47:47', 'Returned'),
(6, 1, 5, NULL, '2024-06-02 17:01:43', 'Returned'),
(7, 1, 5, NULL, '2024-06-09 19:43:13', 'Returned'),
(8, 3, 9, NULL, NULL, 'Borrowed');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `role` enum('admin','user') COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `surname` varchar(100) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `role`, `email`, `name`, `surname`) VALUES
(1, 'librarian', 'Password123!', 'admin', 'librarian@example.com', 'Test', 'Tester'),
(3, 'User', 'user', 'user', 'user@email.com', 'user', 'user');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`book_id`);

--
-- Indexes for table `loans`
--
ALTER TABLE `loans`
  ADD PRIMARY KEY (`loan_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `book_id` (`book_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `book_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `loans`
--
ALTER TABLE `loans`
  MODIFY `loan_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `loans`
--
ALTER TABLE `loans`
  ADD CONSTRAINT `loans_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `loans_ibfk_2` FOREIGN KEY (`book_id`) REFERENCES `books` (`book_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
