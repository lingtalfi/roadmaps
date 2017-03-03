-- phpMyAdmin SQL Dump
-- version 4.6.5.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Mar 03, 2017 at 05:16 PM
-- Server version: 5.6.35
-- PHP Version: 7.1.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `roadmaps`
--

-- --------------------------------------------------------

--
-- Table structure for table `task`
--

CREATE TABLE `task` (
  `id` int(11) NOT NULL,
  `label` varchar(128) NOT NULL,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `description` text NOT NULL,
  `parent_task_id` int(11) DEFAULT NULL,
  `done` tinyint(4) NOT NULL,
  `project_id` int(11) NOT NULL,
  `order` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `task`
--

INSERT INTO `task` (`id`, `label`, `start_date`, `end_date`, `description`, `parent_task_id`, `done`, `project_id`, `order`) VALUES
(1, 'kamille', '2017-03-04 00:00:00', '2017-03-22 00:00:00', '', NULL, 0, 1, 0),
(2, 'Architecture', '2017-03-07 00:00:00', '2017-03-15 00:00:00', '', 1, 0, 1, 0),
(3, 'Services', '2017-03-05 00:00:00', '2017-03-07 00:00:00', '', 1, 0, 1, 0),
(4, 'Modules and hooks', '2017-03-04 00:00:00', '2017-03-15 00:00:00', '', 1, 0, 1, 0),
(5, 'MVC', '2017-03-03 00:00:00', '2017-03-16 00:00:00', '', 1, 0, 1, 0),
(6, 'Admin tools', '2017-04-05 00:00:00', '2017-03-22 00:00:00', '', NULL, 0, 1, 0),
(7, 'Theme implementation', '2017-03-22 00:00:00', '2017-03-15 00:00:00', '', NULL, 0, 1, 0),
(8, 'Nullos MVC', '2017-03-15 00:00:00', '2017-03-15 00:00:00', '', NULL, 0, 1, 0),
(9, 'Module e-commerce', '2017-03-15 00:00:00', '2017-03-15 00:00:00', '', NULL, 0, 1, 0),
(10, 'create datatables tools', '2017-03-15 00:00:00', '2017-03-15 00:00:00', '', 6, 0, 1, 0),
(11, 'create form tools', '2017-03-15 00:00:00', '2017-03-15 00:00:00', '', 6, 0, 1, 0),
(12, 'create generator tools (autoadmin)', '2017-03-15 00:00:00', '2017-03-15 00:00:00', '', 6, 0, 1, 0),
(13, 'test MVC - no css framework (basic html) - redo basic zilu interface', '2017-03-15 00:00:00', '2017-03-15 00:00:00', '', 7, 0, 1, 0),
(14, 'test MVC - bootstrap - https://colorlib.com/polygon/gentelella/index.html', '2017-03-15 00:00:00', '2017-03-15 00:00:00', '', 7, 0, 1, 0),
(15, 'création système import module', '2017-03-15 00:00:00', '2017-03-15 00:00:00', '', 8, 0, 1, 0),
(16, 'création modules basiques de test', '2017-03-15 00:00:00', '2017-03-15 00:00:00', '', 8, 0, 1, 0),
(17, 'conception module e-commerce', '2017-03-15 00:00:00', '2017-03-15 00:00:00', '', 9, 0, 1, 0),
(18, 'implémentation maquette front - https://www.boulanger.com/', '2017-03-15 05:25:35', '2017-03-15 00:00:00', '', 9, 0, 1, 0),
(19, 'implémentation pages backoffice', '2017-03-15 00:00:00', '2017-03-15 00:00:00', '', 9, 0, 1, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `task`
--
ALTER TABLE `task`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_task_task_idx` (`parent_task_id`),
  ADD KEY `fk_task_project1_idx` (`project_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `task`
--
ALTER TABLE `task`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `task`
--
ALTER TABLE `task`
  ADD CONSTRAINT `fk_task_project1` FOREIGN KEY (`project_id`) REFERENCES `project` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_task_task` FOREIGN KEY (`parent_task_id`) REFERENCES `task` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
