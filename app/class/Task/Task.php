<?php


namespace Task;


use QuickPdo\QuickPdo;
use Util\GeneralUtil;

class Task
{
    public static function insertByLabel($label, $projectId)
    {
        $data = [
            "label" => $label,
            "start_date" => date("Y-m-d"),
            "end_date" => date("Y-m-d"),
            "description" => "",
            "parent_task_id" => null,
            "done" => 0,
            "project_id" => $projectId,
            "order" => 0,
        ];
        return QuickPdo::insert("task", $data);
    }


    public static function getStartDate($taskId)
    {
        if (false !== ($res = QuickPdo::fetch("select start_date from task where id=" . (int)$taskId))) {
            return $res['start_date'];
        }
        return false;
    }

    public static function getEndDate($taskId)
    {
        if (false !== ($res = QuickPdo::fetch("select end_date from task where id=" . (int)$taskId))) {
            return $res['end_date'];
        }
        return false;
    }

    public static function getStartTime($taskId)
    {
        if (false !== ($startDate = self::getStartDate($taskId))) {
            return GeneralUtil::gmMysqlToTime($startDate);
        }
        return false;
    }

    public static function getEndTime($taskId)
    {
        if (false !== ($endDate = self::getEndDate($taskId))) {
            return GeneralUtil::gmMysqlToTime($endDate);
        }
        return false;
    }


    public static function hasChildren($taskId)
    {
        if (false !== ($res = QuickPdo::fetch("select id from task where parent_task_id=" . (int)$taskId))) {
            return true;
        }
        return false;
    }


}