<?php


namespace UserHasTask;

use QuickPdo\QuickPdo;


/**
 * user has attributed task to compte_mail(s)
 */
class UserHasTask
{

    public static function insert($userId, $taskId, array $compteMailIds, $mailSent = 0)
    {
        foreach ($compteMailIds as $compteMailId) {
            QuickPdo::insert('users_has_task', [
                'users_id' => $userId,
                'task_id' => $taskId,
                'compte_mail_id' => $compteMailId,
                'mail_sent' => $mailSent,
            ]);
        }
    }

    public static function update($userId, $taskId, array $compteMailIds, $mailSent = 0)
    {
        $userId = (int)$userId;
        $taskId = (int)$taskId;
        QuickPdo::freeQuery("delete from users_has_task where users_id=$userId and task_id=$taskId");
        self::insert($userId, $taskId, $compteMailIds, $mailSent);
    }


    public static function getCompteMailIdsByTaskId($userId, $taskId)
    {
        $userId = (int)$userId;
        $taskId = (int)$taskId;
        return QuickPdo::fetchAll("select id
from users_has_task u 
inner join compte_mail c on c.id=u.compte_mail_id
where u.users_id=$userId and u.task_id=$taskId 
", [], \PDO::FETCH_COLUMN);
    }


    public static function getCompteMailInfoByTaskId($userId, $taskId)
    {
        $userId = (int)$userId;
        $taskId = (int)$taskId;
        return QuickPdo::fetchAll("select 
u.users_id,
u.task_id,
u.compte_mail_id,
u.mail_sent,
c.pseudo,
c.email
 
from users_has_task u 
inner join compte_mail c on c.id=u.compte_mail_id
where u.users_id=$userId and u.task_id=$taskId 
");
    }


    public static function getMailHasBeenSent($userId, $taskId)
    {
        $userId = (int)$userId;
        $taskId = (int)$taskId;
        if (false !== ($res = QuickPdo::fetch("select max(mail_sent) as max from users_has_task WHERE 
users_id=$userId
and task_id=$taskId
"))
        ) {
            return (int)$res['max'] > 0;
        }
        return false;
    }

    public static function incrementMailSent($userId, $taskId, $compteMailId)
    {
        $userId = (int)$userId;
        $taskId = (int)$taskId;
        $compteMailId = (int)$compteMailId;

        $q = "update users_has_task set mail_sent = mail_sent+1 
where 
users_id=$userId and task_id=$taskId and compte_mail_id=$compteMailId
";

        return QuickPdo::freeExec($q);

    }
}