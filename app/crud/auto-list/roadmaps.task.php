<?php

use Crud\CrudHelper;
use Crud\CrudModule;

$fields = '
t.id,
t.label,
t.start_date,
t.end_date,
t.description,
t.parent_task_id,
ta.label as task_label,
t.done,
t.project_id,
p.name as project_name,
t.order,
t.color
';


$query = "select
%s
from roadmaps.task t
inner join roadmaps.project p on p.id=t.project_id
inner join roadmaps.task ta on ta.id=t.parent_task_id
";


$table = CrudModule::getDataTable("roadmaps.task", $query, $fields, ['id']);

$table->title = "Task";


$table->columnLabels= [
    "id" => "id",
    "label" => "label",
    "start_date" => "start date",
    "end_date" => "end date",
    "description" => "description",
    "task_label" => "parent task",
    "done" => "done",
    "project_name" => "project",
    "order" => "order",
    "color" => "color",
];


$table->hiddenColumns = [
    "id",
    "parent_task_id",
    "project_id",
];


$n = 30;
$table->setTransformer('description', function ($v) use ($n) {
    return substr($v, 0, $n) . '...';
});


$table->setTransformer('task_label', function ($v, array $item) {
    return '<a href="' . CrudHelper::getUpdateFormUrl('roadmaps.task', $item['parent_task_id']) . '">' . $v . '</a>';
});

$table->setTransformer('project_name', function ($v, array $item) {
    return '<a href="' . CrudHelper::getUpdateFormUrl('roadmaps.project', $item['project_id']) . '">' . $v . '</a>';
});




$table->displayTable();
