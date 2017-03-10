SET FOREIGN_KEY_CHECKS=0;



INSERT INTO `users` (`id`, `pseudo`, `pass` ) VALUES
(1, 'ling', 'pilates'),
(2, 'jerome', 'pilates');



INSERT INTO `project` (`id`, `name`, `current`, `users_id`) VALUES
(1, 'leaderfit-ecommerce', null, 1),
(6, 'leaderfit-mini', '67:2017-03-10 00:00:00', 1);



INSERT INTO `task` (`id`, `label`, `start_date`, `end_date`, `description`, `parent_task_id`, `done`, `project_id`, `order`, `color`) VALUES
(1, 'kamille', '2017-03-13 00:00:00', '2017-03-27 00:00:00', '', 21, 0, 1, 1, 'rgb(79, 16, 62)'),
(2, 'Architecture', '2017-03-13 00:00:00', '2017-03-17 00:00:00', '', 1, 0, 1, 0, 'rgb(79, 16, 62)'),
(3, 'Services', '2017-03-17 00:00:00', '2017-03-20 00:00:00', '', 1, 0, 1, 1, 'rgb(79, 16, 62)'),
(4, 'Modules and hooks', '2017-03-20 00:00:00', '2017-03-23 00:00:00', '', 1, 0, 1, 2, 'rgb(79, 16, 62)'),
(5, 'MVC', '2017-03-23 00:00:00', '2017-03-27 00:00:00', '', 1, 0, 1, 3, 'green'),
(6, 'Admin tools', '2017-03-27 00:00:00', '2017-04-17 00:00:00', '', 21, 0, 1, 2, '#907611'),
(7, 'Theme implementation', '2017-04-17 00:00:00', '2017-05-01 00:00:00', '', 21, 0, 1, 3, 'rgb(79, 16, 62)'),
(8, 'Nullos MVC', '2017-05-01 00:00:00', '2017-05-14 00:00:00', '', 21, 0, 1, 4, '#34851d'),
(9, 'Module e-commerce', '2017-05-14 00:00:00', '2017-06-15 00:00:00', '', 21, 0, 1, 5, '#c57fc1'),
(10, 'create datatables tools', '2017-03-27 00:00:00', '2017-04-03 00:00:00', '', 6, 0, 1, 0, '#907611'),
(11, 'create form tools', '2017-04-03 00:00:00', '2017-04-10 00:00:00', '', 6, 0, 1, 0, '#907611'),
(12, 'create generator tools (autoadmin)', '2017-04-10 00:00:00', '2017-04-17 00:00:00', '', 6, 0, 1, 0, '#907611'),
(13, 'test MVC - no css framework (basic html) - redo basic zilu interface', '2017-04-24 00:00:00', '2017-05-01 00:00:00', '', 7, 0, 1, 1, 'rgb(79, 16, 62)'),
(14, 'test MVC - bootstrap - https://colorlib.com/polygon/gentelella/index.html', '2017-04-17 00:00:00', '2017-04-24 00:00:00', '', 7, 0, 1, 0, 'rgb(79, 16, 62)'),
(15, 'création système import module', '2017-05-01 00:00:00', '2017-05-07 00:00:00', '', 8, 0, 1, 0, '#34851d'),
(16, 'création modules basiques de test', '2017-05-07 00:00:00', '2017-05-14 00:00:00', '', 8, 0, 1, 0, '#34851d'),
(17, 'conception module e-commerce', '2017-05-14 00:00:00', '2017-05-24 00:00:00', '', 9, 0, 1, 0, '#c57fc1'),
(18, 'implémentation maquette front - https://www.boulanger.com/', '2017-05-24 00:00:00', '2017-05-29 00:00:00', '', 9, 0, 1, 1, '#c57fc1'),
(19, 'implémentation pages backoffice', '2017-05-29 00:00:00', '2017-06-08 00:00:00', '', 9, 0, 1, 2, '#c57fc1'),
(21, 'all', '2017-03-13 00:00:00', '2017-06-15 00:00:00', '', NULL, 0, 1, 0, '#733a4a'),
(22, 'modules paiement', '2017-06-08 00:00:00', '2017-06-15 00:00:00', '', 9, 0, 1, 3, '#c57fc1'),
(65, 'all', '2017-03-10 00:00:00', '2017-05-14 00:00:00', '', NULL, 0, 6, 0, '#733a4a'),
(66, 'kamille', '2017-03-10 00:00:00', '2017-03-20 00:00:00', '', 65, 0, 6, 1, 'rgb(79, 16, 62)'),
(67, 'Architecture', '2017-03-10 00:00:00', '2017-03-13 00:00:00', '', 66, 0, 6, 0, 'rgb(79, 16, 62)'),
(68, 'Services', '2017-03-13 00:00:00', '2017-03-15 00:00:00', '', 66, 0, 6, 1, 'rgb(79, 16, 62)'),
(69, 'Modules and hooks', '2017-03-15 00:00:00', '2017-03-17 00:00:00', '', 66, 0, 6, 2, 'rgb(79, 16, 62)'),
(70, 'MVC', '2017-03-17 00:00:00', '2017-03-20 00:00:00', '', 66, 0, 6, 3, 'green'),
(71, 'Admin tools', '2017-03-20 00:00:00', '2017-04-01 00:00:00', '', 65, 0, 6, 2, '#907611'),
(72, 'create datatables tools', '2017-03-20 00:00:00', '2017-03-24 00:00:00', '', 71, 0, 6, 0, '#907611'),
(73, 'create form tools', '2017-03-24 00:00:00', '2017-03-28 00:00:00', '', 71, 0, 6, 0, '#907611'),
(74, 'create generator tools (autoadmin)', '2017-03-28 00:00:00', '2017-04-01 00:00:00', '', 71, 0, 6, 0, '#907611'),
(75, 'Theme implementation', '2017-04-01 00:00:00', '2017-04-09 00:00:00', '', 65, 0, 6, 3, 'rgb(79, 16, 62)'),
(76, 'test MVC - bootstrap - https://colorlib.com/polygon/gentelella/index.html', '2017-04-01 00:00:00', '2017-04-06 00:00:00', '', 75, 0, 6, 0, 'rgb(79, 16, 62)'),
(77, 'test MVC - no css framework (basic html) - redo basic zilu interface', '2017-04-06 00:00:00', '2017-04-09 00:00:00', '', 75, 0, 6, 1, 'rgb(79, 16, 62)'),
(78, 'Nullos MVC', '2017-04-09 00:00:00', '2017-04-17 00:00:00', '', 65, 0, 6, 4, '#34851d'),
(79, 'création système import module', '2017-04-09 00:00:00', '2017-04-13 00:00:00', '', 78, 0, 6, 0, '#34851d'),
(80, 'création modules basiques de test', '2017-04-13 00:00:00', '2017-04-17 00:00:00', '', 78, 0, 6, 0, '#34851d'),
(81, 'Module e-commerce', '2017-04-17 00:00:00', '2017-05-14 00:00:00', '', 65, 0, 6, 5, '#c57fc1'),
(82, 'conception module e-commerce', '2017-04-17 00:00:00', '2017-04-24 00:00:00', '', 81, 0, 6, 0, '#c57fc1'),
(83, 'implémentation maquette front - https://www.boulanger.com/', '2017-04-24 00:00:00', '2017-04-27 00:00:00', '', 81, 0, 6, 1, '#c57fc1'),
(84, 'implémentation pages backoffice', '2017-04-27 00:00:00', '2017-05-07 00:00:00', '', 81, 0, 6, 2, '#c57fc1'),
(85, 'modules paiement', '2017-05-07 00:00:00', '2017-05-14 00:00:00', '', 81, 0, 6, 3, '#c57fc1');



INSERT INTO `compte_mail` (`id`, `pseudo`, `email`) VALUES
(1, 'Chloé', 'chloe@leaderfit.com'),
(2, 'Nathalie', 'nathalie@leaderfit.com'),
(3, 'Delphine', 'delphine@leaderfit.com'),
(4, 'Jérôme', 'jerome@leaderfit.com'),
(5, 'Pierre', 'pierre@leaderfit.com'),
(6, 'Camille', 'camille@leaderfit.com');





SET FOREIGN_KEY_CHECKS=1;
