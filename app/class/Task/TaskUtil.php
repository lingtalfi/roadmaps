<?php


namespace Task;


use Calendar\CalendarApi;
use Period\Period;
use QuickPdo\QuickPdo;
use Util\GeneralUtil;

class TaskUtil
{

    public static function getTasksByProject($projectId, Period $p)
    {
        $ret = [];
        $items = QuickPdo::fetchAll("select * from task 
where project_id=" . (int)$projectId . "
and parent_task_id is null
order by `order` asc
");

        $level = 0;
        foreach ($items as $item) {
            $item['level'] = $level;
            self::addItem($item, $level, $ret);
        }


        // add hasChildren and timeStart/timeEnd helpers
        foreach ($ret as $k => $v) {

            $timeStart = GeneralUtil::gmMysqlToTime($v['start_date']);
            $timeEnd = GeneralUtil::gmMysqlToTime($v['end_date']);

            $ret[$k]['timeStart'] = $timeStart;
            $ret[$k]['timeEnd'] = $timeEnd;
            $ret[$k]['hasChildren'] = false;
            if (array_key_exists($k + 1, $ret)) {
                if ($ret[$k + 1]['level'] > $ret[$k]['level']) {
                    $ret[$k]['hasChildren'] = true;
                }
            }
        }


        //--------------------------------------------
        // NOW ALIGN TO THE CHOSEN PERIOD GRID
        // (to ensure gui consistency)
        //--------------------------------------------

        $periodStartTime = GeneralUtil::gmMysqlToTime($p->getStartDate());
        $interval = $p->getInterval();

        foreach ($ret as $k => $v) {
            $tStart = $v['timeStart'];
            $tEnd = $v['timeEnd'];
            $newStart = self::alignTime($tStart, $periodStartTime, $interval);
            $newEnd = self::alignTime($tEnd, $periodStartTime, $interval);
            $ret[$k]['timeStart'] = $newStart;
            $ret[$k]['timeEnd'] = $newEnd;
            $ret[$k]['start_date'] = gmdate("Y-m-d H:i:s", $newStart);
            $ret[$k]['end_date'] = gmdate("Y-m-d H:i:s", $newEnd);
        }

        return $ret;
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    private static function alignTime($time, $start, $interval)
    {
        if ($time >= $start) {
            while ($start <= $time) {
                $start += $interval;
            }
            $right = $start;
            $left = $start - $interval;
            $offsetRight = $right - $time;
            $offsetLeft = $time - $left;
        } else {
            while ($start > $time) {
                $start -= $interval;
            }
            $left = $start;
            $right = $start + $interval;
            $offsetLeft = $time - $left;
            $offsetRight = $right - $time;
        }
        if ($offsetRight <= $offsetLeft) {
            return $right;
        }
        return $left;
    }

    private static function addItem(array $item, $level, array &$items)
    {
        $items[] = $item;
        $children = self::getChildrenTasks($item['id']);
        if (count($children) > 0) {
            $level++;
            foreach ($children as $child) {
                $child['level'] = $level;
                self::addItem($child, $level, $items);
            }
        }
    }

    public static function getChildrenTasks($taskId)
    {
        return QuickPdo::fetchAll("select * from task 
where parent_task_id=" . (int)$taskId . "
order by `order` asc
");

    }

    public static function getChildrenIds($taskId, $recursive = true)
    {
        $ids = QuickPdo::fetchAll("select id from task 
where parent_task_id=" . (int)$taskId, [], \PDO::FETCH_COLUMN);
        if (true === $recursive) {
            foreach ($ids as $id) {
                $ids = array_merge($ids, self::getChildrenIds($id, true));
            }
        }
        return $ids;

    }

    public static function getTasksAfter($startDate)
    {
        return QuickPdo::fetchAll("select id from task where start_date > '$startDate'", [], \PDO::FETCH_COLUMN);
    }


    public static function collectChildrenIds($taskId, array &$ids)
    {
        $childrenIds = QuickPdo::fetchAll("select id from task 
where parent_task_id=" . (int)$taskId . "
", [], \PDO::FETCH_COLUMN);

        foreach ($childrenIds as $id) {
            $ids[] = $id;
            self::collectChildrenIds($id, $ids);
        }
    }

    public static function collectParentIds($taskId, array &$ids)
    {

        $parentId = QuickPdo::fetch("select parent_task_id from task 
where id=" . (int)$taskId . " 
", [], \PDO::FETCH_COLUMN);

        if (false !== $parentId && null !== $parentId) {
            $ids[] = $parentId;
            self::collectParentIds($parentId, $ids);
        }
    }


    public static function insertByDuration($projectId, $startDate, $nbDays, $parentId, $label, $position = "last")
    {
        $time = GeneralUtil::gmMysqlToTime($startDate);
        $dateStart = gmdate("Y-m-d H:i:s", $time);
        $time += $nbDays * 86400;
        $dateEnd = gmdate("Y-m-d H:i:s", $time);
        $parentId = (int)$parentId;
        $projectId = (int)$projectId;
        if (0 === $parentId) {
            $parentId = null;
        }


        if (false !== ($id = QuickPdo::insert("task", [
                'label' => $label,
                'start_date' => $dateStart,
                'end_date' => $dateEnd,
                'description' => "",
                'parent_task_id' => $parentId,
                'done' => 0,
                'project_id' => $projectId,
                'order' => 0,
            ]))
        ) {

            if ('last' === $position) {
                CalendarApi::sortBottom($id);
            } else {
                CalendarApi::sortTop($id);
            }
        }


    }
}