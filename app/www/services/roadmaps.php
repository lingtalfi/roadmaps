<?php


use QuickPdo\QuickPdo;
use Task\Task;
use Task\TaskUtil;

require_once __DIR__ . "/../../init.php";


$output = '';
$isHtml = false;
if (array_key_exists('action', $_GET)) {
    $action = $_GET['action'];
    switch ($action) {
        case 'calendrier-update-startdate':
        case 'calendrier-update-enddate':

            if (
                array_key_exists("id", $_POST) &&
                array_key_exists("date", $_POST) &&
                array_key_exists("hour", $_POST) &&
                array_key_exists("minute", $_POST)
            ) {
                $taskId = $_POST['id'];
                $date = $_POST['date'];
                $hour = $_POST['hour'];
                $minute = $_POST['minute'];


                $decaler = false;
                $startDate = null;
                if (array_key_exists("decaler", $_POST) && "true" === $_POST['decaler']) {
                    $decaler = true;
                }


                $fieldToUpdate = "";
                if ('calendrier-update-startdate' === $action) {
                    $fieldToUpdate = "start_date";
                } else {
                    $fieldToUpdate = "end_date";
                }


                $fullDate = $date . " $hour:$minute:00";


                if (true === $decaler) {

//                    $taskIds = TaskUtil::getTasksAfter($startDate);

                    $offset = 0;
                    $startDate = Task::getStartDate($taskId);
                    $originalTime = strtotime($startDate);
                    $newTime = strtotime($date);
                    $mysqlOp = "";
                    if ($newTime > $originalTime) {
                        $mysqlOp = "DATE_ADD";
                        $offset = $newTime - $originalTime;
                    } else {
                        $mysqlOp = "DATE_SUB";
                        $offset = $originalTime - $newTime;
                    }


                    $q = "
update task 
set 
start_date=$mysqlOp(start_date, INTERVAL $offset SECOND),                    
end_date=$mysqlOp(end_date, INTERVAL $offset SECOND)
where start_date >= '$startDate'                    
";
                    QuickPdo::freeExec($q);

                } else {
                    QuickPdo::update("task", [
                        $fieldToUpdate => $fullDate,
                    ], [
                        ["id", "=", $taskId]
                    ]);
                }


                $output = "ok";
            }

            break;
        default:
            break;
    }
}


if (false === $isHtml) {
    echo json_encode($output);
} else {
    echo $output;
}

