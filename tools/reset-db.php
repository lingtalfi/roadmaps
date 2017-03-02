<?php


use QuickPdo\QuickPdo;




QuickPdo::freeExec(file_get_contents(__DIR__ . "/roadmaps.sql"));
require_once __DIR__ . "/crudify.php";










