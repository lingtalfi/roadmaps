<?php


use Crud\CrudModule;

$form = CrudModule::getForm("roadmaps.users_has_task", ['users_id', 'task_id', 'compte_mail_id']);



$form->labels = [
    "users_id" => "users",
    "task_id" => "task",
    "compte_mail_id" => "compte mail",
    "mail_sent" => "mail sent",
];


$form->title = "Users has task";


$form->addControl("users_id")->type("selectByRequest", "select id, pseudo from roadmaps.users");
$form->addControl("task_id")->type("selectByRequest", "select id, label from roadmaps.task");
$form->addControl("compte_mail_id")->type("selectByRequest", "select id, pseudo from roadmaps.compte_mail");
$form->addControl("mail_sent")->type("text")
->value(0);


$form->display();
