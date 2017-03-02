<?php


namespace Task;


use QuickPdo\QuickPdo;

class TaskUtil
{

    public static function getTasksByProject($projectId)
    {
        $ret = [];
        $items = QuickPdo::fetchAll("select * from task 
where project_id=" . (int)$projectId . "
and parent_task_id is null
order by start_date asc
");

        $level = 0;
        foreach ($items as $item) {
            $item['level'] = $level;
            self::addItem($item, $level, $ret);
        }


        // add hasChildren and timeStart/timeEnd helpers
        foreach ($ret as $k => $v) {

            $timeStart = strtotime($v['start_date']);
            $timeEnd = strtotime($v['end_date']);

            $ret[$k]['timeStart'] = $timeStart;
            $ret[$k]['timeEnd'] = $timeEnd;
            $ret[$k]['hasChildren'] = false;
            if (array_key_exists($k + 1, $ret)) {
                if ($ret[$k + 1]['level'] > $ret[$k]['level']) {
                    $ret[$k]['hasChildren'] = true;
                }
            }
        }

        return $ret;
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
order by start_date asc
");

    }

}