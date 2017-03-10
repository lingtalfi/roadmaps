<?php


use Crud\CrudModule;

$form = CrudModule::getForm("roadmaps.compte_mail", ['id']);



$form->labels = [
    "id" => "id",
    "pseudo" => "pseudo",
    "email" => "email",
];


$form->title = "Compte mail";


$form->addControl("pseudo")->type("text")
->addConstraint("required");
$form->addControl("email")->type("text");


$form->display();
