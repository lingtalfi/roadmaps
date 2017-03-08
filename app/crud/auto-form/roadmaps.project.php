<?php


use Crud\CrudModule;

$form = CrudModule::getForm("roadmaps.project", ['id']);



$form->labels = [
    "id" => "id",
    "name" => "name",
    "current" => "current",
    "users_id" => "users",
];


$form->title = "Project";


$form->addControl("name")->type("text")
->addConstraint("required");
$form->addControl("current")->type("text");
$form->addControl("users_id")->type("selectByRequest", "select id, pseudo from roadmaps.users");


$form->display();
