<?php


use AppUser\AppUser;
use Backup\AppBackup;
use Cache\Cache;
use Calendar\CalendarApi;
use MailHelper\MailHelper;
use Project\Project;
use QuickPdo\QuickPdo;
use Task\Task;
use Task\TaskUtil;
use UserHasTask\UserHasTask;
use Util\GeneralUtil;

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
                array_key_exists("offsetRight", $_POST) &&
                array_key_exists("alignedStart", $_POST) &&
                array_key_exists("alignedEnd", $_POST)
            ) {
                $taskId = $_POST['id'];
                $offsetLeft = $_POST['offsetLeft'];
                $offsetRight = $_POST['offsetRight'];
                $alignedStart = $_POST['alignedStart'];
                $alignedEnd = $_POST['alignedEnd'];


                if ('calendrier-update-left' === $action) {
//                    $time = Task::getStartTime($taskId);
                    $time = $alignedStart;
                    $time += $offsetLeft;
                    CalendarApi::setStartDate($taskId, gmdate("Y-m-d H:i:s", $time));
                } elseif ('calendrier-update-right' === $action) {
//                    $time = Task::getEndTime($taskId);
                    $time = $alignedEnd;
                    $time += $offsetRight;
                    CalendarApi::setEndDate($taskId, gmdate("Y-m-d H:i:s", $time));
                } elseif ('calendrier-update-grab' === $action) {
                    CalendarApi::move($taskId, $offsetLeft, $alignedEnd);
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
                array_key_exists("color", $_POST) &&
                array_key_exists("position", $_POST)
            ) {


                $projectId = $_POST['projectId'];
                $parentId = $_POST['parentId'];
                $label = $_POST['label'];
                $date = $_POST['date'];
                $hour = $_POST['hour'];
                $minute = $_POST['minute'];
                $duration = $_POST['duration'];
                $color = $_POST['color'];
                $position = $_POST['position'];
                $compteMailIds = (array_key_exists('compte_mail', $_POST)) ? $_POST['compte_mail'] : [];
                if (!is_array($compteMailIds)) {
                    $compteMailIds = [];
                }


                $startDate = $date . " $hour:$minute:00";;
                $id = TaskUtil::insertByDuration($projectId, $startDate, $duration, $parentId, $label, $color, $position);
                if (false !== $id && count($compteMailIds) > 0) {
                    $userId = AppUser::getUser();
                    UserHasTask::insert($userId, $id, $compteMailIds, 0);
                }

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


                $compteMailIds = (array_key_exists('compte_mail', $_POST)) ? $_POST['compte_mail'] : [];


                Task::update($id, [
                    "label" => $label,
                    "color" => $color,
                ], $applyColorToChildren);

                if (count($compteMailIds) > 0) {
                    $userId = AppUser::getUser();
                    UserHasTask::update($userId, $id, $compteMailIds, 0);
                }


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
        case 'calendrier-move-period':
            if (array_key_exists("direction", $_GET)
            ) {
                $direction = $_GET['direction'];
                $interval = (array_key_exists("periodInterval", $_SESSION)) ? $_SESSION['periodInterval'] : 86400;
                $date = (array_key_exists("periodStartDate", $_SESSION)) ? $_SESSION['periodStartDate'] : date("Y-m-d 00:00:00");

                $time = GeneralUtil::gmMysqlToTime($date);

                if ('prev' === $direction) {
                    $time -= $interval;
                } else {
                    $time += $interval;
                }

                $newDate = gmdate("Y-m-d 00:00:00", $time);
                $_SESSION['periodStartDate'] = $newDate;


                $output = "ok";
            }
            break;
        case 'calendrier-change-parent':
            if (
                array_key_exists("dir", $_GET) &&
                array_key_exists("id", $_GET)
            ) {
                $dir = $_GET['dir'];
                $id = $_GET['id'];

                if ("left" === $dir) {
                    Task::parentLevelUp($id);
                } elseif (array_key_exists("prev", $_GET)) {
                    Task::parentLevelDown($id, $_GET['prev']);
                }


                $output = "ok";
            }
            break;
        case 'calendrier-project-create':
            if (array_key_exists("name", $_POST)) {

                $userId = $_SESSION['user_selected'];
                $name = $_POST['name'];
                if (false !== ($id = Project::insert([
                        "name" => $name,
                        "users_id" => $userId,
                    ]))
                ) {
                    $_SESSION['project_id'] = $id;
                }
                $output = "ok";
            }
            break;
        case 'calendrier-project-duplicate':
            if (
                array_key_exists("name", $_POST) &&
                array_key_exists("id", $_POST)
            ) {
                $id = $_POST['id'];
                $name = $_POST['name'];

                if (false !== ($id = Project::duplicate($id, $name))) {
                    $output = "ok";
                }
            }
            break;
        case 'calendrier-project-delete':
            if (array_key_exists("id", $_POST)) {
                $id = $_POST['id'];

                if (false !== ($id = Project::delete($id))) {
                    $output = "ok";
                }
            }
            break;
        case 'calendrier-project-change':
            if (array_key_exists("id", $_GET)) {
                $id = $_GET['id'];
                $_SESSION['project_id'] = $id;
                $output = "ok";
            }
            break;
        case 'calendrier-all-save':
            if (array_key_exists('pid', $_POST)) {
                $projectId = $_POST['pid'];
                $name = null;
                if (array_key_exists("name", $_POST)) {
                    $name = "manual/" . $_POST["name"];
                }
                Cache::saveProjectTasks($projectId);
                $output = "ok";
            }
            break;
        case 'calendrier-all-restore':
            $name = null;
            if (array_key_exists("name", $_POST)) {

                $name = $_POST["name"];
                $file = APP_ROOT_DIR . "/backup/" . $name;
                if (file_exists($file)) {
                    QuickPdo::freeExec(file_get_contents($file));
                    $output = "ok";
                }
            }
            break;
        case 'calendrier-open-container':
        case 'calendrier-close-container':
            if (array_key_exists("id", $_GET)) {
                if (!array_key_exists("taskOpenStates", $_SESSION)) {
                    $_SESSION['taskOpenStates'] = [];
                }
                $value = ('calendrier-open-container' === $action) ? true : false;
                $_SESSION["taskOpenStates"][$_GET['id']] = $value;
                $output = "ok";
            }
            break;
        case 'calendrier-project-setcursor':
            if (
                array_key_exists("time", $_GET) &&
                array_key_exists("task_id", $_GET) &&
                array_key_exists("project_id", $_GET)
            ) {
                $datetime = gmdate("Y-m-d H:i:s", $_GET['time']);
                $taskId = $_GET['task_id'];
                $projectId = $_GET['project_id'];

                $cursor = $taskId . ":" . $datetime;
                Project::setCursor($projectId, $cursor);

                $output = "ok";
            }
            break;
        case 'calendrier-users-change':
            if (array_key_exists("id", $_GET)) {
                $id = $_GET['id'];
                $_SESSION["user_selected"] = $id;
                unset($_SESSION["project_id"]);
                $output = "ok";
            }
            break;
        case 'calendrier-get-bound-comptemail':
            if (array_key_exists("task_id", $_GET)) {
                $taskId = $_GET['task_id'];
                $userId = AppUser::getUser();
                $output = UserHasTask::getCompteMailIdsByTaskId($userId, $taskId);
            }
            break;
        case 'calendrier-get-mail-template':
            if (array_key_exists("task_id", $_GET)) {
                $taskId = $_GET['task_id'];


                $userId = AppUser::getUser();
                $items = UserHasTask::getCompteMailInfoByTaskId($userId, $taskId);


//                $projectId = Task::getProjectId($taskId);
//                $url = MailHelper::getBestUrlForProject($projectId);
//                $item = current($items);
//                $sName = $item['pseudo'];


                $output = [
                    'subject' => MailHelper::getDefaultSubject(),
                    'plain' => MailHelper::getDefaultPlainText(),
                    'recipients_info' => $items,
                ];
            }
            break;
        case 'calendrier-send-notif-mail':
            if (
                array_key_exists("task_id", $_POST) &&
                array_key_exists("subject", $_POST) &&
                array_key_exists("message", $_POST)
            ) {

                $taskId = $_POST['task_id'];
                $subject = $_POST['subject'];
                $message = $_POST['message'];


                $userId = AppUser::getUser();
                $projectId = Task::getProjectId($taskId);

                $nbSent = MailHelper::sendNotificationMail($userId, $projectId, $taskId, $subject, $message);
                $output = $nbSent;


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

