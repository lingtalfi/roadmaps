<?php


use MailHelper\MailHelper;
use Project\Project;
use Task\Task;
use Umail\Umail;
use UserHasTask\UserHasTask;

require_once __DIR__ . "/../init.php";


$projectId = 1;
$userId = 1;
$taskId = 2;


$items = UserHasTask::getCompteMailInfoByTaskId($userId, $taskId);

$projectId = Task::getProjectId($taskId);
$url = MailHelper::getBestUrlForProject($projectId);


$names = [];
foreach ($items as $item) {
    $names[] = $item['pseudo'];
}
$sName = implode(', ', $names);


$output = [
    'subject' => MailHelper::getDefaultSubject(),
    'plain' => MailHelper::getDefaultPlainText($url, $sName),
    'recipients_info' => $items,
];


a($output);



