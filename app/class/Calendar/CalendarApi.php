<?php


namespace Calendar;

use QuickPdo\QuickPdo;
use Task\Task;
use Task\TaskUtil;
use Util\GeneralUtil;

/**
 * This is the api for the calendar.
 * The model I have in mind follows physical principles, so that it's intuitive to work with.
 *
 * Basically, you have two roles that tasks can have:
 *
 * - container (tasks which contain other tasks)
 * - leaf (task which does not contain other tasks)
 *
 *
 * A container can be extended without altering its children.
 * When a container is shrunk, all its children are trimmed so that they cannot extend
 * beyond the limit of their container.
 * In other words, the container is like a physical stretchable box.
 *
 * An leaf can be extend too.
 * When a leaf is extended, it forces its container(s) to extend with it, so that
 * no leaf can be taller than its container.
 *
 *
 * When a container is moved, all its children moves along with it.
 *
 */
class CalendarApi
{

    public static function setStartDate($taskId, $newStartDate)
    {
        $currentStartDate = Task::getStartDate($taskId);


        QuickPdo::update("task", [
            "start_date" => $newStartDate,
        ], [
            ["id", "=", $taskId],
        ]);
        GeneralUtil::debugLog(QuickPdo::getQuery(), "setStartDate:main");


        if (true === Task::hasChildren($taskId)) {
            //--------------------------------------------
            // CHILDREN PART
            //--------------------------------------------

            if ($newStartDate > $currentStartDate) {

                // trim children
                $childrenIds = [];
                TaskUtil::collectChildrenIds($taskId, $childrenIds);
                if (count($childrenIds) > 0) {

                    $sIds = implode(', ', $childrenIds);

                    $q = "
update task set start_date='$newStartDate' 
where 
id in ($sIds)
and start_date < '$newStartDate'
                ";
                    GeneralUtil::debugLog($q, "setStartDate:children");
                    QuickPdo::freeQuery($q);
                }
            }
        } else {

            //--------------------------------------------
            // PARENTS PART
            //--------------------------------------------
            if ($newStartDate < $currentStartDate) {
                // extend parents
                $parentIds = [];
                TaskUtil::collectParentIds($taskId, $parentIds);

                if (count($parentIds) > 0) {

                    $sIds = implode(', ', $parentIds);

                    $q = "
update task set start_date='$newStartDate' 
where 
id in ($sIds)
and start_date > '$newStartDate'
                ";
                    GeneralUtil::debugLog($q, "setStartDate:parents");
                    QuickPdo::freeQuery($q);
                }
            }
        }
    }

    public static function setEndDate($taskId, $newEndDate)
    {
        $currentEndDate = Task::getEndDate($taskId);

        QuickPdo::update("task", [
            "end_date" => $newEndDate,
        ], [
            ["id", "=", $taskId],
        ]);
        GeneralUtil::debugLog(QuickPdo::getQuery(), "setEndDate:main");


        if (true === Task::hasChildren($taskId)) {
            //--------------------------------------------
            // CHILDREN PART
            //--------------------------------------------

            if ($newEndDate < $currentEndDate) {

                // trim children
                $childrenIds = [];
                TaskUtil::collectChildrenIds($taskId, $childrenIds);
                if (count($childrenIds) > 0) {

                    $sIds = implode(', ', $childrenIds);

                    $q = "
update task set end_date='$newEndDate' 
where 
id in ($sIds)
and end_date > '$newEndDate'
                ";
                    GeneralUtil::debugLog($q, "setEndDate:children");
                    QuickPdo::freeQuery($q);
                }
            }
        } else {

            //--------------------------------------------
            // PARENTS PART
            //--------------------------------------------
            if ($newEndDate > $currentEndDate) {
                // extend parents
                $parentIds = [];
                TaskUtil::collectParentIds($taskId, $parentIds);

                if (count($parentIds) > 0) {

                    $sIds = implode(', ', $parentIds);

                    $q = "
update task set end_date='$newEndDate' 
where 
id in ($sIds)
and end_date < '$newEndDate'
                ";
                    GeneralUtil::debugLog($q, "setEndDate:parents");
                    QuickPdo::freeQuery($q);
                }
            }
        }
    }


    public static function move($taskId, $offset)
    {

        $parentIds = [];
        TaskUtil::collectParentIds($taskId, $parentIds);

        $offset = (int)$offset;
        $addOp = "DATE_ADD";
        $offsetIsPositive = true;
        if ($offset < 0) {
            $offsetIsPositive = false;
            $addOp = "DATE_SUB";
            $offset = -$offset;
            $originalDate = Task::getStartDate($taskId);
        } else {
            $originalDate = Task::getEndDate($taskId);
        }
        $childrenIds = [$taskId];
        TaskUtil::collectChildrenIds($taskId, $childrenIds);
        $sIds = implode(', ', $childrenIds);
        $q = "
update task set
start_date=$addOp(start_date, INTERVAL $offset second),
end_date=$addOp(end_date, INTERVAL $offset second)
where id in ($sIds)";
        GeneralUtil::debugLog($q, "move:main");
        QuickPdo::freeQuery($q);


        if (count($parentIds) > 0) {
            $sParentIds = implode(', ', $parentIds);
            if (true === $offsetIsPositive) {
                $limitTime = GeneralUtil::gmMysqlToTime($originalDate) + $offset;
                $limitDate = gmdate("Y-m-d H:i:s", $limitTime);

                $q = "
update task set
end_date='$limitDate'
where 
id in ($sParentIds)
and end_date <'$limitDate'
";
            } else {
                $limitTime = GeneralUtil::gmMysqlToTime($originalDate) - $offset;
                $limitDate = gmdate("Y-m-d H:i:s", $limitTime);

                $q = "
update task set
start_date='$limitDate'
where 
id in ($sParentIds)
and start_date >'$limitDate'
";
            }
            GeneralUtil::debugLog($q, "move:parents");
            QuickPdo::freeExec($q);

        }

    }


    public static function sortUp($taskId)
    {
        $childrenIds = Task::getOrderedChildren($taskId);
        $taskId = (int)$taskId;

        $pos = null;
        foreach ($childrenIds as $k => $id) {
            if ((int)$id === $taskId) {
                unset($childrenIds[$k]);
                $pos = $k;
            }
        }
        $pos--;
        if ($pos < 0) {
            $pos = 0;
        }
        array_splice($childrenIds, $pos, 0, $taskId);
        foreach ($childrenIds as $order => $id) {
            QuickPdo::update("task", [
                'order' => $order,
            ], [
                ["id", "=", $id],
            ]);
        }
    }

    public static function sortDown($taskId)
    {
        $childrenIds = Task::getOrderedChildren($taskId);

        $taskId = (int)$taskId;

        $pos = null;
        foreach ($childrenIds as $k => $id) {
            if ((int)$id === $taskId) {
                unset($childrenIds[$k]);
                $pos = $k;
            }
        }
        $pos++;
        $max = count($childrenIds);
        if ($pos > $max) {
            $pos = $max;
        }
        array_splice($childrenIds, $pos, 0, $taskId);

        foreach ($childrenIds as $order => $id) {
            QuickPdo::update("task", [
                'order' => $order,
            ], [
                ["id", "=", $id],
            ]);
        }
    }

    public static function sortTop($taskId)
    {
        $childrenIds = Task::getOrderedChildren($taskId);
        $taskId = (int)$taskId;

        $pos = null;
        foreach ($childrenIds as $k => $id) {
            if ((int)$id === $taskId) {
                unset($childrenIds[$k]);
                $pos = $k;
            }
        }
        $pos = 0;
        array_splice($childrenIds, $pos, 0, $taskId);
        foreach ($childrenIds as $order => $id) {
            QuickPdo::update("task", [
                'order' => $order,
            ], [
                ["id", "=", $id],
            ]);
        }
    }


    public static function sortBottom($taskId)
    {
        $childrenIds = Task::getOrderedChildren($taskId);

        $taskId = (int)$taskId;

        $pos = null;
        foreach ($childrenIds as $k => $id) {
            if ((int)$id === $taskId) {
                unset($childrenIds[$k]);
                $pos = $k;
            }
        }
        $pos++;
        $max = count($childrenIds);
        $pos = $max;
        array_splice($childrenIds, $pos, 0, $taskId);

        foreach ($childrenIds as $order => $id) {
            QuickPdo::update("task", [
                'order' => $order,
            ], [
                ["id", "=", $id],
            ]);
        }
    }

}