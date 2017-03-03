<?php


namespace Calendar;

use QuickPdo\QuickPdo;
use Task\Task;
use Task\TaskUtil;

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

        if (true === Task::hasChildren($taskId)) {
            //--------------------------------------------
            // PARENT PART
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
                    QuickPdo::freeQuery($q);
                }
            }
        } else {

            //--------------------------------------------
            // CHILD PART
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
                    QuickPdo::freeQuery($q);
                }
            }
        }
    }

    public static function setEndDate($taskId, $endDate)
    {
    }

}