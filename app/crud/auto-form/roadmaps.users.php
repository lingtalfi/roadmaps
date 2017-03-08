<?php


use Crud\CrudModule;

$form = CrudModule::getForm("roadmaps.users", ['id']);



$form->labels = [
    "id" => "id",
    "pseudo" => "pseudo",
    "pass" => "pass",
    "avatar" => "avatar",
];


$form->title = "Users";


$form->addControl("pseudo")->type("text")
->addConstraint("required");
$form->addControl("pass")->type("text");
$form->addControl("avatar")->type("text");


$form->display();
