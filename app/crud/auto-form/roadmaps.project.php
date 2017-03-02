<?php


use Crud\CrudModule;

$form = CrudModule::getForm("roadmaps.project", ['id']);



$form->labels = [
    "id" => "id",
    "name" => "name",
];


$form->title = "Project";


$form->addControl("name")->type("text")
->addConstraint("required");


$form->display();
