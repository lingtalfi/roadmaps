<?php


namespace Users;


use QuickPdo\QuickPdo;

class Users
{
    public static function getId2Labels()
    {
        return QuickPdo::fetchAll("select id, pseudo from users", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
    }

    public static function getInfos()
    {
        return QuickPdo::fetchAll("select * from users");
    }

    public static function getIdByCredentials($pseudo, $pass)
    {
        $ret =  QuickPdo::fetch("select id from users where pseudo=:pseudo and pass=:pass", [
            'pseudo' => $pseudo,
            'pass' => $pass,
        ], \PDO::FETCH_COLUMN);
        return $ret;
    }

}