<?php


use Crud\CrudModule;

$form = CrudModule::getForm("roadmaps.historique_mail", ['id']);



$form->labels = [
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


$form->title = "Historique mail";


$form->addControl("date_envoi")->type("date6");
$form->addControl("task_id")->type("text")
->value(0);
$form->addControl("task_label")->type("text");
$form->addControl("task_start_date")->type("date6");
$form->addControl("project_id")->type("text");
$form->addControl("project_name")->type("text");
$form->addControl("compte_mail_pseudo")->type("text");
$form->addControl("compte_mail_email")->type("text");
$form->addControl("task_end_date")->type("date6");


$form->display();
