<?php


use QuickPdo\QuickPdo;



QuickPdo::freeExec("drop table if exists task");
QuickPdo::freeExec("drop table if exists project");
QuickPdo::freeExec(file_get_contents(__DIR__ . "/roadmaps.sql"));
QuickPdo::freeExec(file_get_contents(__DIR__ . "/pma-roadmaps.sql"));
require_once __DIR__ . "/crudify.php";










