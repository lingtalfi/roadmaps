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

    public static function delete($taskId)
    {
        QuickPdo::delete("task", [
            ["id", "=", $taskId],
        ]);
    }

    public static function insert(array $item)
    {
        return QuickPdo::insert("task", $item);
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

    public static function getProjectId($taskId)
    {
        return QuickPdo::fetch("select project_id from task where id=" . (int)$taskId, [], \PDO::FETCH_COLUMN);
    }

    public static function getParent($taskId)
    {
        return QuickPdo::fetch("select parent_task_id from task where id=" . (int)$taskId, [], \PDO::FETCH_COLUMN);
    }


    public static function getOrderedChildren($taskId)
    {
        $parentId = self::getParent($taskId);
        if (null === $parentId) {
            return QuickPdo::fetchAll("select id from task 
where parent_task_id is null order by `order` asc", [], \PDO::FETCH_COLUMN);
        } else {
            return QuickPdo::fetchAll("select id from task 
where parent_task_id=$parentId order by `order` asc", [], \PDO::FETCH_COLUMN);
        }

    }


    public static function update($taskId, array $data, $applyColorToChildren = true)
    {
        QuickPdo::update("task", $data, [
            ["id", "=", $taskId],
        ]);

        if (true === $applyColorToChildren && array_key_exists('color', $data)) {
            $ids = TaskUtil::getChildrenIds($taskId);
            $sIds = implode(", ", $ids);
            $q = "update task set color=:color where id in ($sIds)";
            QuickPdo::freeQuery($q, [
                'color' => $data['color'],
            ]);
        }
    }

    public static function getInfo($taskId)
    {
        return QuickPdo::fetch("select * from task where id=" . (int)$taskId);
    }

    public static function parentLevelUp($taskId)
    {
        $parent = self::getParent($taskId);
        if (null !== $parent) {
            $parentItem = self::getInfo($parent);
            QuickPdo::update("task", [
                "parent_task_id" => $parentItem['parent_task_id'],
                "order" => $parentItem['order'] + 1,
            ], [
                ["id", "=", $taskId],
            ]);
        }
    }

    public static function parentLevelDown($taskId, $prevId)
    {

        if (false !== ($prevItem = self::getInfo($prevId))) {
            if (false !== ($item = self::getInfo($taskId))) {

                $itemParent = $item['parent_task_id'];
                $prevItemParent = $prevItem['parent_task_id'];

                if ($itemParent === $prevItemParent) {
                    QuickPdo::update("task", [
                        "parent_task_id" => $prevId,
                    ], [
                        ["id", "=", $taskId],
                    ]);
                } else {
                    /**
                     * Become sibling with the last child
                     * of the previous sibling
                     */
                    $allChildren = Task::getOrderedChildren($taskId);
                    $previousSiblingId = $taskId;
                    foreach ($allChildren as $id) {
                        if ((int)$id === (int)$taskId) {
                            break;
                        }
                        $previousSiblingId = $id;
                    }


                    $q = "select MAX(`order`) as count from task where parent_task_id=" . (int)$previousSiblingId;
                    $order = QuickPdo::fetch($q, [], \PDO::FETCH_COLUMN);


                    QuickPdo::update("task", [
                        "parent_task_id" => $previousSiblingId,
                        "order" => $order + 1,
                    ], [
                        ["id", "=", $taskId],
                    ]);
                }
            }
        }
    }
}