<?php

use Crud\CrudHelper;
use Crud\CrudModule;

$fields = '
id,
pseudo,
pass,
avatar
';


$query = "select
%s
from roadmaps.users
";


$table = CrudModule::getDataTable("roadmaps.users", $query, $fields, ['id']);

$table->title = "Users";


$table->columnLabels= [
    "id" => "id",
    "pseudo" => "pseudo",
    "pass" => "pass",
    "avatar" => "avatar",
];


$table->hiddenColumns = [
    "id",
];


$table->displayTable();
