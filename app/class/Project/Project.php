<?php


namespace Project;


use QuickPdo\QuickPdo;

class Project
{


    public static function getName($projectId)
    {
        return QuickPdo::fetch("select `name` from project where id=" . (int)$projectId, [], \PDO::FETCH_COLUMN);
    }

}