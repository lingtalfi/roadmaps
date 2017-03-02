<?php


namespace Task;


use QuickPdo\QuickPdo;

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

}