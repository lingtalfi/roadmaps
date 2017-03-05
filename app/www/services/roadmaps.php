<?php


use Calendar\CalendarApi;
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
                $fullDate = $date . " $hour:$minute:00";
                if ('calendrier-update-startdate' === $action) {
                    CalendarApi::setStartDate($taskId, $fullDate);
                } else {
                    CalendarApi::setEndDate($taskId, $fullDate);
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

