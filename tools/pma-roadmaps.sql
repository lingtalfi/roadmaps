INSERT INTO `project` (`id`, `name`) VALUES
(1, ''leaderfit-ecommerce'');



INSERT INTO `task` (`id`, `label`, `start_date`, `end_date`, `description`, `parent_task_id`, `done`, `project_id`, `order`) VALUES
(1, 'kamille', '2017-03-04 00:00:00', '2017-03-23 00:00:00', '', NULL, 0, 1, 0),
(2, 'Architecture', '2017-03-04 00:00:00', '2017-03-15 00:00:00', '', 1, 0, 1, 0),
(3, 'Services', '2017-03-04 00:00:00', '2017-03-20 00:00:00', '', 1, 0, 1, 0),
(4, 'Modules and hooks', '2017-03-04 00:00:00', '2017-03-15 00:00:00', '', 1, 0, 1, 0),
(5, 'MVC', '2017-03-04 00:00:00', '2017-03-16 00:00:00', '', 1, 0, 1, 0),
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
(19, 'implémentation pages backoffice', '2017-03-15 00:00:00', '2017-03-15 00:00:00', '', 9, 0, 1, 0),
(20, 'sub architecture', '2017-03-08 00:00:00', '2017-03-14 00:00:00', '', 2, 0, 1, 0);