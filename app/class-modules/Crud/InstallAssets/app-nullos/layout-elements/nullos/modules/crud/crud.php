<?php


use Crud\CrudConfig;

$table = null;
$action = "list";
$logIdentifier = "crud.page.table";

if (array_key_exists('name', $_GET)) {
    $table = (string)$_GET['name'];


    if (array_key_exists('action', $_GET)) {
        $action = (string)$_GET['action'];
    }

    $actions = ['list', 'edit', 'insert'];

    if (in_array($action, $actions, true)) {
        if ('list' !== $action) {
            $action = 'form';
        }


        if ('list' === $action) {
            //--------------------------------------------
            // First try the user file, otherwise try the auto-generated file if any
            //--------------------------------------------
            $file = CrudConfig::getCrudListDir() . '/' . $table . '.php';
            if (file_exists($file)) {
                require_once $file;
            } else {
                $file = CrudConfig::getCrudGenListDir() . '/' . $table . '.php';
                if (file_exists($file)) {
                    require_once $file;
                } else {
                    Logger::log("file does not exist: $file", $logIdentifier);
                }
            }
        } else {
            //--------------------------------------------
            // First try the user file, otherwise try the auto-generated file if any
            //--------------------------------------------
            $file = CrudConfig::getCrudFormDir() . '/' . $table . '.php';
            if (file_exists($file)) {
                require_once $file;
            } else {
                $file = CrudConfig::getCrudGenFormDir() . '/' . $table . '.php';
                if (file_exists($file)) {
                    require_once $file;
                } else {
                    Logger::log("file does not exist: $file", $logIdentifier);
                }
            }
        }
    }


}








