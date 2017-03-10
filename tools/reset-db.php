<?php


use QuickPdo\QuickPdo;




QuickPdo::freeExec("drop database roadmaps");
QuickPdo::freeExec("create database roadmaps character set utf8 collate utf8_general_ci");
QuickPdo::freeExec(file_get_contents(__DIR__ . "/roadmaps.sql"));
QuickPdo::freeExec(file_get_contents(__DIR__ . "/pma-roadmaps.sql"));
require_once __DIR__ . "/crudify.php";










