<?php


use Backup\AppBackup;
use Calendar\CalendarApi;
use QuickPdo\QuickPdo;
use Task\Task;

require_once __DIR__ . "/../init.php";





$previousSiblingId = "21";
$q = "select MAX(`order`) as count from task where parent_task_id=" . (int)$previousSiblingId;


a(QuickPdo::fetch($q, [], \PDO::FETCH_COLUMN));