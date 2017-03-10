<?php

use Crud\CrudHelper;
use Crud\CrudModule;

$fields = '
id,
date_envoi,
task_id,
task_label,
task_start_date,
project_id,
project_name,
compte_mail_pseudo,
compte_mail_email,
task_end_date
';


$query = "select
%s
from roadmaps.historique_mail
";


$table = CrudModule::getDataTable("roadmaps.historique_mail", $query, $fields, ['id']);

$table->title = "Historique mail";


$table->columnLabels= [
    "id" => "id",
    "date_envoi" => "date envoi",
    "task_id" => "task",
    "task_label" => "task label",
    "task_start_date" => "task start date",
    "project_id" => "project",
    "project_name" => "project name",
    "compte_mail_pseudo" => "compte mail pseudo",
    "compte_mail_email" => "compte mail email",
    "task_end_date" => "task end date",
];


$table->hiddenColumns = [
    "id",
];


$table->displayTable();
