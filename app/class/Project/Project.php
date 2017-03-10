<?php


namespace Project;


use QuickPdo\QuickPdo;
use Task\Task;
use Task\TaskUtil;

class Project
{


    public static function getName($projectId)
    {
        return QuickPdo::fetch("select `name` from project where id=" . (int)$projectId, [], \PDO::FETCH_COLUMN);
    }

    public static function getProjectCursorInfo($projectCursor)
    {
        if (null === $projectCursor) {
            return [0, date('Y-m-d 00:00:00')];
        }
        return explode(":", $projectCursor, 2);
    }


    public static function getInfo($projectId)
    {
        return QuickPdo::fetch("select * from project where id=" . (int)$projectId);
    }


    public static function getStartDate($projectId)
    {
        return QuickPdo::fetch("select min(start_date) from task where project_id=" . (int)$projectId, [], \PDO::FETCH_COLUMN);
    }

    public static function getId2Labels($userId)
    {
        return QuickPdo::fetchAll("select id, `name` from project where users_id=" . (int)$userId, [], \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
    }

    public static function insert(array $data)
    {
        return QuickPdo::insert("project", $data);
    }

    public static function duplicate($id, $name)
    {
        $info = self::getInfo($id);
        $info['name'] = $name;
        unset($info['id']);


        $hierarchy = TaskUtil::getTasksHierarchyByProject($id);
        $newId = self::insert($info);
        foreach ($hierarchy as $item) {
            $children = (array_key_exists("children", $item)) ? $item['children'] : [];
            $item['id'] = null;
            $item['parent_task_id'] = null;
            $item['project_id'] = $newId;
            unset($item['children']);
            $taskId = Task::insert($item);

            if (count($children) > 0) {
                foreach ($children as $child) {
                    self::duplicateParent($newId, $taskId, $child);
                }
            }
        }
    }

    public static function duplicateParent($projectId, $taskId, array $item)
    {
        $children = (array_key_exists("children", $item)) ? $item['children'] : [];
        $item['id'] = null;
        $item['parent_task_id'] = $taskId;
        $item['project_id'] = $projectId;
        unset($item['children']);
        $taskId = Task::insert($item);

        if (count($children) > 0) {
            foreach ($children as $child) {
                self::duplicateParent($projectId, $taskId, $child);
            }
        }
    }


    public static function setCursor($projectId, $cursor)
    {
        return QuickPdo::update("project", [
            "current" => $cursor,
        ], [
            ["id", "=", $projectId],
        ]);
    }

    public static function delete($id)
    {
        QuickPdo::delete("project", [
            ["id", "=", $id],
        ]);
    }


    public static function getPeriod($id)
    {
        $id = (int)$id;
        if (false !== ($ret = QuickPdo::fetch("select min(start_date) as mindate, max(end_date) as maxdate
        from task where project_id=$id
        "))
        ) {
            return [
                $ret["mindate"],
                $ret["maxdate"],
            ];
        }
        return false;
    }
}