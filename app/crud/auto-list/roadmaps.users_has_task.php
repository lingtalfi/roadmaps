<?php

use Crud\CrudHelper;
use Crud\CrudModule;

$fields = '
u.users_id,
us.pseudo as users_pseudo,
u.task_id,
t.label as task_label,
u.compte_mail_id,
c.pseudo as compte_mail_pseudo,
u.mail_sent
';


$query = "select
%s
from roadmaps.users_has_task u
inner join roadmaps.compte_mail c on c.id=u.compte_mail_id
inner join roadmaps.task t on t.id=u.task_id
inner join roadmaps.users us on us.id=u.users_id
";


$table = CrudModule::getDataTable("roadmaps.users_has_task", $query, $fields, ['users_id', 'task_id', 'compte_mail_id']);

$table->title = "Users has task";


$table->columnLabels= [
    "users_pseudo" => "users",
    "task_label" => "task",
    "compte_mail_pseudo" => "compte mail",
    "mail_sent" => "mail sent",
];


$table->hiddenColumns = [
    "users_id",
    "task_id",
    "compte_mail_id",
];


$table->setTransformer('users_pseudo', function ($v, array $item) {
    return '<a href="' . CrudHelper::getUpdateFormUrl('roadmaps.users', $item['users_id']) . '">' . $v . '</a>';
});

$table->setTransformer('task_label', function ($v, array $item) {
    return '<a href="' . CrudHelper::getUpdateFormUrl('roadmaps.task', $item['task_id']) . '">' . $v . '</a>';
});

$table->setTransformer('compte_mail_pseudo', function ($v, array $item) {
    return '<a href="' . CrudHelper::getUpdateFormUrl('roadmaps.compte_mail', $item['compte_mail_id']) . '">' . $v . '</a>';
});




$table->displayTable();
