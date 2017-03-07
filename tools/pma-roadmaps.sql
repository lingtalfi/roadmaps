INSERT INTO `project` (`id`, `name`) VALUES
(1, 'leaderfit-ecommerce');



INSERT INTO `task` (`id`, `label`, `start_date`, `end_date`, `description`, `parent_task_id`, `done`, `project_id`, `order`, `color`) VALUES
(1, 'kamille', '2017-03-14 00:00:00', '2017-03-28 00:00:00', '', NULL, 0, 1, 0, 'rgb(79, 16, 62)'),
(2, 'Architecture', '2017-03-19 00:00:00', '2017-03-26 00:00:00', '', 1, 0, 1, 6, 'rgb(79, 16, 62)'),
(3, 'Services', '2017-03-19 00:00:00', '2017-03-26 00:00:00', '', 1, 0, 1, 7, 'rgb(79, 16, 62)'),
(4, 'Modules and hooks', '2017-03-16 00:00:00', '2017-03-27 00:00:00', '', 1, 0, 1, 4, 'rgb(79, 16, 62)'),
(5, 'MVC', '2017-03-19 00:00:00', '2017-03-26 00:00:00', '', 1, 0, 1, 5, 'green'),
(6, 'Admin tools', '2017-04-12 00:00:00', '2017-05-11 00:00:00', '', NULL, 0, 1, 2, '#907611'),
(7, 'Theme implementation', '2017-04-15 00:00:00', '2017-04-21 00:00:00', '', NULL, 0, 1, 3, 'rgb(79, 16, 62)'),
(8, 'Nullos MVC', '2017-03-11 00:00:00', '2017-03-12 00:00:00', '', NULL, 0, 1, 4, 'rgb(79, 16, 62)'),
(9, 'Module e-commerce', '2017-03-29 00:00:00', '2017-05-01 00:00:00', '', NULL, 0, 1, 5, '#c57fc1'),
(10, 'create datatables tools', '2017-04-19 00:00:00', '2017-04-23 00:00:00', '', 6, 0, 1, 0, '#907611'),
(11, 'create form tools', '2017-04-12 00:00:00', '2017-05-01 00:00:00', '', 6, 0, 1, 0, '#907611'),
(12, 'create generator tools (autoadmin)', '2017-04-26 00:00:00', '2017-04-27 00:00:00', '', 6, 0, 1, 0, '#907611'),
(13, 'test MVC - no css framework (basic html) - redo basic zilu interface', '2017-04-20 00:00:00', '2017-04-21 00:00:00', '', 7, 0, 1, 1, 'rgb(79, 16, 62)'),
(14, 'test MVC - bootstrap - https://colorlib.com/polygon/gentelella/index.html', '2017-04-18 00:00:00', '2017-04-19 00:00:00', '', 7, 0, 1, 0, 'rgb(79, 16, 62)'),
(15, 'création système import module', '2017-03-11 00:00:00', '2017-03-12 00:00:00', '', 8, 0, 1, 0, 'rgb(79, 16, 62)'),
(16, 'création modules basiques de test', '2017-03-11 00:00:00', '2017-03-12 00:00:00', '', 8, 0, 1, 0, 'rgb(79, 16, 62)'),
(17, 'conception module e-commerce', '2017-03-29 00:00:00', '2017-05-01 00:00:00', '', 9, 0, 1, 0, '#c57fc1'),
(18, 'implémentation maquette front - https://www.boulanger.com/', '2017-04-08 05:25:35', '2017-04-08 00:00:00', '', 9, 0, 1, 1, '#c57fc1'),
(19, 'implémentation pages backoffice', '2017-04-08 00:00:00', '2017-04-09 00:00:00', '', 9, 0, 1, 2, '#c57fc1'),
(20, 'sub architecture', '2017-03-20 00:00:00', '2017-03-25 00:00:00', '', 2, 0, 1, 0, 'rgb(79, 16, 62)');