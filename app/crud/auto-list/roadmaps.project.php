<?php

use Crud\CrudHelper;
use Crud\CrudModule;

$fields = '
id,
name
';


$query = "select
%s
from roadmaps.project
";


$table = CrudModule::getDataTable("roadmaps.project", $query, $fields, ['id']);

$table->title = "Project";


$table->columnLabels= [
    "id" => "id",
    "name" => "name",
];


$table->hiddenColumns = [
    "id",
];


$table->displayTable();
