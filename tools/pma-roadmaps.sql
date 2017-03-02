-- phpMyAdmin SQL Dump
-- version 4.6.5.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Mar 02, 2017 at 01:07 PM
-- Server version: 5.6.35
-- PHP Version: 7.1.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `roadmaps`
--

-- --------------------------------------------------------

--
-- Table structure for table `project`
--

CREATE TABLE `project` (
  `id` int(11) NOT NULL,
  `name` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `project`
--

INSERT INTO `project` (`id`, `name`) VALUES
(1, 'leaderfit-ecommerce');

-- --------------------------------------------------------

--
-- Table structure for table `task`
--

CREATE TABLE `task` (
  `id` int(11) NOT NULL,
  `label` varchar(128) NOT NULL,
  `nb_hours_min` int(11) DEFAULT NULL,
  `nb_hours_max` int(11) DEFAULT NULL,
  `description` text NOT NULL,
  `parent_task_id` int(11) DEFAULT NULL,
  `done_date` datetime DEFAULT NULL,
  `project_id` int(11) NOT NULL,
  `order` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `task`
--

INSERT INTO `task` (`id`, `label`, `nb_hours_min`, `nb_hours_max`, `description`, `parent_task_id`, `done_date`, `project_id`, `order`) VALUES
(1, 'kamille', 0, 0, 'Première implémentation du framework kam (https://github.com/lingtalfi/kam)', NULL, NULL, 1, 0),
(2, 'Architecture', 0, 0, '', 1, NULL, 1, 0),
(7, 'Services', 0, 0, '', 1, NULL, 1, 0),
(8, 'Modules and hooks', 0, 0, '', 1, NULL, 1, 0),
(9, 'MVC', 0, 0, '', 1, NULL, 1, 0),
(13, 'Admin tools', 0, 0, '', NULL, NULL, 1, 0),
(14, 'Theme implementation', 0, 0, '', NULL, NULL, 1, 0),
(15, 'Nullos MVC', 0, 0, '', NULL, NULL, 1, 0),
(16, 'Module e-commerce', 0, 0, '', NULL, NULL, 1, 0),
(17, 'create datatables tools', 0, 0, '', 13, NULL, 1, 0),
(18, 'create form tools', 0, 0, '', 13, NULL, 1, 0),
(19, 'create generator tools (autoadmin)', 0, 0, '', 13, NULL, 1, 0),
(20, 'test MVC - no css framework (basic html) - redo basic zilu interface', 0, 0, 'test MVC - no css framework (basic html) - redo basic zilu interface', 14, NULL, 1, 0),
(21, 'test MVC - bootstrap - https://colorlib.com/polygon/gentelella/index.html', 0, 0, 'test MVC - bootstrap - https://colorlib.com/polygon/gentelella/index.html', 14, NULL, 1, 0),
(22, 'création système import module', 0, 0, '', 15, NULL, 1, 0),
(23, 'création modules basiques de test', 0, 0, '', 15, NULL, 1, 0),
(24, 'conception module e-commerce', 0, 0, '', 16, NULL, 1, 0),
(25, 'implémentation maquette front - https://www.boulanger.com/', 0, 0, '', 16, NULL, 1, 0),
(26, 'implémentation pages backoffice', 0, 0, '', 16, NULL, 1, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `project`
--
ALTER TABLE `project`
  ADD PRIMARY KEY (`id`);

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
-- AUTO_INCREMENT for table `project`
--
ALTER TABLE `project`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `task`
--
ALTER TABLE `task`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `task`
--
ALTER TABLE `task`
  ADD CONSTRAINT `fk_task_project1` FOREIGN KEY (`project_id`) REFERENCES `project` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_task_task` FOREIGN KEY (`parent_task_id`) REFERENCES `task` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;
