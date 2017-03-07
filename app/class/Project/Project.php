<?php


namespace Project;


use QuickPdo\QuickPdo;

class Project
{


    public static function getName($projectId)
    {
        return QuickPdo::fetch("select `name` from project where id=" . (int)$projectId, [], \PDO::FETCH_COLUMN);
    }

    public static function getStartDate($projectId)
    {
        return QuickPdo::fetch("select min(start_date) from task where project_id=" . (int)$projectId, [], \PDO::FETCH_COLUMN);
    }

    public static function getId2Labels()
    {
        return QuickPdo::fetchAll("select id, name from project", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
    }

    public static function insert(array $data){
        return QuickPdo::insert("project", $data);
    }
}