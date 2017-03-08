<?php


namespace Users;


use QuickPdo\QuickPdo;

class Users
{
    public static function getId2Labels()
    {
        return QuickPdo::fetchAll("select id, pseudo from users", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
    }

    public static function getInfos(){
        return QuickPdo::fetchAll("select * from users");
    }

}