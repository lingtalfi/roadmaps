<?php


use Crud\CrudModule;

$form = CrudModule::getForm("roadmaps.task", ['id']);



$form->labels = [
    "id" => "id",
    "label" => "label",
    "start_date" => "start date",
    "end_date" => "end date",
    "description" => "description",
    "parent_task_id" => "parent task",
    "done" => "done",
    "project_id" => "project",
    "order" => "order",
];


$form->title = "Task";


$form->addControl("label")->type("text")
->addConstraint("required");
$form->addControl("start_date")->type("date6");
$form->addControl("end_date")->type("date6");
$form->addControl("description")->type("message");
$form->addControl("parent_task_id")->type("selectByRequest", "select id, label from roadmaps.task");
$form->addControl("done")->type("text")
->value(0);
$form->addControl("project_id")->type("selectByRequest", "select id, name from roadmaps.project");
$form->addControl("order")->type("text")
->value(0);


$form->display();
