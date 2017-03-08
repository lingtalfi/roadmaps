<?php

use Crud\CrudHelper;
use Crud\CrudModule;

$fields = '
p.id,
p.name,
p.current,
p.users_id,
u.pseudo as users_pseudo
';


$query = "select
%s
from roadmaps.project p
inner join roadmaps.users u on u.id=p.users_id
";


$table = CrudModule::getDataTable("roadmaps.project", $query, $fields, ['id']);

$table->title = "Project";


$table->columnLabels= [
    "id" => "id",
    "name" => "name",
    "current" => "current",
    "users_pseudo" => "users",
];


$table->hiddenColumns = [
    "id",
    "users_id",
];


$table->setTransformer('users_pseudo', function ($v, array $item) {
    return '<a href="' . CrudHelper::getUpdateFormUrl('roadmaps.users', $item['users_id']) . '">' . $v . '</a>';
});




$table->displayTable();
