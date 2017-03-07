<?php


use Calendar\CalendarApi;
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
                $fullDate = $date . " $hour:$minute:00";
                if ('calendrier-update-startdate' === $action) {
                    CalendarApi::setStartDate($taskId, $fullDate);
                } else {
                    CalendarApi::setEndDate($taskId, $fullDate);
                }
                $output = "ok";
            }

            break;
        case 'calendrier-update-left':
        case 'calendrier-update-right':
        case 'calendrier-update-grab':

            if (
                array_key_exists("id", $_POST) &&
                array_key_exists("offsetLeft", $_POST) &&
                array_key_exists("offsetRight", $_POST)
            ) {
                $taskId = $_POST['id'];
                $offsetLeft = $_POST['offsetLeft'];
                $offsetRight = $_POST['offsetRight'];

                if ('calendrier-update-left' === $action) {
                    $time = Task::getStartTime($taskId);
                    $time += $offsetLeft;
                    CalendarApi::setStartDate($taskId, gmdate("Y-m-d H:i:s", $time));
                } elseif ('calendrier-update-right' === $action) {
                    $time = Task::getEndTime($taskId);
                    $time += $offsetRight;
                    CalendarApi::setEndDate($taskId, gmdate("Y-m-d H:i:s", $time));
                } elseif ('calendrier-update-grab' === $action) {
                    CalendarApi::move($taskId, $offsetLeft);
                }


                $output = "ok";
            }

            break;
        case 'calendrier-sort-up':
            if (array_key_exists("id", $_GET)) {
                $taskId = $_GET['id'];
                CalendarApi::sortUp($taskId);
                $output = "ok";
            }
            break;
        case 'calendrier-sort-down':
            if (array_key_exists("id", $_GET)) {
                $taskId = $_GET['id'];
                CalendarApi::sortDown($taskId);
                $output = "ok";
            }
            break;
        case 'calendrier-task-create':
            if (
                array_key_exists("projectId", $_POST) &&
                array_key_exists("parentId", $_POST) &&
                array_key_exists("label", $_POST) &&
                array_key_exists("date", $_POST) &&
                array_key_exists("hour", $_POST) &&
                array_key_exists("minute", $_POST) &&
                array_key_exists("duration", $_POST) &&
                array_key_exists("position", $_POST)
            ) {

                $projectId = $_POST['projectId'];
                $parentId = $_POST['parentId'];
                $label = $_POST['label'];
                $date = $_POST['date'];
                $hour = $_POST['hour'];
                $minute = $_POST['minute'];
                $duration = $_POST['duration'];
                $position = $_POST['position'];


                $startDate = $date . " $hour:$minute:00";;
                TaskUtil::insertByDuration($projectId, $startDate, $duration, $parentId, $label, $position);

                $output = "ok";
            }
            break;
        case 'calendrier-remove-task':
            if (array_key_exists("id", $_GET)) {

                $id = $_GET['id'];
                Task::delete($id);

                $output = "ok";
            }
            break;
        case 'calendrier-task-update':
            if (
                array_key_exists("id", $_POST) &&
                array_key_exists("label", $_POST) &&
                array_key_exists("color", $_POST)
            ) {

                $id = $_POST['id'];
                $label = $_POST['label'];
                $color = $_POST['color'];
                $applyColorToChildren = ("true" === $_POST['applyColorToChildren']) ? true : false;


                Task::update($id, [
                    "label" => $label,
                    "color" => $color,
                ], $applyColorToChildren);
                $output = "ok";
            }
            break;
        case 'calendrier-update-period':
            if (
                array_key_exists("date_start", $_POST) &&
                array_key_exists("interval", $_POST) &&
                array_key_exists("segments", $_POST)
            ) {

                $dateStart = $_POST['date_start'];
                $interval = $_POST['interval'];
                $segments = $_POST['segments'];

                $_SESSION['periodStartDate'] = $dateStart;
                $_SESSION['periodInterval'] = $interval;
                $_SESSION['periodNbSegments'] = $segments;

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

