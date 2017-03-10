<?php


namespace CompteMail;


use QuickPdo\QuickPdo;

class CompteMail
{
    public static function getId2Labels()
    {
        return QuickPdo::fetchAll("select id, pseudo from compte_mail", [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
    }

}