<?php

use Crud\CrudHelper;
use Crud\CrudModule;

$fields = '
id,
pseudo,
email
';


$query = "select
%s
from roadmaps.compte_mail
";


$table = CrudModule::getDataTable("roadmaps.compte_mail", $query, $fields, ['id']);

$table->title = "Compte mail";


$table->columnLabels= [
    "id" => "id",
    "pseudo" => "pseudo",
    "email" => "email",
];


$table->hiddenColumns = [
    "id",
];


$table->displayTable();
