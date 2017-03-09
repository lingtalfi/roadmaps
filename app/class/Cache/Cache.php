<?php


namespace Cache;


use Bat\FileSystemTool;
use Task\TaskUtil;

class Cache
{

    public static $cacheDir;

    public static function getArray($fileName)
    {

        $file = self::$cacheDir . "/$fileName";
        if (file_exists($file)) {
            $arr = [];
            require $file;
            return $arr;
        }
        return false;

    }

    public static function cacheArray($fileName, array $array)
    {

        $file = self::$cacheDir . "/$fileName";
        FileSystemTool::mkfile($file);
        $s = '<?php $arr = ' . PHP_EOL . var_export($array, true) . ';' . PHP_EOL;
        file_put_contents($file, $s);

    }





    //--------------------------------------------
    //
    //--------------------------------------------
    public static function saveProjectTasks($projectId)
    {
        $hierarchy = TaskUtil::getTasksHierarchyByProject($projectId);
        $cacheName = 'project-' . $projectId . "-tasks.php";
        self::cacheArray($cacheName, $hierarchy);
    }

    public static function listProjectTasks($projectId){

    }


//    public static function restoreProjectTasks($projectId)
//    {
//
//        $hierarchy = TaskUtil::getTasksHierarchyByProject($projectId);
//        $cacheName = 'project-' . $projectId . "-tasks.php";
//        self::cacheArray($cacheName, $hierarchy);
//    }
//
//    public static function duplicate($id, $name)
//    {
//        $hierarchy = TaskUtil::getTasksHierarchyByProject($id);
//        $newId = self::insert([
//            "name" => $name,
//        ]);
//        foreach ($hierarchy as $item) {
//            $children = (array_key_exists("children", $item)) ? $item['children'] : [];
//            $item['id'] = null;
//            $item['parent_task_id'] = null;
//            $item['project_id'] = $newId;
//            unset($item['children']);
//            $taskId = Task::insert($item);
//
//            if (count($children) > 0) {
//                foreach ($children as $child) {
//                    self::duplicateParent($newId, $taskId, $child);
//                }
//            }
//        }
//    }
//
//    public static function duplicateParent($projectId, $taskId, array $item)
//    {
//        $children = (array_key_exists("children", $item)) ? $item['children'] : [];
//        $item['id'] = null;
//        $item['parent_task_id'] = $taskId;
//        $item['project_id'] = $projectId;
//        unset($item['children']);
//        $taskId = Task::insert($item);
//
//        if (count($children) > 0) {
//            foreach ($children as $child) {
//                self::duplicateParent($projectId, $taskId, $child);
//            }
//        }
//    }

}